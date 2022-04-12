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

namespace YounitedpayAddon\API\Request;

use YounitedpayAddon\YounitedTests;
use YounitedPaySDK\Response\BestPriceResponse;

require_once __DIR__ . '../../../../bootstrap.php';

class GetBestPriceRequestTest extends YounitedTests
{
    public function testGetBestPriceRequestTest()
    {
        $response = $this->client->getBestPrice(149);
        $this->assertNotNull($response);
        $this->assertNotEmpty($response);
        $this->assertTrue($response['success']) || $this->assertNotTrue($response['success']);
    }
}
