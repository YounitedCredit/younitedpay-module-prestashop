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

namespace YounitedpayAddon;

class MockProxy
{
    protected static $mock;

    /**
     * Set static expectations
     *
     * @param mixed $mock
     */
    public static function setStaticExpectations($mock)
    {
        static::$mock = $mock;
    }

    /**
     * Any static calls we get are passed along to self::$mock. public static
     *
     * @param string $name
     * @param mixed $args
     *
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        return call_user_func_array(
            [static::$mock, $name],
            $args
        );
    }
}

class StockAvailable extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Context extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Db extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Configuration extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Tools extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Category extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;

    public $id = null;
}

class Group extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Manufacturer extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Combination extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Shop extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Feature extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class FeatureValue extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}

class Module extends MockProxy
{
    // Redeclare to use this instead MockProxy::mock
    protected static $mock;
}
