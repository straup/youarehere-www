var _map = null;

function youarehere_map(){

	var container_id = 'map';

	if (! _map){

		$("#" . container_id).show();

		var args = {
			//'scrollWheelZoom': false,
			'zoomControl': false,
			'attributionControl': false
		};

		var map = L.map(container_id, args);

		var toner = 'http://tile.stamen.com/toner-background/{z}/{x}/{y}.jpg';

		var base = L.tileLayer(toner, {
			attribution: '',
			maxZoom: 18,
			minZoom: 12
		});

		base.addTo(map);
		_map = map;

	}

	return _map;
}

function youarehere_map_init(geojson){

	var map = youarehere_map();

	youarehere_map_set_viewport(geojson);
	youarehere_map_draw_features(geojson);
}

function youarehere_map_set_viewport(geojson){

	var map = youarehere_map();

	var feature = geojson['features'][0];
	var bbox = feature.bbox;

	if (! bbox){
		var geom = feature.geometry;
		var coords = geom.coordinates;
		var centroid = [ coords[1], coords[0] ];
		map.setView(centroid, 15);
	}

	else {
		var extent = [ [bbox[1], bbox[0] ], [bbox[3], bbox[2] ] ];
		map.fitBounds(extent);
	}

}

function youarehere_map_draw_features(geojson){

	var layer = null

	var map = youarehere_map();

	var poly_style = {
		"color": '#000',
		"weight": 2,
		"opacity": 1,
		fillOpacity: .8,
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

	var poly_function = function(f){
		if (f['geometry']['type'] != 'Point'){
			return poly_style;
		}
	};

	var on_point = function (f, latlng) {
	        return L.circleMarker(latlng, point_style);
    	};

	var on_mouseover = function(e){
		var _layer = e.target;

		_layer.setStyle({ 'color': 'red', 'fillColor': '#8ca4be' });

		if (!L.Browser.ie && !L.Browser.opera){
			_layer.bringToFront();
		}
	};

	var on_mouseout = function(e){
		var _layer = e.target;
		layer.resetStyle(_layer);   
	};

	var on_feature = function(f, _layer){
		_layer.on({
			// 'click': undefined,
			'mouseover': on_mouseover,
        		'mouseout': on_mouseout
		});
	};

	var args = {
		'style': poly_function,
		'pointToLayer': on_point,
		'onEachFeature': on_feature
	};

	layer = L.geoJson(geojson, args);
	layer.addTo(map);

	return layer;
}

function youarehere_map_latlons_to_geojson(pairs){

	var features = new Array();

	var count = pairs.length;

	for (var i=0; i < count; i++){

		var lat = pairs[i][0];
		var lon = pairs[i][1];

		var geom = {
			'type': 'Point',
			'coordinates': [ lon, lat ]
		};

		var feature = {
			'type': 'Feature',
			'geometry': geom
		};

		features.push(feature);
	}

	var geojson = {
		'type': 'FeatureCollection',
		'features': features
	};

	if (count > 1){
		var bbox = youarehere_map_coords_to_bbox(pairs);
		geojson['bbox'] = bbox;
	}

	return geojson;
}

function youarehere_map_coords_to_bbox(coords, is_lonlat){

	var count = coords.length;

	var swlat = undefined;
	var swlon = undefined;
	var nelat = undefined;
	var nelon = undefined;

	for (var i=0; i < count; i++){

		var lat = (is_lonlat) ? coords[i][1] : coords[i][0];
		var lon = (is_lonlat) ? coords[i][0] : coords[i][0];

		swlat = (swlat == undefined) ? lat : Math.min(swlat, lat);
		swlon = (swlon == undefined) ? lon : Math.min(swlon, lon);
		nelat = (nelat == undefined) ? lat : Math.max(nelat, lat);
		nelon = (nelon == undefined) ? lon : Math.max(nelon, lon);
	}

	var bbox = [ swlon, swlat, nelon, nelat ];

	for (i in bbox){
		bbox[i] = bbox[i].toFixed(3);
	}

	return bbox;
}
