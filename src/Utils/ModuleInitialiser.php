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

namespace YounitedpayAddon\Utils;

use Db;
use YounitedpayAddon\Entity\YounitedPayContract;

class ModuleInitialiser
{
    public function addIndexes()
    {
        $table = _DB_PREFIX_ . YounitedPayContract::$definition['table'];
        $actualIndexes = 'SHOW INDEX FROM `' . $table . '`;';
        $allIndexes = Db::getInstance()->executeS($actualIndexes);

        $indexesToAdd = [
            'id_external_younitedpay_contract',
            'id_order',
            'id_cart',
        ];

        $aSearchColumn = array_column($allIndexes, 'Column_name');
        if (empty($aSearchColumn) === true) {
            $aSearchColumn = array_column($allIndexes, 'column_name');
        }

        foreach ($indexesToAdd as $index) {
            if (array_search($index, $aSearchColumn) === false) {
                $sqlAddIndex = 'ALTER TABLE ' . $table . ' ADD INDEX (`' . $index . '`);';
                Db::getInstance()->execute($sqlAddIndex);
            }
        }
    }

    public function addCurrencyRestrictions($shops, $availableCurrencies, $idModule)
    {
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'module_currency` 
            (`id_module`, `id_shop`, `id_currency`) VALUES (%d, %d, %d)';

        $currencies = array_map(function ($currencyIso) {
            return (int) \Currency::getIdByIsoCode($currencyIso);
        }, $availableCurrencies);

        $currencies = array_filter($currencies, function ($idCurrency) {
            return $idCurrency > 0;
        });

        foreach ($shops as $idShop) {
            if (!Db::getInstance()->execute(sprintf($query, $idModule, $idShop, $currencies))) {
                return false;
            }
        }

        return true;
    }
}
