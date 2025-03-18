@extends('layouts.userlogin')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map { height: 500px; }
    #marker-list { margin-top: 20px; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Peta dengan Marker</div>
                <div class="card-body">
                    <button onclick="panToLocation(-8.671656, 115.228044)" class="btn btn-primary mb-3">
                        Titik Awal
                    </button>
                    <div id="map"></div>
                    <div id="marker-list"></div>
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
    const map = L.map('map').setView([-8.671656, 115.228044], 12);

    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });

    streetLayer.addTo(map);

    let markers = [];

    function panToLocation(lat, lng) {
        map.panTo([lat, lng], {
            animate: true,
            duration: 1
        });
    }

    // Fungsi untuk mengambil marker dari database
    function loadMarkers() {
        fetch('/get-markers')
            .then(response => response.json())
            .then(data => {
                data.forEach(marker => {
                    let newMarker = L.marker([marker.latitude, marker.longitude]).addTo(map)
                        .bindPopup(`<b>${marker.name}</b><br>(${marker.latitude.toFixed(5)}, ${marker.longitude.toFixed(5)})`);
                    markers.push({ lat: marker.latitude, lng: marker.longitude, name: marker.name, marker: newMarker });
                });

                updateMarkerList();
            })
            .catch(error => console.error('Error loading markers:', error));
    }

    loadMarkers(); // Panggil saat halaman dimuat

    // Fungsi untuk menyimpan marker ke database dan menambahkannya ke daftar
    function saveMarker(lat, lng, name) {
        fetch('/save-marker', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: name,
                latitude: lat,
                longitude: lng
            })
        })
        .then(response => response.json())
        .then(data => {
            let newMarker = L.marker([lat, lng]).addTo(map)
                .bindPopup(`<b>${name}</b><br>(${lat.toFixed(5)}, ${lng.toFixed(5)})`);
            markers.push({ lat, lng, name, marker: newMarker });

            updateMarkerList();
            console.log("Marker saved:", data);
        })
        .catch(error => console.error("Error saving marker:", error));
    }

    // Fungsi untuk menambahkan marker dengan klik
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        const markerName = prompt("Masukkan nama marker:", "Marker Baru");

        if (markerName) {
            saveMarker(lat, lng, markerName);
        }
    });

    // Fungsi untuk memperbarui daftar marker di HTML
    function updateMarkerList() {
        let listElement = document.getElementById("marker-list");
        listElement.innerHTML = "<h4>Daftar Marker:</h4>";
        markers.forEach(m => {
            listElement.innerHTML += `<p>${m.name} - (${m.lat.toFixed(5)}, ${m.lng.toFixed(5)})</p>`;
        });
    }
</script>
@endpush
