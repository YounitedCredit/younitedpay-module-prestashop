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
<form id="configuration_form" method="POST" class="defaultForm form-horizontal"
    action="{$configuration.url_form_config|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="account_submit" value="1" />
    <div class="row justify-content-center">
        <div class="col-xl-12 pr-5 pl-5">
            <div class="card">
                <div class="card-header">
                    <div class="col-sm-11">{l s='1. Setting up' mod='younitedpay'}</div>
                </div>
                <div class="form-wrapper justify-content-center col-xl-12">
                    <div class="form-group mt-4 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="younitedpay_mode">
                            {l s='Environment' mod='younitedpay'}
                        </label>
                        <div class="form-select col-lg-4 align-item-center">
                            <select class="form-control" name="production_mode" style="width:130px;">
                                <option value="0" {if $configuration.production_mode==false}selected{/if}>
                                    {l s='Test' mod='younitedpay'}
                                </option>
                                <option value="1" {if $configuration.production_mode==true}selected{/if}>
                                    {l s='Production' mod='younitedpay'}
                                </option>
                            </select>
                            <small class="form-text">
                                {l s='This option defines in whitch environment your module is configured'
                                mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_id">
                            {l s='Client ID' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control"
                                placeholder="{l s='Fill in you Client ID' mod='younitedpay'}" id="client_id"
                                name="client_id" value="{$configuration.client_id|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='This information is located in your dashboard: \'Settings\' > \'General
                                settings\'' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_secret">
                            {l s='Client Secret' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control"
                                placeholder="{l s='Fill in you Client secret' mod='younitedpay'}" id="client_secret"
                                name="client_secret" value="{$configuration.client_secret|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='This information is located in your dashboard: \'Settings\' > \'General
                                settings\'' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="webhook_secret">
                            {l s='WebHook Secret' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control"
                                placeholder="{l s='Fill in you Client secret' mod='younitedpay'}" id="webhook_secret"
                                name="webhook_secret" value="{$configuration.webhook_secret|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='This information is located in your dashboard: \'Settings\' > \'General
                                settings\'' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="whitelist_on">
                            {l s='Enable IP Whitelist' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">                        
                            <span class="ps-switch ps-switch-lg">
                                <input type="radio" name="whitelist_on" id="whitelist_on_off" 
                                    value="0"{if $configuration.whitelist_on === false} checked{/if}/>
                                <label for="whitelist_on_off">Disabled</label>
                                <input type="radio" name="whitelist_on" id="whitelist_on_on" 
                                    value="1"{if $configuration.whitelist_on === true} checked{/if}/>
                                <label for="whitelist_on_on">Enabled</label>
                                <span class="slide-button"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="whitelist_ip">
                            {l s='IP Whitelist' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control" id="whitelist_ip" name="whitelist_ip" 
                                value="{$configuration.whitelist_ip|escape:'htmlall':'UTF-8'}" />
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