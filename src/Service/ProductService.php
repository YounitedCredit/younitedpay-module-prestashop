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
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use YounitedPaySDK\Model\OfferItem;

class ProductService
{
    public $module;

    private $curl;

    /** @var \Context */
    private $context;

    /** @var ProcessLoggerHandler */
    protected $logger;

    /** @var ConfigRepository */
    protected $configRepository;

    public function __construct(
        ProcessLoggerHandler $logger,
        ConfigRepository $configRepository,
        Younitedpay $module
    ) {
        $this->module = $module;
        $this->logger = $logger;
        $this->context = \Context::getContext();
        $this->configRepository = $configRepository;
    }
    
    public function getProductBestPrice(\Product $product)
    {
        $client = new YounitedClient($this->context->shop->id, $this->logger);
        if ($client->isCrendentialsSet() === false) {
            return '';
        }

        $productPrice = (float) \Tools::ps_round($product->getPrice(),2);

        $maturities = $this->getAllMaturities($productPrice);

        if (count($maturities) <= 0) {
            return '';
        }

        /** @var array $response */
        $response = $client->getBestPrice($productPrice);
        // $response = $client->getBestPrice(1500.00);

        if ($response['success'] === false) {
            return '';
        }

        $offers = $this->getValidOffers($response['offers'], array_column($maturities, 'maturity')); 

        $template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/credit_propositions.tpl';

        $this->context->smarty->assign([
            'shop_url' => __PS_BASE_URI__ !== '/' ? substr(__PS_BASE_URI__, 0, 1) : '',
            'logo_younitedpay_url' => '/modules/younitedpay/views/img/logo-younitedpay.png',
            'offers' => $offers,
        ]);

        return $this->context->smarty->fetch($template);
    }

    protected function getValidOffers($offers, $maturities)
    {
        $validOffers = [];        
        foreach($offers AS $offer) {
            /** @var OfferItem $offer */
            $maturityIn = (int) \Tools::ps_round($offer->getMaturityInMonths());
            if (in_array($maturityIn, $maturities) === true) {
                $validOffers[] = [
                    'maturity' => $offer->getMaturityInMonths(),
                    'installment_amount' => $offer->getMonthlyInstallmentAmount(),
                ];
            }
        }
        return $validOffers;
    }

    public function getAllMaturities($productPrice)
    {
        return $this->configRepository->getAllMaturities($productPrice);
    }
}
