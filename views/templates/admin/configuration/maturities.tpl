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

<div class="form-group mt-2 row">
<label class="form-control-label col-lg-3 justify-content-end pt-1">
    {l s='Show range slider for maturities' mod='younitedpay'}
</label>
<div class="col-lg-4 align-item-center">                           
    <span class="ps-switch ps-switch-lg" id="show_ranges_switch">
        <input type="radio" name="show_ranges" id="show_ranges_off" 
            value="0"{if $configuration.show_ranges === false} checked{/if}/>
        <label for="show_ranges_off">Off</label>
        <input type="radio" name="show_ranges" id="show_ranges_on" 
            value="1"{if $configuration.show_ranges === true} checked{/if}/>
        <label for="show_ranges_on">On</label>
        <span class="slide-button"></span>
    </span>
</div>
</div>
<div class="form-group mt-2 row ranges_min_max{if $configuration.show_ranges === false} hidden{/if}">
    <label class="form-control-label col-lg-3 justify-content-end pt-1">
        {l s='Installments from' mod='younitedpay'}
    </label>
    <div class="col-lg-6 form-group d-flex align-items-center" id="range-installment-selects">
        <select class="form-control col-lg-1" id="min_installment" name="min_installment">
            {foreach $configuration.maturitylist item=maturityitem}
                <option name="{$maturityitem|escape:'htmlall':'UTF-8'}" value="{$maturityitem|escape:'htmlall':'UTF-8'}"
                    {if (int) $configuration.min_installment == (int) $maturityitem} selected{/if}>
                        {$maturityitem|escape:'htmlall':'UTF-8'}x
                </option>
            {/foreach}
        </select>  
        <span>{l s=' to ' mod='younitedpay'}</span>
        <select class="form-control col-lg-1" id="max_installment" name="max_installment">
            {foreach $configuration.maturitylist item=maturityitem}
                <option name="{$maturityitem|escape:'htmlall':'UTF-8'}" value="{$maturityitem|escape:'htmlall':'UTF-8'}" 
                    {if (int) $configuration.max_installment == (int) $maturityitem} selected{/if}>
                        {$maturityitem|escape:'htmlall':'UTF-8'}x
                </option>
            {/foreach}
        </select>  
    </div>
</div>
<div class="form-group mt-2 row ranges_min_max{if $configuration.show_ranges === false} hidden{/if}">
    <label class="form-control-label col-lg-3 justify-content-end pt-1" for="min_ranges">
        {l s='Min. amount (tax incl.)' mod='younitedpay'}
    </label>
    <div class="col-lg-9 form-group d-flex align-items-center">
        <input type="text" class="col-lg-1 form-control" name="min_ranges" id="min_ranges" 
            value="{$configuration.min_ranges|escape:'htmlall':'UTF-8'}"/>
        <span class="currency d-flex align-items-center">€</span>
        <label class="form-control-label col-lg-2 ml-4" for="max_ranges">
            {l s='Max. amount (tax incl.)' mod='younitedpay'}
        </label>
        <input type="text" class="col-lg-1 form-control" name="max_ranges" id="max_ranges" 
            value="{$configuration.max_ranges|escape:'htmlall':'UTF-8'}" placeholder="{l s='Infinite' mod='younitedpay'}"/>
        <span class="currency d-flex align-items-center">€</span>
    </div>
</div>
<div class="divhr">
<hr />
</div>
<div class="form-group mt-2 row">
    <label class="form-control-label col-lg-3 justify-content-end pt-1" for="maturity_zone">        
        <span class="ranges_not_min_max{if $configuration.show_ranges === true} hidden{/if}">
            {l s='Selected maturities' mod='younitedpay'}
            </span>
        <span class="ranges_min_max{if $configuration.show_ranges === false} hidden{/if}">
            {l s='Maturities highlighted' mod='younitedpay'}
        </span>
    </label>
    <div class="col-lg-7 ml-3 align-item-center" id="younitedpay_maturities">
        {foreach from=$configuration.maturities item=maturity key=key}
            {include file="./maturity.tpl" maturity=$maturity maturitylist=$configuration.maturitylist key=$key}
        {/foreach}
    </div>
</div>
<div class="form-group row">
    <label class="form-control-label col-lg-3 justify-content-end pt-1"> 
    </label>
    <div class="col-lg-5 align-item-center">
        <button class="btn btn-lg btn-outline-primary" id="younitedpay_maturitybtn" type="submit">
            {l s='Add a maturity' mod='younitedpay'}
        </button>
        <small class="form-text">
            {l s='To keep your site cluter free you should select a maximum of 3 maturities.' 
            mod='younitedpay'}
        </small>
    </div>
</div>