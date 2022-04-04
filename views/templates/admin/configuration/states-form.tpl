{**
* Copyright Bridge
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
* @author 202 ecommerce <tech@202-ecommerce.com>
* @copyright Bridge
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
*}
<form id="states_form" method="POST" class="defaultForm form-horizontal mt-2"
    action="{$configuration.url_form_config|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="states_submit" value="1" />
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-sm-11">{l s='General' mod='younitedpay'}</div>
                </div>
                <div class="form-wrapper justify-content-center">
                    <div class="form-group mt-2 row">
                        <label class="form-control-label col-lg-3 justify-content-end pt-1" for="delivered_status">
                            {l s='Credit trigger status' mod='younitedpay'}
                        </label>
                        <div class="col-lg-4 align-item-center">
                            <select name="delivered_status">
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
                </div>
                <div class="panel-footer">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-lg btn-primary" type="submit">
                            {l s='Save' mod='younitedpay'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
</form>