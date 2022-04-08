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
<form id="states_form" method="POST" class="defaultForm form-horizontal mt-2"
    action="{$configuration.url_form_config|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="states_submit" value="1" />
    <div class="row justify-content-center">
        <div class="col-xl-12 pr-5 pl-5">
            <div class="card">
                <div class="card-header">
                    <div class="col-sm-11">{l s='General settings' mod='younitedpay'}</div>
                </div>
                <div class="form-wrapper justify-content-center col-xl-12">
                    {include file="./maturities.tpl" configuration=$configuration}
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="delivered_status">
                            {l s='Credit trigger status' mod='younitedpay'}
                        </label>
                        <div class="col-lg-5 align-item-center">
                            <select class="custom-select" name="delivered_status">
                                {foreach from=$configuration.order_states item=orderstate}
                                    <option name="{$orderstate.name}" value="{$orderstate.id}" {if
                                        $orderstate.id===$configuration.delivered_status}selected="selected" {/if}>
                                        {$orderstate.name} 
                                    </option> 
                                {/foreach} 
                            </select> 
                            <small class="form-text">
                                {l s='The credit will be activated when the order changes to the selected status.' 
                                mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="delivered_status">
                            {l s='Monthly installments on products pages' mod='younitedpay'}
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
                                {l s='If you wish to only have theses informations on the cart you can select "disabled" to hide it on the product pages.' 
                                mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="delivered_status">
                            {l s='Cache' mod='younitedpay'}
                        </label>
                        <div class="col-lg-5 align-item-center">
                            <button class="btn btn-lg btn-outline-primary" type="submit">
                                {l s='Flush cache' mod='younitedpay'}
                            </button>
                            <small class="form-text">
                                {l s='If you have any problem or the informations in the module aren’t up to date you can empty the cache, new data will be fetched automatically.' 
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
</form>