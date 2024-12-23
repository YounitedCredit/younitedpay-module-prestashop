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
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="bridge_mode">
                            {l s='Environment' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">                        
                            <span class="ps-switch ps-switch-lg" id="younitedpay_prod_switch">
                                <input type="radio" name="production_mode" id="production_mode_off" 
                                    value="0"{if $configuration.production_mode === false} checked{/if}/>
                                <label for="production_mode_off">{l s='Test' mod='younitedpay'}</label>
                                <input type="radio" name="production_mode" id="production_mode_on" 
                                    value="1"{if $configuration.production_mode === true} checked{/if}/>
                                <label for="production_mode_on">{l s='Production' mod='younitedpay'}</label>
                                <span class="slide-button"></span>
                            </span>
                            <small class="form-text">
                                {l s='This option defines in whitch environment your module is configured'
                                mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row{if $configuration.production_mode === true} hidden{/if}" data-test-zone>
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_id">
                            {l s='Client ID' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control"
                                placeholder="{l s='Fill in your Client ID' mod='younitedpay'}" id="client_id"
                                name="client_id" value="{$configuration.client_id|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row{if $configuration.production_mode === true} hidden{/if}" data-test-zone>
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_secret">
                            {l s='Client Secret' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control"
                                placeholder="{l s='Fill in your Client secret' mod='younitedpay'}" id="client_secret"
                                name="client_secret" value="{$configuration.client_secret|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row{if $configuration.production_mode === true} hidden{/if}" data-test-zone>
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="webhook_secret">
                            {l s='WebHook Secret' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control"
                                placeholder="{l s='Fill in your WebHook Secret' mod='younitedpay'}" id="webhook_secret"
                                name="webhook_secret" value="{$configuration.webhook_secret|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row{if $configuration.production_mode === false} hidden{/if}" data-prod-zone>
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_id_production">
                            {l s='Client ID' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control"
                                placeholder="{l s='Fill in your Client ID' mod='younitedpay'}" id="client_id_production"
                                name="client_id_production" value="{$configuration.client_id_production|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row{if $configuration.production_mode === false} hidden{/if}" data-prod-zone>
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_secret_production">
                            {l s='Client Secret' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control"
                                placeholder="{l s='Fill in your Client secret' mod='younitedpay'}" id="client_secret_production_production"
                                name="client_secret_production" value="{$configuration.client_secret_production|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row{if $configuration.production_mode === false} hidden{/if}" data-prod-zone>
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="webhook_secret_production">
                            {l s='WebHook Secret' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" class="form-control"
                                placeholder="{l s='Fill in your WebHook Secret' mod='younitedpay'}" id="webhook_secret_production"
                                name="webhook_secret_production" value="{$configuration.webhook_secret_production|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="whitelist_on">
                            {l s='Enable IP Whitelist' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">                        
                            <span class="ps-switch ps-switch-lg disable_on_change" data-input="whitelist_on_on" 
                                data-toggle="ipwebhook">
                                <input type="radio" name="whitelist_on" id="whitelist_on_off" 
                                    value="0"{if $configuration.whitelist_on === false} checked{/if}/>
                                <label for="whitelist_on_off">{l s='Disabled' mod='younitedpay'}</label>
                                <input type="radio" name="whitelist_on" id="whitelist_on_on" 
                                    value="1"{if $configuration.whitelist_on === true} checked{/if}/>
                                <label for="whitelist_on_on">{l s='Enabled' mod='younitedpay'}</label>
                                <span class="slide-button"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="whitelist_ip">
                            {l s='IP Whitelist' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <input type="text" {if $configuration.whitelist_on === false} disabled{/if}
                                class="form-control" data-ipwebhook id="whitelist_ip" name="whitelist_ip" 
                                value="{$configuration.whitelist_ip|escape:'htmlall':'UTF-8'}" />
                            <small class="form-text">
                                {l s='When enabled, only the listed IPs will see the module’s components on the site' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="webhook_orders">
                            {l s='Webhook create orders' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">                        
                            <span class="ps-switch ps-switch-lg">
                                <input type="radio" name="webhook_orders" id="webhook_orders_off" 
                                    value="0"{if $configuration.webhook_orders === false} checked{/if}/>
                                <label for="webhook_orders_off">{l s='Disabled' mod='younitedpay'}</label>
                                <input type="radio" name="webhook_orders" id="webhook_orders_on" 
                                    value="1"{if $configuration.webhook_orders === true} checked{/if}/>
                                <label for="webhook_orders_on">{l s='Enabled' mod='younitedpay'}</label>
                                <span class="slide-button"></span>
                            </span>
                            <small class="form-text">
                                {l s='When enabled, webhook will create orders. Disable this option if you have issues with shipping and prices.' mod='younitedpay'}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-lg btn-primary" type="submit">
                            {l s='Save' d='Admin.Actions' mod='younitedpay'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>