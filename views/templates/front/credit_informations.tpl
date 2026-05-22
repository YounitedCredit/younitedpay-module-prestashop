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
{if isset($yperror)}
<div class="younitedpay-widget-root">
{/if}
{assign var="offerWithDownPayment" value=false}
{if (int) $offers[$selected_offer].down_payment_amount > 0}
   {assign var="offerWithDownPayment" value=true}
{/if}
<div class="yp-text-md yp-mt-4 yp-mb-5 yp-h4">
    <span>{l s='Choose the number of monthly payments' mod='younitedpay'}</span>
</div>
<div class="yp-flex yp-justify-between yp-mt-6">
{foreach from=$offers item=offer key=key}
   {if $key >= 5}{break}{/if}
   {assign var="background_block" value=''}
   {if (int) $key === (int) $selected_offer}
      {assign var="background_block" value='yp-bg-black-btn'}
   {/if}
   <span class="yp-flex yp-flex-row yp-border-light yp-border yp-space-x-1 yp-mx-2 yp-mb-1 maturity_installment maturity_installment{$key|escape:'htmlall':'UTF-8'} {$background_block|escape:'htmlall':'UTF-8'}"
         data-key="{$key|escape:'htmlall':'UTF-8'}"
         data-amount="{$offer.installment_amount|escape:'htmlall':'UTF-8'}"
         data-totalamount="{$offer.total_amount|escape:'htmlall':'UTF-8'}"
         data-interesttotal="{$offer.interest_total|escape:'htmlall':'UTF-8'}"
         data-feetotal="{$offer.fee_total|escape:'htmlall':'UTF-8'}"
         data-initamount="{$offer.initial_amount|escape:'htmlall':'UTF-8'}"
         data-downpayment="{$offer.down_payment_amount|escape:'htmlall':'UTF-8'}"
         data-taeg="{$offer.taeg|escape:'htmlall':'UTF-8'}"
         data-tdf="{$offer.tdf|escape:'htmlall':'UTF-8'}"
         data-type="{$offer.type|escape:'htmlall':'UTF-8'}"
         data-maturity="{$offer.maturity|escape:'htmlall':'UTF-8'}"
         data-installment="{json_encode($offer.installment)|escape:'htmlall':'UTF-8'}">
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
   <span class="yp-flex yp-flex-row yp-border-light yp-border yp-space-x-1 yp-mx-2 yp-mb-1 maturity_installment maturity_installment9999"
         data-key="9999"
         data-amount="{$offer.installment_amount|escape:'htmlall':'UTF-8'}"
         data-totalamount="{$offer.total_amount|escape:'htmlall':'UTF-8'}"
         data-initamount="{$offer.initial_amount|escape:'htmlall':'UTF-8'}"
         data-interesttotal="{$offer.interest_total|escape:'htmlall':'UTF-8'}"
         data-feetotal="{$offer.fee_total|escape:'htmlall':'UTF-8'}"
         data-downpayment="{$offer.down_payment_amount|escape:'htmlall':'UTF-8'}"
         data-taeg="{$offer.taeg|escape:'htmlall':'UTF-8'}"
         data-tdf="{$offer.tdf|escape:'htmlall':'UTF-8'}"
         data-type="{$offer.type|escape:'htmlall':'UTF-8'}"
         data-maturity="{$offer.maturity|escape:'htmlall':'UTF-8'}"
         data-installment="{json_encode($offer.installment)|escape:'htmlall':'UTF-8'}"
         title="{l s='Customise' mod='younitedpay'}">
      <span class="yp-inline-block yp-h-10" style="width:auto;">
         <span class="yp-inline-block yp-transition-all yp-border-opacity-100 yp-h-10
            blocks_maturity block_maturity{$key|escape:'htmlall':'UTF-8'} yp-flex flexmiddle ">
            <span class="yp-flex flexmiddle yp-p-2 yp-rounded-sm yp-transition-colors
               yp-duration-500 yp-select-none">
               <img class="yp-logo-range" src="{$shop_url|escape:'htmlall':'UTF-8'}{$logo_slider_black_url|escape:'htmlall':'UTF-8'}" alt="Slider icon"/>
            </span>
         </span>
      </span>
   </span>
{/if}
</div>

