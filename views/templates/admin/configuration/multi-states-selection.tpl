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
<div class="row double-list-group">
    <div class="col-xl-6 double-list-left-container d-flex flex-wrap">
        <div class="bold col col-lg-12 justify-content-center d-flex">
            <label>{l s='Available statuses' mod='younitedpay'}</label>
        </div>
        <select id="delivered_status_add" class="double-list double-list-unselected col-lg-12" multiple>
            {foreach from=$input.unselected item='option'}
                <option value="{$option.value|escape:'htmlall':'UTF-8'}">&nbsp;{$option.label|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
        </select>
        <button id="delivered_status_add_btn" class="double-list-btn-select btn btn-lg btn-outline-primary col-lg-12 mt-2">
            {l s='Add' mod='younitedpay'} <i class="icon-arrow-right"></i>
        </button>
    </div>

    <div class="col-xl-6 double-list-right-container d-flex flex-wrap">
        <div class="bold col col-lg-12 justify-content-center d-flex">
            <label>{l s='Selected' mod='younitedpay'}</label>
        </div>
        <select id="delivered_status_remove" class="double-list double-list-selected col-lg-12" multiple>
            {foreach from=$input.selected item='option'}
                <option value="{$option.value|escape:'htmlall':'UTF-8'}">&nbsp;{$option.label|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
        </select>
        <button id="delivered_status_remove_btn" class="double-list-btn-unselect btn btn-lg btn-outline-primary col-lg-12 mt-2">
            <i class="icon-arrow-left"></i> {l s='Remove' mod='younitedpay'}
        </button>
    </div>

    <div class="double-list-values hidden">
        {foreach from=$input.unselected item='option'}
            <input type="checkbox" name="delivered_status[]" value="{$option.value|escape:'htmlall':'UTF-8'}"/>
        {/foreach}
        {foreach from=$input.selected item='option'}
            <input type="checkbox" name="delivered_status[]" value="{$option.value|escape:'htmlall':'UTF-8'}" checked/>
        {/foreach}
    </div>
</div>