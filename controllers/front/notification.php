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

use YounitedpayAddon\Service\LoggerService;
use YounitedpayAddon\Service\OrderService;
use YounitedpayAddon\Service\PaymentService;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedPaySDK\Webhook\Webhook;

class YounitedpayNotificationModuleFrontController extends ModuleFrontController
{
    const EVENT_TYPE_PAYMENT_CREATED = 'payment.created';
    const EVENT_TYPE_PAYMENT_UPDATED = 'payment.updated';
    const EVENT_TYPE_REFUND_CREATED = 'refund.created';
    const EVENT_TYPE_PERSONAL_LOAN_CUSTOMER_WITHDRAWAL = 'personal-loan.customer-withdrawal';

    const PAYMENT_STATUS_CANCELLED = 'Cancelled';

    /** @var \PaymentModule */
    public $module;

    /** @var LoggerService */
    public $loggerService;

    /** Prevent init content from Front Controller (case cart created by webhook) */
    public function init()
    {
    }

    public function initContent()
    {
        /* @var LoggerService */
        $this->loggerService = ServiceContainer::getInstance()->get(LoggerService::class);

        $idShop = $this->context->shop->id;
        $isProduction = (bool) \Configuration::get(Younitedpay::PRODUCTION_MODE, null, null, $idShop);
        $suffix = $isProduction === true ? '_PRODUCTION' : '';
        $webHookSecret = \Configuration::get(Younitedpay::WEBHOOK_SECRET . $suffix, null, null, $idShop);
        $webhook = new Webhook($webHookSecret);
        if ($webhook->getErrorResponse() !== false) {
            $this->endResponse($webhook->getErrorResponse());
        }

        $webhookNotification = $webhook->getEventNotification();

        if (empty($webhookNotification)) {
            $this->endResponse('No parameter caught on webhook', false);
        }

        $this->loggerService->addLogAPI(json_encode($webhookNotification->jsonSerialize()), 'Info', $this);

        $idCart = (int) Tools::getValue('id_cart');

        $cart = new Cart($idCart);

        if (Validate::isLoadedObject($cart) === false
            || $this->module->active == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0
            || $cart->id_customer == 0) {
            $this->endResponse('Error with the cart on webhook');
        }

        switch ($webhookNotification->getType()) {
            case self::EVENT_TYPE_PAYMENT_UPDATED:
                if ($webhookNotification->getData()->getStatus() !== self::PAYMENT_STATUS_CANCELLED) {
                    $this->endResponse('Event type not treat on webhook', false);
                }

                $this->processWebhookEvent($idCart, 'cancel');
                break;
            case self::EVENT_TYPE_REFUND_CREATED:
                $this->processWebhookEvent($idCart, 'refund');
                break;
            case self::EVENT_TYPE_PAYMENT_CREATED:
                $_POST['id_cart'] = (int) $idCart;
                $_POST['granted'] = 1;
                $controller = new YounitedpayValidationModuleFrontController();
                $this->endResponse($controller->initContent());
            case self::EVENT_TYPE_PERSONAL_LOAN_CUSTOMER_WITHDRAWAL:
                $this->endResponse('Event type not treat on webhook', false);
                break;
            default:
                $this->endResponse('Unknown event type caught on webhook', false);
        }
    }

    protected function endResponse($message, $error = true)
    {
        if ($error) {
            $this->loggerService->addLog($message, '[younitedpay notification]', 'info', $this);
        } else {
            $this->loggerService->addLogAPI($message, '[younitedpay notification]', $this);
        }

        if (version_compare(_PS_VERSION_, '1.7.5', '>=')) {
            $this->ajaxRender($message);
            exit;
        } else {
            $this->ajaxDie($message);
        }
    }

    /**
     * @param int $idCart
     * @param string $updateType
     */
    protected function processWebhookEvent($idCart, $updateType)
    {
        /** @var OrderService $orderService */
        $orderService = ServiceContainer::getInstance()->get(OrderService::class);

        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);

        $younitedContract = $paymentService->getContractByCart($idCart);

        if ((int) $younitedContract->id_order <= 0) {
            $this->endResponse('Error on contract activation, no order found with this cart (ID ' . $idCart . ')');
        }

        $order = new Order($younitedContract->id_order);

        if ($updateType === 'cancel') {
            $newIdState = null !== _PS_OS_CANCELED_ ? _PS_OS_CANCELED_ : (int)Configuration::get('PS_OS_CANCELED');

            if ((int)$newIdState === $order->current_state) {
                $this->endResponse('Already cancelled (Order ' . $order->id . ' - ' . $order->reference . ')');
            }

            if ($orderService->setCancelOnYounitedContract($order->id_cart) !== true) {
                $this->endResponse('Error on contract cancellation (Cart ID ' . $idCart . ')');
            }

            $this->setCurrentState((int) $newIdState, $order);

            $this->endResponse('Cancellation contract confirmed Cart ID' . $order->id_cart);
        } elseif ($updateType === 'refund') {
            $newIdState = null !== _PS_OS_REFUND_ ? _PS_OS_REFUND_ : Configuration::get('PS_OS_REFUND');

            if ((int) $newIdState === $order->current_state) {
                $this->endResponse('Already withdraw (Order ' . $order->id . ' - ' . $order->reference . ')');
            }

            if ($orderService->setWithdrawnOnYounitedContract($order->id_cart) !== true) {
                $this->endResponse('Error on contract Withdrawn (Cart ID ' . $order->id_cart . ')');
            }

            $this->setCurrentState((int) $newIdState, $order);

            $this->endResponse('Withdrawn contract confirmed Cart ID' . $order->id_cart);
        }

        $this->endResponse('Event type not treat on webhook', false);
    }

    /**
     * Set current order status
     *
     * @param int $id_order_state
     *
     * @return bool
     */
    private function setCurrentState($id_order_state, $order)
    {
        if (empty($id_order_state) || (int) $id_order_state === (int) $order->current_state) {
            return false;
        }

        $history = new OrderHistory();
        $history->id_order = (int) $order->id;
        $history->id_employee = 0;
        $use_existings_payment = !$order->hasInvoice();
        $history->changeIdOrderState((int) $id_order_state, $order, $use_existings_payment);

        try {
            return $history->add();
        } catch (PrestaShopDatabaseException $e) {
            $this->logError($e->getMessage(), 'setCurrentState PrestaShopDatabaseException');
            $this->logError($e->getTraceAsString(), 'setCurrentState PrestaShopDatabaseException');
            return false;
        } catch (PrestaShopException $e) {
            $this->logError($e->getMessage(), 'setCurrentState PrestaShopException');
            $this->logError($e->getTraceAsString(), 'setCurrentState PrestaShopException');
            return false;
        }
    }

    private function logError($error, $title = 'Error')
    {
        $this->loggerService->addLog(
            $error,
            $title,
            'error',
            $this
        );
    }
}
