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
    var infoInstallment = $('.maturity_installment' + actualOffer.toString()).attr('data-mouseover');
    $('.younitedpay_infoinstallment').html(infoInstallment);
    
    $('.blocks_maturity span').addClass('atw-bg-prple');
    $('.blocks_maturity span').removeClass('atw-bg-blue');
    $('.blocks_maturity').removeClass('atw-border-blue atw-border-b-2');
    $('.block_maturity' + key + ' span').addClass('atw-bg-blue');
    $('.block_maturity' + key + ' span').removeClass('atw-bg-prple');
    $('.block_maturity' + key).addClass('atw-border-blue atw-border-b-2');     
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

document.onreadystatechange = setTimeout(function() {
    if ($(".younitedpay-widget-root").length) {
        try {
            totalOffers = window.younited_product_offers_total;
        } catch(error) {
            console.log(error);
        }
        $('.maturity_installment').on("mouseover", mouseOverMaturity);
        $('.maturity_installment').on("mouseout", mouseOutMaturity);
        $('.younitedpay-howItWorks__planPanel .blocks_maturity').on("click", mouseOverMaturity);
        $('.younited_block').on("click", showPopup);
        $('.younited_btnhide').on("click", function(e) {
            hidePopup(e);
        });
        setTimeout(toggleInstallmentOffer, 2250);
    }
}, 75);

window.__toggleInstallmentOffer = toggleInstallmentOffer;