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

use YounitedpayAddon\API\YounitedClient;
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Utils\CacheYounited;
use YounitedpayClasslib\Install\ModuleInstaller;
use YounitedPaySDK\Request\NewAPI\GetMerchantRequest;

/**
 * @param YounitedPay $module
 *
 * @return bool
 *
 * @throws PrestaShopException
 */
function upgrade_module_2_2_0($module)
{
    $result = true;

    $cacheStorage = new CacheYounited();
    $cacheStorage->remove('token_api');

    $shopIds = Shop::getShops(true, null, true);
    foreach ($shopIds as $shopId) {
        $clientID = Configuration::get(Younitedpay::CLIENT_ID, null, null, $shopId, 'client_id', '');
        $clientIDProd = Configuration::get(Younitedpay::CLIENT_ID_PRODUCTION, null, null, $shopId, 'client_id', '');
        $clientSecret = Configuration::get(Younitedpay::CLIENT_SECRET, null, null, $shopId, 'client_secret', '');
        $clientSecretProd = Configuration::get(Younitedpay::CLIENT_SECRET_PRODUCTION, null, null, $shopId, 'client_secret', '');
        $shopCode = Configuration::get(Younitedpay::SHOP_CODE, null, null, $shopId, 'shop_code', '');
        $shopCodeProd = Configuration::get(Younitedpay::SHOP_CODE_PRODUCTION, null, null, $shopId, 'shop_code_production', '');
        $webHookSecret = Configuration::get(Younitedpay::WEBHOOK_SECRET, null, null, $shopId, 'webhook_secret', '');
        $webHookSecretProd = Configuration::get(Younitedpay::WEBHOOK_SECRET_PRODUCTION, null, null, $shopId, 'webhook_secret', '');
        $isProduction = Configuration::get(Younitedpay::PRODUCTION_MODE, null, null, $shopId, 'production_mode', false);

        $client = new YounitedClient($shopId);
        $request = new GetMerchantRequest();
        $response = $client->sendRequest('', $request);

        foreach (Younitedpay::AVAILABLE_COUNTRIES as $availableCountry) {
            if (empty($response) === true || $response['success'] === false || $availableCountry !== $response['response']['countryLabel']) {
                continue;
            }

            $result
                &= Configuration::updateValue(Younitedpay::CLIENT_ID . '_' . $availableCountry, $clientID, false, null, $shopId)
                && Configuration::updateValue(Younitedpay::CLIENT_ID_PRODUCTION . '_' . $availableCountry, $clientIDProd, false, null, $shopId)
                && Configuration::updateValue(Younitedpay::CLIENT_SECRET . '_' . $availableCountry, $clientSecret, false, null, $shopId)
                && Configuration::updateValue(Younitedpay::CLIENT_SECRET_PRODUCTION . '_' . $availableCountry, $clientSecretProd, false, null, $shopId)
                && Configuration::updateValue(Younitedpay::SHOP_CODE . '_' . $availableCountry, $shopCode, false, null, $shopId)
                && Configuration::updateValue(Younitedpay::SHOP_CODE_PRODUCTION . '_' . $availableCountry, $shopCodeProd, false, null, $shopId)
                && Configuration::updateValue(Younitedpay::WEBHOOK_SECRET . '_' . $availableCountry, $webHookSecret, false, null, $shopId)
                && Configuration::updateValue(Younitedpay::WEBHOOK_SECRET_PRODUCTION . '_' . $availableCountry, $webHookSecretProd, false, null, $shopId)
                && Configuration::updateValue(Younitedpay::PRODUCTION_MODE . '_' . $availableCountry, $isProduction, false, null, $shopId);
        }
    }

    $installer = new ModuleInstaller($module);
    $result = $installer->installObjectModel(YounitedPayContract::class);

    return $result;
}
