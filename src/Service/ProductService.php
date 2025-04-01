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

namespace YounitedpayAddon\Service;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Repository\ConfigRepository;
use YounitedpayAddon\Service\ConfigService;
use YounitedpayAddon\Utils\CacheYounited;
use YounitedPaySDK\Model\NewAPI\BestPrice;
use YounitedPaySDK\Model\OfferItem;
use YounitedPaySDK\Request\NewAPI\BestPriceRequest;

class ProductService
{
    public $module;

    /** @var \Context */
    private $context;

    /** @var LoggerService */
    protected $loggerservice;

    /** @var ConfigRepository */
    protected $configRepository;

    /** @var ConfigService */
    protected $configService;

    public function __construct(
        LoggerService $loggerservice,
        ConfigService $configService,
        Younitedpay $module
    ) {
        $this->module = $module;
        $this->loggerservice = $loggerservice;
        $this->context = \Context::getContext();
        $this->configRepository = $configService->configRepository;
        $this->configService = $configService;
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

        $isProduction = (bool) $this->configRepository->getConfig(Younitedpay::PRODUCTION_MODE);
        $shopCodeKey = $isProduction === false ? Younitedpay::SHOP_CODE : Younitedpay::SHOP_CODE_PRODUCTION;
        $shopCode = $this->configRepository->getConfig($shopCodeKey);
        $isRangeEnabled = (bool) $this->configRepository->getConfig(Younitedpay::SHOW_RANGE_OFFERS);
        $minRange = $this->configRepository->getConfig(Younitedpay::MIN_RANGE_OFFERS, 0);
        $maxRange = $this->configRepository->getConfig(Younitedpay::MAX_RANGE_OFFERS, 0);
        $minInstall = (int) $this->configRepository->getConfig(Younitedpay::MIN_RANGE_INSTALMENT, 12);
        $maxInstall = (int) $this->configRepository->getConfig(Younitedpay::MAX_RANGE_INSTALMENT, 72);
        $widgetBorder = (bool) $this->configRepository->getConfig(Younitedpay::SHOW_WIDGET_BORDERS, false);

        $offers = [];
        $rangeOffers = [];
        if ($cacheExists === true && $cachestorage->isExpired((string) $productPrice) === false) {
            $cacheInformations = $cachestorage->get((string) $productPrice);
            $offers = $cacheInformations['content']['offers'];
            $rangeOffers = $cacheInformations['content']['ranges'];
            $emptyRangeOffers = $isRangeEnabled === true && empty($rangeOffers) === true;
            if (empty($offers) === true || $emptyRangeOffers === true) {
                $cacheExists = false;
            }
        }

        if ($cacheExists === false || $cachestorage->isExpired((string) $productPrice) === true) {
            $maturities = $this->getAllMaturities($productPrice, $isRangeEnabled);

            $configMaturities = ['List' => '24,36'];
            if ($isRangeEnabled) {
                $configMaturities = [
                    'Range' => [
                        'Min' => $minInstall,
                        'Max' => $maxInstall,
                        'Step' => 1
                    ],
                ];
            } else {
                $configMaturities = [
                    'List' => $this->getMaturitiesConfiguration($maturities),
                ];
            }
            $body = (new BestPrice())
                        ->setMaturity($configMaturities)
                        ->setShopCode($shopCode)
                        ->setBorrowedAmount($productPrice);

            $request = new BestPriceRequest();

            try {
                $response = $client->sendRequest($body, $request);
            } catch (\Error $err) {
                $response = ['success' => false];
            } catch (\Exception $ex) {
                $response = ['success' => false];
            }

            if ($response['success'] === false) {
                return $this->noOffers();
            }

            $offers = $this->getValidOffers($response['response'], array_column($maturities, 'maturity'));
            $rangeOffers = $isRangeEnabled === false ? [] : $this->getRangeOffers(
                $response['response'],
                $productPrice,
                $minRange,
                $maxRange
            );

            if (count($rangeOffers) === 0 && count($offers) > 0 && $isRangeEnabled) {
                $offers = [];
            }

            if (count($offers) <= 0 && count($rangeOffers) > 0) {
                $offers[] = $rangeOffers[0];
                $offers[] = $rangeOffers[count($rangeOffers) - 1];
            }

            $cachestorage->set((string) $productPrice, [
                'offers' => $offers,
                'ranges' => $rangeOffers,
            ]);
        }

        $selectedOffer = 0;
        if (empty($offers) === false) {
            if ((int) $offers[0]['maturity'] < $minInstall) {
                $minInstall = (int) $offers[0]['maturity'];
            }
            foreach ($offers as $key => $oneOffer) {
                if ((int) $oneOffer['maturity'] === 36) {
                    $selectedOffer = $key;
                }
                if ((int) $oneOffer['maturity'] === 24 && $selectedOffer === 0) {
                    $selectedOffer = $key;
                }
            }
            if ($selectedOffer === 0) {
                $selectedOffer = count($offers) - 1;
            }
        }
        if (empty($rangeOffers) === false) {
            if ((int) $rangeOffers[count($rangeOffers) - 1]['maturity'] < $maxInstall) {
                $maxInstall = (int) $rangeOffers[count($rangeOffers) - 1]['maturity'];
            }
        }

        $template = 'module:younitedpay/views/templates/front/credit_propositions.tpl';

        $this->context->smarty->assign([
            'shop_url' => __PS_BASE_URI__,
            'iso_code' => \Context::getContext()->language->iso_code,
            'logo_younitedpay_url' => 'modules/younitedpay/views/img/logo-younitedpay.png',
            'hook_younited' => $selectedHook,
            'offers' => $offers,
            'range_offers' => $rangeOffers,
            'show_ranges' => (int) $isRangeEnabled,
            'min_range' => (int) $minRange,
            'max_range' => (int) $maxRange,
            'selected_offer' => (int) $selectedOffer,
            'min_install' => $minInstall,
            'max_install' => $maxInstall,
            'widget_borders' => $widgetBorder,
        ]);

        return [
            'template' => $this->module->fetch($template),
            'offers' => $offers,
            'selectedOffer' => $selectedOffer,
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
                if (empty($validOffers) || $validOffers[0]['maturity'] < $offer->getMaturityInMonths()) {
                    $validOffers[] = $this->returnOffer($offer);
                } else {
                    array_unshift($validOffers, $this->returnOffer($offer));
                }
            }
        }

