function youarehere_correction_draw_map(geojson_url){

	var _onsuccess = function(geojson){
		youarehere_map_init(geojson);

		var feature = geojson['features'][0];
		var props = feature.properties;

		youarehere_correction_draw_shape(props['woe_id']);
	};

	$.ajax({
		'url': geojson_url,
		'success': _onsuccess
	});
}

function youarehere_correction_draw_shape(woeid){

	var geojson_url = 'http://woe.spum.org/id/' + woeid + '/shape.js';

	var _onsuccess = function(rsp){

		var geojson = {
			'type': 'FeatureCollection',
			'features': [ rsp ],
		};

		try {
			var l = youarehere_map_draw_features(geojson);
			l.bringToBack();

			youarehere_map_set_viewport(geojson);
		}

		catch (e){
			// youarehere_set_status("Hrm... I can't seem to draw the shape for that place");
		}
	}

	var _onerror = function(rsp){
		// youarehere_set_status("Hrm... I can't find the shape for that place");
	};

	$.ajax({
		'url': geojson_url,
		'dataType': 'json',
		'success': _onsuccess,
		'error': _onerror,
	});

}
