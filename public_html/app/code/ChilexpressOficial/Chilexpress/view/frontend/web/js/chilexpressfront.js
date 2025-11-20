define([
    "jquery",
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
    'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
    'Magento_Checkout/js/model/shipping-rate-registry',
], function($,quote, defaultProcessor, customerAddressProcessor, rateRegistry) {
        'use strict';
        return function(config, element) {  // NOSONAR
            window.recalculateCheckout = function recalculateCheckout(){
                setTimeout(function(){
                    var processors = [];
                    rateRegistry.set(quote.shippingAddress().getCacheKey(), null);
                    processors.default =  defaultProcessor;
                    processors['customer-address'] = customerAddressProcessor;
                    var type = quote.shippingAddress().getType();
                    var addr =  quote.shippingAddress();
                    addr['city'] = $("select[name=cityx]").val();
                    // console.log(addr['city'], addr);
                    if (processors[type]) {
                        processors[type].getRates(addr);
                    } else {
                        processors.default.getRates(addr);
                    }
                },1000);
            }
            
            var $city = $("input#city");
            var $region = $("#region_id");
            var $city_parent = $city.parents('div.control');
            

            if ($city.length && $city[0].nodeName === 'INPUT') {
                initCity($city,$region,$city_parent, false);
            }

            if ($("body.checkout-index-index").length){
                var timeOutIndex  = 0;
                function initCheckout() {
                    if ($("[name=region_id]").length) {
                        clearTimeout(timeOutIndex);

                        $("[name='street[0]']").parent('div.control').siblings('label').css({"position":"relative","width":"auto"}).text('Nombre de la Calle');
                        $("[name='street[1]']").parent('div.control').siblings('label').css({"position":"relative","width":"auto"}).text('Numeración');
                        $("[name='street[2]']").parent('div.control').siblings('label').css({"position":"relative","width":"auto"}).text('Complemento');

                        initCity($("[name=city]"),$("[name=region_id]"),$("[name=city]").parents('div.control'), true);

                    } else {
                        // console.log('not_found');
                        timeOutIndex = setTimeout(initCheckout,100);
                    }
                }
                var timeOutIndex = setTimeout(initCheckout,100);
            }

            function initCity($city,$region,$city_parent, isOnCheckout) {
                // console.log('initCity isOnCheckout', isOnCheckout);
                var new_city_html = '';
                var cityval_original_val = $city.val();

                if (isOnCheckout) {
                    new_city_html = '<select name="cityx" aria-invalid="false" ';
                    new_city_html += ' id="' + $city.attr('id') + 'x"></select>';
                } else {
                    new_city_html = '<select name="city" title="City" class="input-text required-entry" id="city"><option value="'+cityval_original_val+'" selected="selected">Cargando Comuna</option></select>'
                }

                var $new_city = $(new_city_html);
                $city_parent.append($new_city);
                if(!isOnCheckout){
                    $city.remove();
                } else {
                    $city.hide();
                }

                $.ajax({
                    url: chilexpress_front_geo_comunas_url,
                    data: {'region': $region.val()},
                    type: 'POST',
                    dataType: 'json',
                    // showLoader: true
                }).done(function(data) {
                    var html = '';
                    data.data.forEach(function(item){
                        html += '<option value="' + item.id + '" '+((cityval_original_val == item.id)?'selected="selected"':'')+'>' + item.name + '</option>';
                    });
                    $new_city.html(html);
                    isOnCheckout && $city.val($new_city.val());
                });
                
                $region && $region.on('change', function (ev) {
                    cityval_original_val = $new_city.val();
                    $new_city.html('<option value="'+cityval_original_val+'" selected="selected">Cargando Comuna</option>')
                    $.ajax({
                        url: chilexpress_front_geo_comunas_url,
                        data: { 'region': $region.val() },
                        type: 'POST',
                        dataType: 'json',
                        showLoader: true
                    }).done(function(data) {
                        var html = '';
                        data.data.forEach(function(item){
                            html += '<option value="' + item.id + '" '+((cityval_original_val == item.id)?'selected="selected"':'')+'>' + item.name + '</option>';
                        });
                        $new_city.html(html);
                        isOnCheckout && $city.val($new_city.val());
                        window.recalculateCheckout();
                    });
                });

                isOnCheckout && $new_city.on('change', function (ev) {
                    $city.val($new_city.val());
                    $city.change();
                    $region.change();
                    window.recalculateCheckout();
                });
                
            }

        }
    }
)