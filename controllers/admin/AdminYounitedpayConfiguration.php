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
use YounitedpayAddon\Utils\ServiceContainer;

class AdminYounitedpayConfigurationController extends ModuleAdminController
{
    /** @var \Module Instance of your module automatically set by ModuleAdminController */
    public $module;

    /** @var string Associated object class name */
    public $className = 'Configuration';

    /** @var string Associated table name */
    public $table = 'configuration';

    /** @var string Associated table name */
    public $bootstrap = false;

    /** @var string */
    public $clientID;

    /** @var string */
    public $clientSecret;

    /** @var string */
    public $webHookSecret;

    /** @var string */
    public $isProductionMode;

    /** @var ConfigService */
    public $configService;

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
        $cookieBridgeSave = Context::getContext()->cookie->__get('younitedpaysave');
        if ($cookieBridgeSave == 'ok') {
            $this->confirmations[] = $this->module->l('Successful update.');
        }
        Context::getContext()->cookie->__unset('younitedpaysave', '');

        /* @var ConfigService $configService */
        $this->configService = ServiceContainer::getInstance()->get(ConfigService::class);

        $this->content .= $this->renderConfiguration();
        parent::initContent();
    }

    public function initVarContent()
    {
        $idShop = $this->context->shop->id;
        $this->clientID = Configuration::get(
            Younitedpay::CLIENT_ID,
            null,
            null,
            $idShop,
            Tools::getValue('client_id', '')
        );
        $this->clientSecret = Configuration::get(
            Younitedpay::CLIENT_SECRET,
            null,
            null,
            $idShop,
            Tools::getValue('client_secret', '')
        );
        $this->isProductionMode = (bool) Configuration::get(
            Younitedpay::PRODUCTION_MODE,
            null,
            null,
            $idShop,
            Tools::getValue('production_mode', false)
        );
        $this->webHookSecret = (bool) Configuration::get(
            Younitedpay::WEBHOOK_SECRET,
            null,
            null,
            $idShop,
            Tools::getValue('webhook_secret', false)
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

            $tplVars = [
                'configuration' => $this->getConfigurationVariables(),
                'config_check' => $this->checkSpecifications(),
                'webhook_url_text' => $urlWebhook,
                'webhook_url' => $urlWebhook,
            ];

            $alertHere = empty($this->confirmations) && empty($this->errors);

            $tplVars['config_check']['alert'] = $alertHere !== true;
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
            $isoCode = 'en'; // TODO
        }

        return '/modules/' . $this->module->name . '/views/img/' . $fileImg;
    }

    protected function checkSpecifications()
    {
        $curlInfos = curl_version();
        $versionOpenSSL = null !== OPENSSL_VERSION_NUMBER ? OPENSSL_VERSION_NUMBER : -1;
        $versionSSLCURL = $curlInfos !== false ? $curlInfos['version'] . ' ' . $curlInfos['ssl_version'] : '';

        $sslActivated = $this->configService->isSslActive();
        $tlsCallCurl = $this->configService->isTlsActive();
        $infoSSLTLS = $versionOpenSSL !== -1 && $sslActivated === true
        ? $this->module->l('SSL enabled')
        : $this->module->l('SSL not enabled on all the shop');
        $infoSSLTLS .= $tlsCallCurl['error_message'] !== '' ? ' - ' . $tlsCallCurl['error_message'] : '';

        $isApiConnected = $this->configService->isApiConnected();

        return [
            [
                'name' => 'CURL',
                'info' => $versionSSLCURL !== '' ? 'version v.' . $versionSSLCURL : $this->module->l('not installed'),
                'ok' => $curlInfos !== false,
            ],
            [
                'name' => 'SSL & TLS v1.2',
                'info' => $infoSSLTLS,
                'ok' => $versionOpenSSL !== -1 && $sslActivated === true && $tlsCallCurl['status'],
            ],
            [
                'name' => $this->module->l('Encrypt functions'),
                'info' => '',
                'ok' => (bool) function_exists('hash_hmac'),
            ],
            [
                'name' => $this->module->l('Connected to API'),
                'info' => $isApiConnected['message'],
                'ok' => (bool) $isApiConnected['status'],
            ],
            [
                'name' => $this->module->l('Production environment'),
                'info' => '',
                'ok' => (bool) $this->isProductionMode,
            ],
        ];
    }

    protected function getOrderStates()
    {
        $orderStates = OrderState::getOrderStates($this->context->language->id);

        return array_map(function ($state) {
            return [
                'name' => $state['name'],
                'id' => $state['id_order_state'],
            ];
        }, $orderStates);
    }

    protected function getDefaultMaturities()
    {
        return [
            [
                'id_younitedpay_configuration' => 0,
                'id_shop' => $this->context->shop->id,
                'maturity' => 3,
                'minimum' => 150,
                'maximum' => 0,
                'currency' => 'EUR',
            ],
            [
                'id_younitedpay_configuration' => 0,
                'id_shop' => $this->context->shop->id,
                'maturity' => 10,
                'minimum' => 3000,
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

        if (Tools::isSubmit('account_submit')) {
            $this->postAccountSubmit($idShop);
            $isSubmitted = true;
        } elseif (Tools::isSubmit('states_submit')) {
            $this->postStateSubmit($idShop);
            $isSubmitted = true;
        } elseif (Tools::isSubmit('younitedpay_add_maturity')) {
            $this->ajaxDie($this->postAddNewMaturity($idShop));

            return;
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

    protected function postAddNewMaturity($idShop)
    {
        $this->context->smarty->assign([
            'key' => Tools::getValue('younitedpay_maturities', 0),
            'maturity' => [
                'id_younitedpay_configuration' => 0,
                'id_shop' => $idShop,
                'maturity' => 3,
                'minimum' => 150,
                'maximum' => 0,
                'currency' => 'EUR',
            ],
        ]);

        $template = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/configuration/maturity.tpl';

        return $this->context->smarty->fetch($template);
    }

    protected function postAccountSubmit($idShop)
    {
        $clientID = Tools::getValue('client_id');
        $clientSecret = Tools::getValue('client_secret');
        $webHookSecret = Tools::getValue('webhook_secret');
        $isProduction = Tools::getValue('production_mode');
        Configuration::updateValue(Younitedpay::CLIENT_ID, $clientID, false, null, $idShop);
        Configuration::updateValue(Younitedpay::CLIENT_SECRET, $clientSecret, false, null, $idShop);
        Configuration::updateValue(Younitedpay::WEBHOOK_SECRET, $webHookSecret, false, null, $idShop);
        Configuration::updateValue(Younitedpay::PRODUCTION_MODE, $isProduction, false, null, $idShop);
    }

    protected function postStateSubmit($idShop)
    {
        $deliveredStatus = Tools::getValue('delivered_status');
        $frontHook = Tools::getValue('front_hook');
        $maturities = Tools::getValue('maturity');

        /* @var ConfigService $configService */
        $this->configService = ServiceContainer::getInstance()->get(ConfigService::class);

        $this->configService->saveAllMaturities($maturities, $this->context->shop->id);
        Configuration::updateValue(Younitedpay::ORDER_STATE_DELIVERED, $deliveredStatus, false, null, $idShop);
        Configuration::updateValue(Younitedpay::FRONT_HOOK, $frontHook, false, null, $idShop);
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
            'order_states' => $this->getOrderStates(),
            'delivered_status' => Tools::getValue('delivered_status', $deliveredStatus),
            'front_hook' => Tools::getValue('front_hook', $frontHook),
            'maturities' => $allMaturities,
        ];
    }
}
