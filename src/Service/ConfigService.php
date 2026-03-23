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

namespace YounitedpayAddon\Service;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration;
use Language;
use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Logger\ApiLogger;
use YounitedpayAddon\Repository\ConfigRepository;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use YounitedpayClasslib\Utils\Translate\TranslateTrait;
use YounitedPaySDK\Model\NewAPI\GetOffers;
use YounitedPaySDK\Model\NewAPI\WebHookIntegration;
use YounitedPaySDK\Request\NewAPI\GetOffersRequest;
use YounitedPaySDK\Request\NewAPI\ShopsRequest;
use YounitedPaySDK\Request\NewAPI\WebHooksIntegrationRequest;

class ConfigService
{
    use TranslateTrait;

    public $module;

    private $curl;

    /** @var \Context */
    private $context;

    /** @var ProcessLoggerHandler */
    protected $logger;

    /** @var ConfigRepository */
    public $configRepository;

    const DEF_MATURITIES = [10, 12, 24];

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

            $return['error_message'] = $this->l('TLS call failed');
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
        $message = [];
        $shopCodeList = [];
        $maturityList = [];
        $status = [];
        foreach (Younitedpay::AVAILABLE_COUNTRIES as $availableCountry) {
            $countryCode = strtolower($availableCountry);
            $langId = (int) \Language::getIdByIso($countryCode);

            $client = new YounitedClient($this->context->shop->id, $langId);
            $status[$countryCode] = [];

            if ($client->isCrendentialsSet() === false) {
                $message[$countryCode][] = '[' . $availableCountry . '] ' . $this->l('No credential saved');
                $status[$countryCode][] = 'no_credentials';
                continue;
            }

            if (empty($client->shopCode) === true) {
                $message[$countryCode][] = '[' . $availableCountry . '] ' . $this->l('No Shop Code saved');
                $status[$countryCode][] = 'no_shop_code';
            }

            $shopCodeList[$countryCode] = $this->getShopCodes($countryCode);
            if (empty($shopCodeList[$countryCode]) === true) {
                $message[$countryCode][] = '[' . $availableCountry . '] ' . $this->l('Credentials error');
                $status[$countryCode][] = 'api_error';
                $client->shopCode = '';
            }

            $body = (new GetOffers())->setShopCode($client->shopCode)
                ->setAmount('1500')
                ->setMaturityRangeStep(1)
                ->setMaturityRangeMin(1)
                ->setMaturityRangeMax(84);

            $request = new GetOffersRequest();

            if (empty($client->shopCode) === false) {
                /** @var mixed $response */
                $response = $client->sendRequest($body, $request);

                if (empty($response) === true || $response['success'] === false) {
                    $message[$countryCode][] = '[' . $availableCountry . '] ' . $this->l('Offers response error');
                    $status[$countryCode][] = 'maturities_error';
                }
                if (empty($status[$countryCode]) === false) {
                    continue;
                }
                foreach ($response['response'] as $oneOffer) {
                    $maturity = (int) $oneOffer->getMaturityInMonths();
                    if ($maturity < 5) {
                        ++$maturity;
                    }
                    if (in_array($maturity, $maturityList) === false) {
                        $maturityList[] = $maturity;
                    }
                }
            }
        }

        if (empty($shopCodeList)) {
            return [
                'message' => $message,
                'maturityList' => self::DEF_MATURITIES,
                'shopCodeList' => $shopCodeList,
                'status' => $status,
            ];
        }

        foreach ($status as $countryCode => $statusDetail) {
            if (empty($statusDetail)) {
                $status[$countryCode] = ['ok'];
                $message[$countryCode] = ['[' . strtoupper($countryCode) . '] ' . $this->l('Connexion Ok')];
            }
        }

        return [
            'message' => $message,
            'maturityList' => count($maturityList) > 0 ? $this->sortOffers($maturityList) : $maturityList,
            'shopCodeList' => $shopCodeList,
            'status' => $status,
        ];
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
        if ((bool) Configuration::get(Younitedpay::IS_FILE_LOGGER_ACTIVE) === true) {
            $apiLogger->log($this, $response, 'Config Response', false);
        }

