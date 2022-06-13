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

use YounitedpayAddon\Service\ConfigService;
use YounitedpayAddon\Utils\CacheYounited;
use YounitedpayAddon\Utils\ServiceContainer;

class AdminYounitedpayConfigurationController extends ModuleAdminController
{
    /** @var \Younitedpay Instance of your module automatically set by ModuleAdminController */
    public $module;

    /** @var \Context */
    public $context;

    /** @var string Associated object class name */
    public $className = 'Configuration';

    /** @var string Associated table name */
    public $table = 'configuration';

    /** @var bool Is bootstrap enabled */
    public $bootstrap = false;

    /** @var string */
    public $clientID;

    /** @var string */
    public $clientSecret;

    /** @var string */
    public $clientIDProduction;

    /** @var string */
    public $clientSecretProduction;

    /** @var string */
    public $webHookSecret;

    /** @var string */
    public $webHookSecretProduction;

    /** @var bool */
    public $isProductionMode;

    /** @var string */
    public $whitelistIP;

    /** @var bool */
    public $isWhiteListOn;

    /** @var bool */
    public $isShownMonthly;

    /** @var ConfigService */
    public $configService;

    /** @var array */
    public $maturitylist;

    /**
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        // Remove the help icon of the toolbar which no useful for us
        $this->context->smarty->clearAssign('help_link');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/admin.js');
        $this->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/admin.css');
    }

    public function initContent()
    {
        $langParameter = Tools::getValue('lang', null);
        $prevLanguage = $this->context->language;
        if ($langParameter !== null) {
            try {
                $choosedLanguage = new Language(Language::getIdByIso($langParameter));
                $this->context->language = $choosedLanguage;
            } catch (Exception $ex) {
            }
        }

        $cookieSave = Context::getContext()->cookie->__get('younitedpaysave');
        if ($cookieSave == 'ok') {
            $this->confirmations[] = $this->module->l('Successful update.');
        }
        Context::getContext()->cookie->__unset('younitedpaysave');

        /* @var ConfigService $configService */
        $this->configService = ServiceContainer::getInstance()->get(ConfigService::class);

