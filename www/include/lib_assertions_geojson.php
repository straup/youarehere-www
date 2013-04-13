<?php

	########################################################################

	function assertions_geojson_assertions_to_geojson($assertions, $more=array()){

		$defaults = array(
			'add_bbox' => 0,
		);

		$more = array_merge($defaults, $more);

		$features = array();

		foreach ($assertions as $c){

			$features[] = assertions_geojson_assertion_to_feature($c);
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

	function assertions_geojson_assertion_to_feature(&$c){

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
			'woe_id', 'created', 'perspective',
		);

		foreach ($props as $p){
			$feature['properties'][$p] = intval($c[$p]);
		}

		return $feature;
	}

	########################################################################

	# the end
