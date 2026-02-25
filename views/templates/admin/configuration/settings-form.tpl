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
                    <div class="form-group mt-4 row{if $configuration.production_mode === true} hidden{/if}">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="country_code">
                            {l s='Country Code' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-start">
                            <select class="form-control" placeholder="{l s='Fill in your Shop Code' mod='younitedpay'}"
                                    name="country_code" id="country_code">
                                {foreach from=$configuration.available_countries item='available_country'}
                                    <option value="{$available_country|escape:'htmlall':'UTF-8'}"
                                            {if $available_country == $configuration.country_code} selected{/if}>
                                        {strtoupper($available_country)|escape:'htmlall':'UTF-8'}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    {foreach from=$configuration.available_countries item='available_country'}
                        <div class="form-group mt-2 row" data-country-zone-{$available_country}>
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="younitedpay_prod_switch_{$available_country}">
                                {l s='Environment' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-center">
                                    <span class="ps-switch ps-switch-lg" id="younitedpay_prod_switch_{$available_country}">
                                        <input type="radio" name="production_mode_{$available_country}" id="production_mode_off_{$available_country}"
                                               value="0"{if $configuration.production_mode[$available_country] === false} checked{/if}/>
                                        <label for="production_mode_off_{$available_country}">{l s='Test' mod='younitedpay'}</label>
                                        <input type="radio" name="production_mode_{$available_country}" id="production_mode_on_{$available_country}"
                                               value="1"{if $configuration.production_mode[$available_country] === true} checked{/if}/>
                                        <label for="production_mode_on_{$available_country}">{l s='Production' mod='younitedpay'}</label>
                                        <span class="slide-button"></span>
                                    </span>
                                <small class="form-text">
                                    {l s='This option defines in whitch environment your module is configured'
                                    mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                        <div class="form-group mt-2 row{if $configuration.production_mode[$available_country] === true} hidden{/if}" data-test-zone-{$available_country} data-country-zone-{$available_country}>
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_id_{$available_country}">
                                {l s='Client ID' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-center">
                                <input type="text" class="form-control"
                                       placeholder="{l s='Fill in your Client ID' mod='younitedpay'}" id="client_id_{$available_country}"
                                       name="client_id_{$available_country}" value="{$configuration.client_id[$available_country]|escape:'htmlall':'UTF-8'}" />
                                <small class="form-text">
                                    {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                        <div class="form-group mt-2 row{if $configuration.production_mode[$available_country] === true} hidden{/if}" data-test-zone-{$available_country} data-country-zone-{$available_country}>
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_secret_{$available_country}">
                                {l s='Client Secret' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-center">
                                <input type="text" class="form-control"
                                       placeholder="{l s='Fill in your Client secret' mod='younitedpay'}" id="client_secret_{$available_country}"
                                       name="client_secret_{$available_country}" value="{$configuration.client_secret[$available_country]|escape:'htmlall':'UTF-8'}" />
                                <small class="form-text">
                                    {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                        <div class="form-group mt-2 row{if $configuration.production_mode[$available_country] === true} hidden{/if}" data-test-zone-{$available_country} data-country-zone-{$available_country}>
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="shop_code_{$available_country}">
                                {l s='Shop Code' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-start">
                                {if false === empty($configuration.shop_codes_list[$available_country])}
                                    <select class="form-control" placeholder="{l s='Fill in your Shop Code' mod='younitedpay'}"
                                            name="shop_code_{$available_country}" id="shop_code_{$available_country}">
                                        {foreach from=$configuration.shop_codes_list[$available_country] item='shop_code_name'}
                                            {if empty($shop_code_name.code) === false}
                                                <option value="{$shop_code_name.code|escape:'htmlall':'UTF-8'}"
                                                        {if $shop_code_name.code == $configuration.shop_code[$available_country]} selected{/if}>
                                                    {$shop_code_name.name|escape:'htmlall':'UTF-8'} ({$shop_code_name.code|escape:'htmlall':'UTF-8'})
                                                </option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                {else}
                                    <small class="form-text">
                                        {l s='Please save your configuration to update this section.' mod='younitedpay'}
                                    </small>
                                {/if}
                            </div>
                        </div>
                        <div class="form-group mt-2 row{if $configuration.production_mode[$available_country] === true} hidden{/if}" data-test-zone-{$available_country} data-country-zone-{$available_country}>
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="webhook_secret_{$available_country}">
                                {l s='WebHook Secret' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-center">
                                <input type="text" class="form-control"
                                       placeholder="{l s='Fill in your WebHook Secret' mod='younitedpay'}" id="webhook_secret_{$available_country}"
                                       name="webhook_secret_{$available_country}" value="{$configuration.webhook_secret[$available_country]|escape:'htmlall':'UTF-8'}" />
                                <small class="form-text">
                                    {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                        <div class="form-group mt-2 row{if $configuration.production_mode[$available_country] === false} hidden{/if}" data-prod-zone-{$available_country} data-country-zone-{$available_country}>
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_id_production_{$available_country}">
                                {l s='Client ID' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-center">
                                <input type="text" class="form-control"
                                       placeholder="{l s='Fill in your Client ID' mod='younitedpay'}" id="client_id_production_{$available_country}"
                                       name="client_id_production_{$available_country}" value="{$configuration.client_id_production[$available_country]|escape:'htmlall':'UTF-8'}" />
                                <small class="form-text">
                                    {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                        <div class="form-group mt-2 row{if $configuration.production_mode[$available_country] === false} hidden{/if}" data-prod-zone-{$available_country} data-country-zone-{$available_country}>
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="client_secret_production_{$available_country}">
                                {l s='Client Secret' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-center">
                                <input type="text" class="form-control"
                                       placeholder="{l s='Fill in your Client secret' mod='younitedpay'}" id="client_secret_production_{$available_country}"
                                       name="client_secret_production_{$available_country}" value="{$configuration.client_secret_production[$available_country]|escape:'htmlall':'UTF-8'}" />
                                <small class="form-text">
                                    {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                        <div class="form-group mt-2 row{if $configuration.production_mode[$available_country] === false} hidden{/if}" data-prod-zone-{$available_country} data-country-zone-{$available_country}>
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="shop_code_production_{$available_country}">
                                {l s='Shop Code' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-start">
                                {if false === empty($configuration.shop_codes_list[$available_country])}
                                    <select class="form-control" placeholder="{l s='Fill in your Shop Code' mod='younitedpay'}"
                                            id="shop_code_production_{$available_country}" name="shop_code_production_{$available_country}">
                                        {foreach from=$configuration.shop_codes_list[$available_country] item='shop_code_name'}
                                            {if empty($shop_code_name.code) === false}
                                                <option value="{$shop_code_name.code|escape:'htmlall':'UTF-8'}"
                                                        {if $shop_code_name.code == $configuration.shop_code_production[$available_country]} selected{/if}>
                                                    {$shop_code_name.name|escape:'htmlall':'UTF-8'} ({$shop_code_name.code|escape:'htmlall':'UTF-8'})
                                                </option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                {else}
                                    <small class="form-text">
                                        {l s='Please save your configuration to update this section.' mod='younitedpay'}
                                    </small>
                                {/if}
                            </div>
                        </div>
                        <div class="form-group mt-2 row{if $configuration.production_mode[$available_country] === false} hidden{/if}" data-prod-zone-{$available_country} data-country-zone-{$available_country}>
                            <label class="form-control-label col-lg-3 justify-content-end pt-1" for="webhook_secret_production_{$available_country}">
                                {l s='WebHook Secret' mod='younitedpay'}
                            </label>
                            <div class="col-lg-4 align-item-center">
                                <input type="text" class="form-control"
                                       placeholder="{l s='Fill in your WebHook Secret' mod='younitedpay'}" id="webhook_secret_production_{$available_country}"
                                       name="webhook_secret_production_{$available_country}" value="{$configuration.webhook_secret_production[$available_country]|escape:'htmlall':'UTF-8'}" />
                                <small class="form-text">
                                    {l s='This information is located in your dashboard: \'Settings\' > \'General settings\'' mod='younitedpay'}
                                </small>
                            </div>
                        </div>
                    {/foreach}
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
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="widget_info">
                            {l s='Test Webhook Integration' mod='younitedpay'}
                        </label>
                        <div class="col-lg-6 align-item-center input-group">
                            <input type="text" disabled class="form-control
                                {if $configuration.show_monthly === 0}
                                    widget_disabled
                                {else}
                                    widget_enabled
                                {/if}"
                                   style="border-right:none;font-weight:bold;" data-month id="widget_input"
                                   value="{$configuration.webhook_url|escape:'htmlall':'UTF-8'}"
                                   title="{$configuration.webhook_url|escape:'htmlall':'UTF-8'}" />
                            <div class="input-group-append copy-clipboard"
                                 data-message="{l s='Widget content copied to clipboard' mod='younitedpay'}"
                                 data-clipboard-copy="{$configuration.webhook_url|escape:'htmlall':'UTF-8'}">
                                <span class="input-group-text" style="border-left:none;"
                                      title="{l s='Copy to clipboard' mod='younitedpay'}">
                                    <i class="material-icons input-group-text btn"
                                       style="font-size:20px!important;color:#25B9D7!important;border:none;">content_paste</i>
                                </span>
                            </div>
                            <div class="input-group-append">
                                <span class="input-group-text"
                                      data-test-url="{$configuration.webhook_url|escape:'htmlall':'UTF-8'}"
                                      data-test-webhook style="border-left:none;"
                                      title="{l s='Test the notification' mod='younitedpay'}">
                                    <i class="material-icons input-group-text btn"
                                       style="font-size:20px!important;color:#25B9D7!important;border:none;">webhook</i>
                                </span>
                                <span class="input-group-text" data-test-webhook-result style="border-left:none;display:none;"
                                      title="{l s='Test the notification' mod='younitedpay'}">
                                    <i class="material-icons input-group-text btn"
                                       style="font-size:20px!important;color:#25B9D7!important;border:none;">error</i>
                                </span>
                            </div>
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
