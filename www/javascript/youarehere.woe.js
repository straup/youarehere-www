function youarehere_woe_draw_shapes(woeids){

	var swlat = undefined;
	var swlon = undefined;
	var nelat = undefined;
	var nelon = undefined;

	var count = woeids.length;

	for (var i=0; i < count; i++){

		var woeid = woeids[i];

		var woeid_url = 'http://woe.spum.org/id/' + woeid + '/shape.js';

		if (woeid==18807771){
			woeid_url = 'http://gowanusheights.info/data/gowanus-heights.json';
		}

		var _onsuccess = function(rsp){

			var geojson = undefined;

			if (rsp['type'] == 'FeatureCollection'){
				geojson = rsp;

				var f = geojson['features'][0];

				if (! f['bbox']){
				    
					var coords = f['geometry']['coordinates'];
					coords = coords[0][0];

					var bbox = youarehere_map_coords_to_bbox(coords, 'lonlat');
					f['bbox'] = bbox;
					geojson['features'][0] = f;
				}
			}

			else {

				if (! rsp['bbox']){
					var coords = rsp['geometry']['coordinates'];
					coords = coords[0][0];

					var bbox = youarehere_map_coords_to_bbox(coords, 'lonlat');
					rsp['bbox'] = bbox;
				}

				geojson = {
					'type': 'FeatureCollection',
					'features': [ rsp ]
				};
			}

			if (geojson['features'][0]['bbox']){

				var bbox = geojson['features'][0]['bbox'];

				for (i in bbox){
					bbox[i] = parseFloat(bbox[i]);
				}

				swlat = (swlat == undefined) ? bbox[1] : Math.min(swlat, bbox[1]);
				swlon = (swlon == undefined) ? bbox[0] : Math.min(swlon, bbox[0]);
				nelat = (nelat == undefined) ? bbox[3] : Math.max(nelat, bbox[3]);
				nelon = (nelon == undefined) ? bbox[2] : Math.max(nelon, bbox[2]);
			}

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
			'url': woeid_url,
			'dataType': 'json',
			'success': _onsuccess,
			'error': _onerror
		});
	}

	/*
	var extent = [ swlat, swlon, nelat, nelon ];
	var map = youarehere_map();
	map.fitBounds(extent);
	*/
}
