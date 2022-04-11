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
            <select class="custom-select col-lg-5" name="maturity[{$key}][maturity]"> 
                <option name="3x" value="3"{if $maturity.maturity == 3} selected{/if}>3x</option>
                <option name="4x" value="4"{if $maturity.maturity == 4} selected{/if}>4x</option>
                <option name="5x" value="5"{if $maturity.maturity == 5} selected{/if}>5x</option>
                <option name="10x" value="10"{if $maturity.maturity == 10} selected{/if}>10x</option>
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
                {l s='Minimum amount' mod='younitedpay'}
            </label>
            <input type="text" class="col-lg-5 form-control" name="maturity[{$key}][minimum]" value="{$maturity.minimum}"/>
            <span class="currency d-flex align-items-center">€</span>
        </div>
        <div class="col-xl-6 form-group d-flex align-items-center">
            <label class="form-control-label col-lg-6 justify-content-center pt-1" for="maturity[{$key}][maximum]">
                {l s='Maximum amount' mod='younitedpay'}
            </label>
            <input type="text" class="col-lg-5 form-control" name="maturity[{$key}][maximum]" 
            placeholder="{l s='Infinite' mod='younitedpay'}" 
            value="{if $maturity.maximum > 0}{$maturity.maximum}{/if}" />
            <span class="currency">€</span>
        </div>
    </div>
    <small class="form-text">
        {l s='Installments from ' mod='younitedpay'}{$maturity.minimum} €/{l s='month.' mod='younitedpay'}
        {if $maturity.maximum > 0}
            {l s='And up to ' mod=''}{$maturity.maximum} €/{l s='month.' mod='younitedpay'}
        {/if}
    </small>
</div>