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