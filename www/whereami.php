<?php

	include("include/init.php");
	loadlib("reverse_geocode");

	# TO DO: ensure logged in

	$lat = request_float("lat");
	$lon = request_float("lon");
	$filter = $GLOBALS['cfg']['reverse_geocode_default_filter'];

	if ($f = request_str("filter")){
		$filter = $f;
	}

	if (($lat) && ($lon)){

		# TO DO: validate lat,lon

		$GLOBALS['smarty']->assign("latitude", $lat);
		$GLOBALS['smarty']->assign("longitude", $lon);

		# TO DO: choose endpoint by placetype

		$rsp = reverse_geocode($lat, $lon, $filter);

		if (post_isset("choose")){

			$GLOBALS['smarty']->assign("step", "update");

			# see also: artisanal integers
			$choice = post_int64("whereami");
			$ok = 0;

			foreach ($rsp['data'] as $row){
				if ($row['woe_id'] == $choice){
					$ok = 1;
					break;
				}
			}

			# TO DO: finish error checking; update db...
		}

		else {
			$GLOBALS['smarty']->assign("step", "choose");
			$GLOBALS['smarty']->assign_by_ref("rsp", $rsp);
		}
	}

	$GLOBALS['smarty']->display("page_whereami.txt");
	exit();
