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
            ->where('id_shop = ' . \Context::getContext()->shop->id)
            ->OrderBy('maturity ASC');

        if ($productPrice > 0) {
            $query->where((int) $productPrice . ' >= minimum');
            $query->where('( maximum >= ' . (int) $productPrice . ' or maximum = 0 )');
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

    /**
     * Check if WhiteList enabled and IP In Whitelist
     * The payment options / credit on pages do not display if not in the IP whitelist (if enabled)
     *
     * @return bool true if whitelist disabled, false if whitelist enabled and IP not in the list
     */
    public function checkIPWhitelist()
    {
        $ipClient = \Tools::getRemoteAddr();
        $idShop = \Context::getContext()->shop->id;
        $IpWhiteListOn = (bool) \Configuration::get(\Younitedpay::IP_WHITELIST_ENABLED, null, null, $idShop);
        if ($IpWhiteListOn !== true) {
            return true;
        }
        try {
            $ipWhitelisted = explode(',', \Configuration::get(\Younitedpay::IP_WHITELIST_CONTENT, null, null, $idShop));
        } catch (\Exception $ex) {
            $ipWhitelisted = [];
        }

        if (is_array($ipWhitelisted) === false) {
            return true;
        }

        return in_array($ipClient, $ipWhitelisted);
    }
}