        $this->content .= $this->renderConfiguration();
        parent::initContent();
        $this->context->language = $prevLanguage;
    }

    public function initVarContent()
    {
        $idShop = $this->context->shop->id;

        $productionMode = Younitedpay::PRODUCTION_MODE;
        $ipWhiteList = Younitedpay::IP_WHITELIST_ENABLED;

        $this->clientID = $this->getValue(Younitedpay::CLIENT_ID, $idShop, 'client_id', '');
        $this->clientIDProduction = $this->getValue(Younitedpay::CLIENT_ID_PRODUCTION, $idShop, 'client_id', '');
        $this->clientSecret = $this->getValue(Younitedpay::CLIENT_SECRET, $idShop, 'client_secret', '');
        $this->clientSecretProduction = $this->getValue(
            Younitedpay::CLIENT_SECRET_PRODUCTION,
            $idShop,
            'client_secret',
            ''
        );
        $this->webHookSecret = $this->getValue(Younitedpay::WEBHOOK_SECRET, $idShop, 'webhook_secret', '');
        $this->webHookSecretProduction = $this->getValue(
            Younitedpay::WEBHOOK_SECRET_PRODUCTION,
            $idShop,
            'webhook_secret',
            ''
        );
        $this->whitelistIP = $this->getValue(Younitedpay::IP_WHITELIST_CONTENT, $idShop, 'whitelist_ip', '');
        $this->isProductionMode = (bool) $this->getValue($productionMode, $idShop, 'production_mode', false);
        $this->isWhiteListOn = (bool) $this->getValue($ipWhiteList, $idShop, 'whitelist_on', false);
        $this->isShownMonthly = (bool) $this->getValue(Younitedpay::SHOW_MONTHLY, $idShop, 'show_monthly', false);
    }

    /**
     * @param string $key Configuration key to get
     * @param int $idShop Id Shop concerned
     * @param string $param Param return in form while saving
     * @param string $defValue Default value if nothing's found
     *
     * @return string|bool Value get by Configuration
     */
    protected function getValue($key, $idShop, $param, $defValue)
    {
        return Configuration::get(
            $key,
            null,
            null,
            $idShop,
            Tools::getValue($param, $defValue)
        );
    }

    protected function renderConfiguration()
    {
        $this->initVarContent();
        $tplFile = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/configuration/layout-configuration.tpl';

        if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
            $tplFile = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/configuration/multishop-and-not-selected.tpl';
            $tplVars = [
                'younitedpay_imgfile' => $this->getImgNotSelected(),
            ];
        } else {
            $urlWebhook = Context::getContext()->link->getModuleLink(
                $this->module->name,
                'webhook',
                [
                    'secure_key' => $this->module->secure_key,
                ]
            );

            $specsVariables = $this->configService->checkSpecifications($this->isProductionMode);
            $this->maturitylist = $specsVariables['maturityList'];

            /** @var CacheYounited $cachestorage */
            $cachestorage = new CacheYounited();
            $cachestorage->set('maturitylist', $this->maturitylist);

            $tplVars = [
                'configuration' => $this->getConfigurationVariables(),
                'connected' => $specsVariables['connected'],
                'config_check' => $specsVariables['specs'],
                'webhook_url_text' => $urlWebhook,
                'webhook_url' => $urlWebhook,
                'shop_img_url' => __PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/img/',
                'no_keys_text' => $this->l('Please enter your API credentials before changing the module’s settings'),
            ];

            $alertHere = empty($this->confirmations) && empty($this->errors);

            $tplVars['configuration']['alert'] = $alertHere !== true;
        }

        $tpl = Context::getContext()->smarty->createTemplate($tplFile);
        $tpl->assign($tplVars);

        return $tpl->fetch();
    }

    protected function getImgNotSelected()
    {
        $isoCode = strtolower($this->context->getContext()->language->iso_code);
        $fileImg = '/multishop-not-selected-' . $isoCode . '.jpg';
        if (is_file(_PS_MODULE_DIR_ . $this->module->name . '/' . $fileImg) === false) {
            $isoCode = 'en';
        }

        return __PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/img/' . $fileImg;
    }

    protected function getDefaultMaturities()
    {
        return [
            [
                'id_younitedpay_configuration' => 0,
                'id_shop' => $this->context->shop->id,
                'maturity' => 3,
                'minimum' => 1,
                'maximum' => 0,
                'currency' => 'EUR',
            ],
            [
                'id_younitedpay_configuration' => 0,
                'id_shop' => $this->context->shop->id,
                'maturity' => 10,
                'minimum' => 1,
                'maximum' => 9000,
                'currency' => 'EUR',
            ],
        ];
    }

    protected function getAllMaturities()
    {
        $allMaturities = $this->configService->getAllMaturities();

        return empty($allMaturities) === false ? $allMaturities : $this->getDefaultMaturities();
    }

    public function postProcess()
    {
        $idShop = $this->context->shop->id;
        $isSubmitted = false;
        $isCacheFlushNeeded = true;

        if (Tools::isSubmit('account_submit')) {
            $this->postAccountSubmit($idShop);
            $isSubmitted = true;
            $isCacheFlushNeeded = false;
        } elseif (Tools::isSubmit('flush_cache_submmit')) {
            $isSubmitted = true;
        } elseif (Tools::isSubmit('states_submit')) {
            $this->postStateSubmit($idShop);
            $isSubmitted = true;
        } elseif (Tools::isSubmit('appearance_submit')) {
            $this->postAppearance($idShop);
            $isSubmitted = true;
        } elseif (Tools::isSubmit('younitedpay_add_maturity')) {
            $this->ajaxDie($this->postAddNewMaturity($idShop));

            return;
        }

        if ($isCacheFlushNeeded) {
            $this->deleteAllCache();
        }

        if ($isSubmitted) {
            if (empty($this->_errors) === true) {
                Context::getContext()->cookie->__set('younitedpaysave', 'ok');
            } else {
                Context::getContext()->cookie->__set('younitedpaysave', 'error');
            }
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminYounitedpayConfiguration'));
        }
    }

    protected function deleteAllCache()
    {
        /** @var CacheYounited $cachestorage */
        $cachestorage = new CacheYounited();

        return $cachestorage->cleanCacheDirectory();
    }

    protected function postAddNewMaturity($idShop)
    {
        /** @var CacheYounited $cachestorage */
        $cachestorage = new CacheYounited();
        $cacheExists = $cachestorage->exist('maturitylist');

        if ($cacheExists === false || $cachestorage->isExpired('maturitylist') === true) {
            $this->maturitylist = [1, 3, 5, 10];
        } else {
            $cacheInformations = $cachestorage->get('maturitylist');
            $this->maturitylist = $cacheInformations['content'];
        }

        $this->context->smarty->assign([
            'key' => Tools::getValue('younitedpay_maturities', 0),
            'maturitylist' => $this->maturitylist,
            'maturity' => [
                'id_younitedpay_configuration' => 0,
                'id_shop' => $idShop,
                'maturity' => 3,
                'minimum' => 1,
                'maximum' => 0,
                'currency' => 'EUR',
            ],
        ]);

        $template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/configuration/maturity.tpl';

        return $this->context->smarty->fetch($template);
    }

    protected function postAppearance($idShop)
    {
        $frontHook = Tools::getValue('front_hook');
        $isShownMonthly = Tools::getValue('show_monthly');
        Configuration::updateValue(Younitedpay::FRONT_HOOK, $frontHook, false, null, $idShop);
        Configuration::updateValue(Younitedpay::SHOW_MONTHLY, $isShownMonthly, false, null, $idShop);
    }

    protected function postAccountSubmit($idShop)
    {
        $clientID = Tools::getValue('client_id');
        $clientSecret = Tools::getValue('client_secret');
        $webHookSecret = Tools::getValue('webhook_secret');
        $clientIDProd = Tools::getValue('client_id_production');
        $clientSecretProd = Tools::getValue('client_secret_production');
        $webHookSecretProd = Tools::getValue('webhook_secret_production');
        $ipWhiteList = Tools::getValue('whitelist_ip');
        $isWhiteListOn = Tools::getValue('whitelist_on');
        $isProduction = Tools::getValue('production_mode');
        Configuration::updateValue(Younitedpay::CLIENT_ID, $clientID, false, null, $idShop);
        Configuration::updateValue(Younitedpay::CLIENT_SECRET, $clientSecret, false, null, $idShop);
        Configuration::updateValue(Younitedpay::WEBHOOK_SECRET, $webHookSecret, false, null, $idShop);
        Configuration::updateValue(Younitedpay::CLIENT_ID_PRODUCTION, $clientIDProd, false, null, $idShop);
        Configuration::updateValue(Younitedpay::CLIENT_SECRET_PRODUCTION, $clientSecretProd, false, null, $idShop);
        Configuration::updateValue(Younitedpay::WEBHOOK_SECRET_PRODUCTION, $webHookSecretProd, false, null, $idShop);
        Configuration::updateValue(Younitedpay::IP_WHITELIST_CONTENT, $ipWhiteList, false, null, $idShop);
        Configuration::updateValue(Younitedpay::IP_WHITELIST_ENABLED, $isWhiteListOn, false, null, $idShop);
        Configuration::updateValue(Younitedpay::PRODUCTION_MODE, $isProduction, false, null, $idShop);
    }

    protected function postStateSubmit($idShop)
    {
        $deliveredStatus = Tools::getValue('delivered_status');
        $maturities = Tools::getValue('maturity');

        /* @var ConfigService $configService */
        $this->configService = ServiceContainer::getInstance()->get(ConfigService::class);

        $this->configService->saveAllMaturities($maturities, $this->context->shop->id);
        Configuration::updateValue(
            Younitedpay::ORDER_STATE_DELIVERED,
            json_encode($deliveredStatus),
            false,
            null,
            $idShop
        );
    }

    protected function getConfigurationVariables()
    {
        $idShop = $this->context->shop->id;

        $deliveredname = null !== _PS_OS_DELIVERED_ ? _PS_OS_DELIVERED_ : '_PS_OS_DELIVERED_';
        $defaultdelivered = Configuration::getGlobalValue($deliveredname);

        $deliveredStatus = Configuration::get(
            Younitedpay::ORDER_STATE_DELIVERED,
            null,
            null,
            $idShop,
            $defaultdelivered
        );

        $frontHook = Configuration::get(
            Younitedpay::FRONT_HOOK,
            null,
            null,
            $idShop,
            'disabled'
        );

        $allMaturities = $this->getAllMaturities();
        $urlFormConfig = $this->context->link->getAdminLink('AdminYounitedpayConfiguration');

        Media::addJsDef([
            'younitedpay' => [
                'maturities' => count($allMaturities) + 1,
                'admin_url' => $urlFormConfig,
                'translations' => [
                    'copy_link_webhook' => $this->module->l(
                        'WebHook URL sent to clipboard',
                        'AdminYounitedpayConfiguration'
                    ),
                ],
            ],
        ]);

        return [
            'url_form_config' => $urlFormConfig,
            'production_mode' => $this->isProductionMode,
            'client_id' => $this->clientID,
            'client_secret' => $this->clientSecret,
            'webhook_secret' => $this->webHookSecret,
            'client_id_production' => $this->clientIDProduction,
            'client_secret_production' => $this->clientSecretProduction,
            'webhook_secret_production' => $this->webHookSecretProduction,
            'whitelist_on' => $this->isWhiteListOn,
            'whitelist_ip' => $this->whitelistIP,
            'show_monthly' => $this->isShownMonthly,
            'widget_info' => '{widget name="younitedpay" amount="149.90"}',
            'no_config' => empty($this->clientID) || empty($this->clientSecret),
            'order_states' => $this->configService->getOrderStates(),
            'delivered_status' => Tools::getValue('delivered_status', $deliveredStatus),
            'front_hook' => Tools::getValue('front_hook', $frontHook),
            'link_help' => $this->context->link->getAdminLink('AdminYounitedpayHelp'),
            'maturities' => $allMaturities,
            'maturitylist' => $this->maturitylist,
        ];
    }
}
