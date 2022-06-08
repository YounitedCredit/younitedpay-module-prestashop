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

use Configuration;
use Younitedpay;
use YounitedpayAddon\Service\OrderService;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Hook\AbstractHook;

class HookAdminOrder extends AbstractHook
{
    /** @var \PaymentModule */
    public $module;

    const AVAILABLE_HOOKS = [
        'displayAdminOrder',
        'displayAdminOrderTop',
        'displayAdminOrderTabLink',
        'displayAdminOrderContentOrder',
        'displayAdminOrderTabContent',
        'actionOrderStatusPostUpdate',
        'actionValidateOrder',
    ];

    public function displayAdminOrderTabOrder($params)
    {
        if ($this->isOrderYounitedPay($params) === false) {
            return;
        }

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

        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

        if (in_array((string) $orderStatus->id, $statusActivating) === true) {
            return $orderservice->activateOrder($order->id);
        }

        $idOrderCanceled = null !== _PS_OS_CANCELED_ ? _PS_OS_CANCELED_ : Configuration::get('PS_OS_CANCELED');

        if ((int) $idOrderCanceled === $orderStatus->id) {
            return $orderservice->cancelContract($order->id, '');
        }

        $idOrderWithdraw = null !== _PS_OS_REFUND_ ? _PS_OS_REFUND_ : Configuration::get('PS_OS_REFUND');

        if ((int) $idOrderWithdraw === $orderStatus->id) {
            return $orderservice->withdrawnContract($order->id, '', $order->getTotalPaid());
        }

        return true;
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
        $order = null;
        if (isset($params['order'])) {
            /** @var \Order $order */
            $order = $params['order'];
        }
        if (isset($params['id_order'])) {
            /** @var \Order $order */
            $order = new \Order((int) $params['id_order']);
        }

        if (\Validate::isLoadedObject($order) === false) {
            return;
        }

        if ($order->module != $this->module->name) {
            return;
        }

        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

        return $orderservice->renderTemplate($order->id);
    }

    public function displayAdminOrderTop($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.7', '<')) {
            return false;
        }

        if ($this->isOrderYounitedPay($params) === false) {
            return;
        }

        $return = $this->getAdminOrderPageMessages($params);
        $return .= $this->getPartialRefund($params);

        return $return;
    }

    public function displayAdminOrder($params)
    {
        // Since Ps 1.7.7 this hook is displayed at bottom of a page and we should use a hook DisplayAdminOrderTop
        if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
            return false;
        }

        if ($this->isOrderYounitedPay($params) === false) {
            return;
        }

        $return = $this->getAdminOrderPageMessages($params);
        $return .= $this->getPartialRefund($params);

        return $return;
    }

    protected function isOrderYounitedPay($params)
    {
        $order = new \Order((int) $params['id_order']);

        return $order->module === $this->module->name;
    }

    public function hookActionOrderSlipAdd($params)
    {
        if (\Tools::isSubmit('doPartialRefundYounitedPay')) {
            /** @var OrderService $orderservice */
            $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

            $params = array_merge(\Tools::getAllValues(), $params);
            $amountToRefund = $orderservice->calculatePartialRefund($params);

            return $orderservice->withdrawnContract($params['order']->id, '', $amountToRefund);
        }
    }

    protected function getPartialRefund($params)
    {
        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);
        $idOrder = $params['id_order'];
        $younitedContract = $orderservice->getYounitedContract($idOrder, 'order');

        if (!\Validate::isLoadedObject($younitedContract) || (int) $younitedContract->id_order !== $idOrder) {
            return '';
        }

        $context = \Context::getContext();
        $context->smarty->assign('chb_younited_refund', $this->l('Refund on YounitedPay'));

        $template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/partialRefund.tpl';

        return $context->smarty->fetch($template);
    }

    protected function getAdminOrderPageMessages($params)
    {
        $id_order = $params['id_order'];
        /* To come : Message of refund in progress / or not */
    }
}
