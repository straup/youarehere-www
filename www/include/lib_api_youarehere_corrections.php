<?php

	loadlib("corrections_geojson");

	#################################################################

	function api_youarehere_corrections_doThis(){

		api_output_error(999, "YAHOO SAYS NO");

		$lat = post_float("lat");
		$lon = post_float("lon");

		if ((! $lat) || (! geo_utils_is_valid_latitude($lat))){
			api_output_error(500, "Missing or invalid latitude");
		}

		if ((! $lon) || (! geo_utils_is_valid_longitude($lon))){
			api_output_error(500, "Missing or invalid longitude");
		}

		$woeid = post_float("woeid");

		if (! $woeid){
			api_output_error(500, "Missing WOE ID");
		}
	}

	#################################################################

	function api_youarehere_corrections_getCorrectionsByDate(){

		$start_date = request_str("start_date");

		if (! $start_date){
			api_output_error(404, "Missing start date");
		}

		$end_date = request_str("end_date");

		if (! $end_date){
			api_output_error(404, "Missing end date");
		}

		$start_date = strtotime($start_date);

		if (! $start_date){
			api_output_error(404, "Invalid start date");
		}

		$end_date = strtotime($end_date);

		if (! $end_date){
			api_output_error(404, "Invalid end date");
		}

		if ($start_date >= $end_date){
			api_output_error(999, "Invalid date range");
		}

		# sudo put me in a config file or something...
		$max = 60 * 60 * 24 * 28;

		if (($end_date - $start_date) > $max){
			api_output_error(999, "Date range is too large");
		}

		$args = array(
			'start_date' => $start_date,
			'end_date' => $end_date,
		);

		api_utils_ensure_pagination_args($args);

		$rsp = corrections_get_recent($args);

		if (! $rsp['ok']){
			api_output_error(999, "Failed to retrieve corrections");
		}

		$geojson = corrections_geojson_corrections_to_geojson($rsp['rows']);

		$out = array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'features' => $geojson['features'],
		);

		api_utils_ensure_pagination_results($out, $rsp['pagination']);
		api_output_ok($out);
	}

	#################################################################

	# the end
