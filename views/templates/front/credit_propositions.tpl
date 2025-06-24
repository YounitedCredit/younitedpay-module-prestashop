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
<div class="younitedpay_product_info" data-hook="{$hook_younited|escape:'htmlall':'UTF-8'}" {if $hook_younited == 'widget'}data-widget{/if}>   
<div class="younitedpay-widget-root">
   {if count($offers) > 0}
   <div class="younited_block yp-pb-0 yp-pt-2 yp-my-2{if isset($widget_borders) && (bool) $widget_borders === true} widget_border{/if}">
      <div class="yp-cursor-pointer yp-flex yp-flex-row yp-items-center yp-flex-wrap">
         <img class="yp-mb-2 yp-logo lazyloaded" src="{$shop_url|escape:'htmlall':'UTF-8'}{$logo_younitedpay_url|escape:'htmlall':'UTF-8'}" alt="logo Younited Pay" data-ll-status="loaded">
         {foreach from=$offers item=offer key=key}
            {assign var="background_block" value=''}
            {if (int) $key === (int) $selected_offer}
               {assign var="background_block" value='yp-bg-black-btn'}
            {/if}
            <span class="yp-flex yp-flex-row yp-space-x-1 yp-mx-1 yp-mb-1 maturity_installment maturity_installment{$key|escape:'htmlall':'UTF-8'} {$background_block|escape:'htmlall':'UTF-8'}"
                  data-key="{$key|escape:'htmlall':'UTF-8'}" 
                  data-amount="{$offer.installment_amount|escape:'htmlall':'UTF-8'}" 
                  data-totalamount="{$offer.total_amount|escape:'htmlall':'UTF-8'}" 
                  data-interesttotal="{$offer.interest_total|escape:'htmlall':'UTF-8'}" 
                  data-initamount="{$offer.initial_amount|escape:'htmlall':'UTF-8'}" 
                  data-taeg="{$offer.taeg|escape:'htmlall':'UTF-8'}" 
                  data-tdf="{$offer.tdf|escape:'htmlall':'UTF-8'}" 
                  data-maturity="{$offer.maturity|escape:'htmlall':'UTF-8'}">
               <span class="yp-inline-block yp-h-10">
                  <span class="yp-inline-block yp-transition-all yp-border-opacity-100 yp-h-10 
                     blocks_maturity block_maturity{$key|escape:'htmlall':'UTF-8'} yp-flex flexmiddle ">
                     <span class="yp-flex flexmiddle yp-p-2 yp-rounded-sm yp-transition-colors 
                        yp-duration-500 yp-select-none">
                     {$offer.maturity|escape:'htmlall':'UTF-8'}x
                     </span>
                  </span>
               </span>
            </span> 
         {/foreach}
         {if (bool) $show_ranges === true && empty($range_offers) === false}
            <span class="yp-flex yp-flex-row yp-space-x-1 yp-mx-1 yp-mb-1 maturity_installment maturity_installment9999"
                  data-key="9999" 
                  data-amount="{$offer.installment_amount|escape:'htmlall':'UTF-8'}" 
                  data-totalamount="{$offer.total_amount|escape:'htmlall':'UTF-8'}" 
                  data-initamount="{$offer.initial_amount|escape:'htmlall':'UTF-8'}" 
                  data-interesttotal="{$offer.interest_total|escape:'htmlall':'UTF-8'}" 
                  data-taeg="{$offer.taeg|escape:'htmlall':'UTF-8'}" 
                  data-tdf="{$offer.tdf|escape:'htmlall':'UTF-8'}" 
                  data-maturity="{$offer.maturity|escape:'htmlall':'UTF-8'}"
                  title="{l s='Customise' mod='younitedpay'}">
               <span class="yp-inline-block yp-h-10">
                  <span class="yp-inline-block yp-transition-all yp-border-opacity-100 yp-h-10 
                     blocks_maturity block_maturity{$key|escape:'htmlall':'UTF-8'} yp-flex flexmiddle ">
                     <span class="yp-flex flexmiddle yp-p-2 yp-rounded-sm yp-transition-colors 
                        yp-duration-500 yp-select-none">{l s='...' mod='younitedpay'}</span>
                  </span>
               </span>
            </span>
         {/if}

      {assign var=offer_max_line value=4}
      {if $hook_younited == 'displayAfterProductThumbs'}
         {assign var=offer_max_line value=3}
      {/if}

      {assign var=price_at_bottom value=false}
      {if count($offers) >= $offer_max_line && $hook_younited != 'widget'}
         {assign var=price_at_bottom value=true}
      {/if}

      {if $price_at_bottom == true}
      </div>
      <div class="yp-cursor-pointer yp-flex yp-flex-row yp-items-center yp-flex-wrap yp-text-xs yp-p-2 yp-pb-0">
         <hr class="yp-border-prple yp-opacity-50">
      {/if}            
         <span class="yp-justify-start yp-flex-row yp-space-x-1 yp-mx-2 yp-mb-1{if $price_at_bottom == true} yp-price-block{else} yp-pl-3{/if}">
            <span class="yp-h-7 yp-flex yp-items-center"{if $price_at_bottom == true} style="padding-right:2rem!important;"{/if}>
               <span class="younitedpay_infoinstallment yp-install-amount">
                  {$offers[$selected_offer].installment_amount|escape:'htmlall':'UTF-8'} €
               </span>&nbsp;
               <span>/</span>&nbsp;
               <span>{l s='months' mod='younitedpay'}</span>
         </span>
      </div>
   </div>
   <div id="younited_popupzone"
      class="yp-inset-0 yp-flex yp-fixed yp-justify-center yp-items-center yp-z-100 yp-bg-black yp-bg-opacity-50" style="display:none!important;">
      <div
         class="yp-overflow-auto yp-relative yp-flex yp-flex-row yp-flex-wrap yp-rounded-sm yp-shadow-lg yp-w-screen yp-h-screen md:yp-h-580 lg:yp-w-11/12 md:yp-max-w-4xl yp-max-h-screen yp-bg-white">
         <button class="younited_btnhide yp-opacity-100 yp-absolute yp-top-3 yp-right-4 yp-outline-none close-popup-you">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="#1C1C1C" class="yp-close-black" xmlns="http://www.w3.org/2000/svg">
               <path d="M8.4 7L13.7 1.7C14.1 1.3 14.1 0.7 13.7 0.3C13.3 -0.1 12.7 -0.1 12.3 0.3L7 5.6L1.7 0.3C1.3 -0.1 0.7 -0.1 0.3 0.3C-0.1 0.7 -0.1 1.3 0.3 1.7L5.6 7L0.3 12.3C-0.1 12.7 -0.1 13.3 0.3 13.7C0.5 13.9 0.7 14 1 14C1.3 14 1.5 13.9 1.7 13.7L7 8.4L12.3 13.7C12.5 13.9 12.8 14 13 14C13.2 14 13.5 13.9 13.7 13.7C14.1 13.3 14.1 12.7 13.7 12.3L8.4 7Z" />
            </svg>
         </button>
         <div class="yp-h-full yp-w-full md:yp-w-2/5 yp-border-lprple yp-bg-purple-light">
            <div class="yp-left-title-text yp-font-family-rg">
               <span class="yp-h3">{l s='Buy now and pay as you go' mod='younitedpay'}</span>
            </div>
            <div class="yp-mt-7 yp-font-normal">
                <span class="yp-step">
                    <span class="yp-linumber">1</span>
                    <span>
                        {l s='At checkout step, select ' mod='younitedpay'}
                        <b>{l s='Younited Pay' mod='younitedpay'}</b>
                        {if $iso_code == 'es'}
                           {l s=' como método de pago' mod='younitedpay'}
                        {/if}
                     </span>
                </span>
                <span class="yp-step">
                    <span class="yp-linumber">2</span>
                    <span>
                        {if $iso_code == 'fr'}
                           {l s='Choisissez la ' mod='younitedpay'}
                           <b>{l s='durée' mod='younitedpay'}</b>
                           {l s=' de remboursement' mod='younitedpay'}
                        {else}
                           {l s='Choose the repayment ' mod='younitedpay'}
                           <b>{l s='duration' mod='younitedpay'}</b>
                        {/if}
                     </span>
                </span>
                <span class="yp-step">
                    <span class="yp-linumber">3</span>
                    <span>
                        {if $iso_code == 'fr'}
                           {l s='Connectez ' mod='younitedpay'}
                        {/if}
                        <b>{l s='Simply' mod='younitedpay'}</b>
                        {l s=' and ' mod='younitedpay'}
                        <b>{l s='securely' mod='younitedpay'}</b>
                        {l s=' connect your bank account' mod='younitedpay'}
                     </span>
                </span>
                <span class="yp-step">
                    <span class="yp-linumber">4</span>
                    <span>
                        {l s='Receive a response ' mod='younitedpay'}
                        <b>{l s='within seconds' mod='younitedpay'}</b>
                    </span>
                </span>
            </div>
            <p class="yp-mt-6">
               <span class="yp-inline-block yp-h-10"></span>
            </p>
            <span class="yp-flex yp-popup-logo yp-pl-2">
               <img class="lazyloaded" src="{$shop_url|escape:'htmlall':'UTF-8'}{$logo_younitedpay_url|escape:'htmlall':'UTF-8'}" alt="youpay"
                  data-ll-status="loaded" />
               <noscript>
                  <img src="{$shop_url|escape:'htmlall':'UTF-8'}{$logo_younitedpay_url|escape:'htmlall':'UTF-8'}" alt="youpay">
               </noscript>
            </span>
         </div>
         <div class="yp-right yp-p-6 yp-h-full yp-w-full md:yp-w-3/5 yp-pb-24 md:yp-pb-8 md:yp-pt-8 yp-flex yp-flex-col yp-align-center yp-bg-beige">
            <div class="yp-flex-grow">

               {include file="module:younitedpay/views/templates/front/credit_informations.tpl"}

               <div class="yp-flex yp-justify-end">
                  <button class="btn younited_btnhide yp-opacity-100 yp-mt-3 yp-right-4">
                     {l s='Close' mod='younitedpay'}
                  </button>
               </div>
            </div>
         </div>
      </div>
   </div>
   {/if}
</div>
</div>