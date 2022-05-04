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

use YounitedpayAddon\Service\PaymentService;
use YounitedpayAddon\Utils\ServiceContainer;

class YounitedpaySuccessModuleFrontController extends ModuleFrontController
{
    /** @var \PaymentModule */
    public $module;

    /** Prevent init content from Front Controller (case cart created by webhook) */
    public function init()
    {
    }

    public function initContent()
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

        if (
            Validate::isLoadedObject($cart) === false || $this->module->active == 0 || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0 || $cart->id_customer == 0
        ) {
            $this->errors[] = $this->module->l('Error with the cart. Please refresh your page.');
            $paymentService->addLog(json_encode([
                'isCartLoaded' => Validate::isLoadedObject($cart) === false,
                'isModuleActive' => $this->module->active,
                'idAddressDelivery' => $cart->id_address_delivery,
                'idAddressInvoice' => $cart->id_address_invoice,
                'idCustomer' => $cart->id_customer,
            ]));
            $this->redirectWithNotifications($orderUrl);
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            $this->errors[] = $this->module->l('Error with the customer. Please verify your order.');
            $paymentService->addLog(json_encode([
                'isCustomerLoaded' => Validate::isLoadedObject($customer),
            ]));
            $this->redirectWithNotifications($orderUrl);
        }

        if ($cart->orderExists() === true) {
            $this->redirectToOrder($cart, $customer);
        }

        parent::init();
        parent::initContent();

        try {
            $orderCreated = $paymentService->validateOrder($cart, $customer);
        } catch (Exception $ex) {
            $paymentService->addLog($ex->getMessage(), null, null, null, 'Error creating order');
            $orderCreated = false;
        }

        if ($orderCreated === true) {    
            $this->redirectToOrder($cart, $customer);
        }

        $this->errors[] = $this->module->l('Error while creating Order. Please try again.');
        $paymentService->addLog(json_encode([
            'message' => 'Error creating order',
            'cart' => $cart->id,
            'customer' => $customer->id,
        ]));
        $this->redirectWithNotifications($orderUrl);
    }

    protected function redirectToOrder($cart, $customer)
    {
        $linkOrder = $this->context->link->getPageLink(
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

        $this->confirmations[] = $this->module->l('YounitedPay payment has been created (waiting shipping).');
        $this->redirectWithNotifications($linkOrder);
    }
}
