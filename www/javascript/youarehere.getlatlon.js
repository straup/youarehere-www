var _map = null;
var _points = new Array(); /* not awesome; see below for details */

function youarehere_getlatlon_map(){

	var container_id = 'map';

	if (! _map){

		$("#" . container_id).show();

		var args = {
			'scrollWheelZoom': true,
			'zoomControl': true,
			'attributionControl': false
		};

		var map = L.map(container_id, args);

		var toner = 'http://tile.stamen.com/toner-background/{z}/{x}/{y}.jpg';

		var base = L.tileLayer(toner, {
			attribution: '',
			maxZoom: 18,
			minZoom: 1
		});

		base.addTo(map);
		_map = map;

	}

	return _map;
}
