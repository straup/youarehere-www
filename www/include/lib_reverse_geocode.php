<?php

	loadlib("http");

	$GLOBALS['cfg']['reverse_geocode_endpoint'] = 'http://localhost:5000';

	########################################################################

	function reverse_geocode($lat, $lon, $filter){

		$endpoint = _reverse_geocode_endpoint($filter);

		if (! $endpoint){
			return array('ok' => 0, 'error' => 'invalid endpoint/filter');
		}

		$query = array('lat' => $lat, 'lng' => $lon);

		$url = $endpoint . "?" . http_build_query($query);
		$rsp = http_get($url);

		if ($rsp['ok']){
			$data = json_decode($rsp['body'], 'as hash');
			$rsp['data'] = $data;
		}

		return $rsp;
	}

	########################################################################

	function reverse_geocode_is_valid_filter($filter){

		return (isset($GLOBALS['cfg']['reverse_geocode_endpoints'][$filter])) ? 1 : 0;
	}

	########################################################################

	function _reverse_geocode_endpoint($filter){

		$hosts = $GLOBALS['cfg']['reverse_geocode_endpoints'][$filter];

		if (count($hosts) > 1){
			shuffle($hosts);
		}

		return $hosts[0];
	}

	########################################################################

	# the end
