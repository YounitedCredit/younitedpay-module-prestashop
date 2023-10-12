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
if (!defined('_PS_VERSION_')) {
    exit;
}

namespace YounitedpayAddon\Hook;

use Context;
use Younitedpay;
use YounitedpayClasslib\Hook\AbstractHook;

class CommonHook extends AbstractHook
{
    const AVAILABLE_HOOKS = [
        'actionFrontControllerSetMedia',
        'actionAdminControllerSetMedia',
    ];

    public function actionAdminControllerSetMedia($params)
    {
        /** @var \AdminController $controller */
        $controller = Context::getContext()->controller;
        if ($controller->controller_name === 'AdminOrders') {
            $controller->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/admin.js');
            \Media::addJsDef([
                'younitedpay' => [
                    'translations' => [
                        'slip_refund' => $this->l('Generate a Credit Slip must be selected to refund with Younited Pay'),
                        'title_slip_refund' => $this->l('Refund with Younited Pay'),
                    ],
                ],
            ]);
        }
    }

    public function actionFrontControllerSetMedia($params)
    {
        $controller = Context::getContext()->controller;
        $this->registerMedia($controller);
        switch (true) {
            case $controller instanceof \ProductController:
                $frontModuleLink = Context::getContext()->link->getModuleLink(
                    $this->module->name,
                    'product'
                );
                \Media::addJsDef([
                    'younitedpay' => [
                        'url_product' => $frontModuleLink,
                        'hook_product' => \Configuration::get(Younitedpay::FRONT_HOOK),
                        'id_product' => (int) \Tools::getValue('id_product'),
                    ],
                ]);
        }
    }

    protected function registerMedia(\FrontController $controller)
    {
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
