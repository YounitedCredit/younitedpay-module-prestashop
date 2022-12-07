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
use Customer;
use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Repository\PaymentRepository;
use YounitedpayClasslib\Utils\Translate\TranslateTrait;
use YounitedPaySDK\Model\Address;
use YounitedPaySDK\Model\ArrayCollection;
use YounitedPaySDK\Model\Basket;
use YounitedPaySDK\Model\BasketItem;
use YounitedPaySDK\Model\InitializeContract;
use YounitedPaySDK\Model\LoadContract;
use YounitedPaySDK\Model\MerchantOrderContext;
use YounitedPaySDK\Model\MerchantUrls;
use YounitedPaySDK\Model\PersonalInformation;
use YounitedPaySDK\Request\InitializeContractRequest;
use YounitedPaySDK\Request\LoadContractRequest;

class PaymentService
{
    use TranslateTrait;

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
    public $errorMessage;

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
                'response' => $this->l('Please contact the shop owner payment is actually not possible'),
            ];
        }

        $response = $this->sendContractRequest($maturity, $totalAmount, $customerAdress, $client);

        if ($response['success'] === false) {
            $this->logError('Bad response : ' . json_encode($response));

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
        $additionalAdress = '';
        if (mb_strlen($adresseStreet) > 38) {
            $additionalAdress = substr($adresseStreet, 38) . ' ';
            $adresseStreet = substr($adresseStreet, 0, 38);
        }
        $additionalAdress .= $customerAdress->address2 . ' ' . $customerAdress->other;

        $gender = $customer->id_gender === 2 ? 'FEMALE' : 'MALE';

        $address = (new Address())
            ->setStreetNumber('')
            ->setStreetName($adresseStreet)
            ->setAdditionalAddress($additionalAdress)
            ->setCity($customerAdress->city)
            ->setPostalCode($customerAdress->postcode)
            ->setCountryCode($country->iso_code);

        $personalInformation = (new PersonalInformation())
            ->setFirstName($customer->firstname)
            ->setLastName($customer->lastname)
            ->setGenderCode($gender)
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

    public function isInternationalPhone(\Address $customerAdress)
    {
        $regValidPhone = '/^\+33\d{9}/';

        if (empty($customerAdress->phone) === true) {
            $this->errorMessage = $this->l('Phone number is not filled.');
            $this->errorMessage .= ' ';
            $this->errorMessage .= $this->l('Please update your phone number of your address and try again.');

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
        $this->errorMessage = $this->l(
            'Cell Phone number is not french and in international format (+33X XX XX XX XX).'
        );
        $this->errorMessage .= ' ';
        $this->errorMessage .= $this->l('Please update your phone number of your address and try again.');

        return false;
    }

    protected function saveContractInit($contractRef)
    {
        $dateNull = '0000-00-00 00:00:00';
        /** @var YounitedPayContract $contractYounited */
        $contractYounited = $this->getContractByCart($this->context->cart->id);
        $contractYounited->id_cart = $this->context->cart->id;
        $contractYounited->id_external_younitedpay_contract = $contractRef;
        $contractYounited->is_confirmed = false;
        $contractYounited->is_activated = false;
        $contractYounited->is_withdrawn = false;
        $contractYounited->is_canceled = false;
        $contractYounited->confirmation_date = '';
        $contractYounited->activation_date = '';
        $contractYounited->withdrawn_date = '';
        $contractYounited->withdrawn_amount = 0;
        $contractYounited->canceled_date = '';
        $contractYounited->save();
    }

    /**
     * Confirm that amount of cart and amount paid is the same
     *
     * @param \Cart $cart
     *
     * @return bool|float False if nothing requested on the cart or error | Amount of requested Credit for the cart
     */
    public function getCreditRequestedAmount($cart)
    {
        $client = new YounitedClient($this->context->shop->id);
        if ($client->isCrendentialsSet() === false) {
            return false;
        }

        $younitedContract = $this->getContractByCart($this->context->cart->id);
        if (empty($younitedContract->id_cart) === true || $younitedContract->id_cart === 0) {
            return false;
        }

        $bodyContractRequest = (new LoadContract())
            ->setContractReference($younitedContract->id_external_younitedpay_contract);

        $requestContract = new LoadContractRequest();

        $response = $client->sendRequest($bodyContractRequest, $requestContract);

        $contentResponse = $response['response'];

        if ($response['success'] === true && $contentResponse['offer'] && $contentResponse['status']) {
            $statusOrderDone = ['INITIALIZED', 'GRANTED', 'CONFIRMED'];
            if (in_array($contentResponse['status'], $statusOrderDone) === false) {
                return false;
            }

            $offer = $contentResponse['offer'];
            if (isset($offer['requestedAmount']) === false) {
                return false;
            }

            return (float) $offer['requestedAmount'];
        }

        return false;
    }

    /**
     * Validate and create Order when we have confirmation by API return
     *
     * @param \Cart $cart
     * @param \Customer $customer
     *
     * @return bool Result of validation
     */
    public function validateOrder($cart, $customer = null)
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

        $defaultDelivered = null !== _PS_OS_PAYMENT_ ? _PS_OS_PAYMENT_ : Configuration::getGlobalValue('PS_OS_PAYMENT');

        if (\Validate::isLoadedObject($customer) === false) {
            $customer = new Customer($cart->id_customer);
        }

        $orderCreated = $cart->orderExists();
        if ($orderCreated === false) {
            $orderCreated = $this->module->validateOrder(
                $cart->id,
                (int) $defaultDelivered,
                $total,
                $this->l('Payment via Younited Pay', []),
                null,
                $extra_vars,
                (int) $currency->id,
                false,
                $customer->secure_key
            );
        }

        if ($orderCreated === true) {
            return $this->paymentrepository->confirmContract($this->context->cart->id, $this->module->currentOrder);
        }

        return $orderCreated;
    }

    /**
     * Set contract link to order to activated
     * Launched by Webhook
     *
     * @param int $idOrder - Id Of order concerned
     */
    public function setContractActivated($idOrder)
    {
        return $this->paymentrepository->activateContract($idOrder);
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

    public function logError($error, $title = 'Error')
    {
        $this->loggerservice->addLog(
            $error,
            $title,
            'error',
            $this
        );
    }
}
