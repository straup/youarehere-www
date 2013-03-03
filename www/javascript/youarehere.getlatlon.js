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

		var toner = 'http://tile.stamen.com/toner-background/{z}/{x}/{y}.jpg';

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

// this needs to be taught to deal with multiple features

function youarehere_getlatlon_set_viewport(geojson){

	var map = youarehere_getlatlon_map();

	var feature = geojson['features'][0];
	var bbox = feature.bbox;
	var geom = feature.geometry;

	// See if we can't calculate this based on the place type...

	var zoom = 12;

	if (geom['type'] == 'Point'){
		var coords = geom.coordinates;
		var centroid = [ coords[1], coords[0] ];
		map.setView(centroid, zoom);
	}

	else if (bbox){
		var extent = [ [bbox[1], bbox[0] ], [bbox[3], bbox[2] ] ];
		map.fitBounds(extent);
		// var bounds = map.getBounds();
		// bounds.extend(extent);
	}

	else {
		// console.log("SAD FACE");
	}    
}

// TO DO: allow styles to be passed in at runtime (20130218/straup)

function youarehere_getlatlon_draw_features(geojson){

	var layer = null;

	var map = youarehere_getlatlon_map();

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

	var on_feature = function(f, _layer){
	};

	var args = {
		'style': poly_function,
		'pointToLayer': on_point,
		'onEachFeature': on_feature
	};

	layer = L.geoJson(geojson, args);
	layer.addTo(map);

}
