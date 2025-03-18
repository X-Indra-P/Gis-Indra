@extends('layouts.homepage')
@section('content')
<div id="map" style="width: 100%; height: 500px;">
</div>
<script>
    var peta1 = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
});

var peta2 = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
    attribution: '&copy; <a href="https://www.google.com/maps">Google Maps</a>'
});

var peta3 = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: '&copy; <a href="https://www.esri.com/en-us/home">Esri</a>'
	});

    var map = L.map('map', {
    center: [-8.672515632167892, 115.21105447107882],
    zoom: 13,
    layers: [peta1]
});

var baseMaps = {
    "Map ": peta1,
    "Hybrid ": peta2,
    "SatelliteMap": peta3
};
var layerControl = L.control.layers(baseMaps).addTo(map);
</script>
@endsection