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

use Configuration;
use Customer;
use Younitedpay;
use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Repository\PaymentRepository;
use YounitedpayClasslib\Utils\Translate\TranslateTrait;
use YounitedPaySDK\Adapter\CreatePaymentAdapter;
use YounitedPaySDK\Model\Address;
use YounitedPaySDK\Model\ArrayCollection;
use YounitedPaySDK\Model\Basket;
use YounitedPaySDK\Model\BasketItem;
use YounitedPaySDK\Model\InitializeContract;
use YounitedPaySDK\Model\MerchantOrderContext;
use YounitedPaySDK\Model\MerchantUrls;
use YounitedPaySDK\Model\NewAPI\CustomExperience;
use YounitedPaySDK\Model\NewAPI\Request\GetPayment;
use YounitedPaySDK\Model\NewAPI\TechnicalInformation;
use YounitedPaySDK\Model\PersonalInformation;
use YounitedPaySDK\Request\InitializeContractRequest;
use YounitedPaySDK\Request\NewAPI\GetPaymentRequest;

class PaymentService
{
    const PAYMENT_STATUS_ACCEPTED = 'Accepted';
    const PAYMENT_STATUS_EXECUTED = 'Executed';

    use TranslateTrait;

    public $module;

    /** @var \Context */
    private $context;

    /** @var YounitedClient */
    private $client;

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
        $customerAddress = new \Address($this->context->cart->id_address_delivery);

        $isPhoneInternational = $this->isInternationalPhone($customerAddress);

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

        try {
            $response = $this->sendContractRequest($maturity, $totalAmount, $customerAddress, $client);
        } catch (\PrestaShopDatabaseException $e) {
            $this->logError($e->getMessage(), 'sendContractRequest PrestaShopDatabaseException');
            $this->logError($e->getTraceAsString(), 'sendContractRequest PrestaShopDatabaseException');

            return [
                'success' => false,
                'status' => 0,
                'response' => $this->l('Please contact the shop owner payment is actually not possible'),
            ];
        } catch (\PrestaShopException $e) {
            $this->logError($e->getMessage(), 'sendContractRequest PrestaShopException');
            $this->logError($e->getTraceAsString(), 'sendContractRequest PrestaShopException');

            return [
                'success' => false,
                'status' => 0,
                'response' => $this->l('Please contact the shop owner payment is actually not possible'),
            ];
        } catch (\Exception $e) {
            $this->logError($e->getMessage(), 'sendContractRequest Exception');
            $this->logError($e->getTraceAsString(), 'sendContractRequest Exception');

            return [
                'success' => false,
                'status' => 0,
                'response' => $this->l('Please contact the shop owner payment is actually not possible'),
            ];
        }

        if ($response['success'] === false) {
            $this->logError('Bad response : ' . json_encode($response));

            return $response;
        }

        return $this->treatResponse($response);
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \Exception
     */
    protected function sendContractRequest($maturity, $totalAmount, $customerAddress, YounitedClient $client)
    {
        $customer = $this->context->customer;
        $country = new \Country($customerAddress->id_country);

        $birthdate = empty($customer->birthday) === false && $customer->birthday !== '0000-00-00'
            ? (new \DateTime($customer->birthday))->format('Y-m-d')
            : null;

        $adresseStreet = $customerAddress->address1;
        $additionalAdress = '';
        if (mb_strlen($adresseStreet) > 38) {
            $additionalAdress = substr($adresseStreet, 38) . ' ';
            $adresseStreet = substr($adresseStreet, 0, 38);
        }
        $additionalAdress .= $customerAddress->address2 . ' ' . $customerAddress->other;

        if (mb_strlen($additionalAdress) > 38) {
            $additionalAdress = substr($additionalAdress, 0, 38);
        }

        $gender = $customer->id_gender === 2 ? 'FEMALE' : 'MALE';

        $address = (new Address())
            ->setStreetNumber('')
            ->setStreetName($adresseStreet)
            ->setAdditionalAddress($additionalAdress)
            ->setCity($customerAddress->city)
            ->setPostalCode($customerAddress->postcode)
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
            ->setOnGrantedWebhookUrl($this->getLink('success', ['granted' => 1]))
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

        $isUseAPIv2 = (bool) Configuration::get(Younitedpay::USE_NEW_API, null, null, null, true);

        if ($isUseAPIv2 === false) {
            $this->loggerservice->addLogAPI('Old contract body:' . json_encode($body), 'Info', $this);
            return $client->sendRequest($body, $request);
        }

        $webhookUrl = $this->getLink('notification', ['id_cart' => $this->context->cart->id]);
        $redirectUrl = $this->getLink('validation', ['id_cart' => $this->context->cart->id]);
        $request = $this->convertOldRequest($request->setModel($body), $client->shopCode, $webhookUrl, $redirectUrl);

        return $client->sendRequest($body, $request);
    }

