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

namespace YounitedpayAddon\Service;

use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Repository\PaymentRepository;
use YounitedPaySDK\Model\ActivateContract;
use YounitedPaySDK\Model\CancelContract;
use YounitedPaySDK\Model\ConfirmContract;
use YounitedPaySDK\Request\ActivateContractRequest;
use YounitedPaySDK\Request\CancelContractRequest;
use YounitedPaySDK\Request\ConfirmContractRequest;

class OrderService
{
    public $module;

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
                'response' => $this->module->l('Please contact the shop owner payment is actually not possible'),
            ];
        }

        return ['success' => true];
    }

    /**
     * Confirm the contract - Update the database and make a request to the API
     *
     * @param \Order $order
     */
    public function confirmOrder(\Order $order)
    {
        /** @var YounitedPayContract younitedContract */
        $younitedContract = $this->paymentrepository->getContractByOrder($order->id);

        if ($order->module !== $this->module->name) {
            return $this->cancelContract($order->id, $younitedContract->id_external_younitedpay_contract);
        }

        if ($younitedContract->is_confirmed === true) {
            return true;
        }

        $clientBuildReturn = $this->buildClient();
        if ($clientBuildReturn['success'] !== true) {
            return $clientBuildReturn;
        }

        $body = (new ConfirmContract())
            ->setContractReference((string) $younitedContract->id_external_younitedpay_contract)
            ->setMerchantOrderId((string) $order->id);

        $request = new ConfirmContractRequest();

        return $this->client->sendRequest($body, $request);
    }

    protected function cancelContract($idOrder, $refContract)
    {
        $this->paymentrepository->cancelContract($idOrder);

        $body = (new CancelContract())
            ->setContractReference($refContract);

        $request = new CancelContractRequest();

        return $this->client->sendRequest($body, $request);
    }

    /**
     * Activate the contract - Update the database and make a request to the API
     *
     * @param $idOrder
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
            return $clientBuildReturn;
        }

        $this->paymentrepository->activateContract($idOrder);

        $body = (new ActivateContract())
            ->setContractReference($younitedContract->id_external_younitedpay_contract);

        $request = new ActivateContractRequest();

        return $this->client->sendRequest($body, $request);
    }

    public function renderTemplate($idOrder)
    {
        /** @var YounitedPayContract $younitedContract */
        $younitedContract = $this->paymentrepository->getContractByOrder($idOrder);

        $dateState = $younitedContract->date_upd;
        $state = $this->module->l('Awaiting');
        switch (true) {
            case $younitedContract->is_activated === true:
                $dateState = $younitedContract->activation_date;
                $state = $this->module->l('Activated');
                break;

            case $younitedContract->is_confirmed === true:
                $dateState = $younitedContract->confirmation_date;
                $state = $this->module->l('Confirmed');
                break;

            case $younitedContract->is_withdrawn === true:
                $dateState = $younitedContract->withdrawn_date;
                $state = $this->module->l('Withdrawed');
                break;

            case $younitedContract->is_canceled === true:
                $dateState = $younitedContract->canceled_date;
                $state = $this->module->l('Canceled');
                break;
        }

        \Context::getContext()->smarty->assign([
            'payment' => [
                'id' => $younitedContract->id_external_younitedpay_contract,
                'url' => $younitedContract->id_external_younitedpay_contract,
                'date' => $dateState,
                'status' => $state,
            ],
            'shop_url' => __PS_BASE_URI__ !== '/' ? substr(__PS_BASE_URI__, 0, 1) : '',
            'logo_younitedpay_url' => '/modules/younitedpay/views/img/logo-younitedpay.png',
        ]);

        $template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/displayAdminOrderContentOrder.tpl';

        return \Context::getContext()->smarty->fetch($template);
    }
}
