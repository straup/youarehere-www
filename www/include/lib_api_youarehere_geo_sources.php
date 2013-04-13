<?php

	########################################################################

	function api_youarehere_geo_sources_getList(){

		$sources = array();

		foreach ($GLOBALS['cfg']['reverse_geocoder_sources'] as $id => $name){
			$sources[] = array(
				'id' => $id,
				'name' => $name,
			);
		}

		$out = array(
			'sources' => $sources,
		);

		api_output_ok($out);
	}

	########################################################################

	# the end
