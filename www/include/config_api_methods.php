<?php

	########################################################################

	$GLOBALS['cfg']['api']['methods'] = array_merge(array(

		"api.spec.methods" => array (
			"description" => "Return the list of available API response methods.",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_spec"
		),

		"api.spec.formats" => array(
			"description" => "Return the list of valid API response formats, including the default format",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_spec"
		),

		"test.echo" => array(
			"description" => "A testing method which echo's all parameters back in the response.",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_test"
		),

		"test.error" => array(
			"description" => "Return a test error from the API",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_test"
		),

		"youarehere.corrections.getCorrectionsByDate" => array(
			"description" => "Return a list of corrections by date range",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_corrections"
		),

		"youarehere.geo.geocode" => array(
			"description" => "Gecode a placename",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_geo"
		),

		"youarehere.geo.reverseGeocode" => array(
			"description" => "Reverse geocode a latitude and longitude",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_geo"
		),

		"youarehere.geo.filters.getList" => array(
			"description" => "Return the list of valid filters (for reverse geocoding)",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_geo_filters"
		),

	), $GLOBALS['cfg']['api']['methods']);

	########################################################################

	# the end
