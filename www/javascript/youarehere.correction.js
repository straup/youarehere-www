function youarehere_correction_draw_map(geojson_url){

	var _onsuccess = function(geojson){
		console.log(geojson);
		youarehere_map_init(geojson);
	};

	$.ajax({
		'url': geojson_url,
		'success': _onsuccess
	});
}
