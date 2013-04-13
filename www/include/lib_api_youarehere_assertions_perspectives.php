<?php

	########################################################################

	function api_youarehere_corrections_perspectives_getList(){

		$map = corrections_perspective_map();
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

