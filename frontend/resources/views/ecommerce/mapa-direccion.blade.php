<div x-data="mapaDireccion()" @load-address.window="loadAddress($event.detail)">
    <div class="mb-3 relative">
        <input type="text" x-model="searchQuery" @input.debounce.500ms="searchAddress" placeholder="Buscar dirección..." class="w-full border rounded px-3 py-2 mb-2">
        <input type="text" x-model="referencia" placeholder="Referencia (ej. Cerca del parque)..." class="w-full border rounded px-3 py-2">
        <button @click="useMyLocation" class="absolute right-2 top-2 text-sm text-blue-600 flex items-center">
            Usar mi ubicación
        </button>
    </div>
    <div x-ref="mapContainer" style="height: 350px;" class="rounded border w-full"></div>
    <input type="hidden" x-model="lat">
    <input type="hidden" x-model="lng">
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mapaDireccion', () => ({
        searchQuery: '',
        referencia: '',
        lat: -1.249,
        lng: -78.616,
        map: null,
        marker: null,

        init() {
            setTimeout(() => {
                const mapEl = this.$refs.mapContainer;
                this.map = L.map(mapEl).setView([this.lat, this.lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                this.marker = L.marker([this.lat, this.lng], {draggable: true}).addTo(this.map);
                
                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.lat = pos.lat;
                    this.lng = pos.lng;
                    this.reverseGeocode(pos.lat, pos.lng);
                    this.dispatchData();
                });
                
                this.$watch('searchQuery', () => this.dispatchData());
                this.$watch('referencia', () => this.dispatchData());
                this.$watch('lat', () => this.dispatchData());
                this.$watch('lng', () => this.dispatchData());
                this.dispatchData(); // Initial dispatch

                // Fix map rendering when modal opens
                const resizeObserver = new ResizeObserver(() => {
                    if (this.map) {
                        this.map.invalidateSize();
                    }
                });
                resizeObserver.observe(mapEl);
            }, 100);
        },

        dispatchData() {
            this.$dispatch('update-dir-data', {
                descripcion: this.searchQuery,
                referencia: this.referencia,
                lat: this.lat,
                lng: this.lng
            });
        },

        loadAddress(data) {
            if (data) {
                this.searchQuery = data.descripcion || '';
                this.referencia = data.referencia || '';
                this.lat = data.latitud || -1.249;
                this.lng = data.longitud || -78.616;
                if (this.map && this.marker) {
                    this.map.setView([this.lat, this.lng], 16);
                    this.marker.setLatLng([this.lat, this.lng]);
                }
            } else {
                this.searchQuery = '';
                this.referencia = '';
                this.lat = -1.249;
                this.lng = -78.616;
                if (this.map && this.marker) {
                    this.map.setView([this.lat, this.lng], 13);
                    this.marker.setLatLng([this.lat, this.lng]);
                }
            }
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