    /**
     * @throws \Exception
     */
    private function convertOldRequest($oldRequest, $shopCode, $webhookUrl, $redirectUrl, $apiVersion = '2025-01-01')
    {
        $technicalInformation = (new TechnicalInformation())
            ->setWebhookNotificationUrl($webhookUrl)
            ->setApiVersion($apiVersion);

        $customExperience = (new CustomExperience())
            ->setCustomerRedirectUrl($redirectUrl);

        $adapter = (new CreatePaymentAdapter())
            ->setShopCode($shopCode)
            ->setTechnicalInformation($technicalInformation)
            ->setCustomExperience($customExperience)
            ->convertInitializeContract($oldRequest);

        $this->loggerservice->addLogAPI('New contract body:' . (string) $adapter->getBody(), 'Info', $this);
        
        return $adapter;
    }

    protected function treatResponse($response)
    {
        /** @var ArrayCollection $responseObject */
        $responseObject = $response['response'];

        if (false === empty($responseObject['contractReference']) && false === empty($responseObject['redirectUrl'])) {
            $urlPayment = $responseObject['redirectUrl'];
            $contractRef = $responseObject['contractReference'];

            $this->saveContractInit($contractRef);
        } else {
            $urlPayment = $responseObject['paymentLink'];
            $paymentId = $responseObject['paymentId'];

            $getPaymentResponse = $this->getApiPaymentById($paymentId);

            $contractRef = $getPaymentResponse['personalLoanPaymentDetails']['loanReference'];
            $apiVersion = $getPaymentResponse['apiVersion'];

            $this->saveContractInit($contractRef, $paymentId, $apiVersion);
        }

        $response['url'] = $urlPayment;

        return $response;
    }

    public function isInternationalPhone(\Address $customerAddress)
    {
        $this->cellPhone = '';
        $regValidPhone = '/^\+33\d{9}/';
        $countryDefault = new \Country((int) \Configuration::get('PS_COUNTRY_DEFAULT'), $this->context->language->id);
        if ($countryDefault->iso_code == 'ES') {
            $regValidPhone = '/^\+34\d{9}/';
        }

        if (empty($customerAddress->phone) === true && empty($customerAddress->phone_mobile) === true) {

            return true;
        }

        $isPhoneInternational = preg_match($regValidPhone, $customerAddress->phone);
        if ($isPhoneInternational === 1) {
            $this->cellPhone = $customerAddress->phone;

            return true;
        } else {
            $isPhoneInternational = preg_match($regValidPhone, $customerAddress->phone_mobile);
            if ($isPhoneInternational === 1) {
                $this->cellPhone = $customerAddress->phone_mobile;

                return true;
            }
        }

        return true;
    }

    protected function saveContractInit($contractRef, $contractPaymentId = null, $apiVersion = '2024-01-01')
    {
        /** @var YounitedPayContract $contractYounited */
        $contractYounited = $this->getContractByCart($this->context->cart->id);
        $contractYounited->payment_id = $contractPaymentId;
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
        $contractYounited->api_version = $apiVersion;
        $contractYounited->save();
    }

    /**
     * Get api payment by id
     *
     * @param string $paymentId
     *
     * @return bool|float False if nothing requested on the api payment id or error | Api Payment of id requested
     */
    public function getApiPaymentById($paymentId)
    {
        $client = new YounitedClient($this->context->shop->id);
        if ($client->isCrendentialsSet() === false) {
            return false;
        }

        $getPaymentRequestModel = (new GetPayment())->setId($paymentId);
        $getPaymentRequest = (new GetPaymentRequest())->setModel($getPaymentRequestModel);
        $getPaymentResponse = $client->sendRequest($getPaymentRequestModel, $getPaymentRequest);

        if ($getPaymentResponse['success'] === true) {
            $getPaymentResponse['response']['apiVersion'] = $getPaymentRequest->getApiVersion();

            return $getPaymentResponse['response'];
        }

        return false;
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

        $younitedContract = $this->getContractByCart($cart->id);
        if (empty($younitedContract->id_cart) === true || $younitedContract->id_cart === 0) {
            return false;
        }

        $getPaymentResponse = $this->getApiPaymentById($younitedContract->payment_id);

        if (false === empty($getPaymentResponse) && $getPaymentResponse['amount'] && $getPaymentResponse['status']) {
            $statusOrderDone = [self::PAYMENT_STATUS_ACCEPTED, self::PAYMENT_STATUS_EXECUTED];
            if (in_array($getPaymentResponse['status'], $statusOrderDone) === false) {
                return false;
            }

            return (float) $getPaymentResponse['amount'];
        }

        return false;
    }

    /**
     * Validate and create Order when we have confirmation by API return
     *
     * @param \Cart $cart
     * @param \Customer $customer
     * @param float $total - amount paid given by Younited API
     *
     * @return bool Result of validation
     */
    public function validateOrder($cart, $customer, $total)
    {
        $context = \Context::getContext();
        $currency = $context->currency;

        $younitedContract = $this->getContractByCart($cart->id);
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
                (float) $total,
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

        return false;
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
