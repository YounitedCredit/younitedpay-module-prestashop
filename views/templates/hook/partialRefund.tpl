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
<script>
  {literal}
    // add checkbox
    $(document).ready(() => {
        var chb_younited_refund = "{/literal}{$chb_younited_refund|escape:'htmlall':'UTF-8'}{literal}";

        // Make partial order refund in Order page in BO
        $(document).on('click', '#desc-order-partial_refund', function(){

            // Create checkbox and insert for Paypal refund
            if ($('#doPartialRefundYounitedPay').length == 0) {
                let newCheckBox = `<p class="checkbox"><label id="doPartialRefundYounitedZone" for="doPartialRefundYounitedPay">
                        <input type="checkbox" id="doPartialRefundYounitedPay" name="doPartialRefundYounitedPay" value="1">
                          ${chb_younited_refund}</label></p>`;
                $('button[name=partialRefund]').parent('.partial_refund_fields').prepend(newCheckBox);
                if (refundEvent === false) {
                    refundEvent = true;
                    $('#doPartialRefundYounitedZone').click(refundYounitedPayEvent);
                }
            }
        });

        $(document).on('click', '.partial-refund-display', function(){
            // Create checkbox and insert for Paypal refund
            if ($('#doPartialRefundYounitedPay').length == 0) {
                let newCheckBox = `
                        <div class="cancel-product-element form-group" style="display: block;">
                                <div class="checkbox">
                                    <div class="md-checkbox md-checkbox-inline">
                                      <label id="doPartialRefundYounitedZone" for="doPartialRefundYounitedPay">
                                          <input type="checkbox" id="doPartialRefundYounitedPay" name="doPartialRefundYounitedPay" 
                                          material_design="material_design" value="1">
                                          <i class="md-checkbox-control"></i>
                                            ${chb_younited_refund}
                                        </label>
                                    </div>
                                </div>
                         </div>`;

                $('.refund-checkboxes-container').prepend(newCheckBox);
                if (refundEvent === false) {
                    refundEvent = true;
                    $('.refund-checkboxes-container').click(refundYounitedPayEvent);
                }
            }
        });
    });
  {/literal}
</script>
