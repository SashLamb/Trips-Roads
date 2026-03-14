/**
 * @file Carte interactive avec recherche de points d’intérêt (POI) de la page index
 * @description
 * Ce script initialise une carte Leaflet permettant :
 * - La géolocalisation utilisateur
 * - La recherche d’un lieu via Nominatim
 * - L’affichage de points d’intérêt via l’API Overpass (OpenStreetMap)
 * - Le filtrage par catégorie et réglage du rayon
 * * Dépendances : Leaflet.js, API Overpass, API Nominatim
 */

/* ======================================================
    CONFIGURATION ET VARIABLES GLOBALES
====================================================== */

/** @type {Object} */
const appConfig = window.appConfig || {};

/** @type {number[]} Coordonnées par défaut (Lyon par exemple) */
const defaultCoords = [appConfig.defaultLat || 45.767518, appConfig.defaultLon || 4.833534];

/** @type {L.Map} Instance de la carte Leaflet */
let map;

/** @type {L.LayerGroup} Calques pour les marqueurs */
let searchLayer, poiLayer;

/** @type {number[]} Position actuelle de recherche */
let currentCoords = defaultCoords;

/** @type {L.Circle|null} Cercle visualisant le rayon de recherche */
let currentCircle = null;

/** @type {number} Rayon de recherche en mètres */
let searchRadius = 2000;

/**
 * Configuration des filtres Overpass
 * @typedef {Object} POIFilter
 * @property {string} query - Requête Overpass
 * @property {string} icon - Emoji affiché
 * @property {string} color - Couleur du marqueur
 */

/** @type {Object.<string, POIFilter>} */
const poiFilters = {
    restaurant: { query: 'node["amenity"="restaurant"](around:{radius},{lat},{lon});', icon: '🍽️', color: '#e74c3c' },
    fast_food: { query: 'node["amenity"="fast_food"](around:{radius},{lat},{lon});', icon: '🍔', color: '#e67e22' },
    cafe: { query: 'node["amenity"="cafe"](around:{radius},{lat},{lon});', icon: '☕', color: '#d35400' },
    bar: { query: 'node["amenity"="bar"](around:{radius},{lat},{lon});node["amenity"="pub"](around:{radius},{lat},{lon});', icon: '🍺', color: '#9b59b6' },
    hotel: { query: 'node["tourism"="hotel"](around:{radius},{lat},{lon});', icon: '🏨', color: '#3498db' },
    camping: { query: 'node["tourism"="camp_site"](around:{radius},{lat},{lon});', icon: '🏕️', color: '#27ae60' },
    fuel: { query: 'node["amenity"="fuel"](around:{radius},{lat},{lon});', icon: '⛽', color: '#f39c12' },
    parking: { query: 'node["amenity"="parking"](around:{radius},{lat},{lon});', icon: '🅿️', color: '#34495e' },
    attraction: { query: 'node["tourism"="attraction"](around:{radius},{lat},{lon});', icon: '🎭', color: '#1abc9c' },
    museum: { query: 'node["tourism"="museum"](around:{radius},{lat},{lon});', icon: '🏛️', color: '#8e44ad' },
    park: { query: 'node["leisure"="park"](around:{radius},{lat},{lon});', icon: '🌳', color: '#27ae60' },
    hospital: { query: 'node["amenity"="hospital"](around:{radius},{lat},{lon});', icon: '🏥', color: '#c0392b' }
};

/* ======================================================
   FONCTIONS GLOBALES
   ====================================================== */

/**
 * Gère l'ouverture/fermeture de la barre latérale des filtres.
 * @function toggleSidebar
 */
function toggleSidebar() {
    const sidebar = document.getElementById('mapSidebar');
    const icon = document.getElementById('toggleIcon');
    if (sidebar) {
        sidebar.classList.toggle('closed');
        if (icon) {
            icon.innerHTML = sidebar.classList.contains('closed') ? "▶" : "◀";
        }
        if (map) {
            setTimeout(() => { map.invalidateSize(); }, 300);
        }
    }
}

/**
 * Crée une icône personnalisée Leaflet avec un emoji.
 * @param {string} emoji
 * @param {string} color
 * @returns {L.DivIcon}
 */
function createCustomIcon(emoji, color) {
    return L.divIcon({
        html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3); font-size: 16px;">${emoji}</div>`,
        className: 'custom-poi-icon',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });
}

