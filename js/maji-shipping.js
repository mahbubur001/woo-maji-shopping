(function ($) {
    'use strict';
    let disableDates = [];

    function checkHolidaysDates(date) {
        if (disableDates.length === 0) {
            disableDates.push($.datepicker.formatDate("dd-mm", new Date()));
            const _tomorrow = new Date();
            _tomorrow.setDate(_tomorrow.getDate() + 1);
            disableDates.push($.datepicker.formatDate("dd-mm", _tomorrow));
        }

        let $return = true,
            returnClass = "available",
            checkDate = $.datepicker.formatDate("dd-mm", date),
            checkDayNum = date.getDay();
        //Need time to prepare
        if ($.inArray(checkDate, disableDates) !== -1) {
            $return = false;
            returnClass = "unavailable wms_need_time_to_prepare";
        }
        //weekly closing day
        if ($.inArray(checkDayNum, [0, 6]) !== -1) {
            $return = false;
            returnClass = "unavailable wms_weekly_closing_day";
        }
        return [$return, returnClass];
    }

    var dp_obj = {
        minDate: 0,
        maxDate: 60,
        firstDay: 0,
        showAnim: "slideDown",
        dateFormat: 'DD - M d, yy',
        beforeShowDay: function (date) {
            return checkHolidaysDates(date);
        },
        onSelect: function () {

        }
    };

    $(function () {
        $('.wms-delivery-date').datepicker(dp_obj);
    });
    $('body').on('updated_checkout', function () {
        let selectedShipping = $("#shipping_method input[name='shipping_method[0]']:checked").val();
        if ($("#shipping_method input[name='shipping_method[0]']").length === 1) {
            selectedShipping = $("#shipping_method input[name='shipping_method[0]']").val();
        }
        if (selectedShipping) {
            selectedShipping = selectedShipping.split(':')[0];
            if ("wms_pickup_shipping" === selectedShipping) {
                $('tr.wms-pickup-date-tr').show();
            }
            if ("wms_delivery_shipping" === selectedShipping) {
                $('tr.wms-delivery-date-tr').show();
            }
        }
        $('.wms-date').datepicker(dp_obj);
    });
})(jQuery)