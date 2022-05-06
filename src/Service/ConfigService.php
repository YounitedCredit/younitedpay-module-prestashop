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

namespace YounitedpayAddon\Service;

use Configuration;
use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Logger\ApiLogger;
use YounitedpayAddon\Repository\ConfigRepository;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use YounitedPaySDK\Model\BestPrice;
use YounitedPaySDK\Model\OfferItem;
use YounitedPaySDK\Request\BestPriceRequest;

class ConfigService
{
    public $module;

    private $curl;

    /** @var \Context */
    private $context;

    /** @var ProcessLoggerHandler */
    protected $logger;

    /** @var ConfigRepository */
    protected $configRepository;

    public function __construct(
        ProcessLoggerHandler $logger,
        ConfigRepository $configRepository,
        Younitedpay $module
    ) {
        $this->module = $module;
        $this->logger = $logger;
        $this->context = \Context::getContext();
        $this->configRepository = $configRepository;
    }

    /**
     * Check TLS version 1.2 compability : CURL request to server
     */
    public function isTlsActive()
    {
        $return = [
            'status' => false,
            'error_message' => '',
        ];

        $tls_server = $this->context->link->getModuleLink($this->module->name, 'testcurltls');
        $response = $this->callCURL($tls_server);
        $curl = $this->curl;

        if (trim($response) != 'ok') {
            $return['status'] = false;
            $curl_info = curl_getinfo($curl);
            if ($curl_info['http_code'] == 401) {
                $return['error_message'] = '401 Unauthorised - check htaccess and rights in server.';
            } else {
                $return['error_message'] = curl_error($curl);
            }

            $this->logger->openLogger();
            $this->logger->logError(
                $return['error_message'],
                (new \ReflectionClass($this))->getShortName(),
                null,
                'configuration - TLS test'
            );
            $this->logger->closeLogger();

            $return['error_message'] = $this->module->l('TLS call failed');
        } else {
            $return['status'] = true;
        }

        return $return;
    }

    /**
     * Make a best price request to test if the API is connected
     *
     * @return array string message | array maturityList | bool status
     */
    public function isApiConnected()
    {
        $client = new YounitedClient($this->context->shop->id);
        if ($client->isCrendentialsSet() === false) {
            return [
                'message' => $this->module->l('No credential saved'),
                'maturityList' => [3, 4, 5, 10],
                'status' => false,
            ];
        }

        $body = new BestPrice();
        $body->setBorrowedAmount(15000.00);

        $request = new BestPriceRequest();

        /** @var array $response */
        $response = $client->sendRequest($body, $request);

        if (empty($response) === true || null === $response || $response['success'] === false) {
            return [
                'message' => $this->module->l('Response error'),
                'maturityList' => [3, 4, 5, 10],
                'status' => false,
            ];
        }

        return [
            'message' => $this->module->l('Connexion Ok'),
            'maturityList' => $this->getMaturitiesResponse($response['response']),
            'status' => true,
        ];
    }

    /**
     * @param OfferItem[] $response
     * */
    private function getMaturitiesResponse($response)
    {
        $maturityList = [];
        foreach ($response as $oneItem) {
            $maturityList[] = $oneItem->getMaturityInMonths();
        }

        return $maturityList;
    }

    public function callCURL($url)
    {
        if (defined('CURL_SSLVERSION_TLSv1_2') == false) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

        $response = curl_exec($curl);
        $this->curl = $curl;

        $apiLogger = ApiLogger::getInstance();
        if (\Younitedpay::IS_FILE_LOGGER_ACTIVE === true) {
            $apiLogger->log($this, $response, 'Config Response', false);
        }

        return $response;
    }

    public function checkSpecifications($isProductionMode)
    {
        $curlInfos = curl_version();
        $versionOpenSSL = null !== OPENSSL_VERSION_NUMBER ? OPENSSL_VERSION_NUMBER : -1;
        $versionSSLCURL = $curlInfos !== false ? $curlInfos['version'] . ' ' . $curlInfos['ssl_version'] : '';

        $sslActivated = $this->isSslActive();
        $tlsCallCurl = $this->isTlsActive();
        $infoSSLTLS = $versionOpenSSL !== -1 && $sslActivated === true
        ? $this->module->l('SSL enabled')
        : $this->module->l('SSL not enabled on all the shop');
        $infoSSLTLS .= $tlsCallCurl['error_message'] !== '' ? ' - ' . $tlsCallCurl['error_message'] : '';

        $isApiConnected = $this->isApiConnected();

        return [
            'maturityList' => $isApiConnected['maturityList'],
            'specs' => [
                [
                    'name' => 'CURL',
                    'info' => $versionSSLCURL !== '' ? 'version v.' . $versionSSLCURL : $this->module->l('not installed'),
                    'ok' => $curlInfos !== false,
                ],
                [
                    'name' => 'SSL & TLS v1.2',
                    'info' => $infoSSLTLS,
                    'ok' => $versionOpenSSL !== -1 && $sslActivated === true && $tlsCallCurl['status'],
                ],
                [
                    'name' => $this->module->l('Encrypt functions'),
                    'info' => '',
                    'ok' => (bool) function_exists('hash_hmac'),
                ],
                [
                    'name' => $this->module->l('Connected to API'),
                    'info' => $isApiConnected['message'],
                    'ok' => (bool) $isApiConnected['status'],
                ],
                [
                    'name' => $this->module->l('Production environment'),
                    'info' => '',
                    'ok' => (bool) $isProductionMode,
                ],
            ],
        ];
    }

    public function getOrderStates()
    {
        $statesStatus = \OrderState::getOrderStates($this->context->language->id);

        $orderStates = ['selected' => [], 'unselected' => []];

        $selectedOrders = Configuration::get(Younitedpay::ORDER_STATE_DELIVERED);
        $aOrdersSel = json_decode($selectedOrders, true);
        if ($aOrdersSel == null || is_array($aOrdersSel) === false) {
            $aOrdersSel = [_PS_OS_DELIVERED_ !== null ? _PS_OS_DELIVERED_ : Configuration::get('_PS_OS_DELIVERED_')];
        }

        foreach ($statesStatus as $aState) {
            if (!$aState['hidden'] && !$aState['deleted']) {
                $sSelected = in_array($aState['id_order_state'], $aOrdersSel) !== false ? 'selected' : 'unselected';
                $orderStates[$sSelected][] = [
                    'value' => $aState['id_order_state'],
                    'label' => $aState['name'],
                ];
            }
        }

        return [
            'unselected' => $orderStates['unselected'],
            'selected' => $orderStates['selected'],
        ];
    }

    public function isSslActive()
    {
        return Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
    }

    public function getAllMaturities()
    {
        return $this->configRepository->getAllMaturities();
    }

    /**
     * Save maturities from configuration
     *
     * @param array $maturities
     * @param int $idShop
     */
    public function saveAllMaturities($maturities, $idShop)
    {
        $this->configRepository->saveAllMaturities($maturities, $idShop);
    }
}
