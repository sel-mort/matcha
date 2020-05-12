mapboxgl.accessToken = 'pk.eyJ1IjoiZWxoYWRpYXlvdWIiLCJhIjoiY2s2YjRmNGtqMGJwODNvcDk1aHMzdmhpMyJ9.CTHB72KEqEnTA_j_kxMxNQ';
var longitude = document.getElementById('longitude');
var latitude = document.getElementById('latitude');
var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11',
    center: [
        0, 0
    ],
    zoom: 2
});

var marker = new mapboxgl.Marker({ draggable: true }).setLngLat([0, 0]).addTo(map);

function onDragEnd() {
    var lngLat = marker.getLngLat();
    longitude.value = lngLat.lng;
    latitude.value = lngLat.lat;

    map.flyTo({
        center: [
            lngLat.lng,
            lngLat.lat
        ],
        essential: false // this animation is considered essential with respect to prefers-reduced-motion
    });
}

marker.on('dragend', onDragEnd);