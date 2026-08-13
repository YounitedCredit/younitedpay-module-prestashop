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

require_once _PS_MODULE_DIR_ . 'younitedpay/vendor/autoload.php';
use YounitedpayAddon\Entity\YounitedPayPayment;
use YounitedpayAddon\Service\PaymentService;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Utils\Translate\TranslateTrait;

class AdminYounitedpayPaymentsController extends ModuleAdminController
{
    use TranslateTrait;

    /** @var \ModuleCore Instance of your module automatically set by ModuleAdminController */
    public $module;

    /** @var string Associated object class name */
    public $className = 'YounitedpayAddon\Entity\YounitedPayPayment';

    /** @var string Associated table name */
    public $table = 'younitedpay_payment';

    /** @var bool Is bootstrap enabled */
    public $bootstrap = true;

    public function __construct()
    {
        $this->_orderBy = 'id_younitedpay_payment';
        $this->_orderWay = 'DESC';
        $this->actions_available = [];
        $this->actions = ['view'];

        parent::__construct();

        $this->fields_list = [
            'id_younitedpay_payment' => ['title' => $this->l('ID'), 'class' => 'fixed-width-xs'],
            'id_cart' => ['title' => $this->l('Cart Id')],
            'payment_id' => ['title' => $this->l('API Payment ID')],
            'id_external_younitedpay_contract' => ['title' => $this->l('Contract reference')],
            'date_add' => ['title' => $this->l('Added on')],
        ];
    }

    /**
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        $this->show_toolbar = false;
        parent::initPageHeaderToolbar();
        // Remove the help icon of the toolbar which no useful for us
        $this->context->smarty->clearAssign('help_link');
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function renderView()
    {
        parent::renderView();
        $younitedPayment = new YounitedPayPayment((int) Tools::getValue('id_younitedpay_payment'));

        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);

        $api = $paymentService->getApiPaymentById($younitedPayment->payment_id, $younitedPayment->id_cart);

        $this->context->smarty->assign([
            'younitedpayment' => $younitedPayment,
            'api' => json_encode($api, JSON_PRETTY_PRINT),
            'payments_url' => \Context::getContext()->link->getAdminLink('AdminYounitedpayPayments'),
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/younitedpay/views/templates/admin/viewpayment.tpl');
    }
}
