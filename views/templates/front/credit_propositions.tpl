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
      <div class="younited_block atw-bg-white atw-rounded-md atw-border atw-border-blue atw-border-opacity-50 atw-text-xs atw-p-2 atw-pb-0">
         <div class="atw-bg-white atw-cursor-pointer atw-flex atw-flex-row atw-items-center atw-flex-wrap atw-text-xs atw-p-2 atw-pb-0">
            <img class="atw-h-8 atw-mb-2 lazyloaded" src="{$shop_url}{$logo_younitedpay_url}" alt="logo Younited Pay" data-ll-status="loaded">         
            {foreach from=$offers item=offer key=key}
               {if $key === 0}
                  {assign var="border_block" value='atw-border-blue atw-border-b-2 '}
                  {assign var="background_block" value='atw-bg-blue'}
               {else}
                  {assign var="border_block" value=''}
                  {assign var="background_block" value='atw-bg-prple'}
               {/if}
               <span class="atw-flex atw-flex-row atw-space-x-1 atw-mx-2 atw-mb-1 maturity_installment maturity_installment{$key}"
                     data-key="{$key}"
                     data-mouseover="{$offer.installment_amount}€ x{$offer.maturity}">
                  <span class="atw-inline-block atw-h-7">
                     <span class="atw-inline-block atw-transition-all atw-duration-500 atw-border-opacity-100 atw-h-7 
                        {$border_block}blocks_maturity block_maturity{$key}">
                        <span class="atw-flex atw-p-1 atw-text-white atw-rounded-sm atw-transition-colors 
                           atw-duration-500 atw-select-none {$background_block}">
                        {$offer.maturity}<svg class="plan-pill-multiplier" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.5964 11.404L9.41445 8.22205L12.5964 5.04007C12.7732 4.86329 12.7732 4.50974 12.5964 4.33296L11.8893 3.62585C11.6904 3.42698 11.359 3.44908 11.1822 3.62585L8.00023 6.80783L4.81825 3.62585C4.61938 3.42698 4.28792 3.44908 4.11114 3.62585L3.40404 4.33296C3.20516 4.53183 3.20516 4.84119 3.40404 5.04007L6.58602 8.22205L3.40404 11.404C3.20516 11.6029 3.20517 11.9123 3.40404 12.1111L4.11115 12.8182C4.28792 12.995 4.61938 13.0171 4.81825 12.8182L8.00023 9.63626L11.1822 12.8182C11.359 12.995 11.6904 13.0171 11.8893 12.8182L12.5964 12.1111C12.7732 11.9344 12.7732 11.5808 12.5964 11.404Z" fill="#fff"></path></svg>
                        </span>
                     </span>
                  </span>
               </span> 
            {/foreach}
         {if count($offers) > 3}
         </div>
         <div class="atw-bg-white atw-cursor-pointer atw-flex atw-flex-row atw-items-center atw-flex-wrap atw-text-xs atw-p-2 atw-pb-0">
            <hr class="atw-border-prple atw-opacity-50">
            <span style="width:84px;"></span>
         {/if}            
            <span class="atw-flex atw-flex-row atw-space-x-1 atw-mx-2 atw-mb-1">
               <span class="atw-inline-block atw-h-7">
                  <span class="younitedpay_infoinstallment">
                     {$offers[0].installment_amount}€ x{$offers[0].maturity}
                  </span>               
               </span>
            </span>
         </div>
      </div>
   {/if}
   <div id="younited_popupzone"
      class="atw-inset-0 atw-flex atw-fixed atw-justify-center atw-items-center atw-z-100 atw-bg-black atw-bg-opacity-50" style="display:none!important;">
      <div
         class="atw-overflow-auto atw-relative atw-flex atw-flex-row atw-flex-wrap atw-rounded-sm atw-shadow-lg atw-w-screen atw-h-screen md:atw-h-800 lg:atw-w-11/12 md:atw-max-w-4xl atw-max-h-screen atw-bg-white atw-text-base">
         <button class="younited_btnhide atw-text-4xl atw-opacity-50 hover:atw-opacity-100 
            focus:atw-opacity-100 atw-absolute atw-top-3 atw-right-4 atw-outline-none atw-transform 
            hover:atw-scale-125 focus:atw-scale-125 atw-transition-transform atw-duration-100 close-popup-you">
            x
         </button>
         <div class="atw-w-full md:atw-w-1/2 atw-p-8">
            <span class="atw-justify-center atw-flex">
               <img class="atw-h-16 lazyloaded" src="{$shop_url}{$logo_younitedpay_url}" alt="youpay"
                  data-ll-status="loaded" />
               <noscript>
                  <img class="atw-h-8" src="{$shop_url}{$logo_younitedpay_url}" alt="youpay">
               </noscript>
            </span>
            <div class="younitedpay-title-text atw-text-4xl atw-font-bold atw-mt-8">
               {l s='Younited, it\'s never been easier to pay ' mod='younitedpay'}{l s='in' mod='younitedpay'} 
               <span class="younitedpay-text-highlight-md atw-border-blue">{l s='several times.' mod='younitedpay'}
               </span>
            </div>
            <ul class="younitedpay-howItWorks__steps atw-my-10">
               <li class="atw-pl-8 atw-mb-5">{l s='I validate my shopping cart.' mod='younitedpay'}</li>
               <li class="atw-pl-8 atw-mb-5">{l s='I choose to pay via Younited Pay.' mod='younitedpay'}</li>
               <li class="atw-pl-8 atw-mb-5">{l s='I take a video selfie to verify my identity.' mod='younitedpay'}</li>
               <li class="atw-pl-8 atw-mb-5">{l s='I enter my credit card information.' mod='younitedpay'}</li>
               <li class="atw-pl-8 atw-mb-5">{l s='I sign my contract electronically via SMS.' mod='younitedpay'}</li>
            </ul>
            <p>
               <a href="https://www.younited-credit.com/questions-reponses" target="_blank"
                  class="atw-text-xs atw-underline">
                  {l s='Have a question? Visit our dedicated page' mod='younitedpay'}
               </a>
            </p>
            <p class="atw-mt-7 atw-space-x-3">
               <span class="atw-inline-block atw-h-10 atw-border atw-border-orange atw-rounded-md atw-p-2">
                  <img class="atw-h-full lazyloaded"
                     src="data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='24' height='16' viewBox='0 0 24 16'%3e%3cdefs%3e%3clinearGradient id='cardcb' x1='5.842%25' x2='95.393%25' y1='81.753%25' y2='17.344%25'%3e%3cstop offset='0%25' stop-color='%2339B54A'%3e%3c/stop%3e %3cstop offset='100%25' stop-color='%230A5296'%3e%3c/stop%3e%3c/linearGradient%3e%3c/defs%3e %3cg fill='none' fill-rule='nonzero'%3e%3cpath fill='url(%23cardcb)' d='M22.621 16H1.38C.627 16 0 15.392 0 14.662V1.338C0 .608.627 0 1.379 0H22.62C23.373 0 24 .608 24 1.338v13.324c-.063.73-.627 1.338-1.379 1.338z'%3e%3c/path%3e %3cg fill='white'%3e%3cpath d='M19.094 4.03h-6.437V8h6.498c1.165 0 2.084-.889 2.084-2.015-.06-1.066-.98-1.955-2.145-1.955zM19.094 8.593h-6.437v3.97h6.498c1.165 0 2.084-.889 2.084-2.015-.06-1.067-.98-1.955-2.145-1.955zM7.017 8.06h4.966c-.245-2.371-2.391-4.267-4.966-4.267-2.758 0-5.027 2.074-5.027 4.681s2.269 4.682 5.027 4.682c2.698 0 4.904-2.015 5.027-4.563H7.017v-.534z'%3e%3c/path%3e%3c/g%3e%3c/g%3e%3c/svg%3e"
                     alt="Cartes Bancaires" data-ll-status="loaded"><noscript><img class="atw-h-full"
                        src="data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='24' height='16' viewBox='0 0 24 16'%3e%3cdefs%3e%3clinearGradient id='cardcb' x1='5.842%25' x2='95.393%25' y1='81.753%25' y2='17.344%25'%3e%3cstop offset='0%25' stop-color='%2339B54A'%3e%3c/stop%3e %3cstop offset='100%25' stop-color='%230A5296'%3e%3c/stop%3e%3c/linearGradient%3e%3c/defs%3e %3cg fill='none' fill-rule='nonzero'%3e%3cpath fill='url(%23cardcb)' d='M22.621 16H1.38C.627 16 0 15.392 0 14.662V1.338C0 .608.627 0 1.379 0H22.62C23.373 0 24 .608 24 1.338v13.324c-.063.73-.627 1.338-1.379 1.338z'%3e%3c/path%3e %3cg fill='white'%3e%3cpath d='M19.094 4.03h-6.437V8h6.498c1.165 0 2.084-.889 2.084-2.015-.06-1.066-.98-1.955-2.145-1.955zM19.094 8.593h-6.437v3.97h6.498c1.165 0 2.084-.889 2.084-2.015-.06-1.067-.98-1.955-2.145-1.955zM7.017 8.06h4.966c-.245-2.371-2.391-4.267-4.966-4.267-2.758 0-5.027 2.074-5.027 4.681s2.269 4.682 5.027 4.682c2.698 0 4.904-2.015 5.027-4.563H7.017v-.534z'%3e%3c/path%3e%3c/g%3e%3c/g%3e%3c/svg%3e"
                        alt="Cartes Bancaires"></noscript>
               </span>
               <span class="atw-inline-block atw-h-10 atw-border atw-border-orange atw-rounded-md atw-p-2">
                  <img class="atw-h-full lazyloaded"
                     src="data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='24' height='16' viewBox='0 0 24 16'%3e%3cg fill='none' fill-rule='nonzero'%3e%3cpath fill='%23FCFCFC' d='M22.684 16H1.38C.627 16 0 15.39 0 14.656V1.344C0 .61.627 0 1.379 0H22.62C23.373 0 24 .61 24 1.344v13.374c0 .671-.564 1.282-1.316 1.282z'%3e%3c/path%3e %3cpath fill='%23005098' d='M8.889 10.726l.948-5.393h1.482l-.949 5.393zM15.704 5.452a3.658 3.658 0 0 0-1.304-.237c-1.481 0-2.489.71-2.489 1.778 0 .77.711 1.185 1.304 1.481.592.237.77.415.77.652 0 .355-.474.533-.889.533-.592 0-.889-.059-1.363-.296l-.177-.06-.178 1.186c.355.178 1.007.296 1.659.296 1.54 0 2.548-.71 2.548-1.837 0-.592-.415-1.067-1.244-1.481-.534-.237-.83-.415-.83-.652 0-.237.237-.474.83-.474.474 0 .83.118 1.126.178l.118.059.119-1.126M19.496 5.333H18.37c-.355 0-.592.119-.77.474l-2.193 4.919h1.541s.237-.652.296-.83h1.897c.059.178.178.83.178.83h1.362l-1.185-5.393zM17.66 8.77l.593-1.481s.118-.296.178-.533l.118.474s.296 1.303.356 1.54h-1.245zM7.644 5.333L6.222 9.007l-.178-.77C5.748 7.348 4.92 6.459 4.03 5.985l1.303 4.682h1.541l2.311-5.393h-1.54'%3e%3c/path%3e %3cpath fill='%23F6A500' d='M4.919 5.333h-2.37v.119c1.836.474 3.08 1.54 3.555 2.844L5.57 5.807c-.118-.414-.355-.474-.651-.474'%3e%3c/path%3e %3cpath fill='%230A5296' d='M23.937 3.23H0V1.316C0 .598.627 0 1.379 0H22.62C23.373 0 24 .598 24 1.316V3.23h-.063z'%3e%3c/path%3e %3cpath fill='%23F4A428' d='M.063 13H24v1.8c0 .655-.625 1.2-1.375 1.2H1.375C.625 16 0 15.455 0 14.8V13h.063z'%3e%3c/path%3e%3c/g%3e%3c/svg%3e"
                     alt="Visa" data-ll-status="loaded"><noscript><img class="atw-h-full"
                        src="data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='24' height='16' viewBox='0 0 24 16'%3e%3cg fill='none' fill-rule='nonzero'%3e%3cpath fill='%23FCFCFC' d='M22.684 16H1.38C.627 16 0 15.39 0 14.656V1.344C0 .61.627 0 1.379 0H22.62C23.373 0 24 .61 24 1.344v13.374c0 .671-.564 1.282-1.316 1.282z'%3e%3c/path%3e %3cpath fill='%23005098' d='M8.889 10.726l.948-5.393h1.482l-.949 5.393zM15.704 5.452a3.658 3.658 0 0 0-1.304-.237c-1.481 0-2.489.71-2.489 1.778 0 .77.711 1.185 1.304 1.481.592.237.77.415.77.652 0 .355-.474.533-.889.533-.592 0-.889-.059-1.363-.296l-.177-.06-.178 1.186c.355.178 1.007.296 1.659.296 1.54 0 2.548-.71 2.548-1.837 0-.592-.415-1.067-1.244-1.481-.534-.237-.83-.415-.83-.652 0-.237.237-.474.83-.474.474 0 .83.118 1.126.178l.118.059.119-1.126M19.496 5.333H18.37c-.355 0-.592.119-.77.474l-2.193 4.919h1.541s.237-.652.296-.83h1.897c.059.178.178.83.178.83h1.362l-1.185-5.393zM17.66 8.77l.593-1.481s.118-.296.178-.533l.118.474s.296 1.303.356 1.54h-1.245zM7.644 5.333L6.222 9.007l-.178-.77C5.748 7.348 4.92 6.459 4.03 5.985l1.303 4.682h1.541l2.311-5.393h-1.54'%3e%3c/path%3e %3cpath fill='%23F6A500' d='M4.919 5.333h-2.37v.119c1.836.474 3.08 1.54 3.555 2.844L5.57 5.807c-.118-.414-.355-.474-.651-.474'%3e%3c/path%3e %3cpath fill='%230A5296' d='M23.937 3.23H0V1.316C0 .598.627 0 1.379 0H22.62C23.373 0 24 .598 24 1.316V3.23h-.063z'%3e%3c/path%3e %3cpath fill='%23F4A428' d='M.063 13H24v1.8c0 .655-.625 1.2-1.375 1.2H1.375C.625 16 0 15.455 0 14.8V13h.063z'%3e%3c/path%3e%3c/g%3e%3c/svg%3e"
                        alt="Visa"></noscript>
               </span>
               <span class="atw-inline-block atw-h-10 atw-border atw-border-orange atw-rounded-md atw-p-1">
                  <img class="atw-h-full lazyloaded"
                     src="data:image/svg+xml,%3csvg width='24px' height='16px' viewBox='0 0 24 16' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'%3e%3cg id='Parcours-1C-B' stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'%3e%3cg id='mobile-1C-Paiement-V1' transform='translate(-269.000000%2c -313.000000)'%3e%3cg id='mastercard' transform='translate(269.000000%2c 313.000000)'%3e%3crect id='Rectangle' fill='white' x='0' y='0' width='24' height='16' rx='1'%3e%3c/rect%3e %3cg id='Group-6' transform='translate(4.000000%2c 3.000000)'%3e%3ccircle id='Oval-4-Copy' fill='%23EA001B' cx='5.05263158' cy='5.05263158' r='5.05263158'%3e%3c/circle%3e %3ccircle id='Oval-4' fill-opacity='0.25' fill='%23F79F1A' cx='10.9473684' cy='5.05263158' r='5.05263158'%3e%3c/circle%3e %3ccircle id='Oval-4-Copy-2' fill-opacity='0.9' fill='%23F79F1A' cx='10.9473684' cy='5.05263158' r='5.05263158'%3e%3c/circle%3e %3ccircle id='Oval-4-Copy-3' fill-opacity='0.3' fill='%23EA001B' cx='5.05263158' cy='5.05263158' r='5.05263158'%3e%3c/circle%3e%3c/g%3e%3c/g%3e%3c/g%3e%3c/g%3e%3c/svg%3e"
                     alt="Mastercard" data-ll-status="loaded"><noscript><img class="atw-h-full"
                        src="data:image/svg+xml,%3csvg width='24px' height='16px' viewBox='0 0 24 16' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'%3e%3cg id='Parcours-1C-B' stroke='none' stroke-width='1' fill='none' fill-rule='evenodd'%3e%3cg id='mobile-1C-Paiement-V1' transform='translate(-269.000000%2c -313.000000)'%3e%3cg id='mastercard' transform='translate(269.000000%2c 313.000000)'%3e%3crect id='Rectangle' fill='white' x='0' y='0' width='24' height='16' rx='1'%3e%3c/rect%3e %3cg id='Group-6' transform='translate(4.000000%2c 3.000000)'%3e%3ccircle id='Oval-4-Copy' fill='%23EA001B' cx='5.05263158' cy='5.05263158' r='5.05263158'%3e%3c/circle%3e %3ccircle id='Oval-4' fill-opacity='0.25' fill='%23F79F1A' cx='10.9473684' cy='5.05263158' r='5.05263158'%3e%3c/circle%3e %3ccircle id='Oval-4-Copy-2' fill-opacity='0.9' fill='%23F79F1A' cx='10.9473684' cy='5.05263158' r='5.05263158'%3e%3c/circle%3e %3ccircle id='Oval-4-Copy-3' fill-opacity='0.3' fill='%23EA001B' cx='5.05263158' cy='5.05263158' r='5.05263158'%3e%3c/circle%3e%3c/g%3e%3c/g%3e%3c/g%3e%3c/g%3e%3c/svg%3e"
                        alt="Mastercard"></noscript>
               </span>
            </p>
         </div>
         <div
            class="younitedpay-howItWorks__planPanel atw-w-full md:atw-w-1/2 atw-p-8 atw-pb-24 md:atw-pb-8 md:atw-pt-8 atw-flex atw-flex-col atw-align-center atw-bg-beige">
            <div class="atw-flex-grow">
               <ul class="atw-flex atw-justify-center">
               {foreach from=$offers item=offer key=key}
                  {if $key === 0}
                     {assign var="border_block" value='atw-border-blue atw-border-b-2 '}
                     {assign var="background_block" value='atw-bg-blue'}
                  {else}
                     {assign var="border_block" value=''}
                     {assign var="background_block" value='atw-bg-prple'}
                  {/if}
                  <li
                     class="blocks_maturity block_maturity{$key} atw-group atw-inline-block atw-transition-all duration-200 atw-text-sm atw-mr-3 {$border_block}atw-cursor-pointer"
                        data-key="{$key}"
                        data-mouseover="{$offer.installment_amount}€ x{$offer.maturity}">
                     <span
                        class="atw-flex atw-p-2 atw-text-white atw-rounded-sm atw-my-4 atw-transition-colors atw-duration-200  atw-select-none {$background_block}">
                        {$offer.maturity}<svg
                           class="plan-selector-multiplier" width="16" height="16" viewBox="0 0 16 16" fill="none"
                           xmlns="http://www.w3.org/2000/svg">
                           <path
                              d="M12.5964 11.404L9.41445 8.22205L12.5964 5.04007C12.7732 4.86329 12.7732 4.50974 12.5964 4.33296L11.8893 3.62585C11.6904 3.42698 11.359 3.44908 11.1822 3.62585L8.00023 6.80783L4.81825 3.62585C4.61938 3.42698 4.28792 3.44908 4.11114 3.62585L3.40404 4.33296C3.20516 4.53183 3.20516 4.84119 3.40404 5.04007L6.58602 8.22205L3.40404 11.404C3.20516 11.6029 3.20517 11.9123 3.40404 12.1111L4.11115 12.8182C4.28792 12.995 4.61938 13.0171 4.81825 12.8182L8.00023 9.63626L11.1822 12.8182C11.359 12.995 11.6904 13.0171 11.8893 12.8182L12.5964 12.1111C12.7732 11.9344 12.7732 11.5808 12.5964 11.404Z"
                              fill="#fff"></path>
                        </svg>
                     </span>
                  </li>
               {/foreach}
               </ul>
               {foreach from=$offers item=offer key=key}
                  {if $key === 0}
                     {assign var="hiddenclass" value=''}
                  {else}
                     {assign var="hiddenclass" value='hidden '}
                  {/if}
                  <p class="{$hiddenclass}atw-mt-8 atw-text-lg block_contents block_content{$key}">
                     {l s='Payment by credit card, free of charge, in % instalments:' mod='younitedpay'|sprintf:{$offer.maturity}}
                  </p>
                  <p class="{$hiddenclass}atw-text-m block_contents block_content{$key}">
                     {l s='You will not be charged until the package has arrived.' mod='younitedpay'}
                  </p>
                  <div class="{$hiddenclass}atw-my-4 block_contents block_content{$key}">
                     <div class="atw-flex atw-flex-row atw-justify-between atw-mb-4">
                        <span>{l s='Total credit amount' mod='younitedpay'}</span>
                        <span class="younitedpay-title-text">{$offer.total_amount} €</span>
                     </div>
                     <hr class="atw-border-prple atw-opacity-50">
                     <div class="atw-flex atw-flex-row atw-justify-between atw-my-4">
                        <span>{l s='Monthly payment from the date of delivery' mod='younitedpay'}</span>
                        <span class="younitedpay-title-text">{$offer.installment_amount} €</span>
                     </div>
                     <hr class="atw-border-prple atw-opacity-50">
                     <div class="atw-flex atw-flex-row atw-justify-between atw-my-4">
                        <span>{l s='Credit duration' mod='younitedpay'}</span>
                        <span class="younitedpay-title-text">{$offer.maturity} mois</span>
                     </div>
                     <hr class="atw-border-prple atw-opacity-50">
                     <div class="atw-flex atw-flex-row atw-justify-between atw-my-4">
                        <span>{l s='Fixed APR' mod='younitedpay'}</span>
                        <span class="younitedpay-title-text">{$offer.taeg}%</span>
                     </div>
                     <hr class="atw-border-prple atw-opacity-50">
                     <div class="atw-flex atw-flex-row atw-justify-between atw-my-4">
                        <span>{l s='Fixed borrowing rate' mod='younitedpay'}</span>
                        <span class="younitedpay-title-text">{$offer.tdf}%</span>
                     </div>
                     <hr class="atw-border-prple atw-opacity-50 ">
                     <div class="atw-flex atw-flex-row atw-justify-between atw-my-4">
                        <span>{l s='Interest and fees' mod='younitedpay'}</span>
                        <span class="younitedpay-title-text">{$offer.interest_total} €</span>
                     </div>
                     <hr class="atw-border-prple atw-opacity-50">
                     <div class="atw-flex atw-flex-row atw-justify-between atw-pt-4 younitedpay-title-text">
                        <span>{l s='Total amount owed by the borrower' mod='younitedpay'}</span>
                        <span>{$offer.total_amount} €</span>
                     </div>
                  </div>
               {/foreach}
               <p class="atw-text-m">
                  « {l s='A loan commits you and must be repaid. Check your ability to repay before commit yourself.' mod='younitedpay'} »
               </p>
            </div>
            <div
               class="atw-mt-8 atw-fixed md:atw-relative atw-shadow-above-md md:atw-shadow-none atw-p-4 md:atw-p-0 atw-bg-beige md:atw-bg-transparent atw-bottom-0 atw-left-0 atw-right-0">
               <div class="atw-shadow-xl atw-rounded-sm">
                  <button
                     class="younited_btnhide atw-w-full atw-uppercase atw-bg-prple atw-text-white atw-h-12 
                     atw-rounded-sm younitedpay-title-text atw-bg-gradient-to-b atw-from-prple atw-via-prple 
                     atw-to-prple-700 atw-outline-none hover:atw-shadow-outline-prple focus:atw-shadow-outline-prple 
                     close-popup-you">{l s='I understand' mod='younitedpay'}</button>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>