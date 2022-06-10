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

use YounitedpayAddon\Service\LoggerService;
use YounitedpayAddon\Service\OrderService;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedPaySDK\Client as WebHookClient;
use YounitedPaySDK\Response\AbstractResponse;

class YounitedpayWebhookModuleFrontController extends ModuleFrontController
{
    /** @var \PaymentModule */
    public $module;

    /** @var LoggerService */
    public $loggerService;

    /** Prevent init content from Front Controller (case cart created by webhook) */
    public function init()
    {
    }

    public function initContent()
    {
        $clientSDK = new WebHookClient();
        $suffix = (bool) \Configuration::get(Younitedpay::PRODUCTION_MODE) === true ? '_PRODUCTION' : '';
        $webHookSecret = \Configuration::get(Younitedpay::WEBHOOK_SECRET . $suffix);
        $clientSDK->setCredential('', $webHookSecret);

        /** @var AbstractResponse $response */
        $response = $clientSDK->retrieveCallbackResponse();
        $bodyContent = $response->getModel();

        $idCart = Tools::getValue('id_cart');

        /* @var LoggerService */
        $this->loggerService = ServiceContainer::getInstance()->get(LoggerService::class);

        $logContent = json_encode($bodyContent);
        $this->loggerService->addLogAPI($logContent, 'Info', $this);
        $this->loggerService->addLogAPI('Adresse IP:' . Tools::getRemoteAddr(), 'Info', $this);
        $this->loggerService->addLogAPI('Paramètres GET:' . json_encode(Tools::getAllValues()), 'Info', $this);

        if ($bodyContent === '') {
            $this->endResponse('Contenu du body vide', true);
        }

        if ($idCart === false) {
            $this->endResponse('Error, no Cart Id Provided', true);
        }

        if (Tools::getValue('cancel') !== false) {
            $this->updateContractStatus($idCart, 'cancel');
            $this->endResponse('Cancel contract confirmed');
        }

        if (Tools::getValue('widhdrawn') !== false) {
            $this->updateContractStatus($idCart, 'withdrawn');
            $this->endResponse('Withdrawn contract confirmed');
        }

        $this->endResponse('No parameter catched on webhook', true);
    }

    protected function endResponse($message, $error = false)
    {
        if ($error) {
            $this->loggerService->addLog($message, '[younitedpay webhook]', 'info', $this);
        }
        $this->ajaxDie($message);
    }

    protected function updateContractStatus($idCart, $typeUpdate)
    {
        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

        if ($typeUpdate === 'cancel') {
            return $orderservice->setCancelOnYounitedContract($idCart);
        }

        if ($typeUpdate === 'withdrawn') {
            return $orderservice->setWithdrawnOnYounitedContract($idCart);
        }
    }
}
