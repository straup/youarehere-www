<?php

	########################################################################

	function corrections_sources_for_filter($filter){

		$source_id = $GLOBALS['cfg']['reverse_geocode_endpoints_sources'][$filter];
		return $source_id;
	}

	########################################################################

	# Maybe hard-code here... dunno (20130223/straup)

	function corrections_sources_map(){
		return $GLOBALS['cfg']['reverse_geocode_sources'];
	}

	########################################################################

	function corrections_sources_label($source_id){

		$map = corrections_sources_map();

		if (! isset($map[$source_id])){
			$source_id = 0;
		}

		return $map[$source_id];
	}

	########################################################################

	# the end
