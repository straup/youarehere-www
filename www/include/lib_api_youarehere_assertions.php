<?php

	loadlib("assertions_geojson");

	#################################################################

	function api_youarehere_assertions_assertLocation(){

		$lat = post_float("lat");
		$lon = post_float("lon");

		if ((! $lat) || (! geo_utils_is_valid_latitude($lat))){
			api_output_error(500, "Missing or invalid latitude");
		}

		if ((! $lon) || (! geo_utils_is_valid_longitude($lon))){
			api_output_error(500, "Missing or invalid longitude");
		}

		$filter = post_str("filter");

		if (! $filter){
			$filter = reverse_geocode_default_filter();
		}

		else if (! reverse_geocode_is_valid_filter($filter)){
			api_output_error(500, "Missing or invalid filter");
		}

		else {}

		# TO DO: allow for string values...

		$perspective_id = post_int32("perspective_id");

		if (! assertions_is_valid_perspective($perspective_id)){
			api_output_error(500, "Invalid perspective");
		}

		$woeid = post_int64("woe_id");

		if (! $woeid){
			api_output_error(500, "Missing WOE ID");
		}

		$ok_woeid = 1;

		if ($woeid == -1){
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

		$assertion = array(
			'user_id' => $GLOBALS['cfg']['user']['id'],
			'woe_id' => $woeid,
			'latitude' => $lat,
			'longitude' => $lon,
			'source_id' => $source_id,
			'perspective' => $perspective_id,
		);

		# api_output_ok($assertion);

		if (features_is_enabled("record_remote_address")){
			$addr = assertions_obfuscate_remote_address($_SERVER['REMOTE_ADDR']);
			$assertion['remote_address'] = $addr;
		}

		$rsp = assertions_add_assertion($assertion);

		if (! $rsp['ok']){
			api_output_error(999, "There was a problem adding your assertion");
		}

		$out = array(
			'id' => $rsp['assertion']['id'],
		);

		api_output_ok($out);
	}

	#################################################################

	function api_youarehere_assertions_deleteAssertion(){

		$id = post_int64("assertion_id");

		if (! $id){
			api_output_error(999, "Missing assertion ID");
		}

		$more = array(
			'scrub_assertion' => 0,
		);

		$assertion = assertions_get_by_id($id, $more);

		if (! $assertion){
			api_output_error(999, "Invalid assertion ID");
		}

		if ($assertion['user_id'] != $GLOBALS['cfg']['user']['id']){
			api_output_error(999, "Insufficient permissions");
		}

		$rsp = assertions_delete_assertion($assertion);

		if (! $rsp['ok']){
			api_output_error(999, "There was a problem deleting the assertion");
		}

		api_output_ok();
	}

	#################################################################

	function api_youarehere_assertions_getAssertionsByDate(){

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

		$rsp = assertions_get_recent($args);

		if (! $rsp['ok']){
			api_output_error(999, "Failed to retrieve assertions");
		}

		$geojson = assertions_geojson_assertions_to_geojson($rsp['rows']);

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
