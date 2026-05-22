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

/* ----------------------------------------
 * GLOBAL STATE
 * ---------------------------------------- */

var offerOver = false;
var totalOffers = 0;
var allowedMaturities = [];

/* ----------------------------------------
 * MOUSE / INTERACTIONS
 * ---------------------------------------- */

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

/* ----------------------------------------
 * INSTALLMENT LOGIC
 * ---------------------------------------- */

function YpchangeInstallment(key, maturity = 0)
{
    key = resolveOfferFromMaturity(maturity, key);

    let actualOffer = parseInt(key);
    let dataset = mapOfferDataset(actualOffer);

    updateGlobalUI(dataset);

    handlePersonalLoanUI(dataset);

    handleSplitPaymentUI(dataset);

    handleButtonUI(key);

    ypUpdatePaymentURL(dataset);
}

function resolveOfferFromMaturity(maturity, key)
{
    if (maturity <= 0) return key;

    younitedpay.rangeOffers.forEach(offer => {
        if (parseInt(offer.maturity) === parseInt(maturity)) {
            let zoneCustom = $('.maturity_installment9999');
            zoneCustom.attr('data-amount', offer.installment_amount);
            zoneCustom.attr('data-maturity', offer.maturity);
            zoneCustom.attr('data-initamount', offer.initial_amount);
            zoneCustom.attr('data-taeg', offer.taeg);
            zoneCustom.attr('data-tdf', offer.tdf);
            zoneCustom.attr('data-type', offer.type);
            zoneCustom.attr('data-totalamount', offer.total_amount);
            zoneCustom.attr('data-interesttotal', offer.interest_total);
            zoneCustom.attr('data-feetotal', offer.fee_total);
            zoneCustom.attr('data-downpayment', offer.down_payment_amount);
            zoneCustom.attr('data-installment', new DOMParser().parseFromString(offer.installment, "text/html").documentElement.textContent);
        }
    });

    return "9999";
}

function mapOfferDataset(actualOffer)
{
    let maturityZone = $($.find('.maturity_installment' + actualOffer.toString()));

    let data = {
        amount: parseFloat(maturityZone.attr('data-amount')),
        maturity: parseInt(maturityZone.attr('data-maturity')),
        initial_amount: parseFloat(maturityZone.attr('data-initamount')),
        taeg: parseFloat(maturityZone.attr('data-taeg')),
        tdf: parseFloat(maturityZone.attr('data-tdf')),
        total_amount: parseFloat(maturityZone.attr('data-totalamount')),
        fee_total: parseFloat(maturityZone.attr('data-feetotal')),
        interest_total: parseFloat(maturityZone.attr('data-interesttotal')),
        down_payment_amount: parseFloat(maturityZone.attr('data-downpayment')),
        type: maturityZone.attr('data-type'),
        installment: maturityZone.attr('data-installment'),
    };

    return data;

}

function updateGlobalUI(dataset)
{
    let infoInstallmentAmount = dataset.amount.toFixed(2).replace('.', ',');
    let currentMaturity = dataset.maturity;
    let initialAmount = dataset.initial_amount.toFixed(2).replace('.', ',');
    let taeg = dataset.taeg.toFixed(2).replace('.', ',');
    let tdf = dataset.tdf.toFixed(2).replace('.', ',');
    let totalAmount = dataset.total_amount.toFixed(2).replace('.', ',');
    let feeTotal = dataset.fee_total.toFixed(2).replace('.', ',');
    let interestTotal = dataset.interest_total.toFixed(2).replace('.', ',');
    let downPaymentAmount = dataset.down_payment_amount.toFixed(2).replace('.', ',');
    let type = dataset.type;
    let infoInstallmentMaturity = currentMaturity + 'x';
    let installment = dataset.installment;

    $('.yp-install-amount').text(infoInstallmentAmount + " €");
    $('.yp-install-maturity-split').text(currentMaturity + " x");
    $('.yp-install-maturity').text(infoInstallmentMaturity);
    $('.yp-maturity').text(currentMaturity);
    $('.yp-tdf').text(tdf);
    $('.yp-taeg').text(taeg);
    $('.yp-total').text(totalAmount);
    $('.yp-interest').text(interestTotal);
    $('.yp-fee').text(feeTotal + " €");
    $('.yp-amount').text(initialAmount);
    $('.yp-down-amount').text(downPaymentAmount);
    $('.yp-custom-range').val(getIndexFromMaturity(currentMaturity));
    $('.yp-install-maturity-lite').text(currentMaturity);
}

