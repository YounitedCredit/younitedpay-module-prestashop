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

use Cart;
use Configuration;
use Exception;
use Tools;
use Younitedpay;
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Service\LoggerService;
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
        'actionOrderSlipAdd',
        'displayAdminOrder',
        'displayAdminOrderTop',
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
            return;
        }

        $statusActivating = json_decode(\Configuration::get(Younitedpay::ORDER_STATE_DELIVERED), true);

        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

        if (in_array((string) $orderStatus->id, $statusActivating) === true) {
            $orderservice->activateOrder($order->id);

            return;
        }

        $idOrderCanceled = null !== _PS_OS_CANCELED_ ? _PS_OS_CANCELED_ : Configuration::get('PS_OS_CANCELED');

        if ((int) $idOrderCanceled === $orderStatus->id) {
            $orderservice->cancelContract($order->id, '');

            return;
        }

        $idOrderWithdraw = null !== _PS_OS_REFUND_ ? _PS_OS_REFUND_ : Configuration::get('PS_OS_REFUND');

        if ((int) $idOrderWithdraw === $orderStatus->id) {
            /** @var YounitedPayContract $younitedContract */
            $younitedContract = $orderservice->getYounitedContract($order->id, 'order');
            $amountWithdrawn = $order->getTotalPaid() - $younitedContract->withdrawn_amount;

            $orderservice->withdrawnContract($order->id, '', $amountWithdrawn);
        }
    }

    public function actionValidateOrder($params)
    {
        /** @var \Order $order */
        $order = $params['order'];

        if (isset($order->module) === false || $order->module !== 'younitedpay') {
            return true;
        }

        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

        $orderservice->confirmOrder($order);

        $countOrders = \Order::getByReference($order->reference)->count();

        /** @var Cart $cart */
        $cart = $params['cart'];

        /** @var LoggerService $loggerService */
        $loggerService = ServiceContainer::getInstance()->get(LoggerService::class);

        if ($order->id_carrier !== $cart->id_carrier && $countOrders > 1) {
            $loggerService->addLog(
                sprintf(
                    'Cannot update - more than one order (split packages) - Do not Updating wrong carrier %s (order) to %s (cart)',
                    $order->id_carrier,
                    $cart->id_carrier
                ),
                'actionValidateOrder',
                'error',
                null
            );

            return true;
        }
        if ($order->id_carrier !== $cart->id_carrier) {
            $loggerService->addLog(
                sprintf(
                    'Updating wrong carrier %s (order) to %s (cart)',
                    $order->id_carrier,
                    $cart->id_carrier
                ),
                'Updating carrier',
                'error',
                null
            );
            $order->id_carrier = $cart->id_carrier;
            $context = \Context::getContext();
            $computingPrecision = _PS_PRICE_COMPUTE_PRECISION_;
            if (\method_exists($context, 'getComputingPrecision') === true) {
                $computingPrecision = \Context::getContext()->getComputingPrecision();
            }

            $order->total_shipping_tax_excl = Tools::ps_round(
                (float) $cart->getPackageShippingCost($cart->id_carrier, false),
                $computingPrecision
            );
            $order->total_shipping_tax_incl = Tools::ps_round(
                (float) $cart->getPackageShippingCost($cart->id_carrier, true),
                $computingPrecision
            );
            $order->total_shipping = $order->total_shipping_tax_incl;

            $order->total_wrapping_tax_excl = Tools::ps_round(
                (float) abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING, null, $cart->id_carrier)),
                $computingPrecision
            );
            $order->total_wrapping_tax_incl = Tools::ps_round(
                (float) abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, null, $cart->id_carrier)),
                $computingPrecision
            );
            $order->total_wrapping = $order->total_wrapping_tax_incl;

            $order->total_paid_tax_excl = Tools::ps_round(
                (float) $cart->getOrderTotal(false, Cart::BOTH, null, $cart->id_carrier),
                $computingPrecision
            );
            $order->total_paid_tax_incl = Tools::ps_round(
                (float) $cart->getOrderTotal(true, Cart::BOTH, null, $cart->id_carrier),
                $computingPrecision
            );

            $idOrderCarrier = (int) $order->getIdOrderCarrier();
            if ($idOrderCarrier > 0) {
                $orderCarrier = new \OrderCarrier($idOrderCarrier);
                $orderCarrier->id_carrier = $cart->id_carrier;
                $orderCarrier->shipping_cost_tax_excl = (float) $order->total_shipping_tax_excl;
                $orderCarrier->shipping_cost_tax_incl = (float) $order->total_shipping_tax_incl;
                try {
                    $orderCarrier->save();
                } catch (Exception $ex) {
                    $loggerService->addLog('Error while updating orderCarrier', 'actionValidateOrder');
                    $loggerService->addLog($ex->getMessage(), 'actionValidateOrder');
                }
            }
            try {
                $order->save();
            } catch (Exception $ex) {
                $loggerService->addLog('Error while updating order', 'actionValidateOrder');
                $loggerService->addLog($ex->getMessage(), 'actionValidateOrder');
            }
        }
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

        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

        $younitedContract = $orderservice->getYounitedContract($order->id_cart);

        if ($order->module != $this->module->name && \Validate::isLoadedObject($younitedContract) === false) {
            return;
        }

        return $orderservice->renderTemplate($order);
    }

    public function displayAdminOrderTop($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.7', '<')) {
            return;
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
            return;
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
        if (isset($params['order']) && $params['order'] instanceof \Order) {
            /** @var \Order $order */
            $order = $params['order'];
        } elseif (isset($params['id_order'])) {
            $order = new \Order((int) $params['id_order']);
        } else {
            return false;
        }

        return $order->module === $this->module->name;
    }

    public function actionOrderSlipAdd($params)
    {
        if (\Tools::isSubmit('doPartialRefundYounitedPay')) {
            /** @var OrderService $orderservice */
            $orderservice = ServiceContainer::getInstance()->get(OrderService::class);

            $params = array_merge(\Tools::getAllValues(), $params);
            $amountToRefund = $orderservice->calculatePartialRefund($params);

            return $orderservice->withdrawnContract($params['order']->id, '', (float) $amountToRefund);
        }
    }

    protected function getPartialRefund($params)
    {
        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);
        $idOrder = $params['id_order'];

        /** @var YounitedPayContract $younitedContract */
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
