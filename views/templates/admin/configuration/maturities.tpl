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
    <label class="form-control-label col-lg-3 justify-content-end pt-1" for="maturity_zone">        
        {l s='Selected maturities' mod='younitedpay'}        
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