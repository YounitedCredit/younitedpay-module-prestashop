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
(function ($) {
    $(function () {

        function mouseOverMaturity()
        {
            var maturityObject = $(this)[0];
            var infoInstallment = $(maturityObject).attr('data-mouseover');
            var key = $(maturityObject).attr('data-key');
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

        function showPopup()
        {
            $('#younited_popupzone').attr('style','');
        }

        function hidePopup()
        {
            $('#younited_popupzone').attr('style','display:none!important;');
        }

        if ($(".younitedpay-widget-root").length) {
            $('.maturity_installment').on("mouseover", mouseOverMaturity);
            $('.younitedpay-howItWorks__planPanel .blocks_maturity').on("click", mouseOverMaturity);
            $('.younited_block').on("click", showPopup);
            $('.younited_btnhide').on("click", hidePopup);
        }
    });
})(jQuery);