<div class="yp-range-slider yp-mt-6">
{if (bool) $show_ranges === true && empty($range_offers) === false}
   <div class="yp-flex yp-items-center">
      <button class="yp-mobile yp-minus yp-mr-3">-</button>
      <input type="range" class="yp-custom-range yp-flex-grow yp-mr-3"
            min="{$min_install|escape:'htmlall':'UTF-8'}" max="{$max_install|escape:'htmlall':'UTF-8'}"
            step="1" value="{$offers[$selected_offer].maturity|escape:'htmlall':'UTF-8'}">
      <button class="yp-mobile yp-plus">+</button>
   </div>
{/if}
</div>

<div class="yp-info-buy yp-flex yp-font-family-rg yp-mt-6 yp-mb-6 yp-justify-between yp-items-center yp-not-down-amount-parent {if $offers[$selected_offer].down_payment_amount > 0}hidden{/if}">
   <p class="yp-pol-purpledark yp-text-lg yp-leading-relaxed">
      <span>{l s='Your purchase in' mod='younitedpay'}</span>
      <span class="yp-maturity">{$offers[$selected_offer].maturity|escape:'htmlall':'UTF-8'}</span>
      <span class="yp-without-fee-text {if $offers[$selected_offer].fee_total == 0}hidden{/if}">{l s='times without fees, for' mod='younitedpay'}</span>
      <span class="yp-with-fee-text {if $offers[$selected_offer].fee_total > 0}hidden{/if}">{l s='times, for' mod='younitedpay'}</span>
      <b class="yp-install-amount">{$offers[$selected_offer].installment_amount|escape:'htmlall':'UTF-8'} €</b>
      <span>{l s='by month.' mod='younitedpay'}</span>
   </p>
</div>

<div class="yp-info-buy yp-flex yp-font-family-rg yp-mt-6 yp-mb-6 yp-justify-between yp-items-center yp-down-amount-parent {if $offers[$selected_offer].down_payment_amount <= 0}hidden{/if}">
   <p class="yp-pol-purpledark yp-text-lg yp-leading-relaxed">
      <b><span class="yp-down-amount">{$offers[$selected_offer].down_payment_amount|escape:'htmlall':'UTF-8'}</span>&nbsp;€</b>
      <b>{l s='today,' mod='younitedpay'}</b>
      <span>{l s='then' mod='younitedpay'}</span>
      <span class="yp-install-maturity-lite">{$offers[$selected_offer].maturity|escape:'htmlall':'UTF-8'}</span>
      <span>{l s='payments of' mod='younitedpay'}</span>
      <b class="yp-install-amount">{$offers[$selected_offer].installment_amount|escape:'htmlall':'UTF-8'} €</b>
      <span>{l s='each month.' mod='younitedpay'}</span>
   </p>
</div>

