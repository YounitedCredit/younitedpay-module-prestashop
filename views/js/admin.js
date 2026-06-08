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

var younitedEvents = false;
var webHookTestLaunched = false;
var refundEvent = false;

document.onreadystatechange = function() {
    if (younitedEvents === true) {
        return false;
    }

    $('.younitedpay-collapse').click(toggleAccordion);
    if (younitedpay && younitedpay.nobootstrap === true) {
        // If we are on the configuration of help admin controller - not on orders
        $('#content').removeClass('bootstrap').addClass('nobootstrap');
    }

    var countries = [];
    $("#country_code option").each(function () {
        countries.push($(this).val().toLowerCase());
    });

    addEventsMaturity();
    addDoubleListEvent();
    addCountryEvent(countries);
    $('#younitedpay_maturitybtn').click(function(e) {
        addMaturity(e);
    });
    $('.copy-clipboard').click(function(e) {
        copyToClipboard(e);
    });
    $('.disable_on_change').click(function(e) {
        toggleDisabledZone(e);
    });
    $('[data-test-webhook]').click(function(e) {
        if (webHookTestLaunched === true) {
            return;
        }
        webHookTestLaunched = true;
        const countryCode = $(this).attr('data-test-country');
        var formData = new FormData();
        formData.append('testWebHookURL', $(this).attr('data-test-url'));
        formData.append('country', countryCode);

        $.ajax({
            type: "POST",
            data: formData,
            processData: false,
            enctype: 'multipart/form-data',
            contentType : false,
            cache : false,
            url: younitedpay.admin_url,
            success: function(data) {
                webHookTestLaunched = false;
                const response = JSON.parse(data);
                let success = 'error';
                let color = '#FAB000';
                if (response && response.success && response.success !== false) {
                    success = 'check';
                    color = '#25B9D7';
                    $.growl.notice({
                        title: younitedpay.translations.success,
                        message: younitedpay.translations.success_webhook,
                        duration: 5000,
                        location: 'br'
                    });
                } else {
                    $.growl.warning({
                        title: younitedpay.translations.error,
                        message: younitedpay.translations.error_webhook,
                        duration: 5000,
                        location: 'br'
                    });
                }
                $(this).find('i').css('color', color);
                $(this).find('i').text(success);
                $(this).find('i').attr('title', `Status: ${response.status} - Message: ${response.response}`);
                $(this).show();
                console.log(response);
                console.log(response.success);
                console.log(response.status);
            }
        });
    });
    $('#hide_requirements').click(HideRequirements);
    $('#younitedpay_status_min').click(ShowRequirements);
    $('#show_ranges_switch').click(updateShowHideRanges);
    $('#show_split_payment_switch').click(updateShowHideSplitPayment);
    $('#show_loan_payment_switch').click(updateShowHideLoanPayment);
    countries.forEach(function (country) {
        $('#younitedpay_prod_switch_' + country).click(() => YounitedhideZoneTest(country));
    });
    younitedEvents = true;
};

function addCountryEvent(countries)
{
    $('#country_code').on('change', () => updateShowCountryConfig(countries));
}

function updateShowCountryConfig(countries) {
    let countrySelected = $('#country_code').val().toLowerCase();
    let zoneToShow = [], zoneToHide = [];
    countries.forEach(function (country) {
        if (countrySelected === country) {
            zoneToShow.push('data-country-zone-' + country);
        } else {
            zoneToHide.push('data-country-zone-' + country);
        }
    });

    zoneToShow.forEach(function (zone) {
        $('div [' + zone + ']').removeClass('hidden');
    });

    zoneToHide.forEach(function (zone) {
        $('div [' + zone + ']').addClass('hidden');
    });

    YounitedhideZoneTest(countrySelected);
}

function updateShowHideRanges() {
    var rangesEnabled = $('#show_ranges_off').not(':checked').length > 0;
    if (rangesEnabled === true) {
        $('.ranges_min_max').removeClass('hidden');
        $('.ranges_not_min_max').addClass('hidden');
    } else {
        $('.ranges_min_max').addClass('hidden');
        $('.ranges_not_min_max').removeClass('hidden');
    }
}

