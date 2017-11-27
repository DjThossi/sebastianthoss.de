var GruenderPage = function () {

    return {

        //Basic Map
        initMap: function () {
            var map;
            $(document).ready(function(){
                map = new GMaps({
                    div: '#map',
                    scrollwheel: false,
                    lat: 53.544578,
                    lng: 9.978211,
                    zoom: 16
                });

                var marker = map.addMarker({
                    lat: 53.544578,
                    lng: 9.978211,
                    title: 'kartenmacherei am Neustädter Neuer Weg 22'
                });
            });
        }
    };
}();
