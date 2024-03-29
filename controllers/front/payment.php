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

use YounitedpayAddon\Service\PaymentService;
use YounitedpayAddon\Utils\ServiceContainer;

class YounitedpayPaymentModuleFrontController extends ModuleFrontController
{
    /** @var \PaymentModule */
    public $module;

    public function initContent()
    {
        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);

        $maturity = (int) Tools::getValue('maturity');
        $totalAmount = (float) Tools::getValue('amount');

        try {
            $response = $paymentService->createContract($maturity, $totalAmount);
        } catch (\Exception $ex) {
            $response = [
                'response' => $ex->getMessage(),
                'success' => false,
            ];
            $paymentService->logError($ex->getMessage(), 'Payent error');
            $paymentService->logError($ex->getTraceAsString(), 'Payent error');
        }

        if ($response['success'] === true) {
            $this->redirect_after = $response['url'];
            $this->redirect();
        }

        $this->errors[] = $this->l('Error during payment, please try again.', 'payment');
        $this->errors[] = $this->l($response['response'], 'payment');

        $this->redirectPayment();
    }

    protected function redirectPayment()
    {
        $orderUrl = Context::getContext()->link->getPageLink(
            'order',
            null,
            null,
            [
                'step' => 4,
            ]
        );
        $this->redirectWithNotifications($orderUrl);
    }
}
