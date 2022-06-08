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

use YounitedpayAddon\Service\ProductService;
use YounitedpayAddon\Utils\ServiceContainer;

class YounitedpayProductModuleFrontController extends ModuleFrontController
{
    /** @var \PaymentModule */
    public $module;

    public function initContent()
    {
        $context = \Context::getContext();

        $idProduct = Tools::getValue('id_product');
        $idAttribute = Tools::getValue('id_attribute');

        $product = new \Product($idProduct);

        $price = $product->getPrice(true, $idAttribute);

        /** @var ProductService $productservice */
        $productservice = ServiceContainer::getInstance()->get(ProductService::class);

        $templateCredit = $productservice->getBestPrice($price);

        $frontModuleLink = $context->link->getModuleLink(
            $this->module->name,
            'younitedpayproduct'
        );

        $totalOffers = $templateCredit['offers'];

        $numberOffers = empty($totalOffers) === false && is_array($totalOffers) ? count($totalOffers) - 1 : 0;

        $context->smarty->assign(
            [
                'younited_hook' => 'ajax-refresh-product',
                'widget_younited' => false,
                'credit_template' => $templateCredit['template'],
                'product_url' => $frontModuleLink,
                'product_price' => $price,
                'product_offers_total' => empty($totalOffers) === false && is_array($totalOffers)
                    ? count($totalOffers) - 1
                    : 0,
            ]
        );

        $this->ajaxDie(json_encode([
            'content' => $context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/product_infos.tpl'
            ),
            'number_offers' => $numberOffers,
        ]));
    }
}
