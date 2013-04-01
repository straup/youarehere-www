<?php

	include("include/init.php");
	loadlib("twofishes");

	features_ensure_enabled("geocoder");

	$q = get_str("q");

	if (($q) && (preg_match("/^([\w\d\s]+)$/", $q, $m))){

		$rsp = twofishes_geocode($m[1]);

		if ($rsp['ok']){
			$data = $rsp['data']['interpretations'];

			$geojson = twofishes_interpretations_to_geojson($data);
			$GLOBALS['smarty']->assign_by_ref("geojson", $geojson);
		}
	}

	$GLOBALS['smarty']->display("page_choose.txt");
	exit();
?>
