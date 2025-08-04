<?php
/**
 * Copyright since 2022 Younited Credit
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@202-ecommerce.com so we can send you a copy immediately.
 *
 * @author	 202 ecommerce <tech@202-ecommerce.com>
 * @copyright 2022 Younited Credit
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

namespace YounitedpayAddon\API;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration;
use Exception;
use Younitedpay;
use YounitedpayAddon\Logger\ApiLogger;
use YounitedpayAddon\Utils\CacheYounited;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use YounitedPaySDK\Cache\Registry;
use YounitedPaySDK\Cache\RegistryItem;
use YounitedPaySDK\Client;
use YounitedPaySDK\Model\AbstractModel;
use YounitedPaySDK\Request\AbstractRequest;
use YounitedPaySDK\Response\AbstractResponse;

class YounitedClient
{
    /** @var string */
    public $clientId;

    /** @var string */
    public $clientSecret;

    /** @var string */
    public $shopCode;

    /** @var string */
    public $webHookSecret;

    /** @var bool */
    public $isProductionMode;

    /** @var ProcessLoggerHandler */
    public $logger;

    /** @var ApiLogger */
    public $apiLogger;

    public function __construct($idShop, $testCredentials = [])
    {
        $this->logger = ServiceContainer::getInstance()->get(ProcessLoggerHandler::class);

        if (empty($testCredentials) === false) {
            $this->apiLogger = ApiLogger::getInstance(true);
            $this->testCredentials($testCredentials);
        } else {
            $this->apiLogger = ApiLogger::getInstance();
            $this->setApiCredentials($idShop);
        }
    }

    private function testCredentials($testCredentials)
    {
        $this->clientId = $testCredentials['client_id'];
        $this->clientSecret = $testCredentials['client_secret'];
        $this->shopCode = $testCredentials['shop_code'] ?? '';
        $this->isProductionMode = $testCredentials['production_mode'];
        $this->webHookSecret = $testCredentials['webhook_secret'];
    }

    public function isCrendentialsSet()
    {
        return empty($this->clientId) === false && empty($this->clientSecret) === false;
    }

    /**
     * Send API Requests
     *
     * @return array ['response' => mixed, 'success' => bool]
     */
    public function sendRequest($body, $requestObject)
    {
        $client = new Client();
        $this->getTokenCache($client);
        try {
            /** @var AbstractRequest $request */
            $request = $requestObject;
            if (empty((string) $request->getBody()) && $body instanceof AbstractModel) {
                $request = $requestObject->setModel($body);
            }
            if ($this->isProductionMode === false) {
                $request = $request->enableSandbox();
            }

            $classRequest = (new \ReflectionClass($requestObject))->getShortName();

            $this->apiLogger->log($this, $request, 'Request ' . $classRequest, true);

            /** @var AbstractResponse $response */
            $response = $client
                ->setCredential($this->clientId, $this->clientSecret)
                ->sendRequest($request);

            $this->apiLogger->log($this, $response, 'Response' . $classRequest, true);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 401) {
                $this->setTokenCache();
            }

            $successStatuscode = [200, 201, 204];

            if (in_array($statusCode, $successStatuscode) === true) {
                return [
                    'response' => $response->getModel(),
                    'status' => $response->getStatusCode(),
                    'success' => true,
                ];
            }

            $errorResponse = [
                'response' => $response->getReasonPhrase(),
                'status' => $response->getStatusCode(),
                'success' => false,
            ];

            $this->logger->logError(
                sprintf($response->getReasonPhrase()),
                (new \ReflectionClass($this))->getShortName(),
                null,
                'Error Request' . $classRequest
            );

            $this->apiLogger->log($this, $errorResponse, 'Error Response ' . $classRequest, true);

            return $errorResponse;
        } catch (Exception $e) {
            return $this->setErrorMessage($e, (new \ReflectionClass($requestObject))->getShortName());
        }
    }

    /**
     * Get Token in Cache of PrestaShop
     * Set the token of the client if there's some token in cache
     */
    private function getTokenCache(Client $client)
    {
        /** @var CacheYounited $cacheStorage */
        $cacheStorage = new CacheYounited();

        $cacheExists = $cacheStorage->exist('token_api');

        if ($cacheExists === true) {
            $cacheInformations = $cacheStorage->get('token_api');
            $this->apiLogger->log($this, 'token exists in cache: ' . json_encode($cacheInformations), 'Info');
            $token = $cacheInformations['content']['token'];
            /** @var \DateTimeInterface $expireAt */
            $expireAt = $cacheInformations['content']['expiresat'];
            $client->setTokenCache($token, $expireAt->getTimestamp());
        }
    }

    /**
     * Set the token in cache of PrestaShop
     * Called after a request, always refresh cache token and expiration
     */
    private function setTokenCache()
    {
        /** @var CacheYounited $cacheStorage */
        $cacheStorage = new CacheYounited();

        $cache = Registry::getInstance();
        /** @var RegistryItem $cacheTokenItem */
        $cacheTokenItem = $cache->getItem('token');

        $cacheStorage->set('token_api', [
            'token' => $cacheTokenItem->get(),
            'expiresat' => $cacheTokenItem->getExpiredDate(),
        ]);
    }

    private function setErrorMessage($e, $classRequest)
    {
        $this->logger->logError(
            sprintf($e->getMessage()),
            (new \ReflectionClass($this))->getShortName(),
            null,
            'Error Request' . $classRequest
        );

        $errorMsg = [
            'msg' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];
        $this->apiLogger->log($this, $errorMsg, 'Error', true);

        return [
            'response' => $e->getMessage(),
            'success' => false,
        ];
    }

    private function setApiCredentials($idShop)
    {
        $this->isProductionMode = (bool) Configuration::get(
            Younitedpay::PRODUCTION_MODE,
            null,
            null,
            $idShop,
            false
        );
        $suffix = $this->isProductionMode === true ? '_PRODUCTION' : '';
        $this->clientId = Configuration::get(
            Younitedpay::CLIENT_ID . $suffix,
            null,
            null,
            $idShop,
            ''
        );
        $this->clientSecret = Configuration::get(
            Younitedpay::CLIENT_SECRET . $suffix,
            null,
            null,
            $idShop,
            ''
        );
        $this->shopCode = Configuration::get(
            Younitedpay::SHOP_CODE . $suffix,
            null,
            null,
            $idShop,
            ''
        );
        $this->webHookSecret = Configuration::get(
            Younitedpay::WEBHOOK_SECRET . $suffix,
            null,
            null,
            $idShop,
            ''
        );
    }
}
