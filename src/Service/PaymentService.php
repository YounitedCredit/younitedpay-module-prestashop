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

use Configuration;
use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Repository\PaymentRepository;
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Logger\ApiLogger;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use YounitedPaySDK\Model\Address;
use YounitedPaySDK\Model\ArrayCollection;
use YounitedPaySDK\Model\Basket;
use YounitedPaySDK\Model\BasketItem;
use YounitedPaySDK\Model\InitializeContract;
use YounitedPaySDK\Model\MerchantOrderContext;
use YounitedPaySDK\Model\MerchantUrls;
use YounitedPaySDK\Model\PersonalInformation;
use YounitedPaySDK\Request\InitializeContractRequest;

class PaymentService
{
    public $module;

    /** @var \Context */
    private $context;

    /** @var ProcessLoggerHandler */
    protected $logger;

    /** @var PaymentRepository */
    protected $repository;

    public function __construct(
        ProcessLoggerHandler $logger,
        PaymentRepository $repository,
        Younitedpay $module
    ) {
        $this->module = $module;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->context = \Context::getContext();
    }

    public function createContract($maturity, $totalAmount)
    {
        $regValidPhone = '/^((\+|00)\d{1,3})?\d{6,12}$/';

        $customerAdress = new \Address($this->context->cart->id_address_delivery);
        $customer = $this->context->customer;
        $country = new \Country($customerAdress->id_country);

        $isPhoneInternational = preg_match($regValidPhone, $customerAdress->phone);

        if ($isPhoneInternational === 1) {
            $cellPhone = $customerAdress->phone;
        } else {
            $isPhoneInternational = preg_match($regValidPhone, $customerAdress->phone_mobile);
            if ($isPhoneInternational === 1) {
                $cellPhone = $customerAdress->phone_mobile;
            }
        }

        if ($isPhoneInternational === 0 || $isPhoneInternational === false) {
            return [
                'response' => $this->module->l('Error : Phone number is not in international format (+XXX)'),
                'status' => 0,
                'success' => false,
            ];
        }

        $client = new YounitedClient($this->context->shop->id, $this->logger);
        if ($client->isCrendentialsSet() === false) {
            return [
                'success' => false,
                'response' => 'no credential set',
            ];
        }

        $birthdate = empty($customer->birthday) === false && $customer->birthday !== '0000-00-00'
            ? new \DateTime($customer->birthday . 'T00:00:00')
            : null;

        $adresseStreet = $customerAdress->address1;

        $address = (new Address())
            ->setStreetNumber('')
            ->setStreetName($adresseStreet)
            ->setAdditionalAddress($customerAdress->address2 . ' ' . $customerAdress->other)
            ->setCity($customerAdress->city)
            ->setPostalCode($customerAdress->postcode)
            ->setCountryCode($country->iso_code);

        $personalInformation = (new PersonalInformation())
            ->setFirstName($customer->firstname)
            ->setLastName($customer->lastname)
            ->setGenderCode((new \Gender())->name[$customer->id_gender])
            ->setEmailAddress($customer->email)
            ->setCellPhoneNumber($cellPhone)
            ->setBirthDate($birthdate)
            ->setAddress($address);

        $cartItems = $this->context->cart->getProducts();

        $basketItems = [];
        foreach ($cartItems as $productInCart) {
            $basketItems[] = (new BasketItem())
                ->setItemName($productInCart['name'])
                ->setQuantity((int) $productInCart['cart_quantity'])
                ->setUnitPrice((float) $productInCart['price']);
        }

        $basket = (new Basket())
            ->setBasketAmount((float) $totalAmount)
            ->setItems($basketItems);

        $merchantUrls = (new MerchantUrls())
            ->setOnApplicationFailedRedirectUrl($this->getLink('error'))
            ->setOnApplicationSucceededRedirectUrl($this->getLink('success'))
            ->setOnCanceledWebhookUrl($this->getLink('webhook', ['cancel' => 1]))
            ->setOnWithdrawnWebhookUrl($this->getLink('webhook', ['widhdrawn' => 1]));

        $merchantOrderContext = (new MerchantOrderContext())
            ->setChannel('ONLINE')
            // ->setShopCode((string) $this->context->shop->id)
            ->setMerchantReference((string) $this->context->cart->id);

        $body = (new InitializeContract())
            ->setRequestedMaturity((int) $maturity)
            ->setPersonalInformation($personalInformation)
            ->setBasket($basket)
            ->setMerchantUrls($merchantUrls)
            ->setMerchantOrderContext($merchantOrderContext);

        $request = new InitializeContractRequest();

        $this->addLogAPI(json_encode($body), 'Info');

        $response = $client->sendRequest($body, $request);

        if ($response['success'] === false) {
            return $response;
        }

        /** @var ArrayCollection $responseObject */
        $responseObject = $response['response'];

        $urlPayment = $responseObject['redirectUrl'];
        
        $contractRef = $responseObject['contractReference'];

        $this->saveContractInit($contractRef);

        $response['url'] = $urlPayment;

        return $response;
    }

