<?php

	include("include/init.php");
	loadlib("twofishes");

	features_ensure_enabled("geocoder");

	if ($q = get_str("q")){

		$rsp = twofishes_geocode($q);

		if ($rsp['ok']){
			$data = $rsp['data']['interpretations'];

			$geojson = twofishes_interpretations_to_geojson($data);
			$GLOBALS['smarty']->assign_by_ref("geojson", $geojson);
		}
	}

	$GLOBALS['smarty']->display("page_getlatlon.txt");
	exit();
?>
