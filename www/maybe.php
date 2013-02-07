<?

	include('include/init.php');

	login_ensure_loggedin();

	loadlib("reverse_geocode");

	# Make it work for Null Island

	$lat = request_isset("lat");
	$lon = request_isset("lon");
	$filter = $GLOBALS['cfg']['reverse_geocode_default_filter'];

	if ($f = request_str("filter")){
		$filter = $f;
	}

	$crumb_key = 'whereami';
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if (($lat) && ($lon)){

		$lat = request_float("lat");
		$lon = request_float("lon");

		# TO DO: validate lat,lon

		$GLOBALS['smarty']->assign("latitude", $lat);
		$GLOBALS['smarty']->assign("longitude", $lon);

		# TO DO: choose endpoint by placetype

		$rsp = reverse_geocode($lat, $lon, $filter);

		if ((post_isset("choose")) && (crumb_check($crumb_key))){

			$GLOBALS['smarty']->assign("step", "update");

			# Note: We're expecting a string not an int so we
			# can trap negative values.

			$choice = post_str("whereami");

			$ok = 0;
			$error = null;

			# if choice == -1: try parent
			# if choice == 2: no no no, all wrong
			# if choice: ensure nearby-iness (note that '0' is a
			# valid option, because Null Island)

			# Note: Blank strings are not Null Island

			if ($choice == ''){
				$error = 'You forgot to choose a place!';
			}

			# See what this means: We're going to store a negative
			# number in the database (20130207/straup)

			else if ($choice == -1){
				$ok = 1;
			}

			else if ($choice == -2){
				# TO DO: Parent stuff...
				$error = 'Parent lookup are currently disabled...';
			}

			else {

				foreach ($rsp['data'] as $row){

					if ($row['woe_id'] == $choice){
						$ok = 1;
						break;
					}
				}

				if (! $ok){
					$error = "Invalid WOE ID";
				}
			}

			$GLOBALS['smarty']->assign("do_update", $ok);

			if (! $ok){
				$GLOBALS['smarty']->assign('error', $error);
			}

			else {

				$correction = array(
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'woe_id' => $choice,
					'latitude' => $lat,
					'longitude' => $lon,
				);

				if (features_is_enabled("record_remote_address")){
					$addr = corrections_obfuscate_remote_address($_SERVER['REMOTE_ADDR']);
					$correction['remote_address'] = $addr;
				}

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
