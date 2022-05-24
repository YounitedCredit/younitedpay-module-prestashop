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

        $allValues = json_encode(Tools::getAllValues());

        $this->loggerService = ServiceContainer::getInstance()->get(LoggerService::class);

        $logContent = json_encode($bodyContent);
        $logContent .= "\n" . 'Adresse IP:' . Tools::getRemoteAddr();
        $this->loggerService->addLogAPI($logContent . "\n" . $allValues, 'Info', $this);

        if ($bodyContent === '') {
            $this->endResponse('Contenu du body vide');
        }

        // 'contractReference'
        // $this->paymentrepository->cancelContract($idOrder);

        $this->endResponse('Contenu :' . $logContent . "\nParams:\n" . $allValues);
    }

    protected function endResponse($message)
    {
        $this->loggerService->addLog($message, '[younitedpay webhook]', 'info', $this);
        $this->ajaxDie($message);
    }
}
