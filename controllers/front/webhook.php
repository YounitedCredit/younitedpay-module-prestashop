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

use YounitedpayAddon\Service\OrderService;
use YounitedpayAddon\Service\LoggerService;
use YounitedPaySDK\Client as WebHookClient;
use YounitedpayAddon\Service\PaymentService;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedPaySDK\Response\AbstractResponse;
use YounitedpayAddon\Entity\YounitedPayContract;

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
        $idShop = $this->context->shop->id;
        $isProduction = (bool) \Configuration::get(Younitedpay::PRODUCTION_MODE, null, null, $idShop);
        $suffix = $isProduction === true ? '_PRODUCTION' : '';
        $webHookSecret = \Configuration::get(Younitedpay::WEBHOOK_SECRET . $suffix, null, null, $idShop);
        $clientSDK->setCredential('', $webHookSecret);

        /* @var LoggerService */
        $this->loggerService = ServiceContainer::getInstance()->get(LoggerService::class);

        /** @var AbstractResponse $response */
        $response = $clientSDK->retrieveCallbackResponse();

        $this->loggerService->addLogAPI(json_encode($response), 'Info', $this);

        if ($response->getStatusCode() === 401) {
            // $this->endResponse('Accès refusé : ' . $response->getReasonPhrase());
        }

        $bodyContent = $response->getModel();

        $idCart = Tools::getValue('id_cart');

        $logContent = json_encode($bodyContent);
        $this->loggerService->addLogAPI($logContent, 'Info', $this);
        $this->loggerService->addLogAPI('Adresse IP:' . Tools::getRemoteAddr(), 'Info', $this);
        $this->loggerService->addLogAPI('Paramètres GET:' . json_encode(Tools::getAllValues()), 'Info', $this);

        if ($bodyContent === '') {
            $this->endResponse('Contenu du body vide');
        }

        if ($idCart === false) {
            $this->endResponse('Error, no Cart Id Provided');
        }

        if (Tools::getValue('cancel') !== false) {
            $this->updateContractStatus($idCart, 'cancel');
            $this->endResponse('Cancel contract confirmed Cart ID' . $idCart);
        }

        if (Tools::getValue('widhdrawn') !== false) {
            $this->updateContractStatus($idCart, 'withdrawn');
            $this->endResponse('Withdrawn contract confirmed Cart ID' . $idCart);
        }

        if (Tools::getValue('granted') !== false) {
            $this->updateContractStatus($idCart, 'granted');
        }

        $this->endResponse('No parameter catched on webhook', false);
    }

    protected function endResponse($message, $error = true)
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

        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);

        /** @var YounitedPayContract $younitedContract */
        $younitedContract = $paymentService->getContractByCart($idCart);

        if ((int) $younitedContract->id_order <= 0) {
            $this->endResponse('Error on contract activation, no order found with this cart (ID ' . $idCart . ')');
        }
        $order = new Order($younitedContract->id_order);

        if ($typeUpdate === 'cancel') {
            $newIdState = null !== _PS_OS_CANCELED_ ? _PS_OS_CANCELED_ : Configuration::get('PS_OS_CANCELED');
            if ($orderservice->setCancelOnYounitedContract($idCart) !== true) {
                $this->endResponse('Error on contract cancelation (Cart ID ' . $idCart . ')');
            }
        }

        if ($typeUpdate === 'withdrawn') {
            $newIdState = null !== _PS_OS_REFUND_ ? _PS_OS_REFUND_ : Configuration::get('PS_OS_REFUND');
            if ($orderservice->setWithdrawnOnYounitedContract($idCart) !== true) {
                $this->endResponse('Error on contract Withdrawn (Cart ID ' . $idCart . ')');
            }
        }

        if ($typeUpdate === 'granted') {
            if ($paymentService->validateOrder($idCart)) {
                $this->endResponse('Error on contract activation (Cart ID ' . $idCart . ')');
            }
            $this->endResponse('Granted contract confirmed Cart ID' . $idCart);
        }

        $this->setCurrentState((int) $newIdState, $order);
    }

    /** Set current order status
     * @param int $id_order_state
     * @param int $id_employee (/!\ not optional except for Webservice
     */
    public function setCurrentState($id_order_state, $order)
    {
        if (empty($id_order_state) || (int) $id_order_state === (int) $order->current_state) {
            return false;
        }
        $history = new OrderHistory();
        $history->id_order = (int) $order->id;
        $history->id_employee = 0;
        $use_existings_payment = !$order->hasInvoice();
        $history->changeIdOrderState((int) $id_order_state, $order, $use_existings_payment);
        $history->add();
    }
}
