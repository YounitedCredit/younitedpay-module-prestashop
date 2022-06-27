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

use YounitedpayAddon\Service\LoggerService;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Extensions\ProcessLogger\Controllers\Admin\AdminProcessLoggerController;

require_once _PS_MODULE_DIR_ . 'younitedpay/vendor/autoload.php';

class AdminYounitedpayProcessLoggerController extends AdminProcessLoggerController
{
    private $logPath;

    public function __construct()
    {
        parent::__construct();

        $this->logPath = _PS_MODULE_DIR_ . $this->module->name . '/logs/';
    }

    public function initContent()
    {
        $this->checkFileLogger();

        parent::initContent();

        if (Tools::getValue('show_log_files') !== false) {
            $this->showLogFiles();
        }
    }

    private function showLogFiles()
    {
        $aMonths = scandir($this->logPath);
        $logsFilesFull = [];
        foreach ($aMonths as $aMonth) {
            if ($aMonth !== '.' && $aMonth !== '..') {
                $logsFilesFull[$aMonth] = glob($this->logPath . $aMonth . '/*.log');
            }
        }
        $logsFiles = $logsFilesFull !== false ? $this->onlyFileNames($logsFilesFull) : [];
        $this->context->smarty->assign([
            'logs_files' => $logsFiles,
            'logs_url' => _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/younitedpay/logs/',
        ]);

        $content = $this->context->smarty->getTemplateVars('content');

        $content .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/logs.tpl'
        );

        $this->context->smarty->assign([
            'content' => $content,
        ]);
    }

    private function checkFileLogger()
    {
        if (Tools::getValue('changelogger') === false) {
            return;
        }

        $infoActivation = date('Y-m-d H:i:s') . ' by ';
        $infoActivation .= $this->context->employee->firstname . ' ' . $this->context->employee->lastname;
        $infoActivation .= ' (id ' . $this->context->employee->id . ')';

        $file = _PS_MODULE_DIR_ . $this->module->name . '/logs/loggeractivated.txt';
        if (Tools::getValue('changelogger') === 'disable') {
            /** @var LoggerService $loggerService */
            $loggerService = ServiceContainer::getInstance()->get(LoggerService::class);
            $loggerService->addLog(
                'Logger file disabled ' . $infoActivation,
                'file logger',
                'info',
                (new \ReflectionClass($this))->getShortName()
            );

            return unlink($file);
        }

        if (Tools::getValue('changelogger') === 'enable') {
            /** @var LoggerService $loggerService */
            $loggerService = ServiceContainer::getInstance()->get(LoggerService::class);
            $loggerService->addLog(
                'Logger file enabled ' . $infoActivation,
                'file logger',
                'info',
                (new \ReflectionClass($this))->getShortName()
            );
            file_put_contents($file, 'Activated on ' . $infoActivation);
        }
    }

    private function onlyFileNames($logsFilesFull)
    {
        $logsFiles = [];
        foreach ($logsFilesFull as $aMonth => $fileList) {
            foreach ($fileList as $aFile) {
                $filePath = explode('/', $aFile);
                $logsFiles[$aMonth][] = @end($filePath);
            }
        }

        return $logsFiles;
    }
}
