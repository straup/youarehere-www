<?php

	########################################################################

	function urls_corrections_url(){
		return $GLOBALS['cfg']['abs_root_url'] . "corrections/";
	}

	########################################################################

	function urls_correction_url($correction){
		$root = urls_corrections_url();
		return $root . "{$correction['id']}/";
	}

	########################################################################

	function urls_correction_short_url($correction){
		$root = $GLOBALS['cfg']['abs_root_url'] . "c/";
		$code = base58_encode($correction['id']);
		return $root . "{$code}/";
	}

	########################################################################

	# the end
