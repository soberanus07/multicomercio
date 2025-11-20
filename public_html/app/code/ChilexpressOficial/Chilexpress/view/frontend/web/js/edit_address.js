define([
    "jquery",
], function($) {
        'use strict';
        
        return function(config, element) { // NOSONAR
            var $city = $("input#city");
            var $region = $("#region_id");
            var $city_parent = $city.parents('div.control');
            

            if ($city.length && $city[0].nodeName === 'INPUT') {
                $("#street_1").parent('div.control').siblings('label').css({"position":"relative","width":"auto"}).text('Nombre de la Calle');
                $("#street_2").parent('div.control').siblings('label').css({"position":"relative","width":"auto"}).text('Numeración');
                $("#street_3").parent('div.control').siblings('label').css({"position":"relative","width":"auto"}).text('Complemento');
                initCity($city,$region,$city_parent, false);
            }

            if ($("body.checkout-index-index").length){
                var timeOutIndex  = 0;
                function initCheckout() {
                    if ($("[name=region_id]").length) {
                        clearTimeout(timeOutIndex);
                        initCity($("[name=city]"),$("[name=region_id]"),$("[name=city]").parents('div.control'), true);

                    } else {
                        // console.log('not_found');
                        timeOutIndex = setTimeout(initCheckout,100);
                    }
                }
                var timeOutIndex = setTimeout(initCheckout,100);
            }

            function initCity($city,$region,$city_parent, isOnCheckout) {
                var cityval_original_val;

                // console.log('initCity isOnCheckout', isOnCheckout);
                var new_city_html = '';
                if (isOnCheckout) {
                    new_city_html = '<select name="cityx" aria-invalid="false" ';
                    new_city_html += ' id="' + $city.attr('id') + 'x"></select>';
                } else {
                    new_city_html = '<select name="city" title="City" class="input-text required-entry" id="city"><option value="'+cityval_original_val+'" selected="selected">Cargando Comuna</option></select>'
                }

                cityval_original_val = $city.val();
                
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
                       
                    });
                });

                isOnCheckout && $new_city.on('change', function (ev) {
                    $city.val($new_city.val());
                    $city.change();
                    $region.change();
                    
                });
                        
        }
    }
});