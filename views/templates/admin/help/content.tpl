{**
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
 *}
<div class="younitedpayApp mt-4 pt-4">
  <div class="row">
    <div class="col-lg-12">
        <section class="accordion-section clearfix" aria-label="Question Accordions">
            <div class="container">
                <div class="panel-group pt-2" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default mb-3">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading0" data-target="#collapse0">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse0" aria-expanded="true" aria-controls="collapse0">
                                    {l s='What is YounitedPay ?' mod='younitedpay'}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse0" class="collapse" role="tabpanel" aria-labelledby="heading0">
                            <div class="panel-body px-3 mb-4">
                                {l s='Younited Pay is an instant credit offer from Younited. With Younited Pay, your customers can pay for their purchases in installments, with maturities ranging from 1x to 84x. This solution is available for baskets between 100€ and 50 000€. You will be paid for the entire order and we take care of any outstanding payments. We are paid by taking a commission on the transactions that are made through our payment solution. There are no additional costs. For more information:' mod='younitedpay'}
                                 <a href="https://www.younited-pay.com" target="_blank">https://www.younited-pay.com</a>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading1" data-target="#collapse1">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    {l s='How does YounitedPay ?' mod='younitedpay'}                                    
                                </a>
                            </h4>
                        </div>
                        <div id="collapse1" class="collapse" role="tabpanel" aria-labelledby="heading1">
                            <div class="panel-body px-3 mb-4">
                                {l s='Once the customer has completed his order on your website, and has selected the type of payment in installments that suits him, he will be redirected to our subscription path. During this process, he will be asked to verify his identity, to provide his credit card information for the monthly payments and to fill out a form declaring his solvency. These steps allow the YounitedPay solution to be regulated and responsible, in line with the legislation.' mod='younitedpay'}
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading2" data-target="#collapse2">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse2" aria-expanded="true">
                                    {l s='What information should I add to my General Terms and Conditins (GTC) ?' mod='younitedpay'}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse2" class="collapse" role="tabpanel" aria-labelledby="heading2">
                            <div class="panel-body px-3 mb-4">
                                {l s='In order to comply with the legislation, please add to your General Terms and Conditions (GTC) with the hyperlinks and replace [the Seller] with your company name:'  mod='younitedpay'}
                                <br />{l s='"[The Seller] offers its Customers the credit service of Younited Pay for the settlement of their purchases and the execution of the payment. This is conditional on the Customer\'s acceptance of the credit agreement offered by Younited."'  mod='younitedpay'}
                                <br />{l s='"Any refusal by Younited to grant credit for an order may result in the cancellation of the order.'  mod='younitedpay'}
                                <br />{l s='Any termination of the T&Cs binding the Customer and [the Seller] shall result in the termination of the credit agreement between Younited and the Customer."' mod='younitedpay'}
                                <br />{l s='In addition, also add to your General Terms and Conditions (GTC) (in accordance with Article L312-45, under penalty of fine): "The amount is paid by a credit granted by Younited registered on the REGAFI under number 13156."' mod='younitedpay'}
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default mb-3">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading3" data-target="#collapse3">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse3" aria-expanded="true">
                                    {l s='How do I display YounitedPay on my product page ?' mod='younitedpay'}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse3" class="collapse" role="tabpanel" aria-labelledby="heading3">
                            <div class="panel-body px-3 mb-4">
                                {l s='To optimize your conversion rate and ensure that your customers are aware of the YounitedPay offer, you can display the eligible offers on your product page. To do so, activate the "Show monthly installments" function in the "Display" section of the module. If the display is not optimal, make sure you have downloaded the latest version of the module.' mod='younitedpay'}
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default mb-3">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading4" data-target="#collapse4">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse4" aria-expanded="true">
                                    {l s='How are refunds processed ?' mod='younitedpay'}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse4" class="collapse" role="tabpanel" aria-labelledby="heading4">
                            <div class="panel-body px-3 mb-4">
                                {l s='There are two possibilities of refund: total and partial. These requests can be made directly from the back office of your Prestashop module.' mod='younitedpay'}
                                <br />{l s='You have just change the order status to cancel or refund.' mod='younitedpay'}
                                <br />{l s='Partial refund can be done too in the back office on the order with the button "Partial refund" and select "Refund on YounitedPay".' mod='younitedpay'}
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default mb-3">
                        <div class="panel-heading pt-3 younitedpay-collapse" role="tab" id="heading5" data-target="#collapse5">
                            <h4 class="panel-title">
                                <a role="button" title="" data-parent="#accordion"
                                    href="#collapse5" aria-expanded="true">
                                    {l s='How can I test the module if I don\'t have a staging environment for my website ?' mod='younitedpay'}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse5" class="collapse" role="tabpanel" aria-labelledby="heading5">
                            <div class="panel-body px-3 mb-4">
                                {l s='You have the possibility to whitelist IP addresses, allowing a test of the module on your production environment without display to all clients.' mod='younitedpay'}                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
  </div>
</div>