        return $validOffers;
    }

    protected function getRangeOffers($offers, $productPrice, $minAmount, $maxAmount)
    {
        if ((int) $productPrice < (int) $minAmount) {
            return [];
        }

        if ((int) $productPrice > (int) $maxAmount && (int) $maxAmount > 0) {
            return [];
        }

        $validOffers = [];
        foreach ($offers as $offer) {
            if (empty($validOffers) || $validOffers[0]['maturity'] < $offer->getMaturityInMonths()) {
                $validOffers[] = $this->returnOffer($offer);
            } else {
                array_unshift($validOffers, $this->returnOffer($offer));
            }
        }

        return $validOffers;
    }

    /**
     * Return offer for templates
     */
    protected function returnOffer(OfferItem $offer)
    {
        return [
            'maturity' => (int) $offer->getMaturityInMonths(),
            'installment_amount' => \Tools::ps_round($offer->getMonthlyInstallmentAmount(), 2),
            'initial_amount' => \Tools::ps_round($offer->getRequestedAmount(), 2),
            'total_amount' => \Tools::ps_round($offer->getCreditTotalAmount(), 2),
            'interest_total' => \Tools::ps_round($offer->getInterestsTotalAmount(), 2),
            'taeg' => \Tools::ps_round($offer->getAnnualPercentageRate() * 100, 2),
            'tdf' => \Tools::ps_round($offer->getAnnualDebitRate() * 100, 2),
        ];
    }

    public function getAllMaturities($productPrice, $isRangeEnabled)
    {
        if ($isRangeEnabled === true) {
            // If range is enabled, return all maturities instead of filtering by price
            return $this->configRepository->getAllMaturities();
        }

        return $this->configRepository->getAllMaturities($productPrice);
    }

    public function isWhiteListedIP()
    {
        return $this->configRepository->checkIPWhitelist();
    }

    function getMaturitiesConfiguration($maturities)
    {
        if (empty($maturities)) {
            return '36,24';
        }
        $config = [];
        foreach($maturities as $oneMaturity) {
            $config[] = $oneMaturity['maturity'];
        }
        
        return implode(',', $config);
    }
}
