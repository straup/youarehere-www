<?php

	loadlib("artisanal_integers");
	loadlib("geo_utils");

	loadlib("assertions_redacted");

	########################################################################

	function assertions_perspective_map($string_keys=0){

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

	function assertions_perspective_filter_map($string_keys=0){

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

	function assertions_is_valid_perspective($id){

		$map = assertions_perspective_map();
		return (isset($map[$id])) ? 1 : 0;
	}

	########################################################################

	function assertions_add_assertion($data){

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

		$rsp = db_insert('Assertions', $insert);

		if ($rsp['ok']){
			$data = assertions_scrub_assertion($data);
			$rsp['assertion'] = $data;
		}

		return $rsp;
	}

	########################################################################

	function assertions_get_by_id($id, $more=array()){

		$defaults = array(
			'scrub_assertion' => 1,
		);

		$more = array_merge($defaults, $more);

		$enc_id = AddSlashes($id);

		$sql = "SELECT * FROM Assertions WHERE id='{$enc_id}'";
		$rsp = db_fetch($sql);

		$row = db_single($rsp);

		if ($more['scrub_assertion']){
			$row = assertions_scrub_assertion($row);		
		}

		return $row;
	}

	########################################################################

	function assertions_is_own(&$assertion, &$user){

		# cache me...

		$enc_id = AddSlashes($assertion['id']);

		$sql = "SELECT user_id FROM Assertions WHERE id='{$enc_id}'";
		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		return (($row) && ($row['user_id'] == $user['id'])) ? 1 : 0;
	}

	########################################################################

	function assertions_get_recent($more=array()){

		$enc_id = AddSlashes($user['id']);

		$sql = "SELECT * FROM Assertions";

		$where = array();

		if (array_key_exists('perspective', $more)){
			$enc_pid = AddSlashes($more['perspective']);
			$where[] = "perspective='{$enc_pid}'";
		}

		if (isset($more['start_date'])){
			$enc_date = AddSlashes($more['start_date']);
			$where[] = "created >= {$enc_date}";
		}

		if (isset($more['end_date'])){
			$enc_date = AddSlashes($more['end_date']);
			$where[] = "created <= {$enc_date}";
		}

		if (count($where)){
			$where = implode(" AND ", $where);
			$sql .= " WHERE {$where}";
		}

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		$rsp = assertions_scrub_rsp($rsp);

		return $rsp;
	}

	########################################################################

	function assertions_get_for_user(&$user, $more=array()){

		$enc_id = AddSlashes($user['id']);

		$sql = "SELECT * FROM Assertions WHERE user_id='{$enc_id}'";

		if (array_key_exists('perspective', $more)){
			$enc_pid = AddSlashes($more['perspective']);
			$sql .= " AND perspective='{$enc_pid}'";
		}

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		$rsp = assertions_scrub_rsp($rsp);

		return $rsp;
	}

	########################################################################

	function assertions_get_for_user_latlon(&$user, $lat, $lon, $more=array()){

		$enc_id = AddSlashes($user['id']);
		$enc_lat = AddSlashes($lat);
		$enc_lon = AddSlashes($lon);

		$sql = "SELECT * FROM Assertions WHERE user_id='{$enc_id}'";

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
		$rsp = assertions_scrub_rsp($rsp);

		return $rsp;

	}

	########################################################################

	function assertions_get_for_woe(&$woe, $more=array()){

		$enc_id = AddSlashes($woe['woe_id']);

		$sql = "SELECT * FROM Assertions WHERE woe_id='{$enc_id}'";

		if (array_key_exists('perspective', $more)){
			$enc_pid = AddSlashes($more['perspective']);
			$sql .= " AND perspective='{$enc_pid}'";
		}

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated($sql, $more);
		$rsp = assertions_scrub_rsp($rsp);

		return $rsp;
	}

	########################################################################

	# use bcrypt?

	function assertions_obfuscate_remote_address($addr){

		$secret = $GLOBALS['cfg']['crypto_remote_address_secret'];

		# No soup for you

		if (! $secret){
			return '';
		}

		$hash = hash_hmac("sha256", $addr, $secret);
		return md5($hash);
	}

	########################################################################

	function assertions_scrub_rsp(&$rsp){

		if ($rsp['ok']){
			$rsp['rows'] = assertions_scrub_assertions($rsp['rows']);
		}

		return $rsp;
	}

	########################################################################

	function assertions_scrub_assertions(&$rows){

		$count = count($rows);

		for ($i=0; $i < $count; $i++){
			$rows[$i] = assertions_scrub_assertion($rows[$i]);
		}

		return $rows;
	}

	########################################################################

	function assertions_scrub_assertion($row){

		$to_remove = array(
			'user_id',
			'remote_address',
		);

		foreach ($to_remove as $prop){

			if (array_key_exists($prop, $row)){
				unset($row[$prop]);
			}
		}

		return $row;
	}

	########################################################################

	function assertions_delete_assertion(&$assertion){

		$rsp = assertions_redacted_redact_assertion($assertion);

		if (! $rsp['ok']){
			$error = $rsp['error'];
			return array('ok' => 0, 'error' => "Failed to redact assertion: {$error}");
		}

		$enc_id = AddSlashes($assertion['id']);
		$sql = "DELETE FROM Assertions WHERE id='{$enc_id}'";

		return db_write($sql);
	}

	########################################################################

	# the end
