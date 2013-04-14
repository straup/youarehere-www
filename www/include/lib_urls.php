<?php

	########################################################################

	function urls_assertions_url(){
		return $GLOBALS['cfg']['abs_root_url'] . "assertions/";
	}

	########################################################################

	function urls_assertion_url($assertion){
		$root = urls_assertions_url();
		return $root . "{$assertion['id']}/";
	}

	########################################################################

	function urls_assertion_export_url($assertion, $format="json"){
		$root = urls_assertions_url();
		return $root . "{$assertion['id']}.json";
	}

	########################################################################

	function urls_assertion_short_url($assertion){
		$root = $GLOBALS['cfg']['abs_root_url'] . "a/";
		$code = base58_encode($assertion['id']);
		return $root . "{$code}/";
	}

	########################################################################

	# the end
