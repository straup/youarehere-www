<?php

	loadlib("http");

	########################################################################

	function reverse_geocode($lat, $lon, $filter){

		$endpoint = _reverse_geocode_endpoint($filter);

		if (! $endpoint){
			return array('ok' => 0, 'error' => 'invalid endpoint/filter');
		}

		$cache_key = "reverse_geo_{$filter}_{$lat}_{$lon}";
		$cache = cache_get($cache_key);

		if ($cache['ok']){
			return $cache['data'];
		}

		$query = array('lat' => $lat, 'lng' => $lon);

		$url = $endpoint . "?" . http_build_query($query);
		$rsp = http_get($url);

		if ($rsp['ok']){
			$data = json_decode($rsp['body'], 'as hash');
			$count = count($data);

			# Grrrrn...

			for ($i = 0; $i < $count; $i++){
				$props = $data[$i];

				foreach ($props as $k => $v){

					$lc_k = strtolower($k);

					if ($lc_k != $k){
						$props[$lc_k] = $v;
						unset($props[$k]);
					}
				}

				# Double grrrrnnnn...

				if (! isset($props['woe_id'])){
					$props['woe_id'] = $props['woeid'];
				}

				# Sigh... I am never going to escape these mistakes
				# I made in 2008 (20130408/straup)

				if (! isset($props['name'])){

					if ($props['place_type'] == 'neighbourhood'){

						# To account for stuff like this:
						# Boerum Hill, NY, US, United States

						$parts = explode(", ", $props['label']);
						$idx = count($parts) - 2;
						unset($parts[$idx]);

						$props['name'] = implode(", ", $parts);
					}

					else {
						$props['name'] = $props['label'];
					}
				}

				$data[$i] = $props;
			}

			$rsp['data'] = $data;

			cache_set($cache_key, $rsp);
		}

		return $rsp;
	}

	########################################################################

	function reverse_geocode_is_valid_filter($filter){

		return (isset($GLOBALS['cfg']['reverse_geocoder_clusters'][$filter])) ? 1 : 0;
	}

	########################################################################

	function reverse_geocode_results_to_geojson(&$results){

		$features = array();

		foreach ($results as $row){

			$geom = array(
				'type' => 'Point',
				'coordinates' => array($row['midpoint_lng'], $row['midpoint_lat']),
			);

			$props = array(
				'woe_id' => $row['woe_id'],
				'name' => $row['name'],
				'placetype' => $row['place_type'],
				'placetype_id' => $row['place_ty_1'],
			);

			$id = $props['woe_id'];

			$feature = array(
				'type' => 'Feature',
				'geometry' => $geom,
				'properties' => $props,
				'id' => $id,
			);

			$features[] = $feature;
		}

		return array(
			'type' => 'FeatureCollection',
			'features' => $features,
		);
	}

	########################################################################

	function reverse_geocode_default_filter(){

		foreach ($GLOBALS['cfg']['reverse_geocoder_clusters'] as $filter => $details){

			if (isset($details['default']) && $details['default']){
				return $filter;
			}
		}
	}

	########################################################################

	function reverse_geocode_filter_for_zoom($zoom){

		foreach ($GLOBALS['cfg']['reverse_geocoder_clusters'] as $filter => $details){

			if (in_array($zoom, $details['zoom_levels'])){
				return $filter;
			}
		}

	}

	########################################################################

	function reverse_geocode_filter_source($filter){

		$cluster = $GLOBALS['cfg']['reverse_geocoder_clusters'][$filter];
		return $cluster['source'];

	}

	########################################################################

	function reverse_geocode_source_label($id){

		$sources = $GLOBALS['cfg']['reverse_geocoder_sources'];
		return (isset($sources[$id])) ? $sources[$id] : null;
	}

	########################################################################
	
	function reverse_geocode_get_fallback($filter){

		$possible = $GLOBALS['cfg']['reverse_geocode_fallbacks'];

		if (! isset($possible[$filter])){
			return null;
		}

		return $possible[$filter];
	}

	########################################################################

	# get all the placetypes that are ancestors of $filter

	function reverse_geocode_get_fallback_tree($filter){

		$tree = array();

		$clusters = $GLOBALS['cfg']['reverse_geocoder_clusters'];

		if (! array_key_exists($filter, $clusters)){
			return $tree;
		}

		$parent = $clusters[$filter]['fallback'];
		$filter = $parent;

		if (! $filter){
			return $tree;
		}

		while ($parent){

			$parent = $clusters[$filter]['fallback'];
			$tree[$filter] = $parent;

			$filter = $parent;
		}

		return $tree;
	}

	########################################################################

	function reverse_geocode_is_valid_fallback($fallback){

		$clusters = array_keys($GLOBALS['cfg']['reverse_geocoder_clusters']);
		return (in_array($fallback, $clusters)) ? 1 : 0;
	}

	########################################################################

	# get all the placetypes that are descendants of $filter

	function reverse_geocode_get_falldown_tree($filter){

		$tree = array();

		$clusters = $GLOBALS['cfg']['reverse_geocoder_clusters'];

		if (! array_key_exists($filter, $clusters)){
			return $tree;
		}

		foreach ($clusters as $child => $parent){

			if ($child == $filter){
				break;
			}

			$tree[$child] = $parent;
		}

		return $tree;
	}

	########################################################################

	function _reverse_geocode_endpoint($filter){

		$cluster = $GLOBALS['cfg']['reverse_geocoder_clusters'][$filter];
		$hosts = $cluster['hosts'];

		if (count($hosts) > 1){
			shuffle($hosts);
		}

		return $hosts[0];
	}

	########################################################################

	# the end
