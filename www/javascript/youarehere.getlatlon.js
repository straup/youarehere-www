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

function youarehere_getlatlon_set_viewport(geojson){

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

		if (geom['type'] == 'Point'){

			var coords = geom.coordinates;
			var lat = coords[1];
			var lon = coords[0];

			swlat = (swlat == undefined) ? lat : Math.min(swlat, lat);
			swlon = (swlon == undefined) ? lon : Math.min(swlon, lon);
			nelat = (nelat == undefined) ? lat : Math.max(nelat, lat);
			nelon = (nelon == undefined) ? lon : Math.max(nelon, lon);
		}

		else if (bbox){
			swlat = (swlat == undefined) ? bbox[1] : Math.min(swlat, bbox[1]);
			swlon = (swlon == undefined) ? bbox[0] : Math.min(swlon, bbox[0]);
			nelat = (nelat == undefined) ? bbox[3] : Math.max(nelat, bbox[3]);
			nelon = (nelon == undefined) ? bbox[2] : Math.max(nelon, bbox[2]);
		}

		else {
			// console.log("SAD FACE");
		}    
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

// TO DO: allow styles to be passed in at runtime (20130218/straup)

function youarehere_getlatlon_draw_features(geojson){

	var layer = null;

	var map = youarehere_getlatlon_map();

	var poly_style = {
		"color": 'red',
		"weight": 6,
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

function youarehere_getlatlon_coords(){
	var map = youarehere_getlatlon_map();
	var centroid = map.getCenter();
	var zoom = map.getZoom();

	var lat = centroid['lat'];
	var lon = centroid['lng'];

	var str = lat.toFixed(6) + "," + lon.toFixed(6) + " @ zoom level " + zoom;

	$("#map-coords").html(str);
}
