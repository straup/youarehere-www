<?php

	loadlib("artisanal_integers");
	loadlib("geo_utils");

	########################################################################

	function corrections_perspective_map(){

		$map = array(
			0 => "none of your business",
			1 => "a local",
			2 => "a tourist",
		);

		return $map;
	}

	########################################################################

	function corrections_is_valid_perspective($id){

		$map = corrections_perspective_map();
		return (isset($map[$id])) ? 1 : 0;
	}

	########################################################################

	# sudo give me a better name...
	
	function corrections_get_fallback($filter){

		$possible = $GLOBALS['cfg']['reverse_geocode_fallbacks'];

		if (! isset($possible[$filter])){
			return null;
		}

		return $possible[$filter];
	}

	########################################################################

	function corrections_is_valid_fallback($fallback){

		$possible = array_keys($GLOBALS['cfg']['reverse_geocoder_fallbacks']);
		return (in_array($fallback, $possible)) ? 1 : 0;
	}

	########################################################################

	function corrections_add_correction($data){

		$rsp = artisanal_integers_create();

		if (! $rsp['ok']){
			return $rsp;
		}

		$data['id'] = $rsp['integer'];
		$data['created'] = time();

		$insert = array();

		foreach ($data as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('Corrections', $insert);

		if ($rsp['ok']){
			$rsp['correction'] = $data;
		}

		return $rsp;
	}

	########################################################################

	function corrections_get_by_id($id){

		$enc_id = AddSlashes($id);

		$sql = "SELECT * FROM Corrections WHERE id='{$enc_id}'";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);
		
		return $row;
	}

	########################################################################

	function corrections_get_recent($more=array()){

		$enc_id = AddSlashes($user['id']);

		$sql = "SELECT * FROM Corrections ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		return $rsp;
	}

	########################################################################

	function corrections_get_for_user(&$user, $more=array()){

		$enc_id = AddSlashes($user['id']);

		$sql = "SELECT * FROM Corrections WHERE user_id='{$enc_id}' ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		return $rsp;
	}

	########################################################################

	function corrections_get_for_user_latlon(&$user, $lat, $lon, $more=array()){

		$enc_id = AddSlashes($user['id']);
		$enc_lat = AddSlashes($lat);
		$enc_lon = AddSlashes($lon);

		$sql = "SELECT * FROM Corrections WHERE user_id='{$enc_id}'";

		# $sql .= " AND latitude='{$enc_lat}' AND longitude='{$enc_lon}'";

		$bbox = geo_utils_bbox_from_point($lat, $lon, 0.05);

		foreach (range(0, 3) as $i){
			$bbox[$i] = AddSlashes($bbox[$i]);
		}
		
		$sql .= " AND latitude >= '{$bbox[0]}' AND latitude <= '{$bbox[2]}'";
		$sql .= " AND longitude >= '{$bbox[1]}' AND longitude <= '{$bbox[3]}'";

		# TO DO: recently-ish-ness (20130223/straup)

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		return $rsp;

	}

	########################################################################

	function corrections_get_for_woe(&$woe, $more=array()){

		$enc_id = AddSlashes($woe['woe_id']);

		$sql = "SELECT * FROM Corrections WHERE woe_id='{$enc_id}' ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		return $rsp;
	}

	########################################################################

	# use bcrypt?

	function corrections_obfuscate_remote_address($addr){
		$secret = $GLOBALS['cfg']['crypto_remote_address_secret'];
		$hash = hash_hmac("sha256", $addr, $secret);
		return md5($hash);
	}

	########################################################################

	# the end
