<?php

	########################################################################

	function api_youarehere_assertions_perspectives_getList(){

		$map = assertions_perspective_map();
		$perspectives = array();

		foreach ($map as $id => $label){

			$perspectives[] = array(
				'id' => $id,
				'name' => $label,
			);
		}

		$out = array(
			'perspectives' => $perspectives,
		);

		api_output_ok($out);
	}

	########################################################################

	# the end

