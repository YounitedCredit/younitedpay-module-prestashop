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
        <b>{l s='Total amount due of %s€.' mod='younitedpay' sprintf=[$credit.total_amount]}</b>
        {l s='You pay back %s' mod='younitedpay' sprintf=[$credit.maturity]}&nbsp;
        <b>{l s='installements of %s€' mod='younitedpay' sprintf=[$credit.installment_amount]}</b>&nbsp;
        {l s='over a period of %s months.' mod='younitedpay' sprintf=[$credit.maturity]}
    </p>
    <p>
        <b>{l s='Fixed Annual Percentage Rate (APR) of %s' mod='younitedpay' sprintf=[$credit.taeg]}%.</b>&nbsp;<br />
        {l s='Fixed borrowing rate of %s' mod='younitedpay' sprintf=[$credit.tdf]}%.&nbsp;
        {l s='Interest and fees due of %s€.' mod='younitedpay' sprintf=[$credit.interest_total]}
    </p>
    <p>
        <b>{l s='Taking out a loan is a commitment with an obligation of repayment. Verify your ability to repay the loan before committing.' mod='younitedpay'}</b>
    </p>
    {if count($error) > 0}    
        <div class="alert alert-warning">
            <ul style="margin-bottom:auto!important;list-style:disc!important;">
                {foreach $error item=oneerror}
                    <li>{html_entity_decode($oneerror|escape:'htmlall':'UTF-8')}</li>
                {/foreach}
            </ul> 
        </div>
    {/if}
</div>