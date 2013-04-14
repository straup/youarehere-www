<?php

	########################################################################

	function assertions_redacted_get_by_id($id){

		# cache me...

		$enc_id = AddSlashes($id);

		$sql = "SELECT * FROM AssertionsRedacted WHERE assertion_id='{$enc_id}'";
		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		return $row;
	}

	########################################################################

	function assertions_redacted_redact_assertion(&$assertion){

		$now = time();

		$redacted = array(
			'assertion_id' => $assertion['id'],
			'redacted' => $now
		);

		$insert = array();

		foreach ($redacted as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('AssertionsRedacted', $insert);

		if ($rsp['ok']){
			$rsp['redacted'] = $redacted;
		}

		return $rsp;
	}

	########################################################################

	# the end
