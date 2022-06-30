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
class YounitedpayErrorModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $orderUrl = Context::getContext()->link->getPageLink(
            'order',
            null,
            null,
            [
                'step' => 4,
            ]
        );
        if (Tools::getValue('cancel') !== false) {
            $this->errors[] = $this->l('You have cancelled the payment.', 'error');
        } else {
            $this->errors[] = $this->l('Payment refused, or error occurs during validation. Please try again.', 'error');
        }
        $this->redirectWithNotifications($orderUrl);

        parent::initContent();
    }
}
