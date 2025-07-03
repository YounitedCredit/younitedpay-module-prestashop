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

<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i>{l s='Contract' mod='younitedpay'}
    </div>
    <div class="panel-body">
        <p>
            <img style="height:45px;" src="{$shop_url|escape:'htmlall':'UTF-8'}{$logo_younitedpay_url|escape:'htmlall':'UTF-8'}" alt="logo Younited Pay" />
        </p>
        <h2>{l s='Contract informations in database (PrestaShop)' mod='younitedpay'}</h2>
        {include file='../hook/displayAdminOrderContentOrder.tpl'}
        <div style="display:none">{$contract|escape:'htmlall':'UTF-8'|replace:"\n":'<br/>'}</div>
        <h2>{l s='API Response (Younited)' mod='younitedpay'}</h2>
        <div>
            {if $api != false && $api != 'false'}
                {$api|escape:'htmlall':'UTF-8'|replace:"\n":'<br/>'}
            {else}
                {l s='Error with API response - please check that the credentials used to create the order are set correctly.' mod='younitedpay'}<br />
                {l s='For example, if you have registered this command with a Sandbox account and changed the environment to Production.' mod='younitedpay'}
            {/if}
        </div>
        <a onclick="history.back();" class="btn btn-primary pull-right">{l s='Back' mod='younitedpay'}</a>
    </div>
</div>