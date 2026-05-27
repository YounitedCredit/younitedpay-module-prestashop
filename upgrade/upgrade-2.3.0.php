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

use YounitedpayAddon\Entity\YounitedPayAvailability;
use YounitedpayAddon\Service\ConfigService;
use YounitedpayAddon\Utils\CacheYounited;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Install\ModuleInstaller;

/**
 * @param YounitedPay $module
 *
 * @return bool
 *
 * @throws PrestaShopException
 */
function upgrade_module_2_3_0($module)
{
    $result = true;

    $context = Context::getContext();

    $splitPaymentMaturities = [
        [
            'id_younitedpay_configuration' => 0,
            'id_shop' => $context->shop->id,
            'maturity' => 2,
            'minimum' => 0,
            'maximum' => 3000,
            'deleted' => 0,
            'currency' => 'EUR',
            'type' => YounitedPayAvailability::TYPE_SPLIT_PAYMENT,
        ],
        [
            'id_younitedpay_configuration' => 0,
            'id_shop' => $context->shop->id,
            'maturity' => 3,
            'minimum' => 0,
            'maximum' => 3000,
            'deleted' => 0,
            'currency' => 'EUR',
            'type' => YounitedPayAvailability::TYPE_SPLIT_PAYMENT,
        ],
        [
            'id_younitedpay_configuration' => 0,
            'id_shop' => $context->shop->id,
            'maturity' => 4,
            'minimum' => 0,
            'maximum' => 3000,
            'deleted' => 0,
            'currency' => 'EUR',
            'type' => YounitedPayAvailability::TYPE_SPLIT_PAYMENT,
        ],
    ];

    $configService = ServiceContainer::getInstance()->get(ConfigService::class);
    $configService->saveAllMaturities($splitPaymentMaturities, (int) $context->shop->id);

    $cacheStorage = new CacheYounited();
    $cacheStorage->remove('token_api');

    Configuration::updateValue(Younitedpay::NEED_TO_CLEAR_CACHE, true);
    Configuration::updateValue(Younitedpay::SHOW_LOAN_PAYMENT, true);
    Configuration::updateValue(Younitedpay::SHOW_SPLIT_PAYMENT, false);

    $installer = new ModuleInstaller($module);

    return $result && $installer->installObjectModel(YounitedPayAvailability::class);
}