function handleSplitPaymentUI(dataset)
{
    if (dataset.type !== 'SplitPayment') return;

    $('.yp-offer-zone').removeClass('hidden');
    $('.yp-more-details-zone').addClass('hidden');
    $('.yp-split-payment-info').removeClass('hidden');
    $('.yp-down-amount-parent').addClass('hidden');
    $('.yp-not-down-amount-parent').removeClass('hidden');
    $('.yp-fees-text').removeClass('hidden');
    $('.yp-interest-text').addClass('hidden');

    if (dataset.fee_total > 0) {
        $('.yp-without-fee-text').addClass('hidden');
        $('.yp-with-fee-text').removeClass('hidden');
        $('.yp-without-fee').addClass('hidden');
        $('.yp-with-fee').removeClass('hidden');
    } else {
        $('.yp-without-fee-text').removeClass('hidden');
        $('.yp-with-fee-text').addClass('hidden');
        $('.yp-without-fee').removeClass('hidden');
        $('.yp-with-fee').addClass('hidden');
    }

    let splitPaymentInstallment = document.querySelectorAll('.yp-split-payment-installment');

    splitPaymentInstallment.forEach(element => {
        let splitPaymentMaturity = parseInt(element.dataset.splitpaymentmaturity);
        if (splitPaymentMaturity > dataset.maturity) {
            element.classList.add('hidden');
        } else {
            element.classList.remove('hidden');
        }
    });

    let splitPaymentTimeline = document.querySelectorAll('.yp-timeline__item');

    splitPaymentTimeline.forEach(element => {
        let splitPaymentMaturity = parseInt(element.dataset.splitpaymentmaturity);
        if (splitPaymentMaturity > dataset.maturity) {
            element.classList.add('hidden');
        } else {
            element.classList.remove('hidden');
        }
    });

    if (dataset.installment !== null && typeof dataset.installment !== 'undefined' && dataset.installment !== '') {
        let installmentData = JSON.parse(dataset.installment);

        if (installmentData !== null && typeof installmentData !== 'undefined' && installmentData.length) {
            for (let i = 0; i < installmentData.length; i++) {
                let installmentTotalAmount = parseFloat(installmentData[i].totalAmount).toFixed(2).replace('.', ',');
                let installmentDueDate = new Date(installmentData[i].dueDate).toLocaleDateString('fr-FR');;
                $('.yp-install-' + i).text(installmentTotalAmount + " €");
                $('.yp-due-date-' + i).text(installmentDueDate);
            }
        }
    }
}

function handlePersonalLoanUI(dataset)
{
    if (dataset.type !== 'PersonalLoan') return;

    $('.yp-offer-zone').addClass('hidden');
    $('.yp-more-details-zone').removeClass('hidden');
    $('.yp-split-payment-info').addClass('hidden');
    $('.yp-without-fee-text').addClass('hidden');
    $('.yp-with-fee-text').removeClass('hidden');
    $('.yp-interest-text').removeClass('hidden');
    $('.yp-fees-text').addClass('hidden');

    if (dataset.down_payment_amount <= 0) {
        $('.yp-down-amount-parent').addClass('hidden');
        $('.yp-not-down-amount-parent').removeClass('hidden');
    } else {
        $('.yp-down-amount-parent').removeClass('hidden');
        $('.yp-not-down-amount-parent').addClass('hidden');
    }
}

function handleButtonUI(key)
{
    $('.maturity_installment').removeClass('yp-bg-black-btn');
    $('.maturity_installment' + key).addClass('yp-bg-black-btn');

    let $sliderSvg = $('.maturity_installment9999 img');
    let sliderSvgSrc = $sliderSvg.attr('src') || '';
    if ($('.maturity_installment' + key).hasClass('maturity_installment9999')) {
        if (sliderSvgSrc.indexOf('black') !== -1) {
            let newSliderSvgSrc = sliderSvgSrc.replace('black', 'white');
            $sliderSvg.attr('src', newSliderSvgSrc);
        }
    } else {
        if (sliderSvgSrc.indexOf('white') !== -1) {
            let newSliderSvgSrc = sliderSvgSrc.replace('white', 'black');
            $sliderSvg.attr('src', newSliderSvgSrc);
        }
    }
}

function ypUpdatePaymentURL(dataset)
{
    let withDownPayment = dataset.down_payment_amount > 0;
    if (typeof younitedpay.url_payment !== 'undefined') {
        let link = younitedpay.url_payment + '&maturity=' + dataset.maturity + '&type=' + dataset.type;
        if (withDownPayment === true) {
            link += '&down_payment=1';
        }
        $('form').each((index, form) => {
            let action = $(form).attr('action');
            if (typeof action !== 'undefined' && action.includes(younitedpay.url_payment) !== false) {
                $(form).attr('action', link);
            }
        });
    }
}

/* ----------------------------------------
 * POPUP
 * ---------------------------------------- */

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

/* ----------------------------------------
 * REFRESH LOGIC
 * ---------------------------------------- */

