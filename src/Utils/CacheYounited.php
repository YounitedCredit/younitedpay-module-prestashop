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

namespace YounitedpayAddon\Utils;

if (!defined('_PS_VERSION_')) {
    exit;
}

use YounitedpayClasslib\Utils\CacheStorage\CacheStorage;

class CacheYounited extends CacheStorage
{
    //region Fields

    /**
     * Expired in seconds
     *
     * @var int
     */
    protected $expiry = 3600; // 1h

    /**
     * Check cache is expired
     * Override of ClassLib for 1.7.8.6 bug on product change
     *
     * @param string|array $key
     *
     * @return bool
     */
    public function isExpired($key)
    {
        $cacheData = $this->get($key);

        if (isset($cacheData['expiry']) === false) {
            return true;
        }

        if (is_null($cacheData['expiry'])) {
            return false;
        }

        $currentDateTime = date('Y-m-d H:i:s');
        if ($cacheData['expiry'] < $currentDateTime) {
            return true;
        }

        return false;
    }
}
