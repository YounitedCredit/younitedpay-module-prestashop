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
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'younitedpay/vendor/autoload.php';

use YounitedpayAddon\Entity\YounitedPayAvailability;
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerExtension;
use YounitedpayAddon\Hook\HookDispatcher;
use YounitedpayAddon\Utils\PaymentModuleTrait;
use YounitedpayAddon\Utils\ModuleInitialiser;

class Younitedpay extends PaymentModule
{
    use PaymentModuleTrait {
        PaymentModuleTrait::__construct as private __pmConstruct;
        PaymentModuleTrait::install as private pmInstall;
        PaymentModuleTrait::uninstall as private pmUninstall;
    }

    /** @var string This module requires at least PHP version */
    public $php_version_required = '5.6';

    public $context;

    /**
     * List of ModuleFrontController used in this Module
     * Module::install() register it, after that you can edit it in BO (for rewrite if needed)
     *
     * @var array
     */
    public $controllers = [
    ];

    /**
     * List of objectModel used in this Module
     *
     * @var array
     */
    public $objectModels = [
        YounitedPayContract::class,
        YounitedPayAvailability::class,
    ];

    public $extensions = [
        ProcessLoggerExtension::class,
    ];

    public $moduleAdminControllers = [
        [
            'name' => [
                'en' => 'Younited',
                'fr' => 'Younited',
            ],
            'class_name' => 'AdminYounitedParentMain',
            'parent_class_name' => 'CONFIGURE',
            'visible' => false,
        ],
        [
            'name' => [
                'en' => 'Younited',
                'fr' => 'Younited',
            ],
            'class_name' => 'AdminYounitedParent',
            'parent_class_name' => 'AdminYounitedParentMain',
            'visible' => false,
        ],
        [
            'name' => [
                'en' => 'Configuration',
                'fr' => 'Configuration',
            ],
            'class_name' => 'AdminYounitedpayConfiguration',
            'parent_class_name' => 'AdminYounitedParent',
            'visible' => true,
        ],
        [
            'name' => [
                'en' => 'Logs',
                'fr' => 'Logs',
            ],
            'class_name' => 'AdminYounitedpayProcessLogger',
            'parent_class_name' => 'AdminYounitedParent',
            'visible' => true,
        ],
        [
            'name' => [
                'en' => 'FAQ',
                'fr' => 'FAQ',
            ],
            'class_name' => 'AdminYounitedpayHelp',
            'parent_class_name' => 'AdminYounitedParent',
            'visible' => true,
        ],
    ];

    const CLIENT_SECRET = 'YOUNITED_CLIENT_SECRET';

    const CLIENT_ID = 'YOUNITED_CLIENT_ID';

    const PRODUCTION_MODE = 'YOUNITED_PRODUCTION_MODE';

    const OAUTH_TOKEN = 'YOUNITED_OAUTH_TOKEN';

    const ORDER_STATE_DELIVERED = 'YOUNITED_ORDER_STATE_DELIVERED';

    const WEBHOOK_SECRET = 'YOUNITED_WEBHOOK_SECRET';

    const IS_FILE_LOGGER_ACTIVE = false;

    const PREFERRED_ISO_CODE = 'FR';

    const AVAILABLE_CURRENCIES = [
        'EUR'
    ];

    public function __construct()
    {
        $this->module_key = '';
        $this->name = 'younitedpay';
        $this->version = '@version@';
        $this->author = '202 ecommerce';
        $this->tab = 'payments_gateways';
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_,
        ];
        $this->need_instance = true;

        $this->__pmConstruct();

        $this->secure_key = Tools::encrypt($this->name);
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->displayName = $this->l('Younited Pay');
        $this->description = $this->l('Easily add direct deposit payment for your customers.');
        $this->hookDispatcher = new HookDispatcher($this);
        $this->hooks = array_merge($this->hooks, $this->hookDispatcher->getAvailableHooks());
    }

    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminYounitedpayConfiguration'));
    }

    public function isUsingNewTranslationSystem()
    {
        return false;
    }

    public function install()
    {
        $result = $this->pmInstall();
        $this->registerOrderStates();

        $orderDelivered = Configuration::get('PS_OS_DELIVERED');

        Configuration::updateGlobalValue(self::ORDER_STATE_DELIVERED, $orderDelivered);

        $bridgeInitialiser = new ModuleInitialiser();
        $bridgeInitialiser->addIndexes();

        return $result;
    }

    public function uninstall()
    {
        return Module::uninstall();
    }

    public function addRadioCurrencyRestrictionsForModule(array $shops = [])
    {
        if (!$shops) {
            $shops = Shop::getShops(true, null, true);
        }

        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'module_currency` (`id_module`, `id_shop`, `id_currency`) VALUES (%d, %d, %d)';

        $currencies = array_map(function ($currencyIso) {
            return (int) Currency::getIdByIsoCode($currencyIso);
        }, self::AVAILABLE_CURRENCIES);

        $currencies = array_filter($currencies, function ($idCurrency) {
            return $idCurrency > 0;
        });

        foreach ($shops as $idShop) {
            if (!Db::getInstance()->execute(sprintf($query, $this->id, $idShop, $currencies))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Handle Hooks loaded on extension
     *
     * @param string $name Hook name
     * @param array $arguments Hook arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($result = $this->handleExtensionsHook($name,
            !empty($arguments[0]) ? $arguments[0] : [])
        ) {
            if (!is_null($result)) {
                return $result;
            }
        }
    }
}
