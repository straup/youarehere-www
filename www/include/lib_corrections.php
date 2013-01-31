<?php

	# TO DO: artisanal integers...

	loadlib("spacetimeid");

	########################################################################

	function corrections_perspective_map(){

		$map = array(
			0 => "None of your business",
			1 => "Local",
			2 => "Tourist",
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

		$now = time();

		# TO DO: error handling, etc.
		# ALSO: this will never work unless we store the 'id' as a
		# string – the spacetime IDs are actually 128-bit (confirm)

		$st_rsp = spacetimeid_encode($data['latitude'], $data['longitude'], $now);

		$data['id'] = $st_rsp['data']['id'];
		$data['created'] = $now;

		dumper($data);
		return;

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

	# the end
