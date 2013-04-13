<?

	include('include/init.php');

	login_ensure_loggedin();

	features_ensure_enabled("reverse_geocoder");

	loadlib("reverse_geocode");
	loadlib("geo_utils");

	$lat = request_isset("lat");
	$lon = request_isset("lon");

	$filter = reverse_geocode_default_filter();

	# github issue #16

	if ($zoom = request_int32("zoom")){

		if ($this_filter = reverse_geocode_filter_for_zoom($zoom)){
			$filter = $this_filter;
		}
	}

	else if ($this_filter = request_str("filter")){
		$filter = $this_filter;
	}

	else {}

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

		# Okay, what's around here

		$reversegeo_rsp = reverse_geocode($lat, $lon, $filter);

		if ((post_isset("choose")) && (crumb_check($crumb_key))){

			$GLOBALS['smarty']->assign("step", "update");

			# Note: We're expecting a string not an int so we
			# can trap negative values.

			$choice = post_str("whereami");

			$ok = 0;
			$try_fallback = 0;

			$error = null;

			# if choice == -1: no no no, all wrong - give up
			# if choice == -2-STUFF: where STUFF is a ancestor/fallback
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

			else if (preg_match("/^-(\d)-([a-z]+)$/", $choice, $m)){

				$relationship = $m[1];
				$fallback = $m[2];

				$relations = array();

				if ($relationship == 2){
					$fallback_tree = reverse_geocode_get_fallback_tree($filter);
					$relations = array_keys($fallback_tree);
				}

				else if ($relationship == 3){
					$falldown_tree = reverse_geocode_get_falldown_tree($filter);
					$relations = array_keys($falldown_tree);
				}

				else {}

				$ok_fallback = (in_array($fallback, $relations)) ? 1 : 0;

				if ($ok_fallback){

					$filter = $fallback;
					$reversegeo_rsp = reverse_geocode($lat,	$lon, $filter);
					$GLOBALS['smarty']->assign_by_ref("rsp", $reversegeo_rsp);

					$try_fallback = 1;
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

			else if ($try_fallback){
				$GLOBALS['smarty']->assign("step", "choose");
			}

			# Stick it in the database

			else {

				$source_id = reverse_geocode_filter_source($filter);

				$assertion = array(
					'user_id' => $GLOBALS['cfg']['user']['id'],
					'woe_id' => $choice,
					'latitude' => $lat,
					'longitude' => $lon,
					'source_id' => $source_id,
				);

				if (features_is_enabled("record_remote_address")){
					$addr = assertions_obfuscate_remote_address($_SERVER['REMOTE_ADDR']);
					$assertion['remote_address'] = $addr;
				}

				$perspective = post_int32("perspective");

				if (($perspective) && (assertions_is_valid_perspective($perspective))){
					$assertion['perspective'] = $perspective;
				}

				$rsp = assertions_add_assertion($assertion);
				$GLOBALS['smarty']->assign("update", $rsp);
			}

			# TO DO: notifications (pubsub or ... ?)
		}

		else {

			# All of this has happened before / all of this will happen again
			# TO DO – radial or tiny bbox query (20130331/straup)

			$previous_rsp = assertions_get_for_user_latlon($GLOBALS['cfg']['user'], $lat, $lon);
			$previous = ($previous_rsp['ok']) ? $previous_rsp['rows'] : array();

			$count = count($previous);

			for ($i=0; $i < $count; $i++){

				$crtn = $previous[$i];
				$dist = geo_utils_distance($lat, $lon, $crtn['latitude'], $crtn['longitude']);

				$crtn['distance (miles)'] = number_format($dist, 3);
				$previous[$i] = $crtn;
			}

			$GLOBALS['smarty']->assign_by_ref("previous", $previous);

			$GLOBALS['smarty']->assign("step", "choose");
			$GLOBALS['smarty']->assign_by_ref("rsp", $reversegeo_rsp);
		}
	}

	$GLOBALS['smarty']->assign("filter", $filter);

	$fallback_tree = reverse_geocode_get_fallback_tree($filter);
	$falldown_tree = reverse_geocode_get_falldown_tree($filter);

	$GLOBALS['smarty']->assign("fallback_tree", $fallback_tree);
	$GLOBALS['smarty']->assign("falldown_tree", $falldown_tree);

	$map = assertions_perspective_map();
	$GLOBALS['smarty']->assign_by_ref("perspective_map", $map);

	$GLOBALS['smarty']->display("page_decide.txt");
	exit();
