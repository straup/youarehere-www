<?php

	########################################################################

	function corrections_export_as_geojson($corrections, $more=array()){

		$defaults = array(
			'add_bbox' => 0,
		);

		$more = array_merge($defaults, $more);

		$features = array();

		foreach ($corrections as $c){

			$coords = array(
				floatval($c['longitude']),
				floatval($c['latitude']),
			);

			$geom = array(
				'type' => 'Point',
				'coordinates' => $coords,
			);

			$feature = array(
				'type' => 'Feature',
				'id' => $c['id'],
				'properties' => array(),
				'geometry' => $geom,
			);

			$props = array(
				'user_id', 'woe_id', 'created', 'perspective',
			);

			foreach ($props as $p){
				$feature['properties'][$p] = intval($c[$p]);
			}

			$features[] = $feature;
		}

		# TO DO: bbox for features if count > 1

		if (($more['add_bbox']) && (count($features) > 1)){

		}

		$geojson = array(
			'type' => 'FeatureCollection',
			'features' => $features,
		);

		return $geojson;
	}

	########################################################################

	# the end