<div class="yp-flex yp-border-black yp-pol-purpledark yp-pl-4 yp-split-payment-info {if $offers[$selected_offer].type !== 'SplitPayment'}hidden{/if}">
   <div class="yp-logo-step yp-timeline">
      <div class="yp-timeline__item" data-splitpaymentmaturity="1">
         <div class="yp-timeline__circle yp-timeline__circle--active"></div>
      </div>
      <div class="yp-timeline__item" data-splitpaymentmaturity="2">
         <div class="yp-timeline__circle"></div>
      </div>
      <div class="yp-timeline__item" data-splitpaymentmaturity="3">
         <div class="yp-timeline__circle"></div>
      </div>
      <div class="yp-timeline__item" data-splitpaymentmaturity="4">
         <div class="yp-timeline__circle"></div>
      </div>
   </div>
   <div class="yp-flex yp-flex-col yp-flex-grow yp-text-md">
      <div class="yp-flex yp-font-family-rg yp-weight600 yp-pl-4 yp-p-1 yp-mb-4 yp-justify-between yp-split-payment-installment" data-splitpaymentmaturity="1">
         <span class="yp-due-date-0"></span>
         <span class="yp-mw85 yp-text-right yp-install-0 yp-pr-4"></span>
      </div>
      <div class="yp-flex yp-font-family-rg yp-pl-4 yp-p-1 yp-mb-4 yp-justify-between yp-split-payment-installment" data-splitpaymentmaturity="2">
         <span class="yp-due-date-1"></span>
         <span class="yp-mw85 yp-text-right yp-install-1 yp-pr-4"></span>
      </div>
      <div class="yp-flex yp-font-family-rg yp-pl-4 yp-p-1 yp-mb-4 yp-justify-between yp-split-payment-installment" data-splitpaymentmaturity="3">
         <span class="yp-due-date-2"></span>
         <span class="yp-mw85 yp-text-right yp-install-2 yp-pr-4"></span>
      </div>
      <div class="yp-flex yp-font-family-rg yp-pl-4 yp-p-1 yp-justify-between yp-split-payment-installment" data-splitpaymentmaturity="4">
         <span class="yp-due-date-3"></span>
         <span class="yp-mw85 yp-text-right yp-install-3 yp-pr-4"></span>
      </div>
   </div>
</div>

