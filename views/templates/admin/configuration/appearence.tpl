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
<form id="appearence_form" method="POST" class="defaultForm form-horizontal mt-2"
    action="{$configuration.url_form_config|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="appearance_submit" value="1" />
    <div class="row justify-content-center">
        <div class="col-xl-12 pr-5 pl-5">
            <div class="card">
                <div class="card-header">
                    <div class="col-sm-11">{l s='3. Appearance' mod='younitedpay'}</div>
                </div>
                <div class="form-wrapper justify-content-center col-xl-12">
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1">
                            {l s='Show monthly installments' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">                           
                            <span class="ps-switch ps-switch-lg">
                                <input type="radio" name="show_monthly" id="show_monthly_off" 
                                    value="0"{if $configuration.show_monthly === false} checked{/if}/>
                                <label for="show_monthly_off">Off</label>
                                <input type="radio" name="show_monthly" id="show_monthly_on" 
                                    value="1"{if $configuration.show_monthly === true} checked{/if}/>
                                <label for="show_monthly_on">On</label>
                                <span class="slide-button"></span>
                            </span>
                            <small class="form-text">
                                {l s='If you wish to only have theses informations on the cart you can select "Off" to hide it on the product pages.' 
                                mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="delivered_status">
                            {l s='Monthly installments location' mod='younitedpay'}
                        </label>
                        <div class="col-lg-5 align-item-center">
                            <select class="custom-select" name="front_hook">
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
                                {l s='Theses values are locations registered by your current theme, you can choose any of them to place the widget where it looks the best.' 
                                mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="widget_info">
                            {l s='Monthly installments widget' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center input-group">
                            <input type="text" disabled class="form-control" 
                                style="color:#25B9D7!important;border-right:none;font-weight:bold;"
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
                            <small class="form-text">
                                {l s='When enabled, only the listed IPs will see the module’s components on the site' 
                                    mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-lg btn-primary" type="submit">
                            {l s='Save' mod='younitedpay'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>