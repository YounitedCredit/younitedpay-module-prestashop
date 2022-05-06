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
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Repository\PaymentRepository;
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

    /** @var LoggerService */
    protected $loggerservice;

    /** @var PaymentRepository */
    protected $paymentrepository;

    /** @var string */
    protected $cellPhone;

    /** @var string */
    protected $errorMessage;

    public function __construct(
        LoggerService $loggerservice,
        PaymentRepository $paymentrepository,
        Younitedpay $module
    ) {
        $this->module = $module;
        $this->loggerservice = $loggerservice;
        $this->paymentrepository = $paymentrepository;
        $this->context = \Context::getContext();
    }

    /**
     * Create contract payment with maturity choosed
     */
    public function createContract($maturity, $totalAmount)
    {
        $customerAdress = new \Address($this->context->cart->id_address_delivery);

        $isPhoneInternational = $this->isInternationalPhone($customerAdress);

        if ($isPhoneInternational === false) {
            return [
                'response' => $this->errorMessage,
                'status' => 0,
                'success' => false,
            ];
        }

        $client = new YounitedClient($this->context->shop->id);
        if ($client->isCrendentialsSet() === false) {
            return [
                'success' => false,
                'status' => 0,
                'response' => $this->module->l('Please contact the shop owner payment is actually not possible'),
            ];
        }

        $response = $this->sendContractRequest($maturity, $totalAmount, $customerAdress, $client);

        if ($response['success'] === false) {
            return $response;
        }

        return $this->treatResponse($response);
    }

    protected function sendContractRequest($maturity, $totalAmount, $customerAdress, $client)
    {
        $customer = $this->context->customer;
        $country = new \Country($customerAdress->id_country);

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
            ->setCellPhoneNumber($this->cellPhone)
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

        $this->loggerservice->addLogAPI(json_encode($body), 'Info', $this);

        return $client->sendRequest($body, $request);
    }

    protected function treatResponse($response)
    {
        /** @var ArrayCollection $responseObject */
        $responseObject = $response['response'];

        $urlPayment = $responseObject['redirectUrl'];

        $contractRef = $responseObject['contractReference'];

        $this->saveContractInit($contractRef);

        $response['url'] = $urlPayment;

        return $response;
    }

    protected function isInternationalPhone($customerAdress)
    {
        $regValidPhone = '/^\+\d{10,18}/';

        if (empty($customerAdress->phone) === true) {
            $this->errorMessage = $this->module->l('Phone number is not filled.');
            $this->errorMessage .= ' ';
            $this->errorMessage .= $this->module->l('Please update your phone number of your address and try again.');

            return false;
        }

        $isPhoneInternational = preg_match($regValidPhone, $customerAdress->phone);

        if ($isPhoneInternational === 1) {
            $this->cellPhone = $customerAdress->phone;

            return true;
        } else {
            $isPhoneInternational = preg_match($regValidPhone, $customerAdress->phone_mobile);
            if ($isPhoneInternational === 1) {
                $this->cellPhone = $customerAdress->phone_mobile;

                return true;
            }
        }
        $this->errorMessage = $this->module->l('Phone number is not in international format (+XXX).');
        $this->errorMessage .= ' ';
        $this->errorMessage .= $this->module->l('Please update your phone number of your address and try again.');

        return false;
    }

    protected function saveContractInit($contractRef)
    {
        /** @var YounitedPayContract $contractYounited */
        $contractYounited = $this->getContractByCart($this->context->cart->id);
        $contractYounited->id_cart = $this->context->cart->id;
        $contractYounited->id_external_younitedpay_contract = $contractRef;
        $contractYounited->is_confirmed = false;
        $contractYounited->confirmation_date = null;
        $contractYounited->is_activated = false;
        $contractYounited->activation_date = null;
        $contractYounited->is_withdrawn = false;
        $contractYounited->withdrawn_date = null;
        $contractYounited->is_canceled = false;
        $contractYounited->canceled_date = null;
        $contractYounited->save();
    }

    /**
     * Validate and create Order when we have confirmation by API return
     *
     * @param \Cart $cart
     * @param \Customer $customer
     *
     * @return bool Result of validation
     */
    public function validateOrder($cart, $customer)
    {
        $context = \Context::getContext();
        $currency = $context->currency;

        $total = (float) $cart->getOrderTotal(true, \Cart::BOTH);

        $younitedContract = $this->getContractByCart($this->context->cart->id);
        if (empty($younitedContract->id_cart) === true || $younitedContract->id_cart === 0) {
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
            return $this->paymentrepository->confirmContract($this->context->cart->id, $this->module->currentOrder);
        }
    }

    /**
     * Get Contract linked to the cart
     *
     * @param int $idCart Id of cart concerned
     *
     * @return YounitedPayContract
     */
    public function getContractByCart($idCart)
    {
        return $this->paymentrepository->getContractByCart((int) $idCart);
    }

    /**
     * Get Contract linked to the Order
     *
     * @param int $idOrder Id of Order concerned
     *
     * @return YounitedPayContract
     */
    public function getContractByOrder($idOrder)
    {
        return $this->paymentrepository->getContractByOrder((int) $idOrder);
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
}
