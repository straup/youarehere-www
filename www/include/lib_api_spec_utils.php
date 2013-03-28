<?php

 	#################################################################

	function api_spec_utils_example_for_method($method){

		$path = FLAMEWORK_INCLUDE_DIR . "config.api.examples/{$method}.json";

		if (! file_exists($path)){
			return array('ok'=> 0, 'error' => 'no example defined for {$method} method');
		}

		return array(
			'ok' => 1,
			'example' => file_get_contents($path)
		);
	}

 	#################################################################

	# the end
