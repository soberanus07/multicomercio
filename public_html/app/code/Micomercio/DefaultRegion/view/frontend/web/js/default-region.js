define(['jquery', 'domReady!'], function($) {
    const REGION_ID_METROPOLITANA = "1483";

    function setDefaultRegion() {
        var $region = $('select[name="region_id"]');
        if ($region.length && !$region.val()) {
            $region.val(REGION_ID_METROPOLITANA).trigger('change');
            console.log("[Micomercio_DefaultRegion] Región Metropolitana preseleccionada.");
        }
    }

    setTimeout(setDefaultRegion, 500);
});
