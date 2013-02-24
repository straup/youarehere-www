<?php

	include("include/init.php");
	loadlib("twofishes");

	features_ensure_enabled("getlatlon");

	$ok_geocode = features_is_enabled("geocoder");

	if (($ok_geocode) && ($q = get_str("q"))){

		$rsp = twofishes_geocode($q);
		dumper($rsp['data']);
	}

	$GLOBALS['smarty']->display("page_getlatlon.txt");
	exit();
?>
