<?php

	loadlib("artisanal_integers");

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

	function corrections_get_for_user(&$user, $more=array()){

		$enc_id = AddSlashes($user['id']);

		$sql = "SELECT * FROM Corrections WHERE user_id='{$enc_id}' ORDER BY created DESC";

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

	function corrections_obfuscate_ip_address(&$correction){
		$user = users_get_by_id($correction['user_id']);
		$hash = hash_hmac("sha256", $correction['ip_address'], $user['password']);
		return md5($hash);
	}

	########################################################################

	# the end
