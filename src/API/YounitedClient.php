<?php
/**
 * Copyright Younited
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
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @copyright Younited
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

namespace YounitedpayAddon\API;

use Configuration;
use Exception;
use Younitedpay;
use YounitedpayAddon\Logger\ApiLogger;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use YounitedPaySDK\Client;
use YounitedPaySDK\Model\BestPrice;
use YounitedPaySDK\Request\BestPriceRequest;
use YounitedPaySDK\Response\BestPriceResponse;

class YounitedClient
{
    /** @var string */
    public $clientId;

    /** @var string */
    public $clientSecret;

    /** @var string */
    public $webHookSecret;

    /** @var bool */
    public $isProductionMode;

    /** @var ProcessLoggerHandler */
    public $logger;

    /** @var ApiLogger */
    public $apiLogger;

    public function __construct($idShop, ProcessLoggerHandler $logger, $testCredentials = [])
    {
        $this->logger = $logger;
        $this->apiLogger = ApiLogger::getInstance();

        if (empty($testCredentials) === false) {
            $this->testCredentials($testCredentials);
        } else {
            $this->setApiCredentials($idShop);
        }
    }

    private function testCredentials($testCredentials)
    {
        $this->clientId = $testCredentials['client_id'];
        $this->clientSecret = $testCredentials['client_secret'];
        $this->isProductionMode = $testCredentials['production_mode'];
        $this->webHookSecret = $testCredentials['webhook_secret'];
    }

    public function isCrendentialsSet()
    {
        return empty($this->clientId) === false && empty($this->clientSecret) === false;
    }

    /**
     * @return array
     */
    public function getBestPrice($amount)
    {
        $client = new Client();
        try {
            $body = new BestPrice();
            $body->setBorrowedAmount($amount);

            if ($this->isProductionMode === false) {
                $request = (new BestPriceRequest())
                ->enableSanbox()
                ->setModel($body);
            } else {
                $request = (new BestPriceRequest())
                ->setModel($body);
            }

            $this->apiLogger->log($this, $request, 'Request', true);

            /** @var BestPriceResponse $response */
            $response = $client->setCredential($this->clientId, $this->clientSecret)
                ->sendRequest($request);

            $this->apiLogger->log($this, $response, 'Response', true);

            if ($response->getStatusCode() === 200) {
                return [
                    'message' => $response->getBody(),
                    'success' => true,
                ];
            }

            return [
                'message' => $response->getReasonPhrase(),
                'success' => false,
            ];
        } catch (Exception $e) {
            $this->logger->logError(
                sprintf($e->getMessage()),
                (new \ReflectionClass($this))->getShortName(),
                null,
                'Error BestPrice Request'
            );

            $errorMsg = [
                'msg' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
            $this->apiLogger->log($this, $errorMsg, 'Error', true);

            throw $e;
        }
    }

    private function setApiCredentials($idShop)
    {
        $this->clientId = Configuration::get(
            Younitedpay::CLIENT_ID,
            null,
            null,
            $idShop,
            ''
        );
        $this->clientSecret = Configuration::get(
            Younitedpay::CLIENT_SECRET,
            null,
            null,
            $idShop,
            ''
        );
        $this->isProductionMode = (bool) Configuration::get(
            Younitedpay::PRODUCTION_MODE,
            null,
            null,
            $idShop,
            false
        );
        $this->webHookSecret = Configuration::get(
            Younitedpay::WEBHOOK_SECRET,
            null,
            null,
            $idShop,
            ''
        );
    }
}
