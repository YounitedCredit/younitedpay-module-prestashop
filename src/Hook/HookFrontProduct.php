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

namespace YounitedpayAddon\Hook;

use Younitedpay;
use YounitedpayAddon\Service\ProductService;
use YounitedpayAddon\Utils\CacheYounited;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Hook\AbstractHook;

class HookFrontProduct extends AbstractHook
{
    /** @var \PaymentModule */
    public $module;

    const AVAILABLE_HOOKS = [
        'displayProductPriceBlock',
        'displayAfterProductThumbs',
        'displayProductAdditionalInfo',
        'displayReassurance',
        'displayCartExtraProductActions',
    ];

    public function displayProductPriceBlock($params)
    {
        if ($params['type'] === 'after_price') {
            return $this->displaySelectedHook($params, 'displayProductPriceBlock');
        }
    }

    public function displayAfterProductThumbs($params)
    {
        return $this->displaySelectedHook($params, 'displayAfterProductThumbs');
    }

    public function displayProductAdditionalInfo($params)
    {
        return $this->displaySelectedHook($params, 'displayProductAdditionalInfo');
    }

    public function displayReassurance($params)
    {
        return $this->displaySelectedHook($params, 'displayReassurance');
    }

    public function displayCartExtraProductActions($params)
    {
        return $this->displayCart($params);
    }

    private function displayCart($params)
    {
    }

    private function getHookConfiguration()
    {
        /** @var CacheYounited $cachestorage */
        $cachestorage = new CacheYounited();
        $cacheExists = $cachestorage->exist('hookConfiguration');

        if ($cacheExists === false || $cachestorage->isExpired('hookConfiguration') === true) {
            $hookConfiguration = \Configuration::get(Younitedpay::FRONT_HOOK);

            $cachestorage->set('hookConfiguration', $hookConfiguration);
        } else {
            $cacheInformations = $cachestorage->get('hookConfiguration');
            $hookConfiguration = $cacheInformations['content'];
        }

        return $hookConfiguration;
    }

    private function displaySelectedHook($params, $currentHook)
    {
        $context = \Context::getContext();

        $controller = $context->controller;

        /** @var \Currency $currency */
        $currency = new \Currency($context->cart->id_currency);
        if (array_search($currency->iso_code, Younitedpay::AVAILABLE_CURRENCIES) === false) {
            return;
        }

        $idProduct = 0;
        switch (true) {
            case $controller instanceof \ProductController:
                $idProduct = \Tools::getValue('id_product');
                $idAttribute = \Tools::getValue('id_product_attribute', null);
                $product = new \Product($idProduct);
                $price = $product->getPrice(true, $idAttribute);
                break;
            case $controller instanceof \CartController:
                $price = $context->cart->getOrderTotal();
                break;
            case $controller instanceof \IndexController:
            default:
                return '';
        }

        try {
            $hookConfiguration = $context->smarty->tpl_vars['hookConfiguration']->value;
        } catch (\Exception $ex) {
        }
        if (empty($hookConfiguration) === true) {
            $hookConfiguration = $this->getHookConfiguration();
            $context->smarty->assign('hookConfiguration', $hookConfiguration);
        }

        if ($hookConfiguration === 'disabled' || $hookConfiguration !== $currentHook || $hookConfiguration === 'done') {
            return '';
        }        

        /** @var ProductService $productservice */
        $productservice = ServiceContainer::getInstance()->get(ProductService::class);

        $templateCredit = $productservice->getBestPrice($price);            

        $context->smarty->assign(
            [
                'younited_hook' => $currentHook,
                'widget_younited' => false,
                'credit_template' => $templateCredit['template'],
                'product_price' => $price,
            ]
        );

        $context->smarty->assign('hookConfiguration', 'done');

        return $context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/product_infos.tpl'
        );
    }
}