        return $response;
    }

    public function checkSpecifications($isProductionMode)
    {
        $curlInfos = curl_version();
        $versionOpenSSL = false !== getenv('OPENSSL_VERSION_NUMBER') ? OPENSSL_VERSION_NUMBER : -1;
        $versionSSLCURL = $curlInfos !== false ? $curlInfos['version'] . ' ' . $curlInfos['ssl_version'] : '';

        $sslActivated = $this->isSslActive();
        $tlsCallCurl = $this->isTlsActive();
        $infoSSLTLS = $versionOpenSSL !== -1 && $sslActivated === true
        ? $this->l('SSL enabled')
        : $this->l('SSL not enabled on all the shop');
        $infoSSLTLS .= $tlsCallCurl['error_message'] !== '' ? ' - ' . $tlsCallCurl['error_message'] : '';

        $isApiConnected = $this->isApiConnected();

        $isApiConnectedStatus = false;
        $isApiConnectedMsg = [];
        $isApiConnectedSts = [];
        $isApiConnectedEnv = [];
        foreach (Younitedpay::AVAILABLE_COUNTRIES as $availableCountry) {
            $countryCode = strtolower($availableCountry);
            $isApiConnectedMsg[] = implode(' - ', $isApiConnected['message'][$countryCode]);
            $isApiConnectedSts[] = implode(' - ', $isApiConnected['status'][$countryCode]);
            $isApiConnectedEnv[] = ($isProductionMode[$countryCode] ? 'PROD' : 'TEST');
            if (in_array('no_credentials', $isApiConnected['status'][$countryCode]) || $isApiConnectedStatus) {
                continue;
            }
            $isApiConnectedStatus = in_array('ok', $isApiConnected['status'][$countryCode]);
        }

        return [
            'maturityList' => $isApiConnected['maturityList'],
            'shopCodeList' => $isApiConnected['shopCodeList'],
            'connected' => $isApiConnectedStatus,
            'status' => $isApiConnected['status'],
            'specs' => [
                [
                    'name' => 'CURL',
                    'info' => $versionSSLCURL !== '' ? 'version v.' . $versionSSLCURL : $this->l('not installed'),
                    'ok' => $curlInfos !== false,
                ],
                [
                    'name' => 'SSL & TLS v1.2',
                    'info' => $infoSSLTLS,
                    'ok' => $versionOpenSSL !== -1 && $sslActivated === true && $tlsCallCurl['status'],
                ],
                [
                    'name' => $this->l('Encrypt functions'),
                    'info' => '',
                    'ok' => (bool) function_exists('hash_hmac'),
                ],
                [
                    'name' => $this->l('Connected to API'),
                    'info' => $isApiConnectedMsg,
                    'ok' => $isApiConnectedSts,
                    'env' => $isApiConnectedEnv,
                ],
            ],
        ];
    }

    public function getOrderStates()
    {
        $statesStatus = \OrderState::getOrderStates($this->context->language->id);

        $orderStates = ['selected' => [], 'unselected' => []];

        $idShop = \Context::getContext()->shop->id;
        $selectedOrders = Configuration::get(Younitedpay::ORDER_STATE_DELIVERED, null, null, $idShop);
        $aOrdersSel = json_decode($selectedOrders, true);
        if ($aOrdersSel == null || is_array($aOrdersSel) === false) {
            $aOrdersSel = [
                false !== getenv('_PS_OS_DELIVERED_')
                ? _PS_OS_DELIVERED_
                : Configuration::get('_PS_OS_DELIVERED_', null, null, $idShop),
            ];
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
        $idShop = \Context::getContext()->shop->id;

        return Configuration::get('PS_SSL_ENABLED', null, null, $idShop)
            && Configuration::get('PS_SSL_ENABLED_EVERYWHERE', null, null, $idShop);
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

    /**
     * Return Shop Codes list from API
     */
    public function getShopCodes($countryCode = null)
    {
        $langId = Language::getIdByIso($countryCode);
        $client = new YounitedClient($this->context->shop->id, $langId ?: $this->context->language->id);

        if ($client->isCrendentialsSet() === false) {
            return [];
        }

        $request = new ShopsRequest();

        /** @var mixed $response */
        $response = $client->sendRequest(null, $request);

        if (empty($response) === true || $response['success'] === false) {
            return [];
        }
        $shopCodes = $response['response'];

        $shopCodesNames = [];
        foreach ($shopCodes as $oneShopCode) {
            if (isset($oneShopCode['name']) && isset($oneShopCode['code'])) {
                $shopCodesNames[] = [
                    'name' => $oneShopCode['name'],
                    'code' => $oneShopCode['code'],
                ];
            }
        }

        return $shopCodesNames;
    }

    public function testWebhook()
    {
        $client = new YounitedClient($this->context->shop->id, $this->context->language->id);

        if ($client->isCrendentialsSet() === false || $client->shopCode === '') {
            return false;
        }

        $model = (new WebHookIntegration())->setWebhookUrl(\Tools::getValue('testWebHookURL'));

        $response = $client->sendRequest($model, new WebHooksIntegrationRequest());
        $responseWebHook = $response;
        if (isset($response['response']['responseStatusCode'])) {
            $statutResponse = (int) substr($response['response']['responseBody'], 0, 3);
            $responseWebHook = [
                'status' => $statutResponse,
                'success' => $statutResponse === 200,
                'response' => $response['response']['responseBody'],
            ];
        }

        return json_encode($responseWebHook);
    }

    private function sortOffers($validOffers)
    {
        usort($validOffers, function ($a, $b) {
            return $a > $b ? 1 : -1;
        });

        return $validOffers;
    }

    /**
     * Check if at least one country is not in Production mode
     *
     * @param mixed $isProductionMode
     *
     * @return bool
     */
    public function isProductionModeAllcountries($isProductionMode)
    {
        foreach (Younitedpay::AVAILABLE_COUNTRIES as $availableCountry) {
            $countryCode = strtolower($availableCountry);
            if ($isProductionMode[$countryCode] === false) {
                return false;
            }
        }

        return true;
    }
}
