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
<div id="younitedpay_maturity{$key}" class="maturity_zone card card-header row{if $key >= 1} mt-2{/if}">
    <input 
        type="hidden" 
        name="maturity[{$key}][id_younitedpay_configuration]" 
        value="{$maturity.id_younitedpay_configuration}"
    />
    <input type="hidden" name="maturity[{$key}][deleted]" id="younitedpay_delete{$key}" value="0" />      
    <div class="row">
        <div class="col-xl-6 form-group d-flex align-items-center">
            <label class="form-control-label col-lg-6 justify-content-center pt-1" for="maturity[{$key}][maturity]">
                {l s='Installments' mod='younitedpay'}
            </label>
            <select class="form-control col-lg-5" data-toggle="maturity" id="maturity{$key}"
                data-minimumresultsforsearch="3" aria-hidden="true" name="maturity[{$key}][maturity]">
                {foreach $maturitylist item=maturityitem}
                    <option name="{$maturityitem}" value="{$maturityitem}"{if $maturity.maturity == $maturityitem} selected{/if}>{$maturityitem}x</option>                
                {/foreach}
            </select>  
        </div>
        <div class="col-xl-6 form-group d-flex align-items-center">                   
            <div class="col-lg-12 justify-content-end d-flex">
                <i class="material-icons younitedpay_delmaturity btn" data-target="{$key}" 
                    data-id="{$maturity.id_younitedpay_configuration}">delete</i>
            </div>
        </div>
    </div>
    <hr />                
    <div class="row">
        <div class="col-xl-6 form-group d-flex align-items-center">
            <label class="form-control-label col-lg-6 justify-content-center pt-1" for="maturity[{$key}][minimum]">
                {l s='Min. amount (tax included)' mod='younitedpay'}
            </label>
            <input type="text" data-id="{$key}" id="min_amount_input_{$key}" 
                class="col-lg-5 form-control younitedpay_maturity_change"
                name="maturity[{$key}][minimum]" value="{$maturity.minimum}"/>
            <span class="currency d-flex align-items-center">€</span>
        </div>
        <div class="col-xl-6 form-group d-flex align-items-center">
            <label class="form-control-label col-lg-6 justify-content-center pt-1" for="maturity[{$key}][maximum]">
                {l s='Max. amount (tax included)' mod='younitedpay'}
            </label>
            <input type="text" data-id="{$key}" class="col-lg-5 form-control younitedpay_maturity_change" 
            id="max_amount_input_{$key}"
            name="maturity[{$key}][maximum]" 
            placeholder="{l s='Infinite' mod='younitedpay'}"
            data-id-zone="{$key}" 
            value="{if $maturity.maximum > 0}{$maturity.maximum}{/if}" />
            <span class="currency">€</span>
        </div>
    </div>
    <small class="form-text">
        {l s='Installments from ' mod='younitedpay'}
        {assign var="mininstallment" value=$maturity.minimum / $maturity.maturity}
        {assign var="maxinstallment" value=$maturity.minimum / $maturity.maturity}
        <span id="min_amount_{$key}">
        {if $mininstallment >= 1}
            {$mininstallment}
        {else}
            1
        {/if}
        </span> €/{l s='month.' mod='younitedpay'}
        <span id="max_amount_zone_{$key}"{if $maxinstallment <= 0} class="hidden"{/if}>
            {l s='And up to ' mod=''}<span id="max_amount_{$key}">
            {$maxinstallment}</span> €/{l s='month.' mod='younitedpay'}
        </span>
    </small>
</div>