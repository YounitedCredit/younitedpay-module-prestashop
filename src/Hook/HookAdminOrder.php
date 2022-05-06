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
use YounitedpayAddon\Service\OrderService;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Hook\AbstractHook;

class HookAdminOrder extends AbstractHook
{
    /** @var \PaymentModule */
    public $module;

    const AVAILABLE_HOOKS = [
        'displayAdminOrderTabOrder',
        'displayAdminOrderTabLink',
        'displayAdminOrderContentOrder',
        'displayAdminOrderTabContent',
        'actionOrderStatusPostUpdate',
        'actionValidateOrder',
    ];

    public function displayAdminOrderTabOrder($params)
    {
        $template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/displayAdminOrderTabOrder.tpl';

        return \Context::getContext()->smarty->fetch($template);
    }

    public function displayAdminOrderTabLink($params)
    {
        return $this->displayAdminOrderTabOrder($params);
    }

    public function displayAdminOrderContentOrder($params)
    {
        return $this->renderTemplate($params);
    }

    public function displayAdminOrderTabContent($params)
    {
        return $this->displayAdminOrderContentOrder($params);
    }

    public function actionOrderStatusPostUpdate($params)
    {
        /** @var \OrderState $orderStatus */
        $orderStatus = $params['newOrderStatus'];

        $order = new \Order((int) $params['id_order']);

        if ($order->module !== $this->module->name) {
            return false;
        }

        $statusActivating = json_decode(\Configuration::get(Younitedpay::ORDER_STATE_DELIVERED), true);

        if (in_array((string) $orderStatus->id, $statusActivating) === true) {
            /** @var OrderService $orderservice */
            $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

            $orderservice->activateOrder($order->id);
        }
    }

    public function actionValidateOrder($params)
    {
        /** @var \Order $order */
        $order = $params['order'];

        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

        $orderservice->confirmOrder($order);
    }

    private function renderTemplate($params)
    {
        /** @var \Order $order */
        $order = new \Order((int) $params['id_order']);
        if ($order->module != $this->module->name) {
            return;
        }

        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

        return $orderservice->renderTemplate($order->id);
    }
}
