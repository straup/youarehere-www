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

		"api.test.echo" => array(
			"description" => "A testing method which echo's all parameters back in the response.",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_test"
		),

		"api.test.error" => array(
			"description" => "Return a test error from the API",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_test"
		),

		"youarehere.corrections.doThis" => array(
			"description" => "Submit a correction",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_corrections",
			"request_method" => "POST",
			"parameters" => array(
				array(
					"name" => "lat",
					"description" => "The latitude of the place to reverse geocode",
					"required" => 1,
				),
				array(
					"name" => "lon",
					"description" => "The longitude of the place to reverse geocode",
					"required" => 1,
				),
				array(
					"name" => "woeid",
					"description" => "The WOE ID ...",
					"required" => 1,
				),
				array(
					"name" => "perspective",
					"description" => "The perspective for your correction",
					"required" => 0,
				),
			),
			"notes" => array(
			),
		),

		"youarehere.corrections.getCorrectionsByDate" => array(
			"description" => "Return a list of corrections by date range",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_corrections",
			"parameters" => array(
				array(
					"name" => "start_date",
					"description" => "The earliest date to return corrections for",
					"required" => 1,
				),
				array(
					"name" => "end_date",
					"description" => "The latest date to return corrections for",
					"required" => 1,
				),
			),
			"notes" => array(
				"Dates may be passed in as strings or as Unix timestamps. The API method will attempt to parse either but does not guarantee that if will be able to make sense of edge cases or esoteric formattings.",
				"The maximum date range (between start_date and end_date) is 28 days.",
			),
		),

		"youarehere.corrections.perspectives.getList" => array(
			"description" => "Return the list of valid perspectives (for corrections)",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_corrections_perspectives"
		),

		"youarehere.geo.geocode" => array(
			"description" => "Gecode a place name.",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_geo",
			"parameters" => array(
				array(
					"name" => "query",
					"description" => "The place name to geocode",
					"required" => 1,
				),
			),
			"notes" => array(
				"Results are returned as a list of GeoJSON features.",
			),
		),

		"youarehere.geo.reverseGeocode" => array(
			"description" => "Reverse geocode a latitude and longitude.",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_geo",
			"parameters" => array(
				array(
					"name" => "lat",
					"description" => "The latitude of the place to reverse geocode",
					"required" => 1,
				),
				array(
					"name" => "lon",
					"description" => "The longitude of the place to reverse geocode",
					"required" => 1,
				),
				array(
					"name" => "filter",
					"description" => "The place type to scope your reverse geocoding query to",
					"required" => 0,
				),
			),
			"notes" => array(
				"Results are returned as a list of GeoJSON features.",
			),

		),

		"youarehere.geo.filters.getList" => array(
			"description" => "Return the list of valid filters (for reverse geocoding)",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_geo_filters"
		),

		"youarehere.geo.sources.getList" => array(
			"description" => "Return the list of sources used for reverse geocoding",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_geo_sources"
		),

	), $GLOBALS['cfg']['api']['methods']);

	########################################################################

	# the end
