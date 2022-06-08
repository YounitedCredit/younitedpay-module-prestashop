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

namespace YounitedpayAddon\Service;

use YounitedpayAddon\Logger\ApiLogger;
use YounitedpayAddon\Repository\PaymentRepository;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class LoggerService
{
    public $module;

    /** @var ProcessLoggerHandler */
    protected $logger;

    /** @var PaymentRepository */
    protected $repository;

    /** @var string */
    protected $cellPhone;

    /** @var string */
    protected $errorMessage;

    public function __construct(ProcessLoggerHandler $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Add log to the module logger
     *
     * @param string $msg Message to log
     * @param string $objectModel object Model concerned (null default)
     * @param string $objectId object Model Id (null default)
     * @param string $name Name of message (null default)
     * @param string $level Level of log ('info' default)
     */
    public function addLog($msg, $name = null, $level = 'info', $class = null, $objectModel = null, $objectId = null)
    {
        $class = null === $class ? $this : $class;
        $this->logger->openLogger();
        $this->logger->addLog($msg, $objectModel, $objectId, $name, $level);
        $this->logger->closeLogger();
        $this->addLogAPI($msg, $level, $class);
    }

    /**
     * Add Api file log (if boolean in module to true 'IS_API_FILE_LOGGER')
     */
    public function addLogAPI($msg, $type = 'Error', $class = null)
    {
        $class = null === $class ? $this : str_replace('ModuleFrontController', '', $class);
        /** @var ApiLogger $apiLogger */
        $apiLogger = ApiLogger::getInstance();
        $apiLogger->log($class, $msg, $type, false);
    }
}
