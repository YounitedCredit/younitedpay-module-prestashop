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
{assign var=date_formating value="%d/%m/%Y %H:%M:%S"}
{if $iso_lang != 'fr'}
    {assign var=date_formating value="%Y/%m/%d %H:%M:%S"}
{/if}
<div class="tab-pane" id="younitedpay">
    <p>
        <b>{l s='Payment ID' mod='younitedpay'}</b><br />        
        {$payment.id|escape:'html':'UTF-8'}        
    </p>

    <p>
        <b>{l s='Payment Date' mod='younitedpay'}</b><br />        
        {$payment.date|date_format:$date_formating}
    </p>

    <p><b>
        {l s='Payment Method' mod='younitedpay'}</b><br />
        <img style="height:45px;" src="{$shop_url}{$logo_younitedpay_url}" alt="logo Younited Pay" />
    </p>

    <p>
        <b>{l s='Payment Status' mod='younitedpay'}</b><br />
        {$payment.status|escape:'html':'UTF-8'} - {$payment.date_state|date_format:$date_formating}
    </p>
    {if $payment.withdrawn_amount > 0}
        <p>
            <b>{l s='Withdrawn amount:'} {$payment.withdrawn_amount}€</b><br />
            {if $payment.is_withdrawn_confirmed === false}
                {l s='Waiting confirmation'}
            {else}
                {l s='Withdrawn confirmed'}
            {/if}
        </p>
    {/if}
</div>
