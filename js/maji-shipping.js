(function ($) {
    'use strict';
    let disableDates = [],
        current_city = $('#billing_city').val().trim() || '',
        selectedShipping = $("#shipping_method input[name='shipping_method[0]']:checked").val(),
        public_holidays = maji.public_holidays || [],
        zone_1 = maji.zone_1.map(function (item) {
            return item.toLowerCase()
        }),
        zone_2 = maji.zone_2.map(function (item) {
            return item.toLowerCase()
        });
    selectedShipping = selectedShipping ? selectedShipping.split(':')[0] : '';

    function getFormattedDate(date) {
        var d = ("0" + date.getDate()).slice(-2);
        var m = ("0" + (date.getMonth() + 1)).slice(-2);
        var y = date.getFullYear();
        return d + "-" + m + "-" + y;
    }

    function checkHolidaysDates(date) {
        if (disableDates.length === 0) {
            disableDates.push(getFormattedDate(new Date()));
            const _tomorrow = new Date();
            _tomorrow.setDate(_tomorrow.getDate() + 1);
            disableDates.push(getFormattedDate(_tomorrow));
            if ("wms_pickup_shipping" === selectedShipping && public_holidays.length) {
                disableDates = disableDates.concat(public_holidays);
            }
        }

        let $return = true,
            returnClass = "available",
            checkDate = getFormattedDate(date),
            checkDayNum = date.getDay(),
            weeklyDayOff = [0, 6];

        // Add weekly day off based on city
        if ("wms_delivery_shipping" === selectedShipping) {
            if ($.inArray(current_city.toLowerCase(), zone_1) !== -1) {
                weeklyDayOff.push(2, 4);
            } else if ($.inArray(current_city.toLowerCase(), zone_2) !== -1) {
                weeklyDayOff.push(1, 3, 5);
            }
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

    function nextActiveDay() {
        var today = new Date();
        var _count = 0;
        while (true) {
            var result = checkHolidaysDates(today);
            if (true === result[0]) {
                return _count;
            }
            _count++;
            today.setDate(today.getDate() + 1);
        }
    }

    var dp_obj = {
        minDate: 0,
        maxDate: 60,
        firstDay: 0,
        showAnim: "slideDown",
        dateFormat: 'DD - M d, yy',
        defaultDate: '+' + nextActiveDay(),
        beforeShowDay: function (date) {
            return checkHolidaysDates(date);
        },
        onSelect: function () {
        }
    };

    $('body').on('updated_checkout', function () {
        disableDates = [];
        current_city = $('#billing_city').val().trim();
        if(jQuery("#shipping_method input[name='shipping_method[0]']:checked").val() == null){
            selectedShipping = jQuery('#shipping_method_0_wms_pickup_shipping6').val();
        }else{
            selectedShipping = jQuery("#shipping_method input[name='shipping_method[0]']:checked").val();
        }
        //selectedShipping = $("#shipping_method input[name='shipping_method[0]']:checked").val();
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

        // Date picker
        $.datepicker.setDefaults( $.datepicker.regional["en"] );
        $('.wms-date').datepicker(dp_obj);
        $(".wms-date").keydown(function (event) {
            event.preventDefault();
        });
    });

    $('body').on('change keyup', '#billing_city', function () {
        $('#billing_state').trigger('change');
    });
})(jQuery)
