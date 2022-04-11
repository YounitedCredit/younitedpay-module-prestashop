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

var younitedEvents = false;

document.onreadystatechange = function() {
    if (younitedEvents === true) {
        return false;
    }
    $('.younitedpay-collapse').click(toggleAccordion);
    addEventsDelete();
    $('#younitedpay_maturitybtn').click(function(e) {
        addMaturity(e);
    });
    younitedEvents = true;
};

function toggleAccordion()
{
    var younitedButton = $(this)[0];
    console.log(younitedButton);
    var younitedZone = younitedButton.getAttribute('data-target');
    var younitedButtonId = younitedButton.getAttribute('id');
    console.log(younitedZone);
    console.log(younitedButtonId);
    $(younitedZone.toString()).slideToggle();
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

function addEventsDelete()
{    
    $('.younitedpay_delmaturity').off('click');
    $('.younitedpay_delmaturity').click(deleteZoneMaturity);
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
        addEventsDelete();
        younitedpay.maturities += 1;  
    }
  });      
}