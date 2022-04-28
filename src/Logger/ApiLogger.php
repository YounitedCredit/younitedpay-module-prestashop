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

namespace YounitedpayAddon\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Younitedpay;

class ApiLogger
{
    public $logname;

    const MAX_LOG_FILE_SIZE = 2000000000;

    private static $instance = null;

    private $stream;

    public $module;

    /**
     * @var Logger
     */
    private $logger;

    final protected function __construct()
    {
        $this->module = \Module::getInstanceByName('younitedpay');
        $this->logname = $this->module->name . '-' . date('Ymd') . '.log';
        $this->build();
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    protected function build()
    {
        $logFile = _PS_MODULE_DIR_ . $this->module->name . '/logs/' . $this->logname;
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile));
        }
        if (file_exists($logFile)) {
            $fileSize = filesize($logFile);
            if ($fileSize > self::MAX_LOG_FILE_SIZE) {
                unlink($logFile);
            }
        }
        $this->stream = fopen($logFile, 'a+');
        $this->logger = new Logger($this->module->name, [new StreamHandler($this->stream)]);
    }

    public function log($object, $data, $type = 'Error', $isObject = false)
    {
        if (Younitedpay::IS_FILE_LOGGER_ACTIVE === false) {
            return true;
        }

        $logData = $data;
        if ($isObject === true) {
            $logData = json_encode($data);
        }

        if (substr($type, 0, 8) === 'Response') {
            $this->logger->addInfo($this->getClass($object) . ' - Response Data: ' . json_encode($data->getModel()));
        }

        $this->logger->addInfo($this->getClass($object) . ' - ' . $type . ' - Data: ' . $logData);
    }

    private function getClass($object)
    {
        return (new \ReflectionClass($object))->getShortName();
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __destruct()
    {
        fclose($this->stream);
    }
}
