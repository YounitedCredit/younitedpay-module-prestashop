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
use YounitedpayAddon\Repository\ConfigRepository;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use YounitedPaySDK\Model\Address;
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

    /** @var ConfigRepository */
    protected $configRepository;

    public function __construct(
        ProcessLoggerHandler $logger,
        Younitedpay $module
    ) {
        $this->module = $module;
        $this->logger = $logger;
        $this->context = \Context::getContext();
    }

    public function createContract($maturity, $totalAmount)
    {
        $client = new YounitedClient($this->context->shop->id, $this->logger);
        if ($client->isCrendentialsSet() === false) {
            return [
                'success' => false,
                'error' => ''
            ];
        }

        $customerAdress = new \Address($this->context->cart->id_address_delivery);
        $customer = $this->context->customer;
        $country = new \Country($customerAdress->id_country);

        $birthdate = new \DateTime($customer->birthday);
        
        $address = (new Address())
            ->setStreetNumber('')
            ->setStreetName($customerAdress->address1 . empty($customerAdress->address2))
            ->setAdditionalAddress($customerAdress->other)
            ->setCity($customerAdress->country)
            ->setPostalCode($customerAdress->postcode)
            ->setCountryCode($country->iso_code);
        
        $personalInformation = (new PersonalInformation())
            ->setFirstName($customer->firstname)
            ->setLastName($customer->lastname)
            ->setGenderCode((new \Gender())->name[$customer->id_gender])
            ->setEmailAddress($customer->email)
            ->setCellPhoneNumber($customerAdress->phone_mobile)
            ->setBirthDate($birthdate)
            ->setAddress($address);
             
        // $basketItem1 = (new BasketItem()) @TODO ADD BASKET ITEMS
        //     ->setItemName('Item basket 1')
        //     ->setQuantity(2)
        //     ->setUnitPrice(45.0);
        
        // $basketItem2 = (new BasketItem())
        //     ->setItemName('Item basket 2')
        //     ->setQuantity(1)
        //     ->setUnitPrice(33.0);
        
        $basket = (new Basket())
            ->setBasketAmount($totalAmount)
            ->setItems([]);
            
        $merchantUrls = (new MerchantUrls())
            ->setOnApplicationFailedRedirectUrl('on-application-failed-redirect-url.com')
            ->setOnApplicationSucceededRedirectUrl('on-application-succeeded-redirect-url.com')
            ->setOnCanceledWebhookUrl('on-canceled-webhook-url.com')
            ->setOnWithdrawnWebhookUrl('on-withdrawn-webhook-url.com');
            
        $merchantOrderContext = (new MerchantOrderContext())
            ->setChannel('test')
            ->setShopCode('TEST')
            ->setMerchantReference('MerchantReference')
            ->setAgentEmailAddress('merchant@mail.com');
            
        $body = (new InitializeContract())
            ->setPersonalInformation($personalInformation)
            ->setBasket($basket)
            ->setMerchantUrls($merchantUrls)
            ->setMerchantOrderContext($merchantOrderContext);
        
        $isProductionMode = (bool) \Configuration::get(
            Younitedpay::PRODUCTION_MODE,
            null,
            null,
            $this->context->shop->id,
            false
        );

        // if ($isProductionMode === false) {
        //     $request = (new InitializeContractRequest())
        //     ->enableSanbox()
        //     ->setModel($body);
        // } else {
        //     $request = (new InitializeContractRequest())
        //     ->setModel($body);
        // }

        $request = new InitializeContractRequest();

        return $client->sendRequest($body, $request);
    }

    public function addLog($msg, $objectModel = null, $objectId = null, $name = null, $level = 'info')
    {
        $this->logger->openLogger();
        $this->logger->addLog($msg, $objectModel, $objectId, $name, $level);
        $this->logger->closeLogger();
    }
}
