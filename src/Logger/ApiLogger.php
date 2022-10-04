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

    private $isUnitTest;

    /**
     * @var Logger
     */
    private $logger;

    final protected function __construct($isUnitTest = false)
    {
        $this->module = \Module::getInstanceByName('younitedpay');
        $this->isUnitTest = $isUnitTest;
        $this->logname = $this->module->name . '-' . date('Ymd') . '.log';
        if ($isUnitTest === false) {
            $this->build();
        }
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
        $logDir = _PS_MODULE_DIR_ . $this->module->name . '/logs';
        if (is_dir($logDir) === false) {
            mkdir($logDir);
            copy(_PS_MODULE_DIR_ . $this->module->name . '/index.php', $logDir . '/index.php');
            copy(_PS_MODULE_DIR_ . $this->module->name . '/.htaccess', $logDir . '/.htaccess');
        }

        $logDir .= '/' . date('Ym');
        if (is_dir($logDir) === false) {
            mkdir($logDir);
            copy(_PS_MODULE_DIR_ . $this->module->name . '/index.php', $logDir . '/index.php');
            copy(_PS_MODULE_DIR_ . $this->module->name . '/.htaccess', $logDir . '/.htaccess');
        }

        $logFile = $logDir . '/' . $this->logname;
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
        if (\Configuration::get(Younitedpay::IS_FILE_LOGGER_ACTIVE) === false || $this->isUnitTest === true) {
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
        return str_replace('ModuleFrontController', '', (new \ReflectionClass($object))->getShortName());
    }

    public static function getInstance($isUnitTest = false)
    {
        if (empty(self::$instance)) {
            self::$instance = new static($isUnitTest);
        }

        return self::$instance;
    }

    public function __destruct()
    {
        if ($this->isUnitTest === false) {
            fclose($this->stream);
        }
    }
}
