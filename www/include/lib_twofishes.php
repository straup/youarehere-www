<?php

	loadlib("http");

	########################################################################

	$GLOBALS['cfg']['twofishes_endpoint'] = 'http://localhost:8081';

	########################################################################

	function twofishes_geocode($q){

		$query = array('query' => $q);
		$query = http_build_query($query);

		$url = $GLOBALS['cfg']['twofishes_endpoint'] . "?" . $query;

		$rsp = http_get($url);

		if (! $rsp['ok']){
			return $rsp;
		}

		$data = json_decode($rsp['body'], "as hash");

		# TO DO: error handling

		$rsp['data'] = $data;
		return $rsp;
	}

	########################################################################

	# the end
