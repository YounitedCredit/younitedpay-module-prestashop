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

use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Logger\ApiLogger;
use YounitedpayAddon\Repository\ConfigRepository;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use Configuration;
use YounitedpayAddon\Entity\YounitedPayAvailability;

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
        ConfigRepository $configRepository
    ) {
        $this->module = \Module::getInstanceByName('younitedpay');
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

    public function isApiConnected()
    {
        $client = new YounitedClient($this->context->shop->id, $this->logger);
        if ($client->isCrendentialsSet() === false) {
            return [
                'message' => $this->module->l('No credential saved'),
                'status' => false
            ];
        }

        /** @var array $response */
        $response = $client->getBestPrice(150);

        if (empty($response) === true || null === $response || $response['success'] === false) {
            return [
                'message' => $this->module->l('Response error'),
                'status' => false
            ];
        }

        return [
            'message' => $this->module->l('Connexion Ok'),
            'status' => true
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
        if (\Younitedpay::IS_FILE_LOGGER_ACTIVE === true) {
            $apiLogger->log($this, $response, 'Response', false);
        }

        return $response;
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
     * @param array $maturities
     * @param int $idShop
     */
    public function saveAllMaturities($maturities, $idShop)
    {
        $this->configRepository->saveAllMaturities($maturities, $idShop);
    }
}
