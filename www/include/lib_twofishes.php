<?php

	loadlib("http");

	########################################################################

	function twofishes_geocode($q){

		$query = array('query' => $q);
		$query = http_build_query($query);

		$url = $GLOBALS['cfg']['twofishes_endpoint'] . "?" . $query;

		$rsp = http_get($url);

		if (! $rsp['ok']){
			return $rsp;
		}

		$data = json_decode($rsp['body'], "as hash");

		# TO DO: error handling

		$rsp['data'] = $data;
		return $rsp;
	}

	########################################################################

	function twofishes_interpretations_to_geojson(&$interpretations){

		$features = array();

		$to_copy = array(
			'id' => 'id',
			'name' => 'name',
			'displayName' => 'displayname',
			'woeType' => 'placetype',
		);

		foreach ($interpretations as $row){

			$features = $row['feature'];
			$props = $features['attributes'];
		
			foreach ($to_copy as $copy_from => $copy_to){
				$props[ $copy_to ] = $features[ $copy_from ];
			}

			# dumper($props);
		}

		$geojson = array(
			'type' => 'FeatureCollection',
			'features' => $features,
		);

		return $geojson;
	}

	########################################################################

	# the end
