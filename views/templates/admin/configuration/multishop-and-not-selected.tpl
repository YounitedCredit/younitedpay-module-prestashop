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
<div id="younitedpayApp" class="pt-5">
  <div class="row justify-content-center">
    <div class="col-sm-6">
        <div class="row justify-content-center">
          <div class="col-xl-10">
            <div class="card">
              <div class="card-header">
                <i class="material-icons">error_outline</i>
                  {l s='Unavaliable' mod='younitedpay'}
              </div>
              <div class="card-block row justify-content-center">
                <div class="col-10 mb-3 mt-3">
                  <div class="card-text row justify-content-center">
                    <h4>
                    {l s='To configure this module you need to select a sub shop' mod='younitedpay'}
                    </h4>
                    <img src="{$younitedpay_imgfile|escape:'htmlall':'UTF-8'}" alt="Multishop - shop not selected" />
                  </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>