    protected function saveContractInit($contractRef)
    {
        /** @var YounitedPayContract $contractYounited */
        $contractYounited = $this->getContractByCart();
        $contractYounited->id_cart = $this->context->cart->id;
        $contractYounited->id_external_younitedpay_contract = $contractRef;
        $contractYounited->is_confirmed = false;
        $contractYounited->is_activated = false;
        $contractYounited->is_withdrawn = false;
        $contractYounited->is_canceled = false;
        $contractYounited->save();
    }

    /**
     * Validate and create Order when we have confirmation by API return
     * 
     * @param \Cart $cart
     * 
     * @param \Customer $customer
     * 
     * @return bool Result of validation
     * 
     */
    public function validateOrder($cart, $customer)
    {
        $context = \Context::getContext();
        $currency = $context->currency;

        $total = (float) $cart->getOrderTotal(true, \Cart::BOTH);
        
        $younitedContract = $this->getContractByCart();
        if (empty($younitedContract->id_cart) === true) {
            return false;
        }

        $extra_vars = [
            'transaction_id' => $younitedContract->id_external_younitedpay_contract,
            '{shop_domain}' => Configuration::get('PS_SHOP_DOMAIN'),
        ];

        $defaultDelivered = null !== _PS_OS_WS_PAYMENT_
            ? _PS_OS_WS_PAYMENT_
            : Configuration::getGlobalValue('PS_OS_WS_PAYMENT');

        $orderCreated = $this->module->validateOrder(
            $cart->id,
            (int) $defaultDelivered,
            $total,
            $this->module->l('Payment via YounitedPay', []),
            null,
            $extra_vars,
            (int) $currency->id,
            false,
            $customer->secure_key
        );

        if ($orderCreated === true) {
            $younitedContract->is_confirmed = true;
            $younitedContract->confirmation_date = new \Datetime();
            $younitedContract->id_order = $this->module->currentOrder;
            return $younitedContract->save();            
        }
    }

    protected function getContractByCart()
    {
        return $this->repository->getContractByCart((int) $this->context->cart->id);
    }

    protected function getLink($controller, $params = [])
    {
        $params['id_cart'] = $this->context->cart->id;

        return $this->context->link->getModuleLink(
            $this->module->name,
            $controller,
            $params
        );
    }

    public function addLog($msg, $objectModel = null, $objectId = null, $name = null, $level = 'info')
    {
        $this->logger->openLogger();
        $this->logger->addLog($msg, $objectModel, $objectId, $name, $level);
        $this->logger->closeLogger();
        $this->addLogAPI($msg, $level);
    }

    protected function addLogAPI($msg, $type='Error')
    {
        /** @var ApiLogger $apiLogger */
        $apiLogger = ApiLogger::getInstance();
        $apiLogger->log($this, $msg, $type, false);
    }
}
