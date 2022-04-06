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

use YounitedpayAddon\API\YounitedClient;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use Mockery;
use PHPUnit\Framework\TestCase;

abstract class YounitedTests extends TestCase
{
    /** @var YounitedClient */
    protected $client;

    /** @var ProcessLoggerHandler */
    protected $processHandler;

    protected function setUp()
    {
        $this->processHandler = Mockery::mock(ProcessLoggerHandler::class);
        $this->processHandler->shouldReceive('openLogger')->andReturn(true);
        $this->processHandler->shouldReceive('closeLogger')->andReturn(true);
        $this->client = new YounitedClient(1, $this->processHandler, $this->testCredentials());
    }

    /**
     * You can create a file 'YounitedPayAuth.php' in this folder with your credentials and the array $younitedPayAuth
     * You just have to paste the code below and replace with your credentials to allow good tests
     */
    public function testCredentials()
    {
        $younitedPayAuth = [
            'client_id' => 'my-client-id',
            'client_secret' => 'my-client-secret',
            'production_mode' => false,
            'webhook_secret' => 'my-webhook-secret',
        ];
        
        $secretFile = __DIR__ . 'YounitedPayAuth.php';
        if (is_file($secretFile) === true) {
            require $secretFile;
        }

        return $younitedPayAuth;
    }
}
