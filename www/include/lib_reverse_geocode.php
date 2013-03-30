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

				$data[$i] = $props;
			}

			$rsp['data'] = $data;

			cache_set($cache_key, $rsp);
		}

		return $rsp;
	}

	########################################################################

	function reverse_geocode_is_valid_filter($filter){

		return (isset($GLOBALS['cfg']['reverse_geocode_endpoints'][$filter])) ? 1 : 0;
	}

	########################################################################

	function _reverse_geocode_endpoint($filter){

		$hosts = $GLOBALS['cfg']['reverse_geocode_endpoints'][$filter];

		if (count($hosts) > 1){
			shuffle($hosts);
		}

		return $hosts[0];
	}

	########################################################################

	# the end
