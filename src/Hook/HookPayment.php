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

namespace YounitedpayAddon\Hook;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Context;
use Media;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Service\LoggerService;
use YounitedpayAddon\Service\PaymentService;
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
        $client = new YounitedClient(Context::getContext()->shop->id);
        if (!$this->module->active || $client->isCrendentialsSet() === false) {
            return;
        }

        /** @var ProductService $productservice */
        $productservice = ServiceContainer::getInstance()->get(ProductService::class);

        /** @var PaymentService $paymentservice */
        $paymentservice = ServiceContainer::getInstance()->get(PaymentService::class);

        /** @var LoggerService $loggerservice */
        $loggerservice = ServiceContainer::getInstance()->get(LoggerService::class);

        $errorMessage = [];

        /** @var \Currency $currency */
        $currency = new \Currency(Context::getContext()->cart->id_currency);
        if (array_search($currency->iso_code, Younitedpay::AVAILABLE_CURRENCIES) === false) {
            $errorMessage[] = $this->l('Not available in this currency (only EUR)');

            return [];
        }

        $customerAdressInvoice = new \Address(Context::getContext()->cart->id_address_invoice);
        $country = new \Country($customerAdressInvoice->id_country);
        if ($country->iso_code !== 'FR') {
            // $errorMessage[] = $this->l('Not available for this country (Only France for invoice address).');
        }

        if ($paymentservice->isInternationalPhone($customerAdressInvoice) === false) {
            $errorMessage[] = $paymentservice->errorMessage;
        }

        /** @var \Cart $cart */
        $cart = $params['cart'];

        $this->cartPrice = $cart->getOrderTotal();

        $templateCredit = $productservice->getBestPrice($this->cartPrice);

        $totalOffers = $templateCredit['offers'];
        $selectedOffer = isset($templateCredit['selectedOffer']) ? (int) $templateCredit['selectedOffer'] : 0;

        if (empty($totalOffers) === true) {
            $loggerservice->addLogAPI('No offers in checkout for ' . $this->cartPrice, 'Info', $this);

            return [];
        }

        $paymentOptions = [];
        Context::getContext()->smarty->assign('iso_code', Context::getContext()->language->iso_code);
        try {
            $paymentOptions = $this->getYounitedPaymentOption($errorMessage, $totalOffers, $selectedOffer);
        } catch (\Exception $ex) {
            $msg = [
                'code' => $ex->getCode(),
                'error' => $ex->getMessage(),
            ];
            $loggerservice->addLog('Error retrieving payment :' . json_encode($msg), 'Payment Options', 'error', $this);
        }

        return $paymentOptions;
    }

    protected function getYounitedPaymentOption($errorMessage, $totalOffers, int $selectedOffer)
    {
        $logoPayment = Media::getMediaPath(
            _PS_MODULE_DIR_ . $this->module->name . '/views/img/logo-younitedpay-payment.png'
        );

        $paymentOption = new PaymentOption();

        $context = Context::getContext();
        $creditLink = $context->link->getModuleLink(
            $this->module->name,
            'payment',
            [
                'amount' => $this->cartPrice,
                'maturity' => $totalOffers[$selectedOffer]['maturity'],
            ],
            true
        );

        $smarty = Context::getContext()->smarty;
        $smarty->assign([
            'yperror' => $errorMessage,
            'credit_link' => $context->link->getModuleLink(
                $this->module->name,
                'payment',
                [
                    'amount' => $this->cartPrice,
                ],
                true
            ),
        ]);
        $paymentInfoTemplate = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/credit_informations.tpl';
        $paymentText = $this->l('Pay in several times with ');
        $paymentOption
            ->setAdditionalInformation($smarty->fetch($paymentInfoTemplate))
            ->setCallToActionText($paymentText)
            ->setModuleName($this->module->name)
            ->setAction($creditLink)
            ->setLogo($logoPayment);

        if (empty($errorMessage) === false) {
            $paymentOption->setBinary(true);
        }

        return [$paymentOption];
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
