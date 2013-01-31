<?php

	loadlib("http");

	$GLOBALS['spacetimeid_host'] = 'https://spacetimeid.appspot.com/';

	########################################################################

	function spacetimeid_encode($lat, $lon, $ts){

		$parts = array($lat, $lon, $ts);

		$url = $GLOBALS['spacetimeid_host'] . "encode/{$lat}/{$lon}/{$ts}/";
		$rsp = http_get($url);

		# sudo make emit JSON...

		$xml = new SimpleXMLElement($rsp['body']);
		$items = $xml->xpath('/rsp/spacetime');
		$first = $items[0];

		# please kill me now...

		$attrs_object = $first->attributes();
		$attrs_array = (array) $attrs_object;

		$rsp['data'] = $attrs_array["@attributes"];
		return $rsp;
	}

	########################################################################

	# the end
