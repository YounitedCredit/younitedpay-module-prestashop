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
<form id="appearence_form" method="POST" class="defaultForm form-horizontal mt-2"
    action="{$configuration.url_form_config|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">    
    {if $configuration.no_config === false}
        <input type="hidden" name="appearance_submit" value="1" />
    {/if}    
    <div class="row justify-content-center">
        <div class="col-xl-12 pr-5 pl-5">
            <div class="card">
                <div class="card-header">
                    <div class="col-sm-11">{l s='3. Display' mod='younitedpay'}</div>
                </div>
                <div class="form-wrapper justify-content-center col-xl-12
                    {if $configuration.no_config === true || $configuration.no_shop_code || $connected === false}
                        backimg" style="background: url('{$shop_img_url|escape:'htmlall':'UTF-8'}/appearence-no-config.png');min-height:600px;">
                        <div class="infotext" style="margin-top:250px;">
                            {if $configuration.no_config === true}
                                <p>{$no_keys_text|escape:'htmlall':'UTF-8'}</p>
                            {elseif $configuration.no_shop_code === true}
                                <p>{$no_shop_text|escape:'htmlall':'UTF-8'}</p>
                            {/if}
                            {if $connected === false && $configuration.no_config === false}
                                <p>{$bad_config_text|escape:'htmlall':'UTF-8'}</p>
                            {/if}
                        </div>
                    {else}
                        ">
                        <div class="form-group mt-2 row">
                            <label class="form-control-label col-lg-3 justify-content-end pt-1">
                                {l s='Show monthly installments' mod='younitedpay'}
                            </label>
                            <div class="col-lg-5 align-item-center">     
                                <select class="custom-select" name="show_monthly">
                                    <option name="0" value="0"
                                        {if $configuration.show_monthly == 0} selected{/if}>
                                        {l s='Disabled' mod='younitedpay'}
                                    </option>
                                    <option name="1" value="1"
                                        {if $configuration.show_monthly == 1} selected{/if}>
                                        {l s='Product page only' mod='younitedpay'}
                                    </option>
                                    <option name="2" value="2"
                                        {if $configuration.show_monthly == 2} selected{/if}>
                                        {l s='Cart page only' mod='younitedpay'}
                                    </option>
                                    <option name="3" value="3"
                                        {if $configuration.show_monthly == 3} selected{/if}>
                                        {l s='Product and Cart pages' mod='younitedpay'}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mt-2 row">
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="delivered_status">
                                <b>{l s='Monthly installments location' mod='younitedpay'}</b>
                            </label>
                        </div>
                        <div class="form-group mt-2 row">
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="delivered_status">
                                {l s='Product page' mod='younitedpay'}
                            </label>
                            <div class="col-lg-5 align-item-center">
                                <select class="custom-select" name="front_hook" data-month>
                                    <option name="disabled" value="disabled"
                                        {if $configuration.front_hook == 'disabled'} selected{/if}>
                                        {l s='Disabled' mod='younitedpay'}
                                    </option>
                                    <option name="displayProductPriceBlock" value="displayProductPriceBlock"
                                        title="After products prices (hook displayProductPriceBlock)"
                                        {if $configuration.front_hook == 'displayProductPriceBlock'} selected{/if}>
                                        {l s='After products prices' mod='younitedpay'}
                                    </option>
                                    <option name="displayAfterProductThumbs" value="displayAfterProductThumbs"
                                        title="After products thumbnails (hook displayAfterProductThumbs)"
                                        {if $configuration.front_hook == 'displayAfterProductThumbs'} selected{/if}>
                                        {l s='After products thumbnails' mod='younitedpay'}
                                    </option>
                                    <option name="displayProductAdditionalInfo" value="displayProductAdditionalInfo"
                                        title="After products additional informations (hook displayProductAdditionalInfo)"
                                        {if $configuration.front_hook == 'displayProductAdditionalInfo'} selected{/if}>
                                        {l s='After products additional informations' mod='younitedpay'}
                                    </option>
                                    <option name="displayReassurance" value="displayReassurance" 
                                        title="After Reassurance (hook displayReassurance)"
                                        {if $configuration.front_hook == 'displayReassurance'} selected{/if}>
                                        {l s='After Reassurance' mod='younitedpay'}
                                    </option>
                                </select> 
                                <small class="form-text">
                                    {l s='Theses values are locations registered by your current theme, you can choose any of them to place the widget where it looks the best.' mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                        <div class="form-group mt-2 row">
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="delivered_status">
                                {l s='Cart page' mod='younitedpay'}
                            </label>
                            <div class="col-lg-5 align-item-center">
                                <select class="custom-select" name="front_hook_cart" data-month>
                                    <option name="disabled" value="disabled"
                                        {if $configuration.front_hook_cart == 'disabled'} selected{/if}>
                                        {l s='Disabled' mod='younitedpay'}
                                    </option>
                                    <option name="displayExpressCheckout" value="displayExpressCheckout"
                                        title="{l s='At the bottom of the cart totals' mod='younitedpay'} (hook displayExpressCheckout)"
                                        {if $configuration.front_hook_cart == 'displayExpressCheckout'} selected{/if}>
                                        {l s='At the bottom of the cart totals' mod='younitedpay'}
                                    </option>
                                    <option name="displayShoppingCartFooter" value="displayShoppingCartFooter"
                                        title="{l s='After the product\'s lines' mod='younitedpay'} (hook displayShoppingCartFooter)"
                                        {if $configuration.front_hook_cart == 'displayShoppingCartFooter'} selected{/if}>
                                        {l s='After the product\'s lines' mod='younitedpay'}
                                    </option>
                                </select> 
                                <small class="form-text">
                                    {l s='Theses values are locations registered by your current theme, you can choose any of them to place the widget where it looks the best.' mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                        <div class="form-group mt-2 row">
                            <label class="form-control-label col-lg-3 justify-content-end pt-1">
                                {l s='Display with borders' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-center">                           
                                <span class="ps-switch ps-switch-lg disable_on_change" data-input="widget_borders_on">
                                    <input type="radio" name="widget_borders" id="widget_borders_off" 
                                        value="0"{if $configuration.widget_borders === false} checked{/if}/>
                                    <label for="widget_borders_off">Off</label>
                                    <input type="radio" name="widget_borders" id="widget_borders_on" 
                                        value="1"{if $configuration.widget_borders === true} checked{/if}/>
                                    <label for="widget_borders_on">On</label>
                                    <span class="slide-button"></span>
                                </span>
                                <small class="form-text">
                                    {l s='Add black borders on the widget in product page.' mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                        <div class="form-group mt-2 row">
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="widget_info">
                                {l s='Monthly installments widget' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-center input-group">
                                <input type="text" disabled class="form-control 
                                    {if $configuration.show_monthly === 0}
                                        widget_disabled
                                    {else}
                                        widget_enabled
                                    {/if}" 
                                    style="border-right:none;font-weight:bold;" data-month id="widget_input"
                                    value="{$configuration.widget_info|escape:'htmlall':'UTF-8'}" />
                                <div class="input-group-append copy-clipboard" 
                                data-message="{l s='Widget content copied to clipboard' mod='younitedpay'}" 
                                data-clipboard-copy="{$configuration.widget_info|escape:'htmlall':'UTF-8'}">
                                    <span class="input-group-text" style="border-left:none;" 
                                        title="{l s='Copy to clipboard' mod='younitedpay'}">
                                        <i class="material-icons input-group-text btn" 
                                            style="font-size:20px!important;color:#25B9D7!important;border:none;">content_paste</i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-lg btn-primary" type="submit"
                        {if $configuration.no_config === true} disabled title="{$no_keys_text|escape:'htmlall':'UTF-8'}"{/if}>                            
                            {l s='Save' d='Admin.Actions' mod='younitedpay'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>