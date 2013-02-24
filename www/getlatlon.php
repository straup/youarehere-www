<?php

	include("include/init.php");
	loadlib("twofishes");

	features_ensure_enabled("getlatlon");

	$ok_geocode = features_is_enabled("geocoder");

	if (($ok_geocode) && ($q = get_str("q"))){

		$rsp = twofishes_geocode($q);
		$data = $rsp['data']['interpretations'];

		$geojson = twofishes_interpretations_to_geojson($data);
		
	}

	$GLOBALS['smarty']->display("page_getlatlon.txt");
	exit();
?>
