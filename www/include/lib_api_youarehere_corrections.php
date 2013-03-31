<?php

	loadlib("corrections_geojson");

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
