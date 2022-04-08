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

use YounitedpayAddon\Entity\YounitedPayContract;
use Db;

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
}