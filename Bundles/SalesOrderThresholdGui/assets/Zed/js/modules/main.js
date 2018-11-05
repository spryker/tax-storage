/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

require('ZedGui');
require('../../scss/main.scss');

var softThresholdStrategyToggle = function() {
    var softStrategy = $('input[name="global-threshold[softStrategy]"]:checked').val();
    var softValueBlock = $('#global-threshold_softThreshold').parents('.form-group');
    var softFixedFeeBlock = $('#global-threshold_softFixedFee').parents('.form-group');
    var softFlexibleFeeBlock = $('#global-threshold_softFlexibleFee').parents('.form-group');

    switch (softStrategy) {
        case 'soft-minimum-threshold':
            softValueBlock.removeClass('hidden');
            softFixedFeeBlock.addClass('hidden');
            softFlexibleFeeBlock.addClass('hidden');

            break;
        case 'soft-minimum-threshold-fixed-fee':
            softValueBlock.removeClass('hidden');
            softFixedFeeBlock.removeClass('hidden');
            softFlexibleFeeBlock.addClass('hidden');

            break;
        case 'soft-minimum-threshold-flexible-fee':
            softValueBlock.removeClass('hidden');
            softFixedFeeBlock.addClass('hidden');
            softFlexibleFeeBlock.removeClass('hidden');

            break;

        default:
            softValueBlock.addClass('hidden');
            softFixedFeeBlock.addClass('hidden');
            softFlexibleFeeBlock.addClass('hidden');
    }
};

$(document).ready(function () {
    softThresholdStrategyToggle();
    $('input[name="global-threshold[softStrategy]"]').click(softThresholdStrategyToggle);

    $('#global-threshold_storeCurrency').change(function() {
        window.location.href = '/sales-order-threshold-gui/global?store_currency='+$(this).val();
    })
});
