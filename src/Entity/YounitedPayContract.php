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

class YounitedPayContract extends ObjectModel
{
    /** @var int */
    public $id_younitedpay_contract;

    /** @var int */
    public $id_cart;

    /** @var int */
    public $id_order;

    /** @var string */
    public $payment_id;

    /** @var string */
    public $id_external_younitedpay_contract;

    /** @var bool */
    public $is_confirmed;

    /** @var string */
    public $confirmation_date;

    /** @var bool */
    public $is_activated;

    /** @var string */
    public $activation_date;

    /** @var bool */
    public $is_canceled;

    /** @var string */
    public $canceled_date;

    /** @var bool */
    public $is_withdrawn;

    /** @var string */
    public $withdrawn_date;

    /** @var float */
    public $withdrawn_amount;

    /** @var int */
    public $api_version;

    /** @var string */
    public $date_add;

    /** @var string */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'younitedpay_contract',
        'multilang' => false,
        'primary' => 'id_younitedpay_contract',
        'fields' => [
            'id_cart' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'id_order' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false,
            ],
            'payment_id' => [
                'type' => self::TYPE_STRING,
                'required' => false,
            ],
            'id_external_younitedpay_contract' => [
                'type' => self::TYPE_STRING,
                'required' => false,
            ],
            'is_confirmed' => [
                'type' => self::TYPE_BOOL,
                'required' => false,
            ],
            'confirmation_date' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
            'is_activated' => [
                'type' => self::TYPE_BOOL,
                'required' => false,
            ],
            'activation_date' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
            'is_canceled' => [
                'type' => self::TYPE_BOOL,
                'required' => false,
            ],
            'canceled_date' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
            'is_withdrawn' => [
                'type' => self::TYPE_BOOL,
                'required' => false,
            ],
            'withdrawn_date' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
            'withdrawn_amount' => [
                'type' => self::TYPE_FLOAT,
                'copy_post' => false,
            ],
            'api_version' => [
                'type' => self::TYPE_STRING,
                'required' => false,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
        ],
    ];
}
