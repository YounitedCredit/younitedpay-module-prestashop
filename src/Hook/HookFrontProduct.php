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

use Media;
use Younitedpay;
use YounitedpayClasslib\Hook\AbstractHook;

class HookFrontProduct extends AbstractHook
{
    const AVAILABLE_HOOKS = [
        'displayProductPriceBlock',
        'displayAfterProductThumbs',
        'displayProductAdditionalInfo',
        'displayReassurance',
    ];

    public function displayProductPriceBlock($params)
    {
        $this->displaySelectedHook($params, 'displayProductPriceBlock');
    }

    public function displayAfterProductThumbs($params)
    {
        $this->displaySelectedHook($params, 'displayAfterProductThumbs');
    }

    public function displayProductAdditionalInfo($params)
    {
        $this->displaySelectedHook($params, 'displayProductAdditionalInfo');
    }

    public function displayReassurance($params)
    {
        $this->displaySelectedHook($params, 'displayReassurance');
    }

    private function displaySelectedHook($params, $currentHook)
    {
        $context = \Context::getContext();
        $hookConfiguration = $context->smarty->__get('younitedpay_hook');
        if ($hookConfiguration === null) {
            $hookConfiguration = \Configuration::get(Younitedpay::FRONT_HOOK);
            $context->smarty->assign(
                ['younitedpay_hook' => $hookConfiguration]
            );
        }

        if ($hookConfiguration === 'disabled' || $hookConfiguration !== $currentHook) {
            return '';
        }

        $frontScriptURI = __PS_BASE_URI__ . 'modules/' . $this->module->name / '/views/js/front/younitedpay_product.js';

        $context->smarty->assign(
            ['younitedpay_script' => $frontScriptURI]
        );

        $frontModuleLink = $context->link->getModuleLink(
            $this->module->name,
            'younitedpayproduct'
        );

        $idProduct = $params['id_product'];
        $product = new \Product($idProduct);

        Media::addJsDef([
            'younited_product_url' => $frontModuleLink,
            'younited_product_price' => $product->getPrice(),
        ]);

        $context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/template/front/credit_infos.tpl');
    }
}
