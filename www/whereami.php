<?php

	include("include/init.php");

	loadlib("reverse_geocode");

	$lat = request_float("lat");
	$lon = request_float("lon");

	if (($lat) && ($lon)){
		$rsp = reverse_geocode($lat, $lon);
		$GLOBALS['smarty']->assign("step", "choose");
		$GLOBALS['smarty']->assign_by_ref("rsp", $rsp);
	}

	$GLOBALS['smarty']->display("page_whereami.txt");
	exit();
