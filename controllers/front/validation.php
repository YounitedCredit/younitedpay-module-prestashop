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

if (!defined('_PS_VERSION_')) {
    exit;
}

use YounitedpayAddon\Service\LoggerService;
use YounitedpayAddon\Service\PaymentService;
use YounitedpayAddon\Utils\ServiceContainer;

class YounitedpayValidationModuleFrontController extends ModuleFrontController
{
    const PAYMENT_STATUS_INITIALIZED = 'Initialized';
    const PAYMENT_STATUS_APPROVED = 'Approved';
    const PAYMENT_STATUS_ACCEPTED = 'Accepted';
    const PAYMENT_STATUS_EXECUTED = 'Executed';
    const PAYMENT_STATUS_CANCELLED = 'Cancelled';
    const PAYMENT_STATUS_FAILED = 'Failed';

    /** @var \PaymentModule */
    public $module;

    /** @var LoggerService */
    private $loggerService;

    /** Prevent init content from Front Controller (case cart created by webhook) */
    public function init()
    {
    }

    public function initContent()
    {
        $this->loggerService = ServiceContainer::getInstance()->get(LoggerService::class);

        $idCartYounited = (int) Tools::getValue('id_cart');

        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);

        $younitedContract = $paymentService->getContractByCart($idCartYounited);

        $younitedPayment = $paymentService->getApiPaymentById($younitedContract->payment_id);

        $younitedPaymentStatus = $younitedPayment['status'];

        if (in_array($younitedPaymentStatus, [self::PAYMENT_STATUS_ACCEPTED, self::PAYMENT_STATUS_EXECUTED])) {
            $redirectUrl = $this->processPaymentSuccess();
        } elseif (in_array($younitedPaymentStatus, [self::PAYMENT_STATUS_CANCELLED, self::PAYMENT_STATUS_FAILED])) {
            $redirectUrl = $this->processPaymentError($younitedPaymentStatus);
        } elseif (in_array($younitedPaymentStatus, [self::PAYMENT_STATUS_INITIALIZED, self::PAYMENT_STATUS_APPROVED])) {
            $redirectUrl = $this->processPendingPayment();
        } else {
            $redirectUrl = Context::getContext()->link->getPageLink(
                'order',
                null,
                null,
                [
                    'step' => 1,
                ]
            );
        }

        $this->redirectWithNotifications($redirectUrl);
    }

    private function processPendingPayment()
    {
        $this->errors[] = $this->l('Error during payment, please try again.', 'validation');

        return Context::getContext()->link->getPageLink(
            'order',
            null,
            null,
            [
                'step' => 4,
            ]
        );
    }

    private function processPaymentError($paymentStatus)
    {
        if ($paymentStatus === self::PAYMENT_STATUS_CANCELLED) {
            $this->errors[] = $this->l('You have cancelled the payment.', 'validation');
        } else {
            $this->errors[] = $this->l('Payment refused, or error occurs during validation. Please try again.', 'validation');
        }

        return Context::getContext()->link->getPageLink(
            'order',
            null,
            null,
            [
                'step' => 4,
            ]
        );
    }

    private function processPaymentSuccess()
    {
        $orderUrl = Context::getContext()->link->getPageLink(
            'order',
            null,
            null,
            [
                'step' => 1,
            ]
        );

        $idCartYounited = Tools::getValue('id_cart');

        $cart = new Cart($idCartYounited);

        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);

        if (Validate::isLoadedObject($cart) === false
            || $this->module->active == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0
            || $cart->id_customer == 0
        ) {
            $this->errors[] = $this->l('Error with the cart. Please refresh your page.', 'validation');

            $paymentService->logError(
                json_encode([
                    'isCartLoaded' => Validate::isLoadedObject($cart) === false,
                    'isModuleActive' => $this->module->active,
                    'idAddressDelivery' => $cart->id_address_delivery,
                    'idAddressInvoice' => $cart->id_address_invoice,
                    'idCustomer' => $cart->id_customer,
                ]),
                'Error comparing cart while payment'
            );

            return $orderUrl;
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            $this->errors[] = $this->l('Error with the customer. Please verify your order.', 'validation');

            $paymentService->logError(
                json_encode([
                    'isCustomerLoaded' => Validate::isLoadedObject($customer),
                ]),
                'Error loading Customer'
            );

            return $orderUrl;
        }

        if ($cart->orderExists()) {
            return $this->context->link->getPageLink(
                'order-confirmation',
                null,
                null,
                [
                    'id_cart' => $cart->id,
                    'id_module' => $this->module->id,
                    'id_order' => $this->module->currentOrder,
                    'key' => $customer->secure_key,
                ]
            );
        }

        parent::init();

        $amountCreditRequested = $paymentService->getCreditRequestedAmount($cart);
        if ($amountCreditRequested === false) {
            $this->errors[] = $this->l('Error: impossible to retrieve amount of payment done on Younited Pay', 'validation');

            $paymentService->logError(
                'Impossible to retrieve amount of payment done on Younited Pay',
                'Error Payment amount'
            );

            return $orderUrl;
        }

        $amountCart = $cart->getOrderTotal(true, \Cart::BOTH);
        if (abs($amountCreditRequested - $amountCart) > 0.5) {
            $this->errors[] = $this->l(
                'Error: the amount of the contract is different than the total of the cart',
                'success'
            );

            $errorCart = sprintf($this->l('Cart: %s€ - Contract: %s€', 'validation'), $amountCart, $amountCreditRequested);
            $this->errors[] = $errorCart;

            $paymentService->logError(
                'The amount of the contract is different than the total of the cart',
                'Error Cart amount'
            );

            return $orderUrl;
        }

        parent::initContent();

        try {
            if (Tools::getValue('granted') !== false) {
                if ((bool) Configuration::get(Younitedpay::WEBHOOK_ORDERS, null, null, null, false) === false) {
                    $this->log('WebHook', 'Webhook will not create order.');
                    $this->endResponse('[success]');
                } else {
                    $this->log('WebHook', 'Webhook will create order.');
                }
            }

            $orderCreated = $paymentService->validateOrder($cart, $customer, $amountCreditRequested);
        } catch (Exception $ex) {
            $paymentService->logError(
                json_encode([
                    'message' => $ex->getMessage(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                ]),
                'Error creating order'
            );
            $orderCreated = false;
        }

        if ($orderCreated === true) {
            return $this->context->link->getPageLink(
                'order-confirmation',
                null,
                null,
                [
                    'id_cart' => $cart->id,
                    'id_module' => $this->module->id,
                    'id_order' => $this->module->currentOrder,
                    'key' => $customer->secure_key,
                ]
            );
        }

        $this->errors[] = $this->l('Error while creating Order. Please try again.', 'validation');

        $paymentService->logError(
            json_encode([
                'message' => 'Error creating order',
                'cart' => $cart->id,
                'customer' => $customer->id,
            ]),
            'Error while creating order'
        );

        return $orderUrl;
    }

    private function log($title, $info)
    {
        $this->loggerService->addLog(
            $info,
            $title,
            'info',
            (new \ReflectionClass($this))->getShortName()
        );
    }

    private function endResponse($message)
    {
        if (version_compare(_PS_VERSION_, '1.7.5', '>=')) {
            $this->ajaxRender($message);
            exit;
        } else {
            $this->ajaxDie($message);
        }
    }
}