function updateShowHideSplitPayment() {
    var splitPaymentEnabled = $('#show_split_payment_off').not(':checked').length > 0;
    if (splitPaymentEnabled === true) {
        $('.split-payment-panel').removeClass('hidden');
    } else {
        $('.split-payment-panel').addClass('hidden');
    }
}

function updateShowHideLoanPayment() {
    var loanPaymentEnabled = $('#show_loan_payment_off').not(':checked').length > 0;
    if (loanPaymentEnabled === true) {
        $('.loan-payment-panel').removeClass('hidden');
    } else {
        $('.loan-payment-panel').addClass('hidden');
    }
}

function HideRequirements()
{
    $('#younitedpay_status_block').attr('style','display:none!important;');
    $('#younitedpay_status_min').show();
}

function ShowRequirements()
{
    $('#younitedpay_status_block').removeAttr('style');
    $('#younitedpay_status_min').hide();
}

function refundYounitedPayEvent() {
    var checked = $('#doPartialRefundYounitedPay').is(':checked');
    var errorDisplay = false;
    if (checked === true) {
        var cancel_product_ps17 = $('#cancel_product_credit_slip');
        if (cancel_product_ps17.length > 0 && cancel_product_ps17.is(':checked') === false) {
            cancel_product_ps17.val(1);
            cancel_product_ps17.prop('checked', true);
            cancel_product_ps17.attr('checked', 'checked');
            errorDisplay = true;
        }
    }
    if (errorDisplay === true) {
        $.growl({
            title: younitedpay.translations.title_slip_refund,
            message: younitedpay.translations.slip_refund,
            duration: 5000,
            style: 'warning'
        });
    }
}

function toggleDisabledZone(event)
{
    var clickedZone = event.currentTarget;
    var zoneToToggle = $(clickedZone).attr('data-toggle');
    var inputId = $(clickedZone).attr('data-input');
    var inputValue = $('#' + inputId).not(':checked').length > 0;
    if (inputValue === false) {
        $('[data-' + zoneToToggle + ']').removeAttr('disabled');
        if (zoneToToggle === 'month') {
            $('#widget_input').prop('disabled', true);
            $('#widget_input').removeClass('widget_disabled');
            $('#widget_input').addClass('widget_enabled');
        }
    } else {
        $('[data-' + zoneToToggle + ']').prop('disabled', true);
        if (zoneToToggle === 'month') {
            $('#widget_input').addClass('widget_disabled');
            $('#widget_input').removeClass('widget_enabled');
        }
    }

}

function toggleAccordion()
{
    var younitedButton = $(this)[0];
    var younitedZone = younitedButton.getAttribute('data-target');
    var younitedButtonId = younitedButton.getAttribute('id');
    $($.find(younitedZone.toString())).slideToggle();
    $('#' + younitedButtonId + ' a').toggleClass('collapsed');
}

function deleteZoneMaturity()
{
    var maturityDeleteButton = $(this)[0];
    var keyElement = maturityDeleteButton.getAttribute('data-target');
    var idMaturity = maturityDeleteButton.getAttribute('data-id');
    if (parseInt(idMaturity) <= 0) {
        $('#younitedpay_maturity' + parseInt(keyElement)).remove();
    } else {
        $('#younitedpay_maturity' + parseInt(keyElement)).css('display', 'none');
        $('#younitedpay_delete' + parseInt(keyElement)).val(1);
    }
}

function addEventsMaturity()
{
    $('.younitedpay_delmaturity').off('click');
    $('.younitedpay_delmaturity').click(deleteZoneMaturity);

    $('.younitedpay_maturity_change').off('keyup');
    $('.younitedpay_maturity_change').off('change');
    $('.younitedpay_maturity_change').on('change keyup', UpdateMaturity);
}

