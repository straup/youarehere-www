var _map = null;

function youarehere_map(){

	if (! _map){

		var args = {
			'scrollWheelZoom': false,
		};

		var map = L.map('map', args);

		var toner = 'http://tile.stamen.com/toner-background/{z}/{x}/{y}.jpg';

		var base = L.tileLayer(toner, {
			attribution: '',
			maxZoom: 18
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

	var map = youarehere_map();

	var poly_style = {
		"color": 'orange',
		"weight": 3,
		"opacity": 1,
		fillOpacity: .15,
		fillColor: 'yellow',
		"radius": 5,
	};

	var point_style = {
		"color": 'orange',
		"weight": 2,
		"opacity": 1,
		fillOpacity: .25,
		fillColor: 'red',
		"radius": 10,
	};

	var onfeature = function(f, layer){
		layer.on('click', function(){ });
	};

	var onpoint = function (f, latlng) {
	        return L.circleMarker(latlng, point_style);
    	};

	var args = {
		'style': poly_style,
		'pointToLayer': onpoint,
		'onEachFeature': onfeature
	};

	var shape = L.geoJson(geojson, args);
	shape.addTo(map);
}

function youarehere_map_latlon_to_geojson(lat, lon){

	var geom = {
		'type': 'Point',
		'coordinates': [ lon, lat ],
	};

	var feature = {
		'type': 'Feature',
		'geometry': geom,
	};

	var geojson = {
		'type': 'FeatureCollection',
		'features': [ feature ]
	};

	return geojson;
}
