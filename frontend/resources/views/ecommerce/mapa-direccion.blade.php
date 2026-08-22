<div x-data="mapaDireccion()" @load-address.window="loadAddress($event.detail)" @click.away="showDropdown = false">
    <div class="mb-3 relative">
        <input type="text" x-model="searchQuery" @input.debounce.500ms="searchAddress" @focus="if(searchResults.length > 0) showDropdown = true" placeholder="Buscar dirección..." class="w-full border rounded px-3 py-2 mb-2">
        <button type="button" @click="useMyLocation" class="absolute right-2 top-2 text-sm text-blue-600 flex items-center bg-white px-1">
            Usar mi ubicación
        </button>
        
        <!-- Dropdown de resultados -->
        <div x-show="showDropdown && searchResults.length > 0" class="absolute w-full bg-white mt-[-8px] mb-2 border rounded-md shadow-lg max-h-60 overflow-y-auto" style="display: none; z-index: 9999;">
            <template x-for="result in searchResults" :key="result.place_id">
                <div @click="selectResult(result)" class="px-4 py-3 hover:bg-gray-100 cursor-pointer border-b last:border-b-0 flex justify-between items-center">
                    <div class="flex-1 pr-4">
                        <p class="text-sm font-medium text-gray-800" x-text="result.display_name.split(',')[0]"></p>
                        <p class="text-xs text-gray-500 truncate" x-text="result.display_name"></p>
                    </div>
                    <template x-if="result.distance !== null">
                        <span class="text-xs font-bold text-[#E3001B] whitespace-nowrap bg-red-50 px-2 py-1 rounded-full" x-text="result.distance.toFixed(1) + ' km'"></span>
                    </template>
                </div>
            </template>
        </div>

        <input type="text" x-model="referencia" placeholder="Referencia (ej. Cerca del parque)..." class="w-full border rounded px-3 py-2">
    </div>
    <div x-ref="mapContainer" style="height: 350px; z-index: 10; position: relative;" class="rounded border w-full"></div>
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
        currentLat: null,
        currentLng: null,
        map: null,
        marker: null,
        searchResults: [],
        showDropdown: false,

        init() {
            // Get current location quietly for distance calculation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => { 
                        this.currentLat = pos.coords.latitude; 
                        this.currentLng = pos.coords.longitude; 
                    },
                    (err) => { console.log("Geolocation no disponible o denegada"); }
                );
            }

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
                
                this.map.on('click', (e) => {
                    const pos = e.latlng;
                    this.lat = pos.lat;
                    this.lng = pos.lng;
                    this.marker.setLatLng(pos);
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

        calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the earth in km
            const dLat = this.deg2rad(lat2-lat1);
            const dLon = this.deg2rad(lon2-lon1); 
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) * 
                      Math.sin(dLon/2) * Math.sin(dLon/2); 
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            return R * c; // Distance in km
        },

        deg2rad(deg) { return deg * (Math.PI/180); },

        async searchAddress() {
            if(!this.searchQuery) {
                this.searchResults = [];
                this.showDropdown = false;
                return;
            }
            
            // Allow manual typing without immediately panning, just fetch results
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${this.searchQuery}&addressdetails=1&limit=5`);
            const data = await res.json();
            
            this.searchResults = data.map(item => {
                let distance = null;
                if (this.currentLat && this.currentLng) {
                    distance = this.calculateDistance(this.currentLat, this.currentLng, parseFloat(item.lat), parseFloat(item.lon));
                }
                return { ...item, distance };
            });

            if (this.currentLat && this.currentLng) {
                this.searchResults.sort((a, b) => a.distance - b.distance);
            }

            this.showDropdown = true;
        },

        selectResult(result) {
            this.searchQuery = result.display_name;
            this.lat = parseFloat(result.lat);
            this.lng = parseFloat(result.lon);
            if (this.map && this.marker) {
                this.map.setView([this.lat, this.lng], 16);
                this.marker.setLatLng([this.lat, this.lng]);
            }
            this.searchResults = [];
            this.showDropdown = false;
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
                    this.currentLat = this.lat;
                    this.currentLng = this.lng;
                    this.map.setView([this.lat, this.lng], 16);
                    this.marker.setLatLng([this.lat, this.lng]);
                    this.reverseGeocode(this.lat, this.lng);
                });
            }
        }
    }));
});
</script>
