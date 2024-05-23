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

namespace YounitedpayAddon\Repository;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Db;
use DbQuery;
use YounitedpayAddon\Entity\YounitedPayContract;

class PaymentRepository
{
    /**
     * Get Contract linked to the cart
     *
     * @param int $idCart Id of cart concerned
     *
     * @return YounitedPayContract
     */
    public function getContractByCart($idCart)
    {
        $query = new DbQuery();
        $query->select('id_younitedpay_contract')
            ->from(YounitedPayContract::$definition['table'])
            ->where('id_cart = ' . (int) $idCart);

        $result = Db::getInstance()->getRow($query);

        $idYounitedContract = $result === false || $result === null || empty($result['id_younitedpay_contract'])
            ? 0
            : (int) $result['id_younitedpay_contract'];

        return new YounitedPayContract($idYounitedContract);
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
        $query = new DbQuery();
        $query->select('id_younitedpay_contract')
            ->from(YounitedPayContract::$definition['table'])
            ->where('id_order = ' . (int) pSQL($idOrder));

        $result = Db::getInstance()->getRow($query);

        $idYounitedContract = $result === false || $result === null || empty($result['id_younitedpay_contract'])
            ? 0
            : (int) $result['id_younitedpay_contract'];

        return new YounitedPayContract($idYounitedContract);
    }

    /**
     * Modify the entity to confirm contract on the YounitedContract table
     * Update the id Order linked to the cart concerned
     *
     * @param int $idCart Id Of Cart concerned for confirmation
     * @param int $idOrder Id Of Order concerned for confirmation
     *
     * @return bool Operation done / fail
     */
    public function confirmContract($idCart, $idOrder)
    {
        return Db::getInstance()->update(
            YounitedPayContract::$definition['table'],
            [
                'id_order' => (int) $idOrder,
                'is_confirmed' => true,
                'confirmation_date' => date('Y-m-d H:i:s'),
                'is_canceled' => false,
                'is_activated' => false,
                'is_withdrawn' => false,
            ],
            'id_cart = ' . (int) $idCart
        );
    }

    /**
     * Modify the entity to activate contract on the YounitedContract table
     *
     * @param int $idOrder Id Of Order concerned for activation
     *
     * @return bool Operation done / fail
     */
    public function activateContract($idOrder)
    {
        return Db::getInstance()->update(
            YounitedPayContract::$definition['table'],
            [
                'is_activated' => true,
                'activation_date' => date('Y-m-d H:i:s'),
                'is_canceled' => false,
                'is_withdrawn' => false,
                'is_confirmed' => false,
            ],
            'id_order = ' . (int) $idOrder
        );
    }

    /**
     * Modify the entity to cancel contract on the YounitedContract table
     *
     * @param int $idOrder Id Of Order concerned for cancelation
     *
     * @return bool Operation done / fail
     */
    public function cancelContract($idOrder)
    {
        return Db::getInstance()->update(
            YounitedPayContract::$definition['table'],
            [
                'is_canceled' => true,
                'canceled_date' => date('Y-m-d H:i:s'),
                'is_activated' => false,
                'is_confirmed' => false,
                'is_withdrawn' => false,
            ],
            'id_order = ' . (int) $idOrder
        );
    }

    /**
     * Modify the entity to withdraw contract on the YounitedContract table
     *
     * @param int $idOrder Id Of Order concerned for cancelation
     *
     * @return bool Operation done / fail
     */
    public function withdrawnContract($idOrder)
    {
        return Db::getInstance()->update(
            YounitedPayContract::$definition['table'],
            [
                'is_withdrawn' => true,
                'withdrawn_date' => date('Y-m-d H:i:s'),
                'is_activated' => false,
                'is_confirmed' => false,
                'is_canceled' => false,
            ],
            'id_order = ' . (int) $idOrder
        );
    }

    /**
     * Update the withdrawn amount asked by the SHop
     * Warning : not confirmed by WebHook at this time, the state don't move
     *
     * @param int $idOrder Id Of Order concerned for cancelation
     *
     * @return bool Operation done / fail
     */
    public function setWithdrawnAmount($idOrder, $withdrawnAmount)
    {
        return Db::getInstance()->update(
            YounitedPayContract::$definition['table'],
            [
                'withdrawn_amount' => (float) $withdrawnAmount,
            ],
            'id_order = ' . (int) $idOrder
        );
    }
}
