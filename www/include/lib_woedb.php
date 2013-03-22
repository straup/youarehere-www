<?php

	########################################################################

	function woedb_id2path($woeid){

		$tmp = $woeid;

		$parts = array();

		while (strlen($tmp)){
			$parts[] = substr($tmp, 0, 3);
			$tmp = substr($tmp, 3);
		}

		$parts = implode(DIRECTORY_SEPARATOR, $parts);
		$parts .= DIRECTORY_SEPARATOR;

		return $parts;
	}

	########################################################################

	function woedb_id2fq_path($woeid){

		$tree = woedb_id2path($woeid);
		$fname = "{$woeid}.json";

		$path = $GLOBALS['cfg']['woedb_static_path'] . $tree . $fname;
		return $path;
	}

	########################################################################

	# the end 
