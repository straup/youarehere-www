<?php

	loadlib("twofishes");
	loadlib("reverse_geocode");

	#################################################################

	function api_youarehere_geo_geocode(){

		if (! features_is_enabled("geocoder")){
			api_output_error(999, "The geocoder is currently not available");
		}

		$query = request_str("query");

		if (! $query){
			api_output_error(400, "Missing query term");
		}

		$rsp = twofishes_geocode($query);

		if (! $rsp['ok']){
			api_output_error(999, "The geocoder is sad...");
		}

		$data = $rsp['data']['interpretations'];
		$geojson = twofishes_interpretations_to_geojson($data);

		$out = array(
			'query' => $query,
			'features' => $geojson['features'],
		);

		api_output_ok($out);	
	}

	#################################################################

	function api_youarehere_geo_reverseGeocode(){

		if (! features_is_enabled("reverse_geocoder")){
			api_output_error(999, "The geocoder is currently not available");
		}

		$lat = request_float("lat");
		$lon = request_float("lon");

		if ((! $lat) || (! geo_utils_is_valid_latitude($lat))){
			api_output_error(500, "Missing or invalid latitude");
		}

		if ((! $lon) || (! geo_utils_is_valid_longitude($lon))){
			api_output_error(500, "Missing or invalid longitude");
		}

		$filter = request_str("filter");

		if (! $filter){
			$filter = reverse_geocode_default_filter();
		}

		else if (! reverse_geocode_is_valid_filter($filter)){
			api_output_error(500, "Missing or invalid filter");
		}

		else {}

		$rsp = reverse_geocode($lat, $lon, $filter);

		if (! $rsp['ok']){
			api_output_error(999, "The reverse geocoder is sad...");
		}

		$geojson = reverse_geocode_results_to_geojson($rsp['data']);
		$features = $geojson['features'];

		$out = array(
			'filter' => $filter,
			'latitude' => $lat,
			'longitude' => $lon,
			'features' => $features,
		);

		api_output_ok($out);
	}

	#################################################################

	# the end
