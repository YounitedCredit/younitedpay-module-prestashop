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
use YounitedpayAddon\Entity\YounitedPayContract;
use YounitedpayAddon\Service\OrderService;
use YounitedpayAddon\Service\PaymentService;
use YounitedpayAddon\Utils\ServiceContainer;
use YounitedpayClasslib\Utils\Translate\TranslateTrait;

class AdminYounitedpayContractsController extends ModuleAdminController
{
    use TranslateTrait;

    /** @var \ModuleCore Instance of your module automatically set by ModuleAdminController */
    public $module;

    /** @var string Associated object class name */
    public $className = 'YounitedpayAddon\Entity\YounitedPayContract';

    /** @var string Associated table name */
    public $table = 'younitedpay_contract';

    /** @var string Activate bootstrap */
    public $bootstrap = true;

    public function __construct()
    {
        $this->_orderBy = 'id_younitedpay_contract';
        $this->_orderWay = 'DESC';
        $this->actions_available = [];
        $this->actions = ['view'];

        parent::__construct();

        $this->fields_list = [
            'id_younitedpay_contract' => ['title' => $this->l('ID'), 'class' => 'fixed-width-xs'],
            'id_cart' => ['title' => $this->l('Cart Id')],
            'id_order' => ['title' => $this->l('Order Id')],
            'payment_id' => ['title' => $this->l('API Payment ID')],
            'id_external_younitedpay_contract' => ['title' => $this->l('Contract reference')],
            'date_add' => ['title' => $this->l('Added on')],
            'date_upd' => ['title' => $this->l('Last update')],
            'is_activated' => ['title' => $this->l('Activated')],
            'activation_date' => ['title' => $this->l('Activation date')],
            'is_canceled' => ['title' => $this->l('Cancelled')],
            'canceled_date' => ['title' => $this->l('Cancellation date')],
            'is_withdrawn' => ['title' => $this->l('Withdrawn')],
            'withdrawn_date' => ['title' => $this->l('Withdrawn date')],
            'withdrawn_amount' => ['title' => $this->l('Withdrawn amount')],
            'api_version' => ['title' => $this->l('API Version')],
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
        // $this->display = 'list';
        parent::initContent();
    }

    public function renderView()
    {
        parent::renderView();
        $younitedContract = new YounitedPayContract((int) Tools::getValue('id_younitedpay_contract'));

        /** @var PaymentService $paymentService */
        $paymentService = ServiceContainer::getInstance()->get(PaymentService::class);
        $api = $paymentService->getApiPaymentById($younitedContract->payment_id);

        /** @var OrderService $orderservice */
        $orderservice = ServiceContainer::getInstance()->get(OrderService::class);
        $this->context->smarty->assign($orderservice->getContractInformations($younitedContract));

        $this->context->smarty->assign([
            'younitedcontract' => $younitedContract,
            'contract' => json_encode($younitedContract, JSON_PRETTY_PRINT),
            'api' => json_encode($api, JSON_PRETTY_PRINT),
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . '/younitedpay/views/templates/admin/viewcontract.tpl');
    }
}
