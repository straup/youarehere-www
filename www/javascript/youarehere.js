function youarehere_locate(){

	var set_status = function(msg){
		var status = $("#status");
		status.html("<li>" + msg + "</li>" + status.html());
	};

	var _onsuccess = function(rsp){

		var coords = rsp['coords'];

		var lat = coords['latitude'].toFixed(6);
		var lon = coords['longitude'].toFixed(6);

		set_status("The sensors have placed you at or around " + lat + ", " + lon + ".");
		set_status("Now to figure out where that is.");

		setTimeout(function(){
			location.href = location.href + "?lat=" + lat + "&" + "lon=" + lon;
		}, 2500);
	};

	var _onerror = function(rsp){
		if (rsp.code == rsp.TIMEOUT) {
			set_status("Hmmm... the cloud is taking a long, long time to find you.");
		} else if (rsp.code == rsp.POSITION_UNAVAILABLE) {
			if (rsp.message) {
				set_status("Hmmm... the cloud can't find you, because " + rsp.message);
			} else {
				set_status("Hmmm... the cloud simply can't find you.");
			}
		} else if (rsp.code == rsp.PERMISSION_DENIED) {
			set_status("Oh, it sounds like you don't want to be located. Maybe you want to <a href=\"/choose\">choose</a> your location?");
		}
	};

	navigator.geolocation.getCurrentPosition(_onsuccess, _onerror, {timeout: 30000});

	set_status("Asking the sky where you are.");
}