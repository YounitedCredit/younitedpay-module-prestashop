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
<h3 id="younitedpay_status_min"{if $connected == false} style="display:none;"{/if}>
    {l s='Requirements' mod='younitedpay'}
    <i class="material-icons mt-1" style="color:green;position:relative;bottom:3px;">check</i>
</h3>  
<div class="younitedpayApp">
  <div class="row">
    <div class="col-sm-12">
      {include file="./settings-form.tpl" configuration=$configuration}

      {include file="./states-form.tpl" configuration=$configuration}

      {include file="./appearence.tpl" configuration=$configuration}
    </div>
  </div>
</div>