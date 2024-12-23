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
        'displayExpressCheckout',
        'displayShoppingCartFooter',
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

    public function displayShoppingCartFooter($params)
    {
        return $this->displaySelectedHook($params, 'displayShoppingCartFooter');
    }

    public function displayExpressCheckout($params)
    {
        return $this->displaySelectedHook($params, 'displayExpressCheckout');
    }

    private function displayCart($params)
    {
    }

    private function getHookConfiguration($cartPage = false)
    {
        /** @var CacheYounited $cachestorage */
        $cachestorage = new CacheYounited();
        $idShop = \Context::getContext()->shop->id;
        $cacheKey = 'hookConfiguration' . ($cartPage === true ? 'Cart' : '') . (string) $idShop;
        $cacheExists = $cachestorage->exist($cacheKey);

        if ($cacheExists === false || $cachestorage->isExpired($cacheKey) === true) {
            if ($cartPage === true) {
                $hookConfiguration = (string) \Configuration::get(
                    Younitedpay::FRONT_HOOK_CART,
                    null,
                    null,
                    $idShop,
                    'disabled'
                );
            } else {
                $hookConfiguration = (string) \Configuration::get(
                    Younitedpay::FRONT_HOOK,
                    null,
                    null,
                    $idShop,
                    'disabled'
                );
            }
            $isShownProducts = (int) \Configuration::get(Younitedpay::SHOW_MONTHLY, null, null, $idShop, false);

            $cartPageNotAllowed = $cartPage === true && $isShownProducts < 2;
            $productPageNotAllowed = $cartPage === false && $isShownProducts === 2;
            if ($isShownProducts === 0 || ($cartPageNotAllowed && $productPageNotAllowed)) {
                $hookConfiguration = 'disabled';
            }

            $cachestorage->set($cacheKey, $hookConfiguration);
        } else {
            $cacheInformations = $cachestorage->get($cacheKey);
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
                $idProduct = (int) \Tools::getValue('id_product');
                $idAttribute = (int) \Tools::getValue('id_product_attribute', null);
                $idAttribute = $idAttribute > 0 ? $idAttribute : null;
                $product = new \Product($idProduct);
                $qty = (int) \Tools::getValue('qty', 1);
                $price = $product->getPrice(true, $idAttribute) * $qty;
                break;
            case $controller instanceof \CartController:
                $price = $context->cart->getOrderTotal();
                break;
            case $controller instanceof \IndexController:
            default:
                return '';
        }

        if (isset($context->smarty->tpl_vars['hookConfiguration'])) {
            $hookConfiguration = $context->smarty->tpl_vars['hookConfiguration']->value;
        }
        if (empty($hookConfiguration) === true) {
            $cartPage = false;
            if ($currentHook === 'displayExpressCheckout' || $currentHook === 'displayShoppingCartFooter') {
                $cartPage = true;
            }
            $hookConfiguration = $this->getHookConfiguration($cartPage);
            $context->smarty->assign('hookConfiguration', $hookConfiguration);
        }

        if ($hookConfiguration === 'disabled' || $hookConfiguration !== $currentHook || $hookConfiguration === 'done') {
            return '';
        }

        /** @var ProductService $productservice */
        $productservice = ServiceContainer::getInstance()->get(ProductService::class);

        $templateCredit = $productservice->getBestPrice($price, $currentHook);

        $context->smarty->assign(
            [
                'younited_hook' => $currentHook,
                'credit_template' => $templateCredit['template'],
                'product_price' => $price,
            ]
        );

        $context->smarty->assign('hookConfiguration', 'done');

        return $templateCredit['template'];
    }
}
