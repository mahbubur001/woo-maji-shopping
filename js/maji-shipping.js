(function ($) {
    'use strict';
    let disableDates = [],
        current_city = $('#billing_city').val() || '';

    function checkHolidaysDates(date) {
        if (disableDates.length === 0) {
            disableDates.push($.datepicker.formatDate("dd-mm-yy", new Date()));
            const _tomorrow = new Date();
            _tomorrow.setDate(_tomorrow.getDate() + 1);
            disableDates.push($.datepicker.formatDate("dd-mm-yy", _tomorrow));
            disableDates.concat(maji.public_holidays || []);
        }

        let $return = true,
            returnClass = "available",
            checkDate = $.datepicker.formatDate("dd-mm-yy", date),
            checkDayNum = date.getDay(),
            weeklyDayOff = [0, 6];

        // Add weekly day off based on city
        if($.inArray(current_city, maji.zone_1) !== -1){
            weeklyDayOff.push(2,4);
        }else if($.inArray(current_city, maji.zone_2) !== -1){
            weeklyDayOff.push(1,3,5);
        }

        //Need time to prepare
        if ($.inArray(checkDate, disableDates) !== -1) {
            $return = false;
            returnClass = "unavailable wms_need_time_to_prepare";
        }
        //weekly closing day
        if ($.inArray(checkDayNum, weeklyDayOff) !== -1) {
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

    $('body').on('updated_checkout', function () {
        current_city = $('#billing_city').val();
        let selectedShipping = $("#shipping_method input[name='shipping_method[0]']:checked").val();
        // if ($("#shipping_method input[name='shipping_method[0]']").length === 1) {
        //     selectedShipping = $("#shipping_method input[name='shipping_method[0]']").val();
        // }
        if (selectedShipping) {
            selectedShipping = selectedShipping.split(':')[0];
            // let city_field_wrap = $("#billing_city_field .woocommerce-input-wrapper"),
            //     billing_state = $('#billing_state'),
            //     text_field = $('<input type="text" class="input-text" name="billing_city" id="billing_city" autocomplete="address-level2">')
            // text_field.val(current_city);
            if ("wms_pickup_shipping" === selectedShipping) {
                $('tr.wms-pickup-date-tr').show();
                // billing_state.prop("disabled", false);
                // city_field_wrap.html(text_field);
            }
            if ("wms_delivery_shipping" === selectedShipping) {
                $('tr.wms-delivery-date-tr').show();
                // billing_state
                //     .val("BC")
                //     .prop("disabled", true);
                // var cities = $('<select name="billing_city" id="billing_city">').html(maji.cities_of_bc.map(function (item) {
                //     var option = $('<option />').attr('value', item).text(item);
                //     if (current_city === item) {
                //         option.prop('selected', true);
                //     }
                //     return option;
                // }));
                // city_field_wrap.html(cities);
            }
        }
        $('.wms-date').datepicker(dp_obj);
    });
})(jQuery)