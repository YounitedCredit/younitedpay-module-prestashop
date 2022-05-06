<?php
/**
 * Copyright Bridge
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
 * @copyright Bridge
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

namespace YounitedpayAddon\Hook;

use Context;
use Media;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use Younitedpay;
use YounitedpayAddon\Service\LoggerService;
use YounitedpayAddon\Service\ProductService;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Hook\AbstractHook;

class HookPayment extends AbstractHook
{
    /** @var \PaymentModule */
    public $module;

    /** @var float */
    protected $cartPrice;

    const AVAILABLE_HOOKS = [
        'paymentOptions',
        'paymentReturn',
        'displayPaymentReturn',
    ];

    public function paymentOptions($params)
    {
        if (!$this->module->active) {
            return;
        }

        /** @var \Currency $currency */
        $currency = new \Currency(Context::getContext()->cart->id_currency);
        if (array_search($currency->iso_code, Younitedpay::AVAILABLE_CURRENCIES) === false) {
            return;
        }

        /** @var ProductService $productservice */
        $productservice = ServiceContainer::getInstance()->get(ProductService::class);

        /** @var LoggerService $loggerservice */
        $loggerservice = ServiceContainer::getInstance()->get(LoggerService::class);

        /** @var \Cart $cart */
        $cart = $params['cart'];

        $this->cartPrice = $cart->getOrderTotal();

        $templateCredit = $productservice->getBestPrice($this->cartPrice);

        $totalOffers = $templateCredit['offers'];

        $paymentOptions = [];
        try {
            $paymentOptions = $this->getYounitedPaymentOption($totalOffers);
        } catch (\Exception $ex) {
            $msg = [
                'code' => $ex->getCode(),
                'error' => $ex->getMessage(),
            ];
            $loggerservice->addLog('Error retrieving payment :' . json_encode($msg), 'Payment Options', 'error', $this);
        }

        return $paymentOptions;
    }

    protected function getYounitedPaymentOption($totalOffers)
    {
        $younitedPaymentOptions = [];
        $logoPayment = Media::getMediaPath(
            _PS_MODULE_DIR_ . $this->module->name . '/views/img/logo-younitedpay-payment.png'
        );
        foreach ($totalOffers as $maturity) {
            $paymentOption = new PaymentOption();

            $this->setPaymentNameAndAdditional($paymentOption, $maturity);

            $context = Context::getContext();
            $creditLink = $context->link->getModuleLink(
                $this->module->name,
                'payment',
                [
                    'amount' => $this->cartPrice,
                    'maturity' => $maturity['maturity'],
                ],
                true
            );

            $paymentOption->setModuleName($this->module->name)
            ->setAction($creditLink)
            ->setLogo($logoPayment);

            $younitedPaymentOptions[] = $paymentOption;
        }

        return $younitedPaymentOptions;
    }

    protected function setPaymentNameAndAdditional(PaymentOption $paymentOption, $maturity)
    {
        $smarty = Context::getContext()->smarty;
        $maturity['total_order'] = $this->cartPrice;
        $smarty->assign([
            'credit' => $maturity,
        ]);
        $paymentInfoTemplate = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/payment_infos.tpl';
        $paymentOption->setAdditionalInformation($smarty->fetch($paymentInfoTemplate))
            ->setCallToActionText(
            sprintf(
                $this->l('Pay in %s times without fees with '),
                $maturity['maturity']
            )
        );
    }

    protected function paymentReturnTemplate()
    {
        return Context::getContext()->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/payment_return.tpl'
        );
    }

    public function displayPaymentReturn($params)
    {
        return $this->paymentReturnTemplate();
    }

    public function paymentReturn($params)
    {
        return $this->paymentReturnTemplate();
    }
}
