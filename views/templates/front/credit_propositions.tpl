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
 <div id="younitedpay_propositions">
   {if count($offers) > 0}
      <p id="younited_infoclick">{l s='Click me for more informations' mod='younitedpay'}</p>
   {/if}
    {foreach from=$offers item=offer}
        <div class="younitedpay_offer mt-2 d-flex flex-row">
            <div class="younitedpay_logo flexmiddle">
               <img src="{$shop_url}{$logo_younitedpay_url}" alt="logo Younited Pay" />
            </div>
            <span class="younitedpay_maturity flexmiddle">
               <span class="maturity_top flexmiddle">
                  {$offer.maturity}X
               </span> 
               <span class="maturity_bottom"></span> 
            </span>
            <span class="younited_mensuality">
               {$offer.installment_amount}€ X{$offer.maturity}
            </span>
        </div>
    {/foreach}
 </div>