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
<div class="younitedpay-widget-root">
   {if count($offers) > 0}
      <p id="younited_infoclick">{l s='Click here for more informations' mod='younitedpay'}</p>
      <div class="younited_block yp-bg-white yp-rounded-md yp-border yp-border-blue yp-border-opacity-50 yp-text-xs yp-p-2 yp-pb-0">
         <div class="yp-bg-white yp-cursor-pointer yp-flex yp-flex-row yp-items-center yp-flex-wrap yp-text-xs yp-p-2 yp-pb-0">
            <img class="yp-h-8 yp-mb-2 lazyloaded" src="{$shop_url}{$logo_younitedpay_url}" alt="logo Younited Pay" data-ll-status="loaded">         
            {foreach from=$offers item=offer key=key}
               {if $key === 0}
                  {assign var="border_block" value='yp-border-blue yp-border-b-2 '}
                  {assign var="background_block" value='yp-bg-blue'}
               {else}
                  {assign var="border_block" value=''}
                  {assign var="background_block" value='yp-bg-prple'}
               {/if}
               <span class="yp-flex yp-flex-row yp-space-x-1 yp-mx-2 yp-mb-1 maturity_installment maturity_installment{$key}"
                     data-key="{$key}"
                     data-mouseover="{$offer.installment_amount}€ x{$offer.maturity}">
                  <span class="yp-inline-block yp-h-7">
                     <span class="yp-inline-block yp-transition-all yp-duration-500 yp-border-opacity-100 yp-h-7 
                        {$border_block}blocks_maturity block_maturity{$key}">
                        <span class="yp-flex yp-p-1 yp-text-white yp-rounded-sm yp-transition-colors 
                           yp-duration-500 yp-select-none {$background_block}">
                        {$offer.maturity}<svg class="plan-pill-multiplier" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.5964 11.404L9.41445 8.22205L12.5964 5.04007C12.7732 4.86329 12.7732 4.50974 12.5964 4.33296L11.8893 3.62585C11.6904 3.42698 11.359 3.44908 11.1822 3.62585L8.00023 6.80783L4.81825 3.62585C4.61938 3.42698 4.28792 3.44908 4.11114 3.62585L3.40404 4.33296C3.20516 4.53183 3.20516 4.84119 3.40404 5.04007L6.58602 8.22205L3.40404 11.404C3.20516 11.6029 3.20517 11.9123 3.40404 12.1111L4.11115 12.8182C4.28792 12.995 4.61938 13.0171 4.81825 12.8182L8.00023 9.63626L11.1822 12.8182C11.359 12.995 11.6904 13.0171 11.8893 12.8182L12.5964 12.1111C12.7732 11.9344 12.7732 11.5808 12.5964 11.404Z" fill="#fff"></path></svg>
                        </span>
                     </span>
                  </span>
               </span> 
            {/foreach}
         {if count($offers) > 5 && widget_younited === false}
         </div>
         <div class="yp-bg-white yp-cursor-pointer yp-flex yp-flex-row yp-items-center yp-flex-wrap yp-text-xs yp-p-2 yp-pb-0">
            <hr class="yp-border-prple yp-opacity-50">
            <span style="width:84px;"></span>
         {/if}            
            <span class="yp-flex yp-flex-row yp-space-x-1 yp-mx-2 yp-mb-1">
               <span class="yp-inline-block yp-h-7">
                  <span class="younitedpay_infoinstallment">
                     {$offers[0].installment_amount}€ x{$offers[0].maturity}
                  </span>               
               </span>
            </span>
         </div>
      </div>
   {/if}
   <div id="younited_popupzone"
      class="yp-inset-0 yp-flex yp-fixed yp-justify-center yp-items-center yp-z-100 yp-bg-black yp-bg-opacity-50" style="display:none!important;">
      <div
         class="yp-overflow-hidden yp-relative yp-flex yp-flex-row yp-flex-wrap yp-rounded-sm yp-shadow-lg yp-w-screen yp-h-screen md:yp-h-640 lg:yp-w-11/12 md:yp-max-w-4xl yp-max-h-screen yp-bg-white yp-text-base">
         <button class="younited_btnhide yp-text-4xl yp-opacity-50 hover:yp-opacity-100 
            focus:yp-opacity-100 yp-absolute yp-top-3 yp-right-4 yp-outline-none yp-transform 
            hover:yp-scale-125 focus:yp-scale-125 yp-transition-transform yp-duration-100 close-popup-you">
            x
         </button>
         <div class="yp-w-full md:yp-w-1/2 yp-p-8 yp-border-lprple">
            <span class="yp-flex">
               <img class="yp-h-16 lazyloaded" src="{$shop_url}{$logo_younitedpay_url}" alt="youpay"
                  data-ll-status="loaded" />
               <noscript>
                  <img class="yp-h-8" src="{$shop_url}{$logo_younitedpay_url}" alt="youpay">
               </noscript>
            </span>
            <div class="younitedpay-left-title-text yp-text-4xl yp-font-bold yp-mt-16">
               <h3 class="yp-pl-6">{l s='Simple.' mod='younitedpay'}</h3>
               <h3 class="yp-mt-6 yp-pl-6">{l s='Instant.' mod='younitedpay'}</h3>
               <h3 class="yp-mt-6 yp-pl-6">{l s='Secured.' mod='younitedpay'}</h3>
            </div>
            <div class="yp-text-2xl yp-mt-16 yp-pl-6">
               {l s='Younited, it\'s never been easier' mod='younitedpay'}<br />
               {l s='to pay in several times.' mod='younitedpay'}
            </div>
            <p class="yp-mt-16">
               <a href="https://www.younited-credit.com/questions-reponses" target="_blank"
                  class="yp-text-xs yp-underline">
                  {l s='Have a question? Visit our dedicated page' mod='younitedpay'}
               </a>
            </p>
            <p class="yp-mt-7">
               <span class="yp-inline-block yp-h-10">
                  <img class="yp-h-8 lazyloaded"
                     src="{$shop_url}/modules/younitedpay/views/img/cb.png"
                     alt="Cartes Bancaires" data-ll-status="loaded">
                     <noscript><img class="yp-h-full"
                        src="{$shop_url}/modules/younitedpay/views/img/cb.png"
                        alt="Cartes Bancaires">
                     </noscript>
               </span>
               <span class="yp-inline-block yp-h-10">
                  <img class="yp-h-8 lazyloaded" src="{$shop_url}/modules/younitedpay/views/img/visa.png"
                     alt="Visa" data-ll-status="loaded" />
                  <noscript>
                     <img class="yp-h-full" src="{$shop_url}/modules/younitedpay/views/img/visa.png" alt="Visa">
                  </noscript>
               </span>
               <span class="yp-inline-block yp-h-10">
                  <img class="yp-h-8 lazyloaded"
                     src="{$shop_url}/modules/younitedpay/views/img/mastercard.png"
                     alt="Mastercard" data-ll-status="loaded">
                  <noscript>
                     <img class="yp-h-full" src="{$shop_url}/modules/younitedpay/views/img/mastercard.png" 
                        alt="Mastercard">
                  </noscript>
               </span>
            </p>
         </div>
         <div
            class="younitedpay-howItWorks__planPanel yp-w-full md:yp-w-1/2 yp-p-8 yp-pb-24 md:yp-pb-8 md:yp-pt-8 yp-flex yp-flex-col yp-align-center yp-bg-beige">
            <div class="yp-flex-grow">
               {foreach from=$offers item=offer key=key}
                  {if $key === 0}
                     {assign var="hiddenclass" value=''}
                  {else}
                     {assign var="hiddenclass" value='hidden '}
                  {/if}
                  <h4 class="{$hiddenclass}yp-mt-8 yp-text-lg yp-text-center block_contents block_content{$key}">
                     {l s='Your purchase for %s€/month with' mod='younitedpay' sprintf=[$offer.installment_amount]}
                     <span class="yp-pol-purple">{l s='YounitedPay' mod='younitedpay'}</span>
                  </h4>
               {/foreach}
               <ul class="yp-flex yp-justify-center">
                  {foreach from=$offers item=offer key=key}
                     {if $key === 0}
                        {assign var="background_block" value='yp-bg-black yp-pol-white'}
                     {else}
                        {assign var="background_block" value='yp-bg-white yp-pol-black'}
                     {/if}
                     <li class="blocks_maturities_popup block_maturity_popup{$key} yp-group yp-inline-block yp-transition-all duration-200 
                           yp-text-sm yp-mr-2 yp-cursor-pointer yp-text-sm"
                           data-key="{$key}"
                           data-mouseover="{$offer.installment_amount}€ x{$offer.maturity}">
                        <span
                           class="yp-flex yp-p-2 yp-pt-2 yp-pb-2 yp-my-4 yp-border-black yp-border yp-transition-colors yp-duration-200  yp-select-none {$background_block}">
                           {$offer.maturity} {l s='months' mod='younited'}
                        </span>
                     </li>
                  {/foreach}
               </ul>
               <p class="yp-mt-8 yp-text-lg">
                  {l s='Buy today and start paying' mod='younitedpay'}<br />
                  <span class="yp-pol-purple">{l s='in just 30 days!' mod='younitedpay'}</span>
               </p>
               {foreach from=$offers item=offer key=key}
                  {if $key === 0}
                     {assign var="hiddenclass" value=''}
                  {else}
                     {assign var="hiddenclass" value='hidden '}
                  {/if}
                  <div class="{$hiddenclass}yp-my-8 yp-text-lg block_contents block_content{$key} yp-mb-8">
                     <div class="yp-flex yp-flex-row yp-justify-between yp-mb-2">
                        <p>{l s='Amount of financing' mod='younitedpay'}</p>
                        <p class="younitedpay-title-text">{$offer.initial_amount} €</p>
                     </div>
                     <div class="yp-flex yp-flex-row yp-justify-between yp-mb-2">
                        <p><b>+</b> {l s='Payment cost' mod='younitedpay'}</p>
                        <p class="younitedpay-title-text">{$offer.interest_total} €</p>
                     </div>
                     <hr class="yp-border-prple yp-opacity-50 yp-mb-2">
                     <div class="yp-flex yp-flex-row yp-justify-between yp-mb-2">
                        <p class="younitedpay-title-text"><b>= {l s='Total amount owed' mod='younitedpay'}</b></p>
                        <p class="younitedpay-title-text">{$offer.total_amount} €</p>
                     </div>
                  </div>
                  <div class="{$hiddenclass}yp-my-8 yp-text-lg block_contents block_content{$key}">
                     <div class="yp-justify-end yp-flex yp-flex-row yp-mb-2">
                        <span class="younitedpay-title-text">{l s='Fixed APR' mod='younitedpay'}:&nbsp;&nbsp;
                        {$offer.taeg} %</span>
                     </div>
                     <div class="yp-justify-end yp-flex yp-flex-row yp-mb-2">
                        <p class="younitedpay-title-text">{l s='Fixed borrowing rate' mod='younitedpay'}:&nbsp;&nbsp;
                        {$offer.tdf} %</p>
                     </div>
                  </div>
               {/foreach}               
               <div class="yp-text-lg yp-text-responsabilities yp-p-4 yp-mt-16">
                  {l s='A loan commits you and must be repaid. Check your ability to repay before commit yourself.' mod='younitedpay'}
               </div>
            </div>
         </div>
      </div>
   </div>
</div>