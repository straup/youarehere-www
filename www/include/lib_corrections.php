<?php

	loadlib("artisanal_integers");
	loadlib("geo_utils");

	loadlib("corrections_redacted");

	########################################################################

	function corrections_perspective_map($string_keys=0){

		$map = array(
			0 => "a stranger",
			1 => "a local",
			2 => "a tourist",
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	function corrections_perspective_filter_map($string_keys=0){

		$map = array(
			0 => 'strangers',
			1 => 'locals',
			2 => 'tourists',
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	########################################################################

	function corrections_is_valid_perspective($id){

		$map = corrections_perspective_map();
		return (isset($map[$id])) ? 1 : 0;
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
			$data = corrections_scrub_correction($data);
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
		$row = corrections_scrub_correction($row);		

		return $row;
	}

	########################################################################

	function corrections_is_own(&$correction, &$user){

		# cache me...

		$enc_id = AddSlashes($correction['id']);

		$sql = "SELECT user_id FROM Corrections WHERE id='{$enc_id}'";
		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		return (($row) && ($row['user_id'] == $user['id'])) ? 1 : 0;
	}

	########################################################################

	function corrections_get_recent($more=array()){

		$enc_id = AddSlashes($user['id']);

		$sql = "SELECT * FROM Corrections";

		if (array_key_exists('perspective', $more)){
			$enc_pid = AddSlashes($more['perspective']);
			$sql .= " WHERE perspective='{$enc_pid}'";
		}

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		$rsp = corrections_scrub_rsp($rsp);

		return $rsp;
	}

	########################################################################

	function corrections_get_for_user(&$user, $more=array()){

		$enc_id = AddSlashes($user['id']);

		$sql = "SELECT * FROM Corrections WHERE user_id='{$enc_id}'";

		if (array_key_exists('perspective', $more)){
			$enc_pid = AddSlashes($more['perspective']);
			$sql .= " AND perspective='{$enc_pid}'";
		}

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		$rsp = corrections_scrub_rsp($rsp);

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
		$rsp = corrections_scrub_rsp($rsp);

		return $rsp;

	}

	########################################################################

	function corrections_get_for_woe(&$woe, $more=array()){

		$enc_id = AddSlashes($woe['woe_id']);

		$sql = "SELECT * FROM Corrections WHERE woe_id='{$enc_id}'";

		if (array_key_exists('perspective', $more)){
			$enc_pid = AddSlashes($more['perspective']);
			$sql .= " AND perspective='{$enc_pid}'";
		}

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		$rsp = corrections_scrub_rsp($rsp);

		return $rsp;
	}

	########################################################################

	# use bcrypt?

	function corrections_obfuscate_remote_address($addr){

		$secret = $GLOBALS['cfg']['crypto_remote_address_secret'];

		# No soup for you

		if (! $secret){
			return '';
		}

		$hash = hash_hmac("sha256", $addr, $secret);
		return md5($hash);
	}

	########################################################################

	function corrections_scrub_rsp(&$rsp){

		if ($rsp['ok']){
			$rsp['rows'] = corrections_scrub_corrections($rsp['rows']);
		}

		return $rsp;
	}

	########################################################################

	function corrections_scrub_corrections(&$rows){

		$count = count($rows);

		for ($i=0; $i < $count; $i++){
			$rows[$i] = corrections_scrub_correction($rows[$i]);
		}

		return $rows;
	}

	########################################################################

	function corrections_scrub_correction($row){

		$to_remove = array(
			'user_id',
		);

		foreach ($to_remove as $prop){

			if (array_key_exists($prop, $row)){
				unset($row[$prop]);
			}
		}

		return $row;
	}

	########################################################################

	function corrections_delete_correction(&$correction){

		$rsp = corrections_redacted_redact_correction($correction);

		if (! $rsp['ok']){
			$error = $rsp['error'];
			return array('ok' => 0, 'error' => "Failed to redact correction: {$error}");
		}

		$enc_id = AddSlashes($correction['id']);
		$sql = "DELETE FROM Corrections WHERE id='{$enc_id}'";

		return db_write($sql);
	}

	########################################################################

	# the end
