let mapInstance = null;
let currentMarker = null;

export const initMap = (containerId, lat, lng, zoom = 13) => {
    if (!window.L) {
        console.error("Leaflet not loaded");
        return;
    }
    mapInstance = window.L.map(containerId).setView([lat, lng], zoom);
    
    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapInstance);
    
    return mapInstance;
};

export const addPin = (lat, lng, popupText = "") => {
    if (!mapInstance) return;
    currentMarker = window.L.marker([lat, lng]).addTo(mapInstance);
    if (popupText) {
        currentMarker.bindPopup(popupText).openPopup();
    }
    return currentMarker;
};

export const movePinTo = (lat, lng) => {
    if (currentMarker) {
        currentMarker.setLatLng([lat, lng]);
        mapInstance.panTo([lat, lng]);
    }
};

export const geocodeAddress = async (address) => {
    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`);
    const data = await res.json();
    if (data && data.length > 0) {
        return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
    }
    return null;
};

export const reverseGeocode = async (lat, lng) => {
    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
    const data = await res.json();
    return data?.display_name || null;
};

export const getRoute = async (origin, destination) => {
    const url = `https://router.project-osrm.org/route/v1/driving/${origin.lng},${origin.lat};${destination.lng},${destination.lat}?overview=full&geometries=geojson`;
    const res = await fetch(url);
    return res.json();
};
