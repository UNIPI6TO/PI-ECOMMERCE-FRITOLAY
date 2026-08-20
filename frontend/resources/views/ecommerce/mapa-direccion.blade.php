<div x-data="mapaDireccion()">
    <div class="mb-3 relative">
        <input type="text" x-model="searchQuery" @input.debounce.500ms="searchAddress" placeholder="Buscar dirección..." class="w-full border rounded px-3 py-2">
        <button @click="useMyLocation" class="absolute right-2 top-2 text-sm text-blue-600 flex items-center">
            Usar mi ubicación
        </button>
    </div>
    <div id="mapa-entrega" style="height: 350px;" class="rounded border"></div>
    <input type="hidden" x-model="lat">
    <input type="hidden" x-model="lng">
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mapaDireccion', () => ({
        searchQuery: '',
        lat: -1.249,
        lng: -78.616,
        map: null,
        marker: null,

        init() {
            setTimeout(() => {
                this.map = L.map('mapa-entrega').setView([this.lat, this.lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                this.marker = L.marker([this.lat, this.lng], {draggable: true}).addTo(this.map);
                
                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.lat = pos.lat;
                    this.lng = pos.lng;
                    this.reverseGeocode(pos.lat, pos.lng);
                });
            }, 100);
        },

        async searchAddress() {
            if(!this.searchQuery) return;
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${this.searchQuery}`);
            const data = await res.json();
            if(data.length > 0) {
                this.lat = data[0].lat;
                this.lng = data[0].lon;
                this.map.setView([this.lat, this.lng], 16);
                this.marker.setLatLng([this.lat, this.lng]);
            }
        },

        async reverseGeocode(lat, lng) {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await res.json();
            if(data.display_name) {
                this.searchQuery = data.display_name;
            }
        },

        useMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((pos) => {
                    this.lat = pos.coords.latitude;
                    this.lng = pos.coords.longitude;
                    this.map.setView([this.lat, this.lng], 16);
                    this.marker.setLatLng([this.lat, this.lng]);
                    this.reverseGeocode(this.lat, this.lng);
                });
            }
        }
    }));
});
</script>
