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

namespace YounitedpayAddon\Repository;

use Db;
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
        $query = new \DbQuery();
        $query->select('id_younitedpay_contract')
            ->from(YounitedPayContract::$definition['table'])
            ->where('id_cart = ' . pSQL($idCart));

        $result = Db::getInstance()->getRow($query);

        $idYounitedContract = $result === false || $result === null || empty($result['id_younitedpay_contract'])
            ? 0
            : $result['id_younitedpay_contract'];
        
        return new YounitedPayContract($idYounitedContract);
    }

    public function saveAllMaturities($maturities, $idShop)
    {
        foreach ($maturities as $maturity) {
            $maturityEntity = new YounitedPayAvailability($maturity['id_younitedpay_configuration']);

            if ((bool) $maturity['deleted'] === true) {
                $maturityEntity->delete();
                continue;
            }

            foreach ($maturity as $key => $value) {
                if ($key !== 'deleted' && $key !== 'id_younitedpay_configuration') {
                    $maturityEntity->$key = $value;
                }
            }

            if (empty($maturityEntity->maximum) === true) {
                $maturityEntity->maximum = 0;
            }

            $maturityEntity->currency = 'EUR';
            $maturityEntity->id_shop = $idShop;

            $maturityEntity->save();
        }
    }
}
