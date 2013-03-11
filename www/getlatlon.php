<?php

	include("include/init.php");
	loadlib("twofishes");

	features_ensure_enabled("getlatlon");

	$ok_geocode = features_is_enabled("geocoder");

	if (($ok_geocode) && ($q = get_str("q"))){

		$rsp = twofishes_geocode($q);

		if ($rsp['ok']){
			$data = $rsp['data']['interpretations'];
			$more = array('favour_centroids' => 1);
			$geojson = twofishes_interpretations_to_geojson($data, $more);
			$GLOBALS['smarty']->assign_by_ref("geojson", $geojson);
		}
	}

	$GLOBALS['smarty']->display("page_getlatlon.txt");
	exit();
?>
