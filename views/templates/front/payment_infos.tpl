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
* @author 202 ecommerce <tech@202-ecommerce.com>
* @copyright Younited
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
*}
<div>
    <p>
        {l s='Take advantage of a payment in' mod='younitedpay'} {$credit.maturity} 
        <b>{l s='instalments for' mod='younitedpay'} {$credit.installment_amount}{l s='€/month' mod='younitedpay'}</b> 
        {if $credit.taeg <= 0 && $credit.tdf <= 0}
            {l s='without any fees (i.e. fixed APR of 0%, fixed borrowing rate 0%).' mod='younitedpay'}
        {else}
            <b>{l s='with fixed APR of' mod='younitedpay'} {$credit.tdf}%</b>
            {l s='%, fixed borrowing rate' mod='younitedpay'} {$credit.taeg}%{l s=').' mod='younitedpay'}
        {/if}
    </p>
    <p>
        <b>
            {l s='Financing cost:' mod='younitedpay'} {$credit.interest_total} {l s='euros' mod='younitedpay'}. 
            {l s='Financing period:' mod='younitedpay'} {$credit.maturity} {l s='months' mod='younitedpay'}.<br />
            {l s='Amount of financing:' mod='younitedpay'} {$credit.total_order} {l s='euros' mod='younitedpay'}.
            {l s='Total amount due:' mod='younitedpay'} {$credit.total_amount} {l s='euros' mod='younitedpay'}.<br />
    </p>
    <p>
            {l s='A loan commits you and must be repaid. Check your ability to repay before committing yourself' 
                mod='younitedpay'}. 
            {l s='Place your order with your bank card. You will not be debited until the contract is activated.'
                mod='younitedpay'}
        </b>
    </p>
    {if count($error) > 0}    
        <div class="alert alert-warning">
            <ul style="margin-bottom:auto!important;list-style:disc!important;">
                {foreach $error item=oneerror}
                    <li>{$oneerror nofilter}</li>
                {/foreach}
            </ul> 
        </div>
    {/if}
</div>