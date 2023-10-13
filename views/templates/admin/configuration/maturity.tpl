{**
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
 *}
<div id="younitedpay_maturity{$key|escape:'htmlall':'UTF-8'}" class="maturity_zone card card-header row{if $key >= 1} mt-2{/if}">
    <input 
        type="hidden" 
        name="maturity[{$key|escape:'htmlall':'UTF-8'}][id_younitedpay_configuration]" 
        value="{$maturity.id_younitedpay_configuration|escape:'htmlall':'UTF-8'}"
    />
    <input type="hidden" name="maturity[{$key|escape:'htmlall':'UTF-8'}][deleted]" id="younitedpay_delete{$key|escape:'htmlall':'UTF-8'}" value="0" />      
    <div class="row">
        <div class="col-xl-6 form-group d-flex align-items-center">
            <label class="form-control-label col-lg-6 justify-content-center pt-1" for="maturity[{$key|escape:'htmlall':'UTF-8'}][maturity]">
                {l s='Installments' mod='younitedpay'}
            </label>
            <select class="form-control col-lg-5 younitedpay_maturity_change" data-toggle="maturity" id="maturity{$key|escape:'htmlall':'UTF-8'}"
                data-minimumresultsforsearch="3" aria-hidden="true" data-id="{$key|escape:'htmlall':'UTF-8'}" name="maturity[{$key|escape:'htmlall':'UTF-8'}][maturity]">
                {foreach $maturitylist item=maturityitem}
                    <option name="{$maturityitem|escape:'htmlall':'UTF-8'}" value="{$maturityitem|escape:'htmlall':'UTF-8'}"{if $maturity.maturity == $maturityitem} selected{/if}>{$maturityitem|escape:'htmlall':'UTF-8'}x</option>
                {/foreach}
            </select>  
        </div>
        <div class="col-xl-6 form-group d-flex align-items-center">                   
            <div class="col-lg-12 justify-content-end d-flex">
                <i class="material-icons younitedpay_delmaturity btn" data-target="{$key|escape:'htmlall':'UTF-8'}" 
                    data-id="{$maturity.id_younitedpay_configuration|escape:'htmlall':'UTF-8'}">delete</i>
            </div>
        </div>
    </div>
    <hr />                
    <div class="row mt-1">
        <div class="col-xl-6 form-group d-flex align-items-center">
            <label class="form-control-label col-lg-6 justify-content-center pt-1" for="maturity[{$key|escape:'htmlall':'UTF-8'}][minimum]">
                {l s='Min. amount (tax incl.)' mod='younitedpay'}
            </label>
            <input type="text" data-id="{$key|escape:'htmlall':'UTF-8'}" id="min_amount_input_{$key|escape:'htmlall':'UTF-8'}" 
                class="col-lg-5 form-control younitedpay_maturity_change"
                name="maturity[{$key|escape:'htmlall':'UTF-8'}][minimum]" value="{$maturity.minimum|escape:'htmlall':'UTF-8'}"/>
            <span class="currency d-flex align-items-center">€</span>
        </div>
        <div class="col-xl-6 form-group d-flex align-items-center">
            <label class="form-control-label col-lg-6 justify-content-center pt-1" for="maturity[{$key|escape:'htmlall':'UTF-8'}][maximum]">
                {l s='Max. amount (tax incl.)' mod='younitedpay'}
            </label>
            <input type="text" data-id="{$key|escape:'htmlall':'UTF-8'}" class="col-lg-5 form-control younitedpay_maturity_change" 
            id="max_amount_input_{$key|escape:'htmlall':'UTF-8'}"
            name="maturity[{$key|escape:'htmlall':'UTF-8'}][maximum]" 
            placeholder="{l s='Infinite' mod='younitedpay'}"
            data-id-zone="{$key|escape:'htmlall':'UTF-8'}" 
            value="{if $maturity.maximum > 0}{$maturity.maximum|escape:'htmlall':'UTF-8'}{/if}" />
            <span class="currency">€</span>
        </div>
    </div>
    <small class="form-text">
        {l s='Reminder : the minimum monthly installment is 9.99 €/month' mod='younitedpay'}<br />
        {l s='Installments from ' mod='younitedpay'}
        {assign var="mininstallment" value=Tools::ps_round($maturity.minimum / $maturity.maturity, 2)}
        {assign var="maxinstallment" value=Tools::ps_round($maturity.maximum / $maturity.maturity, 2)}
        <span id="min_amount_{$key|escape:'htmlall':'UTF-8'}">{$mininstallment|escape:'htmlall':'UTF-8'}</span> €/{l s='month.' mod='younitedpay'}
        <span id="max_amount_zone_{$key|escape:'htmlall':'UTF-8'}"{if $maturity.maximum <= 0} class="hidden"{/if}>
            {l s='And up to ' mod='younitedpay'}<span id="max_amount_{$key|escape:'htmlall':'UTF-8'}">
            {$maxinstallment|escape:'htmlall':'UTF-8'}</span> €/{l s='month.' mod='younitedpay'}
        </span>
    </small>
</div>