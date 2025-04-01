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
<div class="yp-text-base yp-mt-4 yp-mb-5 yp-h4 yp-text-center">
    <span>{l s='Start paying' mod='younitedpay'} {l s='in just 30 days !' mod='younitedpay'}</span>
</div>
<div class="yp-flex yp-justify-center yp-mt-6">
{foreach from=$offers item=offer key=key}
   {assign var="background_block" value=''}
   {if (int) $key === (int) $selected_offer}
      {assign var="background_block" value='yp-bg-black-btn'}
   {/if}
   <span class="yp-flex yp-flex-row yp-space-x-1 yp-mx-2 yp-mb-1 maturity_installment maturity_installment{$key|escape:'htmlall':'UTF-8'} {$background_block|escape:'htmlall':'UTF-8'}"
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
   <span class="yp-flex yp-flex-row yp-space-x-1 yp-mx-2 yp-mb-1 maturity_installment maturity_installment9999"
         data-key="9999" 
         data-amount="{$offer.installment_amount|escape:'htmlall':'UTF-8'}" 
         data-totalamount="{$offer.total_amount|escape:'htmlall':'UTF-8'}" 
         data-initamount="{$offer.initial_amount|escape:'htmlall':'UTF-8'}" 
         data-interesttotal="{$offer.interest_total|escape:'htmlall':'UTF-8'}" 
         data-taeg="{$offer.taeg|escape:'htmlall':'UTF-8'}" 
         data-tdf="{$offer.tdf|escape:'htmlall':'UTF-8'}" 
         data-maturity="{$offer.maturity|escape:'htmlall':'UTF-8'}"
         title="{l s='Customise' mod='younitedpay'}">
      <span class="yp-inline-block yp-h-10" style="width:auto;">
         <span class="yp-inline-block yp-transition-all yp-border-opacity-100 yp-h-10 
            blocks_maturity block_maturity{$key|escape:'htmlall':'UTF-8'} yp-flex flexmiddle ">
            <span class="yp-flex flexmiddle yp-p-2 yp-rounded-sm yp-transition-colors 
               yp-duration-500 yp-select-none">{l s='Customise' mod='younitedpay'}</span>
         </span>
      </span>
   </span>
{/if}
</div>

<div>
<div class="yp-pol-purpledark yp-flex yp-justify-center yp-align-center yp-pol-purple yp-font-bold yp-text-md yp-font-family-rg yp-mt-6 yp-mb-3">
   <span class="yp-install-maturity-lite">{$offers[$selected_offer].maturity|escape:'htmlall':'UTF-8'}</span> 
   <span>&nbsp;{l s='months' mod='younitedpay'}</span>
</div>
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

<div class="yp-info-buy yp-flex yp-font-family-rg yp-mt-6 yp-justify-between yp-items-center">
<p class="yp-flex yp-items-center yp-weight600 yp-pol-purpledark yp-text-20">
   <span>{l s='Your purchase for ' mod='younitedpay'}</span>&nbsp;
   <span class="yp-pol-purple yp-text-20 yp-font-bold yp-font-family-rg yp-install-amount">
      {$offers[$selected_offer].installment_amount|escape:'htmlall':'UTF-8'} €
   </span>&nbsp;
   <span>/ {l s='months' mod='younitedpay'}</span>
</p>
</div>

{assign var="offer" value=$offers[$selected_offer]}
<div class="yp-border-prple yp-border-2 yp-p-3 yp-rounded-bg yp-mt-6">
<div class="yp-mb-8 yp-text-lg yp-pol-purpledark">
   <div class="yp-flex yp-flex-row yp-justify-between yp-mb-2">
      <p class="yp-pol-purpledark yp-font-normal">{l s='Total credit amount' mod='younitedpay'}</p>
      <p class="yp-weight600 yp-pol-purpledark yp-mw85 yp-text-right">
         <span class="yp-amount">{$offer.initial_amount|escape:'htmlall':'UTF-8'}</span>&nbsp;€
      </p>
   </div>
   <div class="yp-flex yp-flex-row yp-justify-between yp-pb-6">
      <p class="yp-pol-purpledark yp-font-normal"><b>+</b> {l s='Interest (excl. optional insurance)' mod='younitedpay'}</p>
      <p class="yp-weight600 yp-pol-purpledark yp-mw85 yp-text-right">
         <span class="yp-interest">{$offer.interest_total|escape:'htmlall':'UTF-8'}</span>&nbsp;€
      </p>
   </div>
   <div class="yp-flex yp-flex-row yp-justify-between yp-pol-purple yp-mb-2 yp-text-20 yp-weight600">
      <span><b>= {l s='Total amount due' mod='younitedpay'}</b></span>
      <span class="yp-mw85 yp-text-right">
         <span class="yp-total">{$offer.total_amount|escape:'htmlall':'UTF-8'}</span>&nbsp;€
      </span>
   </div>
</div>
<div class="yp-mt-6 block_contents block_content_range">
   <div class="yp-justify-between yp-flex yp-flex-row yp-mb-2 yp-text-20">
      <span class="yp-weight600 yp-pol-purpledark">{l s='Fixed APR' mod='younitedpay'}
      {if $iso_code != 'es'}
         <br /><span>{l s='(excluding optional insurance)' mod='younitedpay'}</span>
      {/if}
      </span>
      <span class="yp-weight600 yp-pol-purpledark yp-mw85 yp-text-right">
         <span class="yp-taeg">{$offer.taeg|escape:'htmlall':'UTF-8'}</span> %
      </span>
   </div>
   <div class="yp-justify-between yp-flex yp-flex-row yp-mb-2 yp-pol-purpledark yp-font-normal">
      <span>{l s='Fixed lending rate' mod='younitedpay'}</span>
      <span class="yp-weight600 yp-mw85 yp-text-right">
         <span class="yp-tdf">{$offer.tdf|escape:'htmlall':'UTF-8'}</span> %
      </span>
   </div>
</div>
<script type="text/javascript">
   younitedpay.rangeOffers = [
      {foreach from=$range_offers item=range_offer key=key}
         {
            "maturity" : {$range_offer['maturity']|escape:'htmlall':'UTF-8'},
            "installment_amount" : {$range_offer['installment_amount']|escape:'htmlall':'UTF-8'},
            "initial_amount" : {$range_offer['initial_amount']|escape:'htmlall':'UTF-8'},
            "total_amount" : {$range_offer['total_amount']|escape:'htmlall':'UTF-8'},
            "interest_total" : {$range_offer['interest_total']|escape:'htmlall':'UTF-8'},
            "taeg" : {$range_offer['taeg']|escape:'htmlall':'UTF-8'},
            "tdf" : {$range_offer['tdf']|escape:'htmlall':'UTF-8'},
         }{if $key < count($range_offers) - 1},{/if}
      {/foreach}
   ];
   younitedpay.rangeEnabled = {$show_ranges|escape:'htmlall':'UTF-8'};
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
   <p class="yp-weight600 yp-pol-purpledark yp-text-20">
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
                    <li>{html_entity_decode($oneerror|escape:'htmlall':'UTF-8')}</li>
                {/foreach}
            </ul> 
        </div>
    {/if}
{/if}