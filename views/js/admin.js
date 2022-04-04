/**
 * Copyright Bridge
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
 * @copyright Bridge
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

var younitedEvents = false;

document.onreadystatechange = function() {
    if (younitedEvents === true) {
        return false;
    }
    $('.younitedpay-collapse').click(toggleAccordion);
    younitedEvents = true;
};

function toggleAccordion() {    
    var younitedButton = $(this)[0];
    console.log(younitedButton);
    var younitedZone = younitedButton.getAttribute('data-target');
    var younitedButtonId = younitedButton.getAttribute('id');
    console.log(younitedZone);
    console.log(younitedButtonId);
    $(younitedZone.toString()).slideToggle();
    $('#' + younitedButtonId + ' a').toggleClass('collapsed');
}