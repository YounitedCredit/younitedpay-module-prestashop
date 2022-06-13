/**
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
 */
var actualOffer = 0;
var offerOver = false;
var totalOffers = 0;

function mouseOverMaturity()
{
    offerOver = true;
    var maturityObject = $(this)[0];
    var key = $(maturityObject).attr('data-key');
    changeInstallment(key);
}

function mouseOutMaturity()
{
    offerOver = false;
}

function changeInstallment(key)
{
    actualOffer = parseInt(key);
    console.log(actualOffer);
    var infoInstallmentAmount = $('.maturity_installment' + actualOffer.toString()).attr('data-amount');
    var infoInstallmentMaturity = $('.maturity_installment' + actualOffer.toString()).attr('data-maturity');
    $('.yp-install-amount').html(infoInstallmentAmount);
    $('.yp-install-maturity').html(infoInstallmentMaturity);
    
    $('.blocks_maturity span').addClass('yp-bg-prple-grey');
    $('.blocks_maturity span').removeClass('yp-bg-prple');
    // $('.blocks_maturity').removeClass('yp-border-blue yp-border-b-2');

    $('.block_maturity' + key + ' span').addClass('yp-bg-prple');
    $('.block_maturity' + key + ' span').removeClass('yp-bg-prple-grey');
    // $('.block_maturity' + key).addClass('yp-border-blue yp-border-b-2');     
    
    $('.blocks_maturities_popup span').addClass('yp-bg-white yp-pol-black');
    $('.blocks_maturities_popup span').removeClass('yp-bg-black yp-pol-white');

    $('.block_maturity_popup' + key + ' span').addClass('yp-bg-black yp-pol-white');
    $('.block_maturity_popup' + key + ' span').removeClass('yp-bg-white yp-pol-black');

    $('.block_contents').addClass('hidden');
    $('.block_content' + key).removeClass('hidden');
}

function toggleInstallmentOffer(disable)
{
    var stylePopup = $('#younited_popupzone').attr('style');
    if (totalOffers > 0 && offerOver === false && stylePopup === 'display:none!important;') {
        actualOffer += 1;
        if (actualOffer > totalOffers) {
            actualOffer = 0;
        }
        changeInstallment(actualOffer);
    }
    if (disable === null || disable === false || disable === undefined) {
        setTimeout(toggleInstallmentOffer, 2250);
    }
}

function showPopup()
{
    $('#younited_popupzone').attr('style','');
    offerOver = false;
}

function hidePopup(e)
{
    e.preventDefault();
    mouseOutMaturity();
    $('#younited_popupzone').attr('style','display:none!important;');
}

function updateCreditZone(event)
{    
      $.ajax({
          url: younitedpay.url_product,
          type: 'POST',
          dataType: 'JSON',
          data: {
              ajax: true,
              id_product: younitedpay.id_product,
              id_attribute: event.id_product_attribute,
              qty: event.quantity_wanted,
          },
          success(response) {
              if ('content' in response) {            
                $('.younitedpay_product_info').html(response.content);
                bindEventsYounitedPay();
              }
              if ('number_offers' in response) {            
                younitedpay.number_offers = response.number_offers;
              }
          },
          error(errorMessage) {
            console.log(errorMessage);
          }
      });
}

function bindEventsYounitedPay()
{
    $('.maturity_installment').on("mouseover", mouseOverMaturity);
    $('.maturity_installment').on("mouseout", mouseOutMaturity);
    $('.blocks_maturities_popup').on("click", mouseOverMaturity);
    $('.younited_block').on("click", showPopup);
    $('.younited_btnhide').on("click", function(e) {
        hidePopup(e);
    });
}

var younitedEvents = false;

document.onreadystatechange = setTimeout(function() {
    if (younitedEvents === true) {
        return false;
    }

    younitedEvents = true;
    if ($(".younitedpay-widget-root").length) {
        bindEventsYounitedPay();
        if (typeof prestashop !== 'undefined') {
            prestashop.on(
                'updatedProduct',
                function (event) {
                    console.log(event);
                    if (event.quantity_wanted == undefined) {
                        event.quantity_wanted = $('#quantity_wanted').val();
                    }
                    updateCreditZone(event);
                }
            );
        }
    }
}, 75);

window.__toggleInstallmentOffer = toggleInstallmentOffer;