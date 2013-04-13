<?php

	########################################################################

	function api_youarehere_geo_filters_getList(){

		$filters = array();

		foreach ($GLOBALS['cfg']['reverse_geocoder_clusters'] as $name => $details){

			$filter = array(
				'name' => $name,
				'fallback' => $details['fallback'],
				'source_id' => $details['source'],
			);

			if ($details['default']){
				$filter['is_default'] = 1;
			}

			$filters[] = $filter;
		}

		$out = array(
			'filters' => $filters,
		);

		api_output_ok($out);
	}

	########################################################################

	# the end