/* ======================================================
   3. INITIALISATION ET LOGIQUE DOM
   ====================================================== */

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('poiSearchIndex');
    const searchResults = document.getElementById('searchResultsIndex');
    const categorySelect = document.getElementById('categorySelect');
    const clearFilterBtn = document.getElementById('clearFilterBtn');
    const radiusSlider = document.getElementById('radiusSlider');
    const radiusValueSpan = document.getElementById('radiusValue');

    if (!document.getElementById('userMapIndex')) return;

    /**
     * Initialise la carte et les calques.
     */
    function initMap() {
        map = L.map('userMapIndex').setView(currentCoords, 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 19
        }).addTo(map);

        searchLayer = L.layerGroup().addTo(map);
        poiLayer = L.layerGroup().addTo(map);

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                pos => updateSearchPosition(pos.coords.latitude, pos.coords.longitude, 13),
                () => updateSearchPosition(currentCoords[0], currentCoords[1], 6)
            );
        } else {
            updateSearchPosition(currentCoords[0], currentCoords[1], 6);
        }

        map.on('click', e => updateSearchPosition(e.latlng.lat, e.latlng.lng));

        // Correctif pour les problèmes d'affichage de tuiles au chargement
        setTimeout(() => { map.invalidateSize(); }, 200);
    }

    /**
     * Met à jour la position du marqueur central et du cercle de rayon.
     */
    function updateSearchPosition(lat, lng, zoom = null) {
        currentCoords = [lat, lng];
        searchLayer.clearLayers();

        const userIcon = L.divIcon({
            html: '<div style="font-size:30px; margin-top:-20px; text-align:center;">📍</div>',
            className: 'custom-pin',
            iconSize: [30, 42],
            iconAnchor: [15, 20]
        });

        L.marker([lat, lng], { icon: userIcon }).addTo(searchLayer);

        currentCircle = L.circle([lat, lng], {
            color: '#3498db',
            fillColor: '#3498db',
            fillOpacity: 0.15,
            radius: searchRadius
        }).addTo(searchLayer);

        if (zoom) map.setView([lat, lng], zoom);
        if (categorySelect && categorySelect.value) loadPOI(categorySelect.value);
    }

    /**
     * Appelle l'API Overpass pour charger les POI selon la catégorie.
     */
    async function loadPOI(filterType) {
        poiLayer.clearLayers();
        document.body.style.cursor = 'wait';

        const filter = poiFilters[filterType];
        if (!filter) {
            document.body.style.cursor = 'default';
            return;
        }

        const query = filter.query
            .replace(/{lat}/g, currentCoords[0])
            .replace(/{lon}/g, currentCoords[1])
            .replace(/{radius}/g, searchRadius);

        const overpassUrl = 'https://overpass-api.de/api/interpreter';
        const overpassQuery = `[out:json][timeout:25];(${query});out body;`;

        try {
            const response = await fetch(overpassUrl, { method: 'POST', body: overpassQuery });
            const data = await response.json();
            document.body.style.cursor = 'default';

            if (data.elements.length > 0) {
                if (clearFilterBtn) clearFilterBtn.style.display = 'block';
                data.elements.forEach(element => {
                    if (element.lat && element.lon) {
                        const icon = createCustomIcon(filter.icon, filter.color);
                        L.marker([element.lat, element.lon], { icon: icon })
                            .addTo(poiLayer)
                            .bindPopup(`<b>${filter.icon} ${element.tags.name || 'Sans nom'}</b>`);
                    }
                });
            }
        } catch (error) {
            console.error("Erreur Overpass :", error);
            document.body.style.cursor = 'default';
        }
    }


    if (radiusSlider) {
        radiusSlider.addEventListener('input', function() {
            const km = this.value;
            if(radiusValueSpan) radiusValueSpan.textContent = km;
            searchRadius = km * 1000;
            if (currentCircle) currentCircle.setRadius(searchRadius);
        });

        radiusSlider.addEventListener('change', function() {
            if (categorySelect && categorySelect.value) loadPOI(categorySelect.value);
        });
    }


    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            if (this.value) loadPOI(this.value);
            else {
                poiLayer.clearLayers();
                if (clearFilterBtn) clearFilterBtn.style.display = 'none';
            }
        });
    }


    if (clearFilterBtn) {
        clearFilterBtn.addEventListener('click', function() {
            poiLayer.clearLayers();
            if (categorySelect) categorySelect.value = "";
            this.style.display = 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length > 2) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.textContent = item.display_name;
                            li.addEventListener('click', () => {
                                updateSearchPosition(parseFloat(item.lat), parseFloat(item.lon), 14);
                                searchResults.innerHTML = '';
                                searchInput.value = '';
                            });
                            searchResults.appendChild(li);
                        });
                    });
            } else { searchResults.innerHTML = ''; }
        });
    }


    initMap();
});
