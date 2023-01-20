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
* @author 202 ecommerce <tech@202-ecommerce.com>
* @copyright Younited
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
*}
<div class="younitedpay_product_info" data-hook="{$hook_younited|escape:'htmlall':'UTF-8'}" {if $hook_younited == 'widget'}data-widget{/if}>   
<div class="younitedpay-widget-root">
   {if count($offers) > 0}
      <div class="younited_block yp-pb-0 yp-pt-2">
         <div class="yp-cursor-pointer yp-flex yp-flex-row yp-items-center yp-flex-wrap yp-pt-4 yp-pb-4">
            <img class="yp-mb-2 lazyloaded" src="{$shop_url|escape:'htmlall':'UTF-8'}{$logo_younitedpay_url_btn|escape:'htmlall':'UTF-8'}" alt="logo Younited Pay" data-ll-status="loaded">
            {foreach from=$offers item=offer key=key}
               {if $key === 0}
                  {assign var="background_block" value='yp-border-2 yp-border-opacity-0'}
               {else}
                  {assign var="background_block" value='yp-border yp-border-opacity-50'}
               {/if}
               <span class="yp-flex yp-flex-row yp-space-x-1 yp-mx-2 yp-mb-1 maturity_installment maturity_installment{$key|escape:'htmlall':'UTF-8'}"
                     data-key="{$key|escape:'htmlall':'UTF-8'}" 
                     data-amount="{$offer.installment_amount|escape:'htmlall':'UTF-8'}€" 
                     data-maturity="x{$offer.maturity|escape:'htmlall':'UTF-8'}">
                  <span class="yp-inline-block yp-h-10">
                     <span class="yp-inline-block yp-transition-all yp-duration-500 yp-border-opacity-100 yp-h-10 
                        blocks_maturity block_maturity{$key|escape:'htmlall':'UTF-8'} yp-flex flexmiddle ">
                        <span class="yp-flex flexmiddle yp-p-2 yp-pol-black yp-rounded-sm yp-transition-colors 
                           yp-duration-500 yp-select-none yp-border-prple {$background_block|escape:'htmlall':'UTF-8'}">
                        {$offer.maturity|escape:'htmlall':'UTF-8'}x
                        </span>
                     </span>
                  </span>
               </span> 
            {/foreach}

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
            <span class="yp-flex yp-justify-start yp-flex-row yp-space-x-1 yp-mx-2 yp-mb-1{if $price_at_bottom == true} yp-price-block{/if}">
               <span style="padding-left:70px!important;" class="yp-inline-block yp-h-7"{if $price_at_bottom == true} style="padding-right:2rem!important;"{/if}>
                  <span class="younitedpay_infoinstallment yp-install-amount yp-inline">
                     {$offers[0].installment_amount|escape:'htmlall':'UTF-8'}€
                  </span>
                  <span class="younitedpay_infoinstallment yp-font-normal yp-install-maturity yp-inline">x{$offers[0].maturity|escape:'htmlall':'UTF-8'}</span>
               </span>
            </span>
         </div>
      </div>
   {/if}
   <div id="younited_popupzone"
      class="yp-inset-0 yp-flex yp-fixed yp-justify-center yp-items-center yp-z-100 yp-bg-black yp-bg-opacity-50" style="display:none!important;">
      <div
         class="yp-overflow-auto yp-relative yp-flex yp-flex-row yp-flex-wrap yp-rounded-sm yp-shadow-lg yp-w-screen yp-h-screen md:yp-h-580 lg:yp-w-11/12 md:yp-max-w-4xl yp-max-h-screen yp-bg-white">
         <button class="younited_btnhide yp-text-4xl yp-opacity-50 hover:yp-opacity-100 
            focus:yp-opacity-100 yp-absolute yp-top-3 yp-right-4 yp-outline-none yp-transform 
            hover:yp-scale-125 focus:yp-scale-125 yp-transition-transform yp-duration-100 close-popup-you">
            x
         </button>
         <div class="yp-h-full yp-w-full md:yp-w-2/5 yp-p-6 yp-border-lprple yp-bg-purple-light">
            <span class="yp-flex yp-popup-logo yp-pl-2">
               <img class="yp-h-16 lazyloaded" src="{$shop_url|escape:'htmlall':'UTF-8'}{$logo_younitedpay_url|escape:'htmlall':'UTF-8'}" alt="youpay"
                  data-ll-status="loaded" />
               <noscript>
                  <img class="yp-h-8" src="{$shop_url|escape:'htmlall':'UTF-8'}{$logo_younitedpay_url|escape:'htmlall':'UTF-8'}" alt="youpay">
               </noscript>
            </span>
            <div class="younitedpay-left-title-text yp-font-family-rg yp-text-4xl yp-mt-16">
               <h3 class="yp-pl-6">{l s='Simple.' mod='younitedpay'}</h3>
               <h3 class="yp-pl-6">{l s='Instant.' mod='younitedpay'}</h3>
               <h3 class="yp-pl-6">{l s='Secured.' mod='younitedpay'}</h3>
            </div>
            <div class="yp-font-normal yp-pol-grey yp-text-2xl yp-mt-6 yp-pl-6">
               {l s='Younited, it\'s never been easier' mod='younitedpay'}
               {l s='to pay in several times.' mod='younitedpay'}
            </div>
            <p class="yp-mt-16 yp-pl-6 yp-pt-6">
               <span class="yp-inline-block yp-h-10">
                  <img class="yp-h-8 lazyloaded"
                     src="{$shop_url|escape:'htmlall':'UTF-8'}modules/younitedpay/views/img/cb.png"
                     alt="Cartes Bancaires" data-ll-status="loaded">
                     <noscript><img class="yp-h-full"
                        src="{$shop_url|escape:'htmlall':'UTF-8'}modules/younitedpay/views/img/cb.png"
                        alt="Cartes Bancaires">
                     </noscript>
               </span>
               <span class="yp-inline-block yp-h-10">
                  <img class="yp-h-8 lazyloaded" src="{$shop_url|escape:'htmlall':'UTF-8'}modules/younitedpay/views/img/visa.png"
                     alt="Visa" data-ll-status="loaded" />
                  <noscript>
                     <img class="yp-h-full" src="{$shop_url|escape:'htmlall':'UTF-8'}modules/younitedpay/views/img/visa.png" alt="Visa">
                  </noscript>
               </span>
               <span class="yp-inline-block yp-h-10">
                  <img class="yp-h-8 lazyloaded"
                     src="{$shop_url|escape:'htmlall':'UTF-8'}modules/younitedpay/views/img/mastercard.png"
                     alt="Mastercard" data-ll-status="loaded">
                  <noscript>
                     <img class="yp-h-full" src="{$shop_url|escape:'htmlall':'UTF-8'}modules/younitedpay/views/img/mastercard.png" 
                        alt="Mastercard">
                  </noscript>
               </span>
            </p>
            <div class="yp-mt-5 yp-px-6">
               <p class="yp-bg-prple-grey yp-border yp-border-prple yp-p-4">
                  {l s='Have a question? Visit our' mod='younitedpay'}&nbsp;
                  <a href="https://www.younited-credit.com/questions-reponses" target="_blank"
                     class="yp-text-xs yp-underline">
                     {l s='dedicated page' mod='younitedpay'}
                  </a>
               </p>
            </div>
         </div>
         <div class="younitedpay-howItWorks__planPanel yp-h-full yp-w-full md:yp-w-3/5 yp-p-8 yp-pb-24 md:yp-pb-8 md:yp-pt-8 yp-flex yp-flex-col yp-align-center yp-bg-beige">
            <div class="yp-flex-grow">
               {foreach from=$offers item=offer key=key}
                  {if $key === 0}
                     {assign var="hiddenclass" value=''}
                  {else}
                     {assign var="hiddenclass" value='hidden '}
                  {/if}
                  <h4 class="{$hiddenclass|escape:'htmlall':'UTF-8'}yp-font-family-rg yp-text-xl block_contents block_content{$key|escape:'htmlall':'UTF-8'}">
                     {l s='Your purchase for ' mod='younitedpay'}
                     <span class="yp-text-4xl">{l s='%s€/month' mod='younitedpay' sprintf=[$offer.installment_amount]}</span><br>
                     {l s=' with' mod='younitedpay' sprintf=[$offer.installment_amount]}
                     <span class="yp-pol-purple yp-font-bold yp-text-xl yp-font-family-rg">{l s='Younited Pay' mod='younitedpay'}</span>
                  </h4>
               {/foreach}
               <p class="yp-mt-4 yp-mb-5">
                  <span class="">{l s='Buy today and start paying' mod='younitedpay'}</span>
                  <span class="yp-pol-purpledark yp-font-bold yp-text-sm">{l s='in just 30 days !' mod='younitedpay'}</span>
               </p>
               <ul class="yp-flex yp-justify-left">
                  {foreach from=$offers item=offer key=key}
                     {if $key === 0}
                        {assign var="background_block" value='yp-bg-prple-grey'}
                     {else}
                        {assign var="background_block" value='yp-bg-white'}
                     {/if}
                     <li class="blocks_maturities_popup block_maturity_popup{$key|escape:'htmlall':'UTF-8'} yp-group yp-inline-block yp-transition-all duration-200 
                           yp-text-sm yp-mr-3 yp-cursor-pointer yp-text-sm"
                           data-key="{$key|escape:'htmlall':'UTF-8'}"
                           data-mouseover="{$offer.installment_amount|escape:'htmlall':'UTF-8'}€ x{$offer.maturity|escape:'htmlall':'UTF-8'}">
                        <span
                           class="yp-flex yp-p-2 yp-pt-2 yp-pb-2 yp-my-4 yp-border-purple-bright yp-border yp-transition-colors yp-duration-200 yp-select-none {$background_block|escape:'htmlall':'UTF-8'}">
                           {$offer.maturity|escape:'htmlall':'UTF-8'} {l s='months' mod='younitedpay'}
                        </span>
                     </li>
                  {/foreach}
               </ul>
               <div class="yp-bg-prple-grey yp-border yp-border-prple yp-p-6">
               {foreach from=$offers item=offer key=key}
                  {if $key === 0}
                     {assign var="hiddenclass" value=''}
                  {else}
                     {assign var="hiddenclass" value='hidden '}
                  {/if}
                  <div class="{$hiddenclass|escape:'htmlall':'UTF-8'}yp-mb-8 yp-text-lg block_contents block_content{$key|escape:'htmlall':'UTF-8'} yp-pol-purpledark yp-mb-8">
                     <div class="yp-flex yp-flex-row yp-justify-between yp-mb-2">
                        <p class="yp-pol-purpledark yp-font-normal">{l s='Amount of financing' mod='younitedpay'}</p>
                        <p class="yp-font-bold yp-pol-purpledark">{$offer.initial_amount|escape:'htmlall':'UTF-8'} €</p>
                     </div>
                     <div class="yp-flex yp-flex-row yp-justify-between yp-pb-6">
                        <p class="yp-pol-purpledark yp-font-normal"><b>+</b> {l s='Payment cost' mod='younitedpay'}</p>
                        <p class="yp-font-bold yp-pol-purpledark">{$offer.interest_total|escape:'htmlall':'UTF-8'} €</p>
                     </div>
                     <hr class="yp-border-prple yp-opacity-50 yp-pb-6">
                     <div class="yp-flex yp-flex-row yp-justify-between yp-mb-2">
                        <p class="yp-font-bold yp-pol-purpledark"><b>= {l s='Total amount owed' mod='younitedpay'}</b></p>
                        <p class="yp-font-bold yp-pol-purpledark">{$offer.total_amount|escape:'htmlall':'UTF-8'} €</p>
                     </div>
                  </div>
                  <div class="{$hiddenclass|escape:'htmlall':'UTF-8'}yp-mt-6 yp-text-lg block_contents block_content{$key|escape:'htmlall':'UTF-8'}">
                     <div class="yp-justify-between yp-flex yp-flex-row yp-mb-2">
                        <p class="yp-font-bold yp-pol-purpledark">{l s='Fixed APR' mod='younitedpay'}</p>
                        <p class="yp-font-bold yp-pol-purpledark">{$offer.taeg|escape:'htmlall':'UTF-8'} %</p>
                     </div>
                     <div class="yp-justify-between yp-flex yp-flex-row yp-mb-2">
                        <p class="yp-font-bold yp-pol-purpledark">{l s='Fixed borrowing rate' mod='younitedpay'}</p>
                        <p class="yp-font-bold yp-pol-purpledark">{$offer.tdf|escape:'htmlall':'UTF-8'} %</p>
                     </div>
                  </div>
               {/foreach}
               </div>
               <div class="yp-mt-4 yp-text-responsabilities">
                  <p class="yp-text-lg">
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
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>