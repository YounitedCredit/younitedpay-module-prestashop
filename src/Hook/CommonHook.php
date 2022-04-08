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

use YounitedpayClasslib\Hook\AbstractHook;
use Context;

class CommonHook extends AbstractHook
{
    const AVAILABLE_HOOKS = [
        'actionFrontControllerSetMedia',
    ];

    public function actionFrontControllerSetMedia($params)
    {
        $controller = Context::getContext()->controller;
        if ($controller instanceof \ProductController) {
            $controller->registerJavascript(
                'younitedpay-main',
                'modules/' . $this->module->name . '/views/js/front/front.js',
                [
                    'priority' => 500,
                ]
            );
            $controller->registerStylesheet(
                'younitedpay-main',
                'modules/' . $this->module->name . '/views/css/front.css',
                [
                    'priority' => 500,
                ]
            );
        }
    }
}