function addMaturity(event)
{
  event.preventDefault();

  var formData = new FormData();
  formData.append('younitedpay_maturities', younitedpay.maturities);
  formData.append('younitedpay_add_maturity', true);

  $.ajax({
    type: "POST",
    data: formData,
    processData: false,
    enctype: 'multipart/form-data',
    contentType : false ,
    cache : false,
    url: younitedpay.admin_url,
    success: function(response){
        $("#younitedpay_maturities").append( response );
        addEventsMaturity();
        updateShowHideRanges();
        younitedpay.maturities += 1;
    }
  });
}

function UpdateMaturity()
{
    try {
        var targetObject = $(this)[0];
        var key = parseInt(targetObject.getAttribute('data-id'));

        var maturity = parseInt($('#maturity' + key).val());

        var minAmountVal = $('#min_amount_input_' + key).val();
        $('#min_amount_' + key).html((minAmountVal / maturity).toFixed(2));

        var maxAmountVal = $('#max_amount_input_' + key).val();
        $('#max_amount_' + key).html((maxAmountVal / maturity).toFixed(2));

        if (parseInt(maxAmountVal / maturity) > 0) {
            $('#max_amount_zone_' + key).removeClass('hidden');
        } else {
            $('#max_amount_zone_' + key).addClass('hidden');
        }
    } catch(error) {
        console.log(error);
    }
}

function copyToClipboard(event) {
    var text = $(event.currentTarget).attr('data-clipboard-copy');
    var message = $(event.currentTarget).attr('data-message');
    try {
        jQueryCopy(text);
        showConfZone(message);
        return true;
    } catch (errorjQuery) {
        console.log('Error copy jQuery' + errorjQuery);
    }
    try {
        navigator.clipboard.writeText(text);
        showConfZone(message);
        return true;
    } catch(error) {
        console.log('Error writeText' + error);
    }
    try {
        navigator.clipboard.write(text);
        showConfZone(message);
        return true;
    } catch(errorWrite) {
        console.log('Error write' + errorWrite);
    }
}

function showConfZone(msgInfo) {
    $.growl({ message: msgInfo });
}

function jQueryCopy(text) {
    var copyTextArea = document.createElement("textarea");
    document.body.appendChild(copyTextArea);
    copyTextArea.value = text;
    copyTextArea.select();
    document.execCommand("copy");
    document.body.removeChild(copyTextArea);
}

function addDoubleListEvent()
{
	$(".double-list-group").each(function()
    {
		var doubleList = $(this);
		var unselectedList = doubleList.find('.double-list-unselected');
		var selectedList = doubleList.find('.double-list-selected');

		doubleList.find('.double-list-btn-select').click(function(event) {
            event.preventDefault();
			unselectedList.find('option:selected').appendTo(selectedList);
			statutorder_doubleListUpdate(doubleList);
		});

		doubleList.find('.double-list-btn-unselect').click(function(event) {
            event.preventDefault();
			selectedList.find('option:selected').appendTo(unselectedList);
			statutorder_doubleListUpdate(doubleList);
		});
	});
}

function statutorder_doubleListUpdate(doubleList)
{
    var unselectedList = doubleList.find('.double-list-unselected');
    var selectedList = doubleList.find('.double-list-selected');
    var doubleListValues = doubleList.find('.double-list-values');

    selectedList.find('option').each(function() {
        doubleListValues.find("[value='"+this.value+"']").attr('checked', true);
    });
    unselectedList.find('option').each(function() {
        doubleListValues.find("[value='"+this.value+"']").attr('checked', false);
    });
}

function YounitedhideZoneTest(country)
{
    var valTest = $('#production_mode_on_' + country).not(':checked').length > 0;
    var zoneToShow = 'data-test-zone-' + country;
    var zoneToHide = 'data-prod-zone-' + country;
    if (valTest === false) {
        zoneToShow = 'data-prod-zone-' + country;
        zoneToHide = 'data-test-zone-' + country;
    }
    $('div [' + zoneToShow + ']').removeClass('hidden');
    $('div [' + zoneToHide + ']').addClass('hidden');
}
