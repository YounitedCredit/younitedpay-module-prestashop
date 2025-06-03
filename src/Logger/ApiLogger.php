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

namespace YounitedpayAddon\Logger;

if (!defined('_PS_VERSION_')) {
    exit;
}

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

    private $fileLoggerActivated = false;

    /**
     * @var Logger
     */
    private $logger;

    final protected function __construct($isUnitTest = false)
    {
        $this->module = \Module::getInstanceByName('younitedpay');
        $this->isUnitTest = $isUnitTest;
        $date = date('Ymd');
        $hashOfDay = md5(_COOKIE_KEY_ . $date);
        $this->logname = $this->module->name . '-' . $date . '-' . $hashOfDay . '.log';
        if ($isUnitTest === false) {
            $this->fileLoggerActivated = (bool) \Configuration::get(Younitedpay::IS_FILE_LOGGER_ACTIVE);
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
        $logDir = _PS_MODULE_DIR_ . $this->module->name . '/logs/' . date('Ym');
        if (is_dir($logDir) === false) {
            mkdir($logDir);
            copy(_PS_MODULE_DIR_ . $this->module->name . '/index.php', $logDir . '/index.php');
            copy(_PS_MODULE_DIR_ . $this->module->name . '/logs/.htaccess', $logDir . '/.htaccess');
        }

        $logFile = $logDir . '/' . $this->logname;
        if (file_exists($logFile)) {
            $fileSize = filesize($logFile);
            if ($fileSize > self::MAX_LOG_FILE_SIZE) {
                unlink($logFile);
            }
        }

        try {
            $this->deleteLogFilesOld();
        } catch (\Exception $ex) {
            $this->stream = fopen($logFile, 'a+');
            $this->logger = new Logger($this->module->name, [new StreamHandler($this->stream)]);
            $this->logger->addInfo('Error while deleting old logs ' . $ex->getMessage());
        }

        if ($this->fileLoggerActivated === false) {
            return true;
        }
        $this->stream = fopen($logFile, 'a+');
        $this->logger = new Logger($this->module->name, [new StreamHandler($this->stream)]);
    }

    public function log($object, $data, $type = 'Error', $isObject = false)
    {
        if ($this->fileLoggerActivated === false || $this->isUnitTest === true) {
            return true;
        }

        $logData = $data;
        if ($isObject === true) {
            $logData = json_encode($data);
        }

        if (substr($type, 0, 8) === 'Response') {
            if ($type === 'ResponseBestPriceRequest' && \Tools::getvalue('younitedfulllogs') === false) {
                $response = $data->getModel();
                $this->logger->addInfo($this->getClass($object) . ' - Response BestPrice count: ' . count($response));
            } else {
                $this->logger->addInfo($this->getClass($object) . ' - Response Data: ' . json_encode($data->getModel()));
            }
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
        if ($this->isUnitTest === false && $this->fileLoggerActivated !== false) {
            fclose($this->stream);
        }
    }

    /**
     * Delete log files older than three month
     */
    private function deleteLogFilesOld($deleteFromDays = 60)
    {
        $logDir = _PS_MODULE_DIR_ . $this->module->name . '/logs/';
        $previousLogDirs = scandir($logDir);
        $origin = new \DateTimeImmutable('now');
        foreach ($previousLogDirs as $oneLogFolder) {
            if (in_array($oneLogFolder, ['.', '..', date('Ym')]) === true || is_dir($logDir . $oneLogFolder) === false) {
                continue;
            }
            $filesWereDeleted = false;
            $logFiles = scandir($logDir . $oneLogFolder);
            foreach ($logFiles as $oneFileLog) {
                if (in_array($oneFileLog, ['.', '..', '.htaccess', 'index.php']) === true) {
                    continue;
                }
                $fileNameExploded = explode('-', str_replace('.log', '', $oneFileLog));
                $dateFile = \sprintf(
                    '%s-%s-%s',
                    substr($fileNameExploded[1], 0, 4),
                    substr($fileNameExploded[1], 4, 2),
                    substr($fileNameExploded[1], 6, 2)
                );
                if (count($fileNameExploded) !== 3 || \mb_strlen($fileNameExploded[1]) !== 8) {
                    continue;
                }
                $target = new \DateTimeImmutable($dateFile);
                $interval = $origin->diff($target);
                if ((int) $interval->format('%a') > $deleteFromDays) {
                    $filesWereDeleted = true;
                    @unlink($logDir . $oneLogFolder . '/' . $oneFileLog);
                }
            }
            if ($filesWereDeleted === false) {
                if (count($logFiles) <= 3) {
                    foreach ($logFiles as $oneFileLog) {
                        if (in_array($oneFileLog, ['.', '..']) === true) {
                            continue;
                        }
                        @unlink($logDir . $oneLogFolder . '/' . $oneFileLog);
                    }
                    @rmdir($logDir . $oneLogFolder);
                }
            }
        }
    }
}
