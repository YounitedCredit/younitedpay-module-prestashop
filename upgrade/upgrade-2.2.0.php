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

    $shopIds = Shop::getShops(true, null, true);
    foreach ($shopIds as $shopId) {
        $clientID = getValue(Younitedpay::CLIENT_ID, $shopId, 'client_id', '');
        $clientIDProd = getValue(Younitedpay::CLIENT_ID_PRODUCTION, $shopId, 'client_id', '');
        $clientSecret = getValue(Younitedpay::CLIENT_SECRET, $shopId, 'client_secret', '');
        $clientSecretProd = getValue(Younitedpay::CLIENT_SECRET_PRODUCTION, $shopId, 'client_secret', '');
        $shopCode = getValue(Younitedpay::SHOP_CODE, $shopId, 'shop_code', '');
        $shopCodeProd = getValue(Younitedpay::SHOP_CODE_PRODUCTION, $shopId, 'shop_code_production', '');
        $webHookSecret = getValue(Younitedpay::WEBHOOK_SECRET, $shopId, 'webhook_secret', '');
        $webHookSecretProd = getValue(Younitedpay::WEBHOOK_SECRET_PRODUCTION, $shopId, 'webhook_secret', '');
        $isProduction = getValue(Younitedpay::PRODUCTION_MODE, $shopId, 'production_mode', false);

        $result
            &= Configuration::updateValue(Younitedpay::CLIENT_ID . '_FR', $clientID, false, null, $shopId)
            && Configuration::updateValue(Younitedpay::CLIENT_ID_PRODUCTION . '_FR', $clientIDProd, false, null, $shopId)
            && Configuration::updateValue(Younitedpay::CLIENT_SECRET . '_FR', $clientSecret, false, null, $shopId)
            && Configuration::updateValue(Younitedpay::CLIENT_SECRET_PRODUCTION . '_FR', $clientSecretProd, false, null, $shopId)
            && Configuration::updateValue(Younitedpay::SHOP_CODE . '_FR', $shopCode, false, null, $shopId)
            && Configuration::updateValue(Younitedpay::SHOP_CODE_PRODUCTION . '_FR', $shopCodeProd, false, null, $shopId)
            && Configuration::updateValue(Younitedpay::WEBHOOK_SECRET . '_FR', $webHookSecret, false, null, $shopId)
            && Configuration::updateValue(Younitedpay::WEBHOOK_SECRET_PRODUCTION . '_FR', $webHookSecretProd, false, null, $shopId)
            && Configuration::updateValue(Younitedpay::PRODUCTION_MODE . '_FR', $isProduction, false, null, $shopId);
    }

    return $result;
}

/**
 * @param string $key Configuration key to get
 * @param int $shopId Id Shop concerned
 * @param string $param Param return in form while saving
 * @param mixed $defValue Default value if nothing's found
 *
 * @return string|bool Value get by Configuration
 */
protected function getValue($key, $shopId, $param, $defValue)
{
    return Configuration::get(
        $key,
        null,
        null,
        $shopId,
        Tools::getValue($param, $defValue)
    );
}