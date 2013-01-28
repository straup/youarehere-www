<?php

	loadlib("http");

	$GLOBALS['cfg']['reverse_geocode_endpoint'] = 'http://localhost:5000';

	function reverse_geocode($lat, $lon){

		$query = array('lat' => $lat, 'lng' => $lon);

		$url = $GLOBALS['cfg']['reverse_geocode_endpoint'] . "?" . http_build_query($query);
		$rsp = http_get($url);

		if ($rsp['ok']){
			$data = json_decode($rsp['body'], 'as hash');
			$rsp['data'] = $data;
		}

		return $rsp;
	}
