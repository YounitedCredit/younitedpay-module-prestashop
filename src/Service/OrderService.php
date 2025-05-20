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

namespace YounitedpayAddon\Service;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Exception;
use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Repository\PaymentRepository;
use YounitedpayClasslib\Utils\Translate\TranslateTrait;
use YounitedPaySDK\Model\NewAPI\GetPaymentId;
use YounitedPaySDK\Model\NewAPI\Request\CancelPayment;
use YounitedPaySDK\Model\NewAPI\Request\ExecutePayment;
use YounitedPaySDK\Model\NewAPI\Request\RefundPayment;
use YounitedPaySDK\Request\NewAPI\GetPaymentIdRequest;
use YounitedPaySDK\Request\NewAPI\CancelPaymentRequest;
use YounitedPaySDK\Request\NewAPI\ExecutePaymentRequest;
use YounitedPaySDK\Request\NewAPI\RefundPaymentRequest;

class OrderService
{
    use TranslateTrait;

    public $module;

    public $context;

    /** @var LoggerService */
    protected $loggerservice;

    /** @var PaymentRepository */
    protected $paymentrepository;

    /** @var YounitedClient */
    protected $client;

    public function __construct(
        LoggerService $loggerservice,
        PaymentRepository $paymentrepository,
        Younitedpay $module
    ) {
        $this->module = $module;
        $this->paymentrepository = $paymentrepository;
        $this->loggerservice = $loggerservice;
        $this->context = \Context::getContext();
    }

    /**
     * If credential are set return success to true, otherwise return the error response
     * Fill the YounitedClient ($this->client)
     *
     * @return array bool success | int status | string response
     */
    protected function buildClient()
    {
        $this->client = new YounitedClient($this->context->shop->id);
        if ($this->client->isCrendentialsSet() === false) {
            return [
                'success' => false,
                'status' => 0,
                'response' => $this->l('Please contact the shop owner payment is actually not possible'),
            ];
        }

        return ['success' => true];
    }

    protected function sendRequest($body, $request, $type, $refContract = '')
    {
        try {
            $response = $this->client->sendRequest($body, $request);
        } catch (Exception $ex) {
            $response = [
                'success' => false,
                'response' => $ex->getMessage(),
            ];
        }

        if ($response['success'] === false) {
            $this->loggerservice->addLog(
                $response['response'],
                'error response ' . $type,
                'error - ' . $refContract,
                $this
            );
        } else {
            $this->loggerservice->addLog($type . ' - success', $refContract, 'success', $this);
        }

        return $response;
    }

    public function cancelContract($idOrder, $refContract)
    {
        $clientBuildReturn = $this->buildClient();
        if ($clientBuildReturn['success'] !== true) {
            return true;
        }

        /** @var YounitedPayContract $younitedContract */
        $younitedContract = $this->paymentrepository->getContractByOrder($idOrder);
        if ($refContract === '') {
            $refContract = $younitedContract->id_external_younitedpay_contract;
        }

        if ($younitedContract->is_canceled === true) {
            return true;
        }

        $body = (new CancelPayment())
                ->setId($younitedContract->payment_id);

        $request = new CancelPaymentRequest();

        $this->sendRequest($body, $request, 'cancel contract', $younitedContract->payment_id);

        return true;
    }

    public function withdrawnContract($idOrder, $refContract, $amountWithdraw)
    {
        $clientBuildReturn = $this->buildClient();
        if ($clientBuildReturn['success'] !== true) {
            return true;
        }

        /** @var YounitedPayContract $younitedContract */
        $younitedContract = $this->paymentrepository->getContractByOrder($idOrder);
        if ($refContract === '') {
            $refContract = $younitedContract->id_external_younitedpay_contract;
        }

        if ($younitedContract->is_withdrawn === true) {
            return true;
        }

        $this->paymentrepository->setWithdrawnAmount($idOrder, $amountWithdraw);

        $body = (new RefundPayment())
                ->setPaymentId($younitedContract->payment_id)
                ->setAmount((float) \Tools::ps_round($amountWithdraw, 2))
                ->setIdempotencyKey($refContract);

        $request = new RefundPaymentRequest();

        $this->sendRequest($body, $request, 'withdraw contract', $younitedContract->payment_id);

        return true;
    }

    /**
     * Set the contract saved on table (Younited pay section on BO) to withdrawn
     *
     * @param int $idCart
     */
    public function setWithdrawnOnYounitedContract($idCart)
    {
        /** @var YounitedPayContract $younitedContract */
        $younitedContract = $this->paymentrepository->getContractByCart($idCart);

        return $this->paymentrepository->withdrawnContract($younitedContract->id_order);
    }

    /**
     * Set the contract saved on table (Younited pay section on BO) to withdrawn
     *
     * @param int $idCart
     */
    public function setCancelOnYounitedContract($idCart)
    {
        /** @var YounitedPayContract $younitedContract */
        $younitedContract = $this->paymentrepository->getContractByCart($idCart);

        return $this->paymentrepository->cancelContract($younitedContract->id_order);
    }

    /**
     * @param mixed $params
     *
     * @return float
     */
    public function calculatePartialRefund($params)
    {
        $amount = 0;

        if (empty($params['productList'])) {
            return $amount;
        }

        foreach ($params['productList'] as $product) {
            $amount += $product['amount'];
        }

        if (false == empty($params['partialRefundShippingCost'])) {
            $amount += $params['partialRefundShippingCost'];
        }

        // For prestashop version > 1.7.7
        if (false == empty($params['cancel_product'])) {
            $refundData = $params['cancel_product'];
            $amount += (float) str_replace(',', '.', $refundData['shipping_amount']);
        }

        $amount -= $this->calculatePartialDiscount($params);

        return $amount;
    }

