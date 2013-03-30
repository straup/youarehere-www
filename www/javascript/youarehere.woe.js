function youarehere_woe_draw_shapes(woeids){

	var swlat = undefined;
	var swlon = undefined;
	var nelat = undefined;
	var nelon = undefined;

	var count = woeids.length;

	// this is being weird... (20130218/straup)

	var to_draw = [];

	for (var i=0; i < count; i++){

		var woeid = woeids[i];

		if (woeid == -1){
			continue;
		}

		if ($.inArray(woeid, to_draw) == -1){
			to_draw.push(woeid);
		}
	}

	count = to_draw.length;

	for (var i=0; i < count; i++){

		var woeid = to_draw[i];

		var woeid_url = _cfg.woedb_static_url_template.replace("{W}", woeid);

		// see the '__' ?
		// it's important and explained below

		var __onsuccess = function(rsp){

			youarehere_map_update_feedback("drawing place boundaries...");

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

				for (var b in bbox){
					bbox[b] = parseFloat(bbox[b]);
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
				// console.log(e);
				// youarehere_set_status("Hrm... I can't seem to draw the shape for that place");
			}

			// Not convinced this is the best place to do this
			// (20130218/straup)

			var props = geojson['features'][0]['properties'];

			if (props){

				var id = props['woe_id'];
				var name = props['label'];

				if (props['id']){
					id = props['id'];
				}


				if (props['name']){
					name = props['name'];
				}

				var links = $(".woeid-" + id);
				links.html(name + " ( " + id + " )");
			}

			youarehere_map_update_feedback(null);
		};

		// I am guessing that there is some magic closure declaration
		// that I can invoke to do this automagically but I gave up trying
		// to figure out what it is. Remember how above we've been building
		// a bounding box based on all the shapes? If this is the last
		// shape then zoom to its extent after we've drawn the shape as we
		// might usually (20130218/straup)

		var _onsuccess = __onsuccess;

		if ((i + 1) == count){

			_onsuccess = function(rsp){
				__onsuccess(rsp);

				var extent = [[swlat, swlon], [nelat, nelon]];
				var map = youarehere_map();
				map.fitBounds(extent);
			};
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

		youarehere_map_update_feedback("fetching place boundaries...");
	}
}