function updateCreditZone(event)
{
    if (typeof younitedpay === 'undefined' || !younitedpay) {
        return;
    }
    var ajaxData = {
        ajax: true,
        id_product: younitedpay.id_product,
        id_attribute: typeof event.id_product_attribute !== 'undefined' ? event.id_product_attribute : 0,
        type: younitedpay.type,
        qty: event.quantity_wanted,
    };
    if (typeof younitedpay.id_lang !== 'undefined') {
        ajaxData.id_lang = younitedpay.id_lang;
    }
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

/* ----------------------------------------
 * RANGE LOGIC
 * ---------------------------------------- */

function YpsetRangeValue()
{
    if (allowedMaturities && allowedMaturities.length) {
        if (!allowedMaturities.includes(parseInt(younitedpay.selected_maturity))) {
            younitedpay.selected_maturity = allowedMaturities[0];
        }
    }

    var minValue = allowedMaturities[0];
    var maxValue = allowedMaturities[allowedMaturities.length - 1];
    if (younitedpay.selected_maturity > maxValue) {
        younitedpay.selected_maturity = maxValue;
    }
    if (younitedpay.selected_maturity < minValue) {
        younitedpay.selected_maturity = minValue;
    }

    $('.yp-custom-range').val(getIndexFromMaturity(younitedpay.selected_maturity));

    YpchangeInstallment(0, younitedpay.selected_maturity);

    syncRange(younitedpay.selected_maturity);
}

function YpchangeRangeMaturity(value)
{
    const index = parseInt(value);
    const maturity = getMaturityFromIndex(index);

    younitedpay.selected_maturity = maturity;

    YpchangeInstallment(index, maturity);

    syncRange(maturity);
}

function updateRangeLimits()
{
    const range = document.querySelector('.yp-custom-range');

    if (!range || !allowedMaturities.length) {
        return;
    }

    range.min = 0;
    range.max = allowedMaturities.length - 1;
    range.step = 1;
}

function getMaturityFromIndex(index)
{
    return allowedMaturities[index] ?? allowedMaturities[0];
}

function getIndexFromMaturity(maturity)
{
    const index = allowedMaturities.indexOf(parseInt(maturity));

    return index !== -1 ? index : 0;
}

function syncRange(maturity)
{
    const range = document.querySelector('.yp-custom-range');
    if (!range) return;

    range.value = getIndexFromMaturity(maturity);

    syncActiveButtonWithMaturity(maturity);
}

function syncActiveButtonWithMaturity(maturity)
{
    $('.maturity_installment').removeClass('yp-bg-black-btn');
    $('.maturity_popup').addClass('yp-bg-grey-btn');

    const button = $('.maturity_installment[data-maturity="' + maturity + '"]');

    button.removeClass('yp-bg-grey-btn');
    button.addClass('yp-bg-black-btn');

    document.querySelectorAll('.maturity_installment9999 img').forEach(function(el){
        let sliderSvgSrc = el.getAttribute('src') || '';

        if (sliderSvgSrc.indexOf('black') !== -1) {
            let newSliderSvgSrc = sliderSvgSrc.replace('black','white');
            el.setAttribute('src', newSliderSvgSrc);
        }
    });
}

/* ----------------------------------------
 * INIT EVENTS
 * ---------------------------------------- */

function bindEventsYounitedPay()
{
    allowedMaturities = younitedpay.rangeOffers
        .map(offer => parseInt(offer.maturity))
        .sort((a, b) => a - b);

    younitedpay.allowed_maturities = allowedMaturities;

    bindMaturityEvents();

    bindRangeEvents();
}

function bindMaturityEvents()
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

    $('.maturity_installment9999').on('mouseover', (e) => {
        $('.yp-range-text').removeClass('hidden');
        if (younitedpay.rangeForced === true) {
            $('.yp-range-slider').removeClass('hidden');
        }
    });

    $('.maturity_installment:not(.maturity_installment9999)').on('mouseover', (e) => {
        $('.yp-range-text').addClass('hidden');
        if (younitedpay.rangeForced === true) {
            $('.yp-range-slider').addClass('hidden');
        }
    });
}

function bindRangeEvents()
{
    updateRangeLimits();

    $('body').off('click', '.yp-custom-range');
    $('body').on('click', '.yp-custom-range', function (e) {
        e.preventDefault();
        YpchangeRangeMaturity($(this).val());
    });

    $('body').on('mousedown', '.yp-custom-range', function (e) {
        younitedpay.is_range_down = true;

        const index = parseInt($(this).val());

        younitedpay.selected_maturity = getMaturityFromIndex(index);
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

        const current = younitedpay.selected_maturity;
        const index = getIndexFromMaturity(current);

        const newIndex = index + 1;

        if (newIndex >= allowedMaturities.length) return;

        const maturity = allowedMaturities[newIndex];

        younitedpay.selected_maturity = maturity;

        YpsetRangeValue();
    });

    $('.yp-minus').on('click', function() {
        const current = younitedpay.selected_maturity;
        const index = getIndexFromMaturity(current);

        const newIndex = index - 1;

        if (newIndex < 0) return;

        const maturity = allowedMaturities[newIndex];

        younitedpay.selected_maturity = maturity;

        YpsetRangeValue();
    });
}

/* ----------------------------------------
 * BOOT
 * ---------------------------------------- */

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
