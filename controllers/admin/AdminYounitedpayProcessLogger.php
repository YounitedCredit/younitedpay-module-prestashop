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
if (!defined('_PS_VERSION_')) {
    exit;
}

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

        $this->fields_options['processLogger']['fields'][Younitedpay::IS_FILE_LOGGER_ACTIVE] = [
            'title' => $this->module->l(
                'Activate Log files',
                'AdminProcessLoggerController'
            ),
            'hint' => $this->module->l(
                'Add all requests to log files',
                'AdminProcessLoggerController'
            ),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
        ];

        $this->logPath = _PS_MODULE_DIR_ . $this->module->name . '/logs/';
    }

    public function initContent()
    {
        parent::initContent();

        $idShop = \Context::getContext()->shop->id;
        $isLoggerFileActive = Configuration::get(Younitedpay::IS_FILE_LOGGER_ACTIVE, null, null, $idShop);

        if ($isLoggerFileActive !== false) {
            $this->showLogFiles();
        }
    }

    public function saveConfiguration()
    {
        $shops = \Shop::getShops(false, null, true);
        $shops[] = 0;
        $idShop = \Context::getContext()->shop->id;
        $loggerFileState = Configuration::get(Younitedpay::IS_FILE_LOGGER_ACTIVE, null, null, $idShop, '');
        $isLoggerActive = Tools::getValue(Younitedpay::IS_FILE_LOGGER_ACTIVE);

        if ($loggerFileState !== $isLoggerActive) {
            Configuration::updateValue(
                Younitedpay::IS_FILE_LOGGER_ACTIVE,
                (bool) $isLoggerActive,
                false,
                null,
                $idShop
            );

            $infoActivation = 'Logger file ';
            $infoActivation .= (bool) $isLoggerActive === true ? 'enabled ' : 'disabled ';
            $infoActivation .= date('Y-m-d H:i:s') . ' by ';
            $infoActivation .= $this->context->employee->firstname . ' ' . $this->context->employee->lastname;
            $infoActivation .= ' (id ' . $this->context->employee->id . ' on shop ' . $idShop . ')';

            /** @var LoggerService $loggerService */
            $loggerService = ServiceContainer::getInstance()->get(LoggerService::class);
            $loggerService->addLog(
                $infoActivation,
                'file logger',
                'info',
                (new \ReflectionClass($this))->getShortName()
            );
            $this->confirmations[] = $infoActivation;
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
            'logs_url' => $this->context->link->getAdminLink(
                'AdminYounitedpayProcessLogger',
                true
            ),
        ]);

        $content = $this->context->smarty->getTemplateVars('content');

        $fileName = Tools::getValue('display_file');
        if ($$fileName !== false) {
            $fileToDisplay = $this->logPath . $fileName;
            if ($this->checkSecurityFile($fileName) === true) {
                $this->context->smarty->assign([
                    'logfile_content' => Tools::file_get_contents($fileToDisplay),
                    'logfile_name' => $fileName,
                ]);
            }
        }

        $contentLogs = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/logs.tpl'
        );

        $this->context->smarty->assign([
            'content' => $content . $contentLogs,
        ]);
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

    private function checkSecurityFile($fileName)
    {
        if (strpos($fileName, '.log') === false) {
            $this->context->controller->errors[] = $this->module->l(
                'File extension other that log is forbidden.'
            );

            return false;
        }

        if (dirname($this->logPath . $fileName) !== $this->logPath) {
            $this->context->controller->errors[] = $this->module->l('Directory in log file is forbidden.');

            return false;
        }

        if (file_exists($this->logPath . $fileName) === false) {
            $this->context->controller->errors[] = $this->module->l('Log file do not exists.');

            return false;
        }

        return true;
    }
}
