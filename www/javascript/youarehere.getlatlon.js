var _map = null;
var _points = new Array(); /* not awesome; see below for details */

function youarehere_getlatlon_map(){

	var container_id = 'map';

	if (! _map){

		$("#" . container_id).show();

		var args = {
			'scrollWheelZoom': true,
			'zoomControl': true,
			'attributionControl': false
		};

		var map = L.map(container_id, args);

		// map.on('load', youarehere_getlatlon_coords);
		map.on('move', youarehere_getlatlon_coords);

		var toner = 'http://tile.stamen.com/toner/{z}/{x}/{y}.jpg';

		var base = L.tileLayer(toner, {
			attribution: '',
			maxZoom: 18,
			minZoom: 1
		});

		base.addTo(map);
		_map = map;

	}

	return _map;
}

function youarehere_getlatlon_set_viewport(geojson){

	var parse_geom = function(geom){

		var _swlat = undefined;
		var _swlon = undefined;
		var _nelat = undefined;
		var _nelon = undefined;

		if (geom['type'] == 'GeometryCollection'){

			var count_geoms = geom['geometries'].length;

			for (var g=0; g < count_geoms; g++){

				var _bbox = parse_geom(geom['geometries'][g]);

				_swlat = (_swlat == undefined) ? _bbox[1] : Math.min(_swlat, _bbox[1]);
				_swlon = (_swlon == undefined) ? _bbox[0] : Math.min(_swlon, _bbox[0]);
				_nelat = (_nelat == undefined) ? _bbox[3] : Math.max(_nelat, _bbox[3]);
				_nelon = (_nelon == undefined) ? _bbox[2] : Math.max(_nelon, _bbox[2]);
			}
		}

		else if (geom['type'] == 'Point'){

			var coords = geom.coordinates;
			var lat = coords[1];
			var lon = coords[0];

			_swlat = (_swlat == undefined) ? lat : Math.min(_swlat, lat);
			_swlon = (_swlon == undefined) ? lon : Math.min(_swlon, lon);
			_nelat = (_nelat == undefined) ? lat : Math.max(_nelat, lat);
			_nelon = (_nelon == undefined) ? lon : Math.max(_nelon, lon);
		}

		else if (geom['type'] == 'Polygon'){

			var polys = geom.coordinates;
			var polys_count = polys.length;

			for (var p=0; p < polys_count; p++){

				var lines = polys[p];
				var lines_count = lines.length;

				for (var l=0; l < lines_count; l++){

					var coords = lines[l];
					var lat = coords[1];
					var lon = coords[0];

					_swlat = (_swlat == undefined) ? lat : Math.min(_swlat, lat);
					_swlon = (_swlon == undefined) ? lon : Math.min(_swlon, lon);
					_nelat = (_nelat == undefined) ? lat : Math.max(_nelat, lat);
					_nelon = (_nelon == undefined) ? lon : Math.max(_nelon, lon);
				}
			}
		}

		else {
			// console.log("SAD FACE " + geom['type']);
		}    

		return [ _swlat, _swlon, _nelat, _nelon ];
	};

	var map = youarehere_getlatlon_map();

	var features = geojson['features'];
	var count = features.length;

	var swlat = undefined;
	var swlon = undefined;
	var nelat = undefined;
	var nelon = undefined;

	for (var i=0; i < count; i++){

		var feature = features[i];
		var bbox = feature.bbox;
		var geom = feature.geometry;

		if (! bbox){
			bbox = parse_geom(geom);
		}

		swlat = (swlat == undefined) ? bbox[1] : Math.min(swlat, bbox[1]);
		swlon = (swlon == undefined) ? bbox[0] : Math.min(swlon, bbox[0]);
		nelat = (nelat == undefined) ? bbox[3] : Math.max(nelat, bbox[3]);
		nelon = (nelon == undefined) ? bbox[2] : Math.max(nelon, bbox[2]);
	}

	if ((swlat == nelat) && (swlon == nelon)){
		var centroid = [ swlat, swlon ];
		var zoom = 12;
		map.setView(centroid, zoom);
	}

	else {
		var extent = [[swlat, swlon], [nelat, nelon]];
		map.fitBounds(extent);
	}

}

// TO DO: pass in a UID and track to bounding box, etc (20130303/straup)

function youarehere_getlatlon_jumpto(lat, lon){

	var map = youarehere_getlatlon_map();

	var centroid = [ lat, lon ];
	var zoom = 12;	// make me better...
	map.setView(centroid, zoom);
}

// TO DO: allow styles to be passed in at runtime (20130218/straup)

function youarehere_getlatlon_draw_features(geojson){

	var layer = null;

	var map = youarehere_getlatlon_map();

	var poly_style = {
		"color": 'green',
		"weight": 6,
		"opacity": 1,
		fillOpacity: 0,
		fillColor: '#afceee'
	};

	var point_style = {
		"color": 'red',
		"weight": 4,
		"opacity": 1,
		fillOpacity: 1,
		fillColor: 'white',
		"radius": 8
	};

	// this needs to draw a dot if we're <= zoom ... 10?
	// (20130303/straup    

	var poly_function = function(f){

		if (f['geometry']['type'] != 'Point'){
			return poly_style;
		}
	};

	var on_point = function (f, latlng) {
	        return L.circleMarker(latlng, point_style);
    	};

	var on_feature = function(f, _layer){
	};

	// Basically the only way (I've found) to not draw
	// draw polygons if we're dealing with GeometryCollections
	// short of diving into the Leaflet source code and
	// patching things which I'm not interested in doing
	// at 7:30 in the morning... (20130312/straup)
    
	var features = geojson['features'];
	var count_f = features.length;

	for (var i=0; i < count_f; i++){

		var f = features[i];
		var g = f['geometry'];

		if (g['type'] == 'GeometryCollection'){
			var geoms = g['geometries'];
			var count_g = geoms.length;

			for (var j=0; j < count_g; j++){

				if (geoms[j]['type'] == 'Point'){
					g = geoms[j];
					f['geometry'] = g;
					geojson['features'][i] = f;
					break;
				}
			}
		}
	}

	var args = {
		// 'style': poly_function,
		'pointToLayer': on_point,
		'onEachFeature': on_feature
	};

	layer = L.geoJson(geojson, args);
	layer.addTo(map);

}

function youarehere_getlatlon_coords(){
	var map = youarehere_getlatlon_map();
	var centroid = map.getCenter();
	var zoom = map.getZoom();

	var lat = centroid['lat'];
	var lon = centroid['lng'];

	// TO DO: try to precalculate filter type based
	// on zoom level (20130303/straup)
    
	var href = "/maybe?lat=" + lat + "&lon=" + lon;

	var html = '';
	html += lat.toFixed(6) + ", " + lon.toFixed(6);
	html += " @ zoom level " + zoom;

	html += ' <span class="pointer">⇦</span> <a href="' + href + '">I am here</a>';

	$("#map-coords").html(html);
}
