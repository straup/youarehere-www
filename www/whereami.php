<?php

	include("include/init.php");
	loadlib("reverse_geocode");
	loadlib("corrections");

	# login_ensure_loggedin();

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

			# TO DO: something with $choice is -1

			# see also: artisanal integers
			$choice = post_int64("whereami");
			$ok = 0;

			foreach ($rsp['data'] as $row){
				if ($row['woe_id'] == $choice){
					$ok = 1;
					break;
				}
			}

			if ($ok){

				$correction = array(
					'latitude' => $lat,
					'longitude' => $lon,
					'woe_id' => $choice,
				);

				$perspective = post_int32("perspective");

				if (($perspective) && (corrections_is_valid_perspective($perspective))){
					$correction['perspective'] = $perspective;
				}

				$rsp = corrections_add_correction($correction);
			}

			# TO DO: notifications (pubsub or ... ?)
		}

		else {
			$GLOBALS['smarty']->assign("step", "choose");
			$GLOBALS['smarty']->assign_by_ref("rsp", $rsp);
		}
	}

	$map = corrections_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_whereami.txt");
	exit();
