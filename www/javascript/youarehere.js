function youarehere_locate(){

	var set_status = function(msg){
		var status = $("#status");
		status.html("<li>" + msg + "</li>" + status.html());
	};

	var _onsuccess = function(rsp){

		var coords = rsp['coords'];

		// trim me...
		var lat = coords['latitude'];
		var lon = coords['longitude'];

		set_status("The sensors have placed you at or around " + lat + "," + lon + ".");
		set_status("Now to figure out where that is.");

		setTimeout(function(){
			location.href = location.href + "?lat=" + lat + "&" + "lon=" + lon;
		}, 2500);
	};

	var _onerror = function(rsp){
		console.log("error");
		console.log(rsp);
	};

	navigator.geolocation.getCurrentPosition(_onsuccess, _onerror);

	set_status("Asking the sky where you are.");
}