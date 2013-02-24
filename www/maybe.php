<?

	include('include/init.php');

	login_ensure_loggedin();

	loadlib("reverse_geocode");
	loadlib("geo_utils");

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

		$lat = (geo_utils_is_valid_latitude($lat)) ? $lat : null;
		$lon = (geo_utils_is_valid_longitude($lon)) ? $lon : null;

		# TO DO: error reporting (20130223/straup)
	}

	if (($lat) && ($lon)){

		$GLOBALS['smarty']->assign("latitude", $lat);
		$GLOBALS['smarty']->assign("longitude", $lon);

		# Not really sure what this means or should do yet
		# (20130223/straup)

		$rsp = corrections_get_for_user_latlon($GLOBALS['cfg']['user'], $lat, $lon);
		# $count = $rsp['pagination']['total_count'];

		$reversegeo_rsp = reverse_geocode($lat, $lon, $filter);

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

			# The user has given up

			# See what this means: We're going to store a negative
			# number in the database (20130207/straup)

			else if ($choice == -1){
				$ok = 1;
			}

			# The user wants more choices

			else if ($choice == -2){

				if ($filter = corrections_get_fallback($filter)){
					$reversegeo_rsp = reverse_geocode($lat,	$lon, $filter);
					$GLOBALS['smarty']->assign_by_ref("rsp", $reversegeo_rsp);
					$ok = 1;
				}

				else {
					$error = 'Invalid fallback';
				}
			}

			# Validate what the user is saying

			else {

				foreach ($reversegeo_rsp['data'] as $row){

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

			# Sad face
			
			if (! $ok){
				$GLOBALS['smarty']->assign('error', $error);
			}

			# See this: we're going to pop out of the stack and
			# present the user with a new set of places (20130208/straup)

			else if ($choice == -2){
				$GLOBALS['smarty']->assign("step", "choose");
			}

			# Stick it in the database

			else {

				$source_id = corrections_sources_for_filter($filter);

				$correction = array(
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'woe_id' => $choice,
					'latitude' => $lat,
					'longitude' => $lon,
					'source_id' => $source_id,
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
			$GLOBALS['smarty']->assign_by_ref("rsp", $reversegeo_rsp);
		}
	}

	$fallback = corrections_get_fallback($filter);
	$GLOBALS['smarty']->assign("fallback", $fallback);

	$map = corrections_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_maybe.txt");
	exit();
