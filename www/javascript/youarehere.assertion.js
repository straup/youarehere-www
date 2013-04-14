function youarehere_assertion_draw_map(geojson_url){

	var _onsuccess = function(geojson){
		youarehere_map_init(geojson);

		var feature = geojson['features'][0];
		var props = feature.properties;

		var woeids = [props['woe_id']];
		youarehere_woe_draw_shapes(woeids);
	};

	$.ajax({
		'url': geojson_url,
		'success': _onsuccess
	});
}
