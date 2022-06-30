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
<div class="younitedpayApp mb-2{if $alert == false} mt-5{/if}">
  <div class="row">
    <div class="col-sm-12 pr-5 pl-5">
      <div class="row justify-content-center">
        <div class="col-sm-4 d-flex">
            <div class="card col-sm-12 pl-2 pr-2 d-flex flex-wrap flex-row">
              <div class="card-block justify-content-start align-items-start pb-2 d-flex flex-wrap">  
                <h3 class="row col-xl-12 justify-content-center mt-3">
                  {if $connected == false}
                    {l s='Create your account' mod='younitedpay'}
                  {else}
                    {l s='Need Help ?' mod='younitedpay'}
                  {/if}
                </h3>
                {if $connected == false}
                  <p class="row col-xl-12">
                    {l s='Create your account in order to have access to your YounitedPay Back Office, connect with our sales team and start setting up the offer displayed to your customers.' mod='younitedpay'}<br />
                    </p>                    
                {/if}
                <p class="row col-xl-12">
                    {l s='Have a question about' mod='younitedpay'}&nbsp;
                  <a href="mailto:contact@younited-pay.fr" class="link">
                    {l s='Younited Pay' mod='younitedpay'}
                  </a>&nbsp;?
                </p>
                <p class="row col-xl-12">
                  {l s='You can reach a technical team or your account manager from your back office via our ticketing system.' mod='younitedpay'}&nbsp;
                </p>
                <p class="row col-xl-12" style="display:inline;">
                  {l s='If your question concerns technical difficulties with the module, please refer to ' mod='younitedpay'}
                  <a class="link" href="https://addons.prestashop.com/contact-form.php?id_product=88719" target="_blank">
                    {l s='our support team' mod='younitedpay'}
                  </a>
                </p>
                <div class="col-xl-12 d-flex flex-wrap justify-content-end align-items-end bootstrap">
                    <a class="btn btn-lg btn-primary" target="_blank" href="{$configuration.link_help|escape:'htmlall':'UTF-8'}">
                        {l s='More informations' mod='younitedpay'}
                    </a>
                </div>
              </div>
            </div>
          </div>
        <div class="col-sm-4 d-flex">
          <img class="col-xl-12" src="{$shop_img_url|escape:'htmlall':'UTF-8'}/image-marketing-{$configuration.iso_lang|escape:'htmlall':'UTF-8'}.png" alt="marketting image" />
        </div>
        <div class="col-sm-4 d-flex" {if $connected == true}style="display:none!important" {/if}id="younitedpay_status_block">
          <div class="card col-xl-12 pl-2 pr-2 d-flex flex-wrap flex-row" id="younitedpay_status_zone">
            <h3 class="mt-2 row col-xl-12">
              <div class="col-xl-11">
                {l s='Module Requirements' mod='younitedpay'}
              </div>
              <div class="col-xl-1">
                  <i class="material-icons mt-1" id="hide_requirements">close</i>
              </div>
            </h3>
            <div class="row col-xl-12 ml-2 mb-1">
              {l s='To insure your module works accurately , please correct the following technical requirements ' mod='younitedpay'}
            </div>
            <div class="card-block justify-content-start align-items-start">
              {foreach from=$specifications item=spec}
                <div class="row col-xl-12 ml-2 mb-1">
                  {if $spec.ok == true}
                      <i class="material-icons mt-1" style="color:green;">check</i>
                  {else}
                      <i class="material-icons mt-1" style="color:red;">close</i>
                  {/if}
                  <span class="pt-1 inline"
                  {if empty($spec.title) != true}
                    title="{foreach from=$spec.title item=bank}{$bank|escape:'htmlall':'UTF-8'}{/foreach}" 
                  {/if}
                  >{$spec.name|escape:'htmlall':'UTF-8'}{if $spec.info != ''} - {/if}{$spec.info|escape:'htmlall':'UTF-8'}</span>
                </div>
              {/foreach}
            </div> {* card-block *}
          </div> {* card *}
        </div> {* col-sm4 *}
      </div> {* row-justif *}
    </div> {* sm-12 *}
  </div> {* row *}  
</div>      