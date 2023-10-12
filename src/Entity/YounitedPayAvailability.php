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

namespace YounitedpayAddon\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}

use ObjectModel;

class YounitedPayAvailability extends ObjectModel
{
    /** @var int */
    public $id_younitedpay_configuration;

    /** @var int */
    public $id_shop;

    /** @var int */
    public $maturity;

    /** @var float */
    public $minimum;

    /** @var float */
    public $maximum;

    /** @var string */
    public $currency;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'younitedpay_configuration',
        'multilang' => false,
        'primary' => 'id_younitedpay_configuration',
        'fields' => [
            'id_shop' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'maturity' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'minimum' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isUnsignedFloat',
                'required' => true,
            ],
            'maximum' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isUnsignedFloat',
                'required' => true,
            ],
            'currency' => [
                'type' => self::TYPE_STRING,
                'required' => true,
            ],
        ],
    ];
}
