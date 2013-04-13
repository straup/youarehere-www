<?php

	########################################################################

	function corrections_redacted_get_by_id($id){

		# cache me...

		$enc_id = AddSlashes($id);

		$sql = "SELECT * FROM CorrectionsRedacted WHERE correction_id='{$enc_id}'";
		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		return $row;
	}

	########################################################################

	function corrections_redacted_redact_correction(&$correction){

		$now = time();

		$redacted = array(
			'correction_id' => $correction['id'],
			'redacted' => $now
		);

		$insert = array();

		foreach ($redacted as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('CorrectionsRedacted', $insert);

		if ($rsp['ok']){
			$rsp['redacted'] = $redacted;
		}

		return $rsp;
	}

	########################################################################

	# the end