{assign var="offer" value=$offers[$selected_offer]}
<div class="yp-border-black yp-border yp-bg-purple-light yp-p-3 yp-rounded-bg">
   <div class="yp-mb-5 yp-text-lg yp-pol-purpledark">
      <div class="yp-flex yp-flex-row yp-justify-between yp-weight600 yp-pol-purpledark yp-mb-2 yp-text-20">
         <span><b>{l s='Total' mod='younitedpay'}</b></span>
         <span class="yp-mw85 yp-text-right">
            <span class="yp-total">{$offer.total_amount|escape:'htmlall':'UTF-8'}</span>&nbsp;€
         </span>
      </div>
      <div class="yp-flex yp-flex-row yp-justify-between yp-pol-purpledark yp-mb-2 yp-text-sm yp-fees-text">
         <span>{l s='Including fees' mod='younitedpay'}</span>
         <span class="yp-mw85 yp-text-right">
            <span class="yp-fee">{$offer.fee_total|escape:'htmlall':'UTF-8'}</span>&nbsp;€
         </span>
      </div>
   </div>
   <div>
      <p class="yp-pol-purpledark yp-font-normal yp-mb-2 yp-leading-relaxed">
         <span>{l s='For a credit amount of' mod='younitedpay'}</span>
         <b class="yp-text-lg"><span class="yp-amount">{$offer.initial_amount|escape:'htmlall':'UTF-8'}</span>&nbsp;€</b>
         <span>{l s=', to which are added' mod='younitedpay'}</span>
         <b class="yp-text-lg yp-interest-text {if $offer.type === 'SplitPayment'}hidden{/if}">
            <span class="yp-interest">{$offer.interest_total|escape:'htmlall':'UTF-8'}</span>&nbsp;€
         </b>
         <b class="yp-text-lg yp-fees-text {if $offer.type !== 'SplitPayment'}hidden{/if}">
            <span class="yp-fee">{$offer.fee_total|escape:'htmlall':'UTF-8'}</span>&nbsp;€
         </b>
         <b class="yp-text-lg yp-fees-text {if $offer.type !== 'SplitPayment'}hidden{/if}">
            {l s='financing fees,' mod='younitedpay'}
         </b>
         <b class="yp-text-lg yp-interest-text {if $offer.type === 'SplitPayment'}hidden{/if}">
            {l s='interest,' mod='younitedpay'}
         </b>
         <span>{l s='either a' mod='younitedpay'}</span>
         <b class="yp-text-lg">{l s='total amount due of' mod='younitedpay'}</b>
         <b class="yp-text-lg"><span class="yp-total">{$offer.total_amount|escape:'htmlall':'UTF-8'}</span>&nbsp;€</b>.
         <span>{l s='The' mod='younitedpay'}</span>
         <b class="yp-text-lg">{l s='APR fixed is' mod='younitedpay'}</b>
         <b class="yp-text-lg"><span class="yp-taeg">{$offer.taeg|escape:'htmlall':'UTF-8'}</span>&nbsp;%</b>
         <span>{l s='excluding optional insurance and the fixed interest rate of' mod='younitedpay'}</span>
         <b class="yp-text-lg"><span class="yp-tdf">{$offer.tdf|escape:'htmlall':'UTF-8'}</span>&nbsp;%</b>.
      </p>
   </div>
   <script type="text/javascript">
      younitedpay.rangeOffers = [
         {foreach from=$range_offers item=range_offer key=key}
            {
               "maturity" : "{$range_offer['maturity']|escape:'htmlall':'UTF-8'}",
               "installment_amount" : "{$range_offer['installment_amount']|escape:'htmlall':'UTF-8'}",
               "down_payment_amount" : "{$range_offer['down_payment_amount']|escape:'htmlall':'UTF-8'}",
               "initial_amount" : "{$range_offer['initial_amount']|escape:'htmlall':'UTF-8'}",
               "total_amount" : "{$range_offer['total_amount']|escape:'htmlall':'UTF-8'}",
               "interest_total" : "{$range_offer['interest_total']|escape:'htmlall':'UTF-8'}",
               "taeg" : "{$range_offer['taeg']|escape:'htmlall':'UTF-8'}",
               "tdf" : "{$range_offer['tdf']|escape:'htmlall':'UTF-8'}",
               "type" : "{$range_offer['type']|escape:'htmlall':'UTF-8'}",
               "installment" : "{json_encode($range_offer['installment'])|escape:'htmlall':'UTF-8'}",
            }{if $key < count($range_offers) - 1},{/if}
         {/foreach}
      ];
      younitedpay.rangeEnabled = {$show_ranges|escape:'htmlall':'UTF-8'};
      younitedpay.rangeForced = {$range_forced|escape:'htmlall':'UTF-8'};
      younitedpay.minRange = {$min_range|escape:'htmlall':'UTF-8'};
      younitedpay.maxRange = {$max_range|escape:'htmlall':'UTF-8'};
      younitedpay.minInstall = {$min_install|escape:'htmlall':'UTF-8'};
      younitedpay.maxInstall = {$max_install|escape:'htmlall':'UTF-8'};
      {if isset($yperror)}
      younitedpay.url_payment = "{$credit_link|escape:'htmlall':'UTF-8'}";
      {/if}
   </script>
</div>

<div class="yp-text-responsabilities{if isset($yperror)} yp-my-4{else} yp-mt-6{/if}">
{if $iso_code == 'fr'}
   <p class="yp-pol-purpledark yp-text-lg yp-leading-relaxed yp-text-lg yp-font-bold">
      {l s='Taking out a loan is a commitment with an' mod='younitedpay'}
      {l s='obligation of repayment.' mod='younitedpay'}
      {if $iso_code == 'fr'}
         {l s='Verify your ability' mod='younitedpay'}
         {l s='to repay the loan' mod='younitedpay'}
      {else}
         {l s='Verify your ability' mod='younitedpay'}
         {l s='to repay the loan' mod='younitedpay'}
      {/if}
      {l s='before committing.' mod='younitedpay'}
   </p>
{/if}
</div>

{if isset($yperror)}
    </div>
    {if count($yperror) > 0}
        <div class="alert alert-warning">
            <ul style="margin-bottom:auto!important;list-style:disc!important;">
                {foreach $yperror item=oneerror}
                    <li>{$oneerror|escape:'htmlall':'UTF-8'}</li>
                {/foreach}
            </ul>
        </div>
    {/if}
{/if}
