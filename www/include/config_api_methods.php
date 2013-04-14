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

		"youarehere.assertions.assertLocation" => array(
			"description" => "Assert the location for a latitude and longitude.",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_assertions",
			"request_method" => "POST",
			"parameters" => array(
				array(
					"name" => "lat",
					"description" => "The latitude of the place you are assering a location for.",
					"required" => 1,
				),
				array(
					"name" => "lon",
					"description" => "The longitude of the place you are asserting a location for.",
					"required" => 1,
				),
				array(
					"name" => "woe_id",
					"description" => "The WOE ID of the place that contains the latitude and longitude.",
					"required" => 1,
				),
				array(
					"name" => "perspective_id",
					"description" => "The numeric perspective ID for your assertion. See the documentation for the youarehere.geo.sources.getList API method for details.",
					"required" => 0,
				),
			),
			"notes" => array(
			),
		),

		"youarehere.assertions.redactAssertion" => array(
			"description" => "Redact an assertion.",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_assertions",
			"request_method" => "POST",
			"parameters" => array(
				array(
					"name" => "assertion_id",
					"description" => "",
					"required" => 1,
				),
			),
		),

		"youarehere.assertions.getAssertionsByDate" => array(
			"description" => "Return a list of assertions by date range",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_assertions",
			"parameters" => array(
				array(
					"name" => "start_date",
					"description" => "The earliest date to return assertions for",
					"required" => 1,
				),
				array(
					"name" => "end_date",
					"description" => "The latest date to return assertions for",
					"required" => 1,
				),
			),
			"notes" => array(
				"Dates may be passed in as strings or as Unix timestamps. The API method will attempt to parse either but does not guarantee that if will be able to make sense of edge cases or esoteric formattings.",
				"The maximum date range (between start_date and end_date) is 28 days.",
			),
		),

		"youarehere.assertions.perspectives.getList" => array(
			"description" => "Return the list of valid perspectives (for assertions)",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_youarehere_assertions_perspectives"
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
