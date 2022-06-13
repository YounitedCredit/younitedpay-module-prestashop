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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use YounitedpayAddon\Entity\YounitedPayAvailability;
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Hook\HookDispatcher;
use YounitedpayAddon\Service\ProductService;
use YounitedpayAddon\Utils\ModuleInitialiser;
use YounitedpayAddon\Utils\PaymentModuleTrait;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Extensions\ProcessLogger\ProcessLoggerExtension;

class Younitedpay extends PaymentModule implements WidgetInterface
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
                'en' => 'Younited Pay',
                'fr' => 'Younited Pay',
            ],
            'class_name' => 'AdminYounitedParentMain',
            'parent_class_name' => 'CONFIGURE',
            'visible' => false,
        ],
        [
            'name' => [
                'en' => 'Younited Pay',
                'fr' => 'Younited Pay',
            ],
            'class_name' => 'AdminYounitedParent',
            'parent_class_name' => 'AdminYounitedParentMain',
            'visible' => false,
        ],
        [
            'name' => [
                'en' => 'Settings',
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

    const CLIENT_SECRET = 'YOUNITEDPAY_CLIENT_SECRET';

    const CLIENT_ID = 'YOUNITEDPAY_CLIENT_ID';

    const CLIENT_SECRET_PRODUCTION = 'YOUNITEDPAY_CLIENT_SECRET_PRODUCTION';

    const CLIENT_ID_PRODUCTION = 'YOUNITEDPAY_CLIENT_ID_PRODUCTION';

    const PRODUCTION_MODE = 'YOUNITEDPAY_PRODUCTION_MODE';

    const OAUTH_TOKEN = 'YOUNITEDPAY_OAUTH_TOKEN';

    const ORDER_STATE_DELIVERED = 'YOUNITEDPAY_ORDER_STATE_DELIVERED';

    const WEBHOOK_SECRET = 'YOUNITEDPAY_WEBHOOK_SECRET';

    const WEBHOOK_SECRET_PRODUCTION = 'YOUNITEDPAY_WEBHOOK_SECRET_PRODUCTION';

    const FRONT_HOOK = 'YOUNITEDPAY_FRONT_HOOK';

    const IP_WHITELIST_CONTENT = 'YOUNITEDPAY_IP_WHITELIST_CONTENT';

    const IP_WHITELIST_ENABLED = 'YOUNITEDPAY_IP_WHITELIST_ENABLED';

    const SHOW_MONTHLY = 'YOUNITEDPAY_SHOW_MONTHLY';

    const IS_FILE_LOGGER_ACTIVE = true;

    const PREFERRED_ISO_CODE = 'FR';

    const AVAILABLE_CURRENCIES = [
        'EUR',
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
        $this->need_instance = 1;

        $this->__pmConstruct();

        $this->secure_key = Tools::encrypt($this->name);
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->displayName = $this->l('Younited Pay - Instant Credit Payment solutions');
        $this->description = $this->l('Enhance your customer experience with Instant Credit and boost your conversion.');
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

        Configuration::updateGlobalValue(self::ORDER_STATE_DELIVERED, json_encode([$orderDelivered]));
        Configuration::updateGlobalValue(self::FRONT_HOOK, 'disabled');

        $moduleInitialiser = new ModuleInitialiser();
        $moduleInitialiser->addIndexes();

        return $result;
    }

    public function uninstall()
    {
        return \Module::uninstall();
    }

    public function renderWidget($hookName, array $configuration)
    {
        $price = isset($configuration['amount']) ? (float) $configuration['amount'] : 0;

        if ($price === 0) {
            return '';
        }

        /** @var ProductService $productservice */
        $productservice = ServiceContainer::getInstance()->get(ProductService::class);

        if ($productservice->isWhiteListedIP() === false) {
            return '';
        }

        $templateCredit = $productservice->getBestPrice($price, 'widget');

        $context = $this->context;

        $frontModuleLink = $context->link->getModuleLink(
            $this->name,
            'younitedpayproduct'
        );

        $context->smarty->assign(
            [
                'younited_hook' => 'widget',
                'credit_template' => $templateCredit['template'],
                'product_url' => $frontModuleLink,
                'product_price' => $price,
            ]
        );

        return $context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/front/product_infos.tpl'
        );
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
    }

    public function addRadioCurrencyRestrictionsForModule(array $shops = [])
    {
        if (!$shops) {
            $shops = Shop::getShops(true, null, true);
        }

        $moduleInitialiser = new ModuleInitialiser();

        return $moduleInitialiser->addCurrencyRestrictions($shops, self::AVAILABLE_CURRENCIES, $this->id);
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
