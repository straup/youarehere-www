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

		$filter = post_str("filter");

		if ((! $filter) || (! reverse_geocode_is_valid_filter($filter))){
			api_output_error(500, "Missing or invalid filter");
		}

		# TO DO: allow for string values...

		$perspective_id = post_int32("perspective");

		if (! corrections_is_valid_perspective($perspective_id)){
			api_output_error(500, "Invalid perspective");
		}

		$woeid = post_int64("woeid");

		if (! $woeid){
			api_output_error(500, "Missing WOE ID");
		}

		$ok_woeid = 1;

		if ($woeid = -1){
			# pass
		}

		else if ($woeid < 0){
			$ok_woeid = 0;
		}

		else {

			$reversegeo_rsp = reverse_geocode($lat, $lon, $filter);

			foreach ($reversegeo_rsp['data'] as $row){

				if ($row['woe_id'] == $woeid){
					$ok_woeid = 1;
					break;
				}
			}
		}

		if (! $ok_woeid){
			api_output_error(999, "Invalid WOE ID");
		}

		# Go!

		$source_id = reverse_geocode_filter_source($filter);

		$correction = array(
			'user_id' => $GLOBALS['cfg']['user']['id'],
			'woe_id' => $choice,
			'latitude' => $lat,
			'longitude' => $lon,
			'source_id' => $source_id,
			'perspective' => $perspective_id,
		);

		if (features_is_enabled("record_remote_address")){
			$addr = corrections_obfuscate_remote_address($_SERVER['REMOTE_ADDR']);
			$correction['remote_address'] = $addr;
		}

		$rsp = corrections_add_correction($correction);

		if (! $rsp['ok']){
			api_output_error(999, "There was a problem adding your correction");
		}

		$out = array(
			'id' => $rsp['correction']['id'],
		);

		api_output_ok($out);
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
