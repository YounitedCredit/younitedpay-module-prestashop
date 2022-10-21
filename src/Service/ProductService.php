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

use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Repository\ConfigRepository;
use YounitedpayAddon\Utils\CacheYounited;
use YounitedPaySDK\Model\BestPrice;
use YounitedPaySDK\Model\OfferItem;
use YounitedPaySDK\Request\BestPriceRequest;

class ProductService
{
    public $module;

    /** @var \Context */
    private $context;

    /** @var LoggerService */
    protected $loggerservice;

    /** @var ConfigRepository */
    protected $configRepository;

    public function __construct(
        LoggerService $loggerservice,
        ConfigRepository $configRepository,
        Younitedpay $module
    ) {
        $this->module = $module;
        $this->loggerservice = $loggerservice;
        $this->context = \Context::getContext();
        $this->configRepository = $configRepository;
    }

    public function getBestPrice($product_price, $selectedHook = 'widget')
    {
        $client = new YounitedClient($this->context->shop->id);
        if ($client->isCrendentialsSet() === false || $this->configRepository->checkIPWhitelist() === false) {
            return $this->noOffers();
        }

        /** @var \Currency $currency */
        $currency = new \Currency($this->context->cart->id_currency);
        if (array_search($currency->iso_code, Younitedpay::AVAILABLE_CURRENCIES) === false) {
            return $this->noOffers();
        }

        $productPrice = (float) \Tools::ps_round($product_price, 2);

        /** @var CacheYounited $cachestorage */
        $cachestorage = new CacheYounited();
        $cacheExists = $cachestorage->exist((string) $productPrice);

        $offers = [];
        if ($cacheExists === true && $cachestorage->isExpired((string) $productPrice) === false) {
            $cacheInformations = $cachestorage->get((string) $productPrice);
            $offers = $cacheInformations['content']['offers'];
            if (empty($offers) === true) {
                $cacheExists = false;
            }
        }

        if ($cacheExists === false || $cachestorage->isExpired((string) $productPrice) === true) {
            $maturities = $this->getAllMaturities($productPrice);

            if (count($maturities) <= 0) {
                return $this->noOffers();
            }

            $body = new BestPrice();
            $body->setBorrowedAmount($productPrice);

            $request = new BestPriceRequest();

            /** @var array $response */
            $response = $client->sendRequest($body, $request);

            if ($response['success'] === false) {
                return $this->noOffers();
            }

            $offers = $this->getValidOffers($response['response'], array_column($maturities, 'maturity'));

            $cachestorage->set((string) $productPrice, [
                'offers' => $offers,
            ]);
        }

        $template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/credit_propositions.tpl';

        $this->context->smarty->assign([
            'shop_url' => __PS_BASE_URI__,
            'iso_code' => \Context::getContext()->language->iso_code,
            'logo_younitedpay_url' => 'modules/younitedpay/views/img/logo-younitedpay.png',
            'logo_younitedpay_url_btn' => 'modules/younitedpay/views/img/logo-younitedpay-btn.png',
            'hook_younited' => $selectedHook,
            'offers' => $offers,
        ]);

        return [
            'template' => $this->context->smarty->fetch($template),
            'offers' => $offers,
        ];
    }

    protected function noOffers()
    {
        return [
            'template' => '',
            'offers' => [],
        ];
    }

    protected function getValidOffers($offers, $maturities)
    {
        $validOffers = [];
        $marutitiesIn = [];
        foreach ($offers as $offer) {
            /** @var OfferItem $offer */
            $maturityIn = (int) \Tools::ps_round($offer->getMaturityInMonths());
            if (in_array($maturityIn, $maturities) === true && in_array($maturityIn, $marutitiesIn) === false) {
                $marutitiesIn[] = $maturityIn;
                $validOffers[] = [
                    'maturity' => $offer->getMaturityInMonths(),
                    'installment_amount' => $offer->getMonthlyInstallmentAmount(),
                    'initial_amount' => $offer->getRequestedAmount(),
                    'total_amount' => $offer->getCreditTotalAmount(),
                    'interest_total' => $offer->getInterestsTotalAmount(),
                    'taeg' => $offer->getAnnualPercentageRate() * 100,
                    'tdf' => $offer->getAnnualDebitRate() * 100,
                ];
            }
        }

        return $validOffers;
    }

    public function getAllMaturities($productPrice)
    {
        return $this->configRepository->getAllMaturities($productPrice);
    }

    public function isWhiteListedIP()
    {
        return $this->configRepository->checkIPWhitelist();
    }
}
