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
use YounitedpayAddon\Entity\YounitedPayAvailability;

class ConfigRepository
{
    /**
     * Get all Maturities saved in configuration
     *
     * @return array
     */
    public function getAllMaturities($productPrice = -1)
    {
        $query = new \DbQuery();
        $query->select('*')
            ->from(YounitedPayAvailability::$definition['table'])
            ->OrderBy('maturity ASC');

        if ($productPrice > 0) {
            $query->where((int) pSQL($productPrice) . ' >= minimum');
            $query->where('maximum >= ' . (int) pSQL($productPrice));
        }

        $result = Db::getInstance()->executeS($query);

        return $result === false || $result === null ? [] : $result;
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
