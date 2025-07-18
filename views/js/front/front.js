/**
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
 */
var offerOver = false;
var totalOffers = 0;

function YpClickOnMaturity()
{
    var maturityObject = $(this)[0];
    var key = $(maturityObject).attr('data-key');
    YpchangeInstallment(key);
}

function mouseOverMaturity()
{
    offerOver = true;
    var maturityObject = $(this)[0];
    var key = $(maturityObject).attr('data-key');
    YpchangeInstallment(key);
}

function mouseOutMaturity()
{
    offerOver = false;
}

function YpchangeInstallment(key, maturity = 0)
{
    if (maturity > 0) {
        younitedpay.rangeOffers.forEach(offer => {
            if (parseInt(offer.maturity) === parseInt(maturity)) {
                var zoneCustom = $('.maturity_installment9999');
                zoneCustom.attr('data-amount', offer.installment_amount);
                zoneCustom.attr('data-maturity', offer.maturity);
                zoneCustom.attr('data-initamount', offer.initial_amount);
                zoneCustom.attr('data-taeg', offer.taeg);
                zoneCustom.attr('data-tdf', offer.tdf);
                zoneCustom.attr('data-totalamount', offer.total_amount);
                zoneCustom.attr('data-interesttotal', offer.interest_total);
                key = "9999";
            }
        });
    }
    var actualOffer = parseInt(key);
    var maturityZone = $($.find('.maturity_installment' + actualOffer.toString()));
    var infoInstallmentAmount = parseFloat(maturityZone.attr('data-amount'));
    var currentMaturity = parseInt(maturityZone.attr('data-maturity'));
    var infoInstallmentMaturity = currentMaturity + 'x';
    var initialAmount = parseFloat(maturityZone.attr('data-initamount'));
    var taeg = parseFloat(maturityZone.attr('data-taeg'));
    var tdf = parseFloat(maturityZone.attr('data-tdf'));
    var totalAmount = parseFloat(maturityZone.attr('data-totalamount'));
    var interestTotal = parseFloat(maturityZone.attr('data-interesttotal'));
    
    $('.maturity_installment').removeClass('yp-bg-black-btn');
    $('.maturity_installment' + key).addClass('yp-bg-black-btn');

    $('.yp-install-amount').html(infoInstallmentAmount + " €");
    $('.yp-install-maturity').html(infoInstallmentMaturity);
    $('.yp-tdf').html(tdf);
    $('.yp-taeg').html(taeg);
    $('.yp-total').html(totalAmount);
    $('.yp-interest').html(interestTotal);
    $('.yp-amount').html(initialAmount);

    $('.yp-custom-range').val(currentMaturity);
    $('.yp-install-maturity-lite').html(currentMaturity);

    ypUpdatePaymentURL(currentMaturity);
}

function ypUpdatePaymentURL(maturity)
{
    if (typeof younitedpay.url_payment !== 'undefined') {
        var link = younitedpay.url_payment + '&maturity=' + maturity;
        $('form').each((index, form) => {
            var action = $(form).attr('action');
            if (typeof action !== 'undefined' && action.includes(younitedpay.url_payment) !== false) {
                $(form).attr('action', link);
            }
        });
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
    var ajaxData = {
        ajax: true,
        id_product: younitedpay.id_product,
        id_attribute: typeof event.id_product_attribute !== 'undefined' ? event.id_product_attribute : 0,
        type: younitedpay.type,
        qty: event.quantity_wanted,
    };
    $.ajax({
        url: younitedpay.url_product,
        type: 'POST',
        dataType: 'JSON',
        data: ajaxData,
        success(response) {
            if ('content' in response) {            
                $('.younitedpay_product_info').html(response.content);
                if (younitedpay.hook_product === 'displayAfterProductThumbs') {
                    var younitedProductZone = $('.younitedpay_product_info');
                    if (younitedProductZone.length > 1) {
                        for (var zone = 0; zone < younitedProductZone.length - 2; zone++) {
                            younitedProductZone[zone].remove();
                        }
                    }
                }
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

function YpsetRangeValue()
{
    var minValue = parseInt($('.yp-custom-range').attr('min'));
    var maxValue = parseInt($('.yp-custom-range').attr('max'));
    if (younitedpay.selected_maturity > maxValue) {
        younitedpay.selected_maturity = maxValue;
    }
    if (younitedpay.selected_maturity < minValue) {
        younitedpay.selected_maturity = minValue;
    }
    $('.yp-custom-range').val(younitedpay.selected_maturity);
    YpchangeInstallment(0, younitedpay.selected_maturity);
}

function bindEventsYounitedPay()
{
    if (typeof younitedpay.url_payment === 'undefined') {
        $('.maturity_installment').on("mouseover", mouseOverMaturity);
        $('.maturity_installment').on("mouseout", mouseOutMaturity);
        $('.blocks_maturities_popup').on("click", YpClickOnMaturity);
        $('.younited_block').on("click", showPopup);
        $('.younited_btnhide').on("click", function(e) {
            hidePopup(e);
        });
    }
    $('.maturity_installment').on("click", YpClickOnMaturity);
    
    $('body').off('click', '.yp-custom-range');
    $('body').on('click', '.yp-custom-range', function (e) {
        e.preventDefault();
        YpchangeRangeMaturity($(this).val());
    });

    $('body').on('mousedown', '.yp-custom-range', function (e) {
        younitedpay.is_range_down = true;
        younitedpay.selected_maturity = $(this).val();
    });
    $('body').on('mouseup', '.yp-custom-range', function (e) {
        younitedpay.is_range_down = false;
        YpchangeRangeMaturity($(this).val());
    });

    $('body').on('mousemove', '.yp-custom-range', function (e) {
        if (younitedpay.is_range_down) {
            YpchangeRangeMaturity($(this).val());
        }
    });

    $('body').on('touchmove', '.yp-custom-range', function (e) {
        YpchangeRangeMaturity($(this).val());
    });

    $('body').on('touchend', '.yp-custom-range', function (e) {
        YpchangeRangeMaturity($(this).val());
    });

    $('body').on('change', '.yp-custom-range', function (e) {
        YpchangeRangeMaturity($(this).val());
    });

    $('.yp-plus').on('click', function() {
        younitedpay.selected_maturity = parseInt($('.yp-custom-range').val()) + 1;
        YpsetRangeValue();
    });

    $('.yp-minus').on('click', function() {
        younitedpay.selected_maturity = parseInt($('.yp-custom-range').val()) - 1;
        YpsetRangeValue();
    });
}

function YpchangeRangeMaturity(value)
{
    if (value != younitedpay.selected_maturity) {
        younitedpay.selected_maturity = value;
        YpchangeInstallment(0, younitedpay.selected_maturity);
    }
}

var younitedEvents = false;

document.onreadystatechange = setTimeout(function() {
    if (younitedEvents === true) {
        return false;
    }

    younitedEvents = true;
    if ($(".maturity_installment").length) {
        bindEventsYounitedPay();
    }
    if (typeof prestashop !== 'undefined') {
        prestashop.on(
            'updatedProduct',
            function (event) {
                if (event.quantity_wanted == undefined) {
                    event.quantity_wanted = $('#quantity_wanted').val();
                }
                updateCreditZone(event);
            }
        );
        prestashop.on(
            'updatedCart',
            function (event) {
                if (event.quantity_wanted == undefined) {
                    event.quantity_wanted = $('#quantity_wanted').val();
                }
                updateCreditZone(event);
            }
        );
    }
}, 75);