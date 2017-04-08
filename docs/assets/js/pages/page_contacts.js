var ContactPage = function () {

    return {
        
    	//Basic Map
        initMap: function () {
			var map;
			$(document).ready(function(){
			  map = new GMaps({
				div: '#map',
				scrollwheel: false,				
				lat: 53.675538,
				lng: 10.2382131,
				zoom: 14
			  });
			});
        }
    };
}();