    /**
     * @param mixed $params
     *
     * @return float
     */
    public function calculatePartialDiscount($params)
    {
        // $params differs according PS version
        $amount = 0;

        if (false == empty($params['refund_voucher_off'])) {
            if (false == empty($params['order_discount_price'])) {
                return (float) $params['order_discount_price'];
            }
        }

        if (false == empty($params['cancel_product']['voucher_refund_type'])) {
            if ($params['cancel_product']['voucher_refund_type'] == 1) {
                if ($params['order'] instanceof \Order) {
                    return (float) $params['order']->total_discounts_tax_incl;
                }
            }
        }

        return $amount;
    }

    /**
     * Activate the contract - Update the database and make a request to the API
     *
     * @param int $idOrder
     */
    public function activateOrder($idOrder)
    {
        /** @var YounitedPayContract younitedContract */
        $younitedContract = $this->paymentrepository->getContractByOrder($idOrder);

        if ($younitedContract->is_activated === true) {
            return true;
        }

        $clientBuildReturn = $this->buildClient();
        if ($clientBuildReturn['success'] !== true) {
            return true;
        }

        $this->paymentrepository->activateContract($idOrder);

        $body = (new ExecutePayment())
            ->setId((string) $younitedContract->payment_id);

        $request = new ExecutePaymentRequest();

        $this->sendRequest($body, $request, 'activate order', $younitedContract->payment_id);

        return true;
    }

    public function renderTemplate(\Order $order)
    {
        /** @var YounitedPayContract $younitedContract */
        $younitedContract = $this->getYounitedContract($order->id_cart);

        if ((int) $younitedContract->id_order !== (int) $order->id) {
            try {
                \Db::getInstance()->update(
                    YounitedPayContract::$definition['table'],
                    [
                        'id_order' => (int) $order->id,
                    ],
                    'id_cart = ' . (int) $order->id_cart
                );
            } catch (Exception $ex) {
                $this->loggerservice->addLog(
                    'Exception while linking order to younited contract: ' . $ex->getMessage(),
                    $order->reference,
                    'Error',
                    $this
                );
            }
        }

        $dateState = $younitedContract->date_upd;
        $state = $this->l('Awaiting');
        $stateWithdrawn = false;
        $withdrawnAmount = $younitedContract->withdrawn_amount;
        switch (true) {
            case (bool) $younitedContract->is_activated === true:
                $dateState = $younitedContract->activation_date;
                $state = $this->l('Activated');
                break;

            case (bool) $younitedContract->is_withdrawn === true:
                $dateState = $younitedContract->withdrawn_date;
                $state = $this->l('Withdrawed');
                $stateWithdrawn = true;
                break;

            case (bool) $younitedContract->is_canceled === true:
                $dateState = $younitedContract->canceled_date;
                $state = $this->l('Canceled');
                break;
        }

        if (empty($younitedContract->payment_id) || is_null($younitedContract->payment_id)) {
            $clientBuildReturn = $this->buildClient();
            if ($clientBuildReturn['success'] === true) {
                $this->getPaymentIdFromLegacy($younitedContract);   
            } else {
                $younitedContract->payment_id = 'Unknown - Error from API';
            }
        }

        \Context::getContext()->smarty->assign([
            'iso_lang' => \Context::getContext()->language->iso_code,
            'payment' => [
                'id' => $younitedContract->payment_id,
                'api_version' => (int) $younitedContract->api_version <= 0 ? '2024' : $younitedContract->api_version,
                'reference' => $younitedContract->id_external_younitedpay_contract,
                'date' => $younitedContract->date_add,
                'date_state' => $dateState,
                'status' => $state,
                'withdrawn_amount' => \Tools::ps_round($withdrawnAmount, 2),
                'is_withdrawn_confirmed' => $stateWithdrawn,
            ],
            'shop_url' => __PS_BASE_URI__,
            'logo_younitedpay_url' => 'modules/younitedpay/views/img/logo-younitedpay.png',
        ]);

        $template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/displayAdminOrderContentOrder.tpl';

        return \Context::getContext()->smarty->fetch($template);
    }

    /**
     * Get YounitedPayContract by cart or Order
     *
     * @param int $id Id of the cart / order concerned
     * @param string $type Type of object concerned: 'cart' | 'order'
     */
    public function getYounitedContract($id, $type = 'cart')
    {
        if ($type === 'cart') {
            return $this->paymentrepository->getContractByCart($id);
        }
        if ($type === 'order') {
            return $this->paymentrepository->getContractByOrder($id);
        }

        return new YounitedPayContract();
    }

    /**
     * Retrieve paymentId with contract reference (new id)
     *
     * @param YounitedPayContract $younitedContract
     */
    private function getPaymentIdFromLegacy(YounitedPayContract &$younitedContract)
    {
        $body = (new GetPaymentId())->setContractReference($younitedContract->id_external_younitedpay_contract);
        $response = $this->sendRequest($body, new GetPaymentIdRequest(), 'GetPaymentIdRequest');
        if (isset($response['paymentId'])) {
            $younitedContract->payment_id = $response['paymentId'];
            $this->paymentrepository->updatePaymentId($younitedContract->id_order, $response['paymentId']);
        } else {
            $younitedContract->payment_id = 'Unknown - Not found';
        }
    }
}
