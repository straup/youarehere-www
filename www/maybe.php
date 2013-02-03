<?

	include('include/init.php');

	login_ensure_loggedin();

	loadlib("reverse_geocode");

	$lat = request_float("lat");
	$lon = request_float("lon");
	$filter = $GLOBALS['cfg']['reverse_geocode_default_filter'];

	if ($f = request_str("filter")){
		$filter = $f;
	}

	$crumb_key = 'whereami';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if (($lat) && ($lon)){

		# TO DO: validate lat,lon

		$GLOBALS['smarty']->assign("latitude", $lat);
		$GLOBALS['smarty']->assign("longitude", $lon);

		# TO DO: choose endpoint by placetype

		$rsp = reverse_geocode($lat, $lon, $filter);

		if ((post_isset("choose")) && (crumb_check($crumb_key))){

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

				$addr = ip2long($_SERVER['REMOTE_ADDR']);

				$correction = array(
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'woe_id' => $choice,
					'latitude' => $lat,
					'longitude' => $lon,
					'ip_address' => $addr,
				);

				$perspective = post_int32("perspective");

				if (($perspective) && (corrections_is_valid_perspective($perspective))){
					$correction['perspective'] = $perspective;
				}

				$rsp = corrections_add_correction($correction);
				$GLOBALS['smarty']->assign("update", $rsp);
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

	$GLOBALS['smarty']->display("page_maybe.txt");
	exit();
