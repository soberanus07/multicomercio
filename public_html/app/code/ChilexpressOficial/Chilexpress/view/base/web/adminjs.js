require(["jquery"], function($) {
    $(document).ready(function(){
        console.log('adminjs');
        var $region,$comuna,$codigo_comuna;
        if ($("#chilexpress_devolucion_chilexpress_devolucion_group_region_devolucion").length) {
            $region = $("#chilexpress_devolucion_chilexpress_devolucion_group_region_devolucion");
            $comuna = $("#chilexpress_devolucion_chilexpress_devolucion_group_comuna_devolucion");        
            $codigo_comuna = $("#chilexpress_devolucion_chilexpress_devolucion_group_codigo_comuna_devolucion");
        }
        else if ( $("#chilexpress_origen_chilexpress_origen_group_region_origen") ){
            $region = $("#chilexpress_origen_chilexpress_origen_group_region_origen");
            $comuna = $("#chilexpress_origen_chilexpress_origen_group_comuna_origen");        
            $codigo_comuna = $("#chilexpress_origen_chilexpress_origen_group_codigo_comuna_origen");
        }

        // $codigo_comuna.parents('tr').hide();
        $codigo_comuna && $codigo_comuna.on("focus", function(ev) {
            ev.currentTarget.blur();
        });
        $region && $region.on('change', function (ev) {
            $.ajax({
                url: chilexpress_geo_comunas_url,
                data: {'form_key':  window.FORM_KEY, 'region': $(ev.currentTarget).val() },
                type: 'POST',
                dataType: 'json',
                showLoader: true
            }).done(function(data) {
                var html = '';
                data.data.forEach(function(item){
                    html += '<option value="' + item.id + '" '+(($codigo_comuna.val() == item.id)?'selected="selected"':'')+'>' + item.name + '</option>';
                });
                $comuna.html(html);
                $codigo_comuna.val($comuna.val());
            });
        });

        $region && $comuna.on('change', function (ev) {
            $codigo_comuna.val($comuna.val());
        });
        
        $.ajax({
            url: chilexpress_geo_comunas_url,
            data: {'form_key':  window.FORM_KEY, 'region': $region? $region.val():'RM'},
            type: 'POST',
            dataType: 'json',
            showLoader: true
        }).done(function(data) {
            var html = '';
            data.data.forEach(function(item){
                html += '<option value="' + item.id + '" '+(($codigo_comuna.val() == item.id)?'selected="selected"':'')+'>' + item.name + '</option>';
            });
            $comuna.html(html);
        });
        
    });
});
