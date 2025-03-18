@extends('layouts.userlogin')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="crossorigin=""/>

    <style>
    #map { height: 500px; }
    </style>
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Simple Map</div>
                    <div class="card-body">
                        <div id="map"></div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
@endsection

@push('javascript')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Titi awal Peta
    const map = L.map('map').setView([-8.671656274097865, 115.22804432035959], 12);

    // Jenis-Jenis Peta
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '&copy; <a href="https://www.esri.com">Esri</a>'
    });

    const hybridLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        attribution: '&copy; <a href="https://www.google.com/maps">Google</a>'
    });

    // Peta Street Jadi Deafault
    streetLayer.addTo(map);

    // Tombol Ganti Peta
    const baseMaps = {
        "Street": streetLayer,
        "Satellite": satelliteLayer,
        "Hybrid": hybridLayer
    };

    L.control.layers(baseMaps).addTo(map);
</script>
@endpush
