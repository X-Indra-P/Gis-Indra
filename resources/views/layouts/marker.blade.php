@extends('layouts.userlogin')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map { height: 500px; }
    #marker-list { margin-top: 20px; }
    .debug-info { 
        background-color: #f8f9fa; 
        padding: 10px; 
        border-radius: 5px; 
        margin-bottom: 15px; 
        display: none; 
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Peta dengan Marker
                </div>
                    <button onclick="panToLocation(-8.671656, 115.228044)" class="btn btn-primary mb-3">
                        Titik Awal
                    </button>
                    <div id="map"></div>
                    <div id="marker-list" class="mt-4">
                        <h4>Daftar Marker <span id="marker-count">(0)</span>:</h4>
                        <div id="marker-items"></div>
                    </div>
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

    // Inisialisasi peta
    let map;
    let markers = []; // Array untuk menyimpan marker

    // Fungsi inisialisasi
    function initMap() {
        
        // Inisialisasi peta jika belum ada
        if (!map) {
            map = L.map('map').setView([-8.671656, 115.228044], 12);

            // Tambahkan layer OpenStreetMap
            const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            });
            streetLayer.addTo(map);
            
            // Tambahkan event listener untuk menambah marker saat peta diklik
            map.on('click', function(e) {
                const { lat, lng } = e.latlng;
                const markerName = prompt("Masukkan nama marker:", "Marker Baru");

                if (markerName) {
                    saveMarker(lat, lng, markerName);
                }
            });
        }
    }
    
    // Pindah ke lokasi tertentu
    function panToLocation(lat, lng) {
        if (map) {
            map.panTo([lat, lng], {
                animate: true,
                duration: 1
            });
        }
    }

    // Fungsi untuk memuat ulang marker secara manual (untuk debugging)
    function refreshMarkers() {
        loadMarkers();
    }

    // Fungsi untuk mengambil marker dari database
    function loadMarkers() {
        // Kosongkan array markers terlebih dahulu
        markers = [];
        
        // Hapus semua marker dari peta
        if (map) {
            map.eachLayer(function(layer) {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });
        }
        
        // Ambil data marker dari server
        fetch('/get-markers')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                
                // Debug output untuk melihat struktur data
                console.log("Data marker dari server:", data);
                
                // Update counter
                document.getElementById('marker-count').textContent = `(${data.length})`;
                
                if (data.length === 0) {
                } else {
                    data.forEach((marker, index) => {
                        try {
                            
                            // Konversi latitude dan longitude ke number untuk memastikan
                            const lat = parseFloat(marker.latitude);
                            const lng = parseFloat(marker.longitude);
                            
                            if (isNaN(lat) || isNaN(lng)) {
                                throw new Error(`Koordinat tidak valid: (${marker.latitude}, ${marker.longitude})`);
                            }
                            
                            let newMarker = L.marker([lat, lng]).addTo(map)
                                .bindPopup(`<b>${marker.name}</b><br>(${lat.toFixed(5)}, ${lng.toFixed(5)})`);
                            
                            markers.push({ 
                                id: marker.id,
                                lat: lat, 
                                lng: lng, 
                                name: marker.name, 
                                marker: newMarker 
                            });
                        } catch (err) {
                        }
                    });
                }

                // Update daftar marker di halaman
                updateMarkerList();
            })
            .catch(error => {
                console.error('Error saat memuat marker:', error);
                
                document.getElementById('marker-items').innerHTML = 
                    `<div class="alert alert-danger">Gagal memuat marker: ${error.message}</div>`;
            });
    }

    // Fungsi untuk menyimpan marker ke database
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            
            // Setelah berhasil menyimpan, muat ulang semua marker
            loadMarkers();
        })
        .catch(error => {
            console.error("Error saat menyimpan marker:", error);
            
            alert(`Gagal menyimpan marker: ${error.message}`);
        });
    }

    // Fungsi untuk memperbarui daftar marker di HTML
    function updateMarkerList() {
        let listElement = document.getElementById("marker-items");
        listElement.innerHTML = "";
        
        if (markers.length === 0) {
            listElement.innerHTML = "<p>Belum ada marker tersimpan.</p>";
            return;
        }
        
        markers.forEach((m, index) => {
            listElement.innerHTML += `
                <div class="card mb-2">
                    <div class="card-body p-2">
                        <strong>${m.name}</strong> - (${m.lat.toFixed(5)}, ${m.lng.toFixed(5)})
                    </div>
                </div>
            `;
        });
    }
    
    // Inisialisasi semua komponen saat DOM selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        
        // Inisialisasi peta
        initMap();
        
        // Muat marker dari database
        setTimeout(() => {
            loadMarkers();
        }, 500); // Tunggu sebentar untuk memastikan peta telah selesai diinisialisasi
    });
</script>
@endpush