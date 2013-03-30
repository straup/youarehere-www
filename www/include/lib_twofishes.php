<?php

	loadlib("http");

	########################################################################

	function twofishes_geocode($q){

		$cache_key = "geocode_{$q}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

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

		cache_set($cache_key, $rsp);

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

			$f = $row['feature'];
			$g = $f['geometry'];

			$props = $f['attributes'];

			foreach ($to_copy as $copy_from => $copy_to){
				$props[ $copy_to ] = $f[ $copy_from ];
			}

			$props['latitude'] = $g['center']['lat'];
			$props['longitude'] = $g['center']['lng'];

			# TO DO: names and concordances

			$geom = array(
				'type' => 'GeometryCollection',
				'geometries' => [],
			);

			$bbox = null;

			$pt_geom = array(
				'type' => 'Point',
				'coordinates' => array($g['center']['lng'], $g['center']['lat'])
			);

			$geom['geometries'][] = $pt_geom;

			if (isset($g['bounds'])){

				$swlat = $g['bounds']['sw']['lat'];
				$swlon = $g['bounds']['sw']['lng'];
				$nelat = $g['bounds']['ne']['lat'];
				$nelon = $g['bounds']['ne']['lng'];

				$bbox = array($swlon, $swlat, $nelon, $nelat);

				$coords = array(array(
					array($swlon, $swlat),
					array($swlon, $nelat),
					array($nelon, $nelat),
					array($nelon, $swlat),
					array($swlon, $swlat),
				));

				$poly_geom = array(
					'type' => 'Polygon',
					'coordinates' => $coords
				);

				$geom['geometries'][] = $poly_geom;
			}

			$feature = array(
				'type' => 'Feature',
				'geometry' => $geom,
				'properties' => $props
			);

			if ($bbox){
				$feature['bbox'] = $bbox;
			}

			$features[] = $feature;
		}

		$geojson = array(
			'type' => 'FeatureCollection',
			'features' => $features,
		);

		return $geojson;
	}

	########################################################################

	# the end
