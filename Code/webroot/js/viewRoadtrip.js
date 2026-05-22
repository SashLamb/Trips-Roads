/**
 * @file Roadtrip Visualization and Trip Data Management
 * @description
 * Handles the display of roadtrip routes on Leaflet maps, including:
 * - Drawing routes with numbered markers
 * - Mode of transport selection (car, bike, foot)
 * - ETA and duration calculation via OSRM
 * - Markdown rendering for descriptions
 * @requires Leaflet.js
 * @requires Leaflet.markercluster
 * @requires OSRM
 * @requires marked.js
 * @requires DOMPurify
 */
document.addEventListener('DOMContentLoaded', () => {
    initGlobalMap();
    setTimeout(calculateAllSegments, 300);
});

/** * Stores instances of individual step maps indexed by ID
 * @type {Object.<string, L.Map>}
 */
const viewMapInstances = {};

/**
 * Fetches a route via the server-side proxy (bypasses browser proxy restrictions).
 * @param {string} coords - Semicolon-separated "lon,lat" pairs
 * @param {string} mode - Lowercase transport mode string
 * @param {string} queryParams - URL query string
 * @returns {Promise<Object|null>}
 */
async function fetchViewRoute(coords, mode, queryParams) {
    const proxyUrl = `/roadtrips/route-proxy?coords=${encodeURIComponent(coords)}&mode=${encodeURIComponent(mode)}&params=${encodeURIComponent(queryParams)}`;
    try {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), 15000);
        const resp = await fetch(proxyUrl, { signal: controller.signal });
        clearTimeout(timer);
        if (resp.ok) {
            const json = await resp.json();
            if (json.code === 'Ok') return json;
        }
    } catch (e) {
        console.warn(`Route proxy failed: ${e.message}`);
    }
    return null;
}

/** * Color palette for distinct trip paths
 * @type {string[]}
 */
const colorsPalette = [
    '#e6194b', '#3cb44b', '#ffe119', '#4363d8', '#f58231',
    '#911eb4', '#42d4f4', '#f032e6', '#bfef45', '#fabed4',
    '#469990', '#dcbeff', '#9A6324', '#fffac8', '#800000'
];

/**
 * Retrieves trip data from the global roadTripData array.
 * @function getTrajetData
 * @param {string|number} id - The ID of the trip
 * @returns {Object|null} The trip data object or null
 */

function getTrajetData(id) {
    if (typeof roadTripData === 'undefined' || !roadTripData) return null;
    return roadTripData.find(t => t.id == id);
}
/**
 * Initializes the main global map showing all trips.
 * @async
 * @function initGlobalMap
 * @returns {Promise<void>}
 */
async function initGlobalMap() {
    const mapDiv = document.getElementById('map-global');
    if (!mapDiv) return;

    mapDiv.style.display = 'block';

    const hasData = (typeof roadTripData !== 'undefined' && roadTripData && roadTripData.length > 0);
    const map = L.map('map-global');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    setTimeout(() => { map.invalidateSize(); }, 200);

    if (!hasData) {
        map.setView([46.6, 2.2], 5);
        return;
    }

    const markersCluster = L.markerClusterGroup({
        maxClusterRadius: 40,
        showCoverageOnHover: false
    });
    map.addLayer(markersCluster);

    const bounds = [];
    let colorIndex = 0;

    for (const data of roadTripData) {
        if (!data.depart || !data.arrivee) continue;

        data.color = colorsPalette[colorIndex % colorsPalette.length];

        try {
            await drawRoute(map, data, data.color, false, markersCluster);

            if(data.depart.lat) bounds.push([data.depart.lat, data.depart.lon]);
            if(data.arrivee.lat) bounds.push([data.arrivee.lat, data.arrivee.lon]);
            if(data.sousEtapes) {
                data.sousEtapes.forEach(s => { if(s.lat) bounds.push([s.lat, s.lon]); });
            }
        } catch (e) { console.error(e); }
        colorIndex++;
    }

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [50, 50] });
    } else {
        map.setView([46.6, 2.2], 6);
    }
}
/**
 * Initializes a specific map for a single trip step.
 * @async
 * @function initStepMap
 * @param {string|number} id - Trip ID
 * @returns {Promise<void>}
 */
async function initStepMap(id) {
    const data = getTrajetData(id);
    const divId = 'map-trajet-' + id;
    const divElement = document.getElementById(divId);

    if (!divElement) return;

    if (!data || !data.depart) {
        divElement.innerHTML = '<div style="display:flex; align-items:center; justify-content:center; height:100%; color:#888;">Données en attente...</div>';
        return;
    }

    if (viewMapInstances[id]) {
        setTimeout(() => {
            viewMapInstances[id].invalidateSize();
            if(data.layerBounds) viewMapInstances[id].fitBounds(data.layerBounds, { padding: [20, 20] });
        }, 200);
        return;
    }

    const map = L.map(divId);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM' }).addTo(map);

    viewMapInstances[id] = map;

    setTimeout(async () => {
        map.invalidateSize();
        await drawRoute(map, data, data.color || '#3388ff', true, null);
    }, 100);
}
/**
 * Draws the route polyline and markers on a map using OSRM routing.
 * Falls back to straight lines if the API fails.
 * @async
 * @function drawRoute
 * @param {L.Map} map - Target Leaflet map
 * @param {Object} data - Trip data
 * @param {string} color - Polyline color
 * @param {boolean} fitBounds - Whether to zoom into the route
 * @param {L.MarkerClusterGroup|null} clusterGroup - Optional cluster group for markers
 * @returns {Promise<void>}
 */
async function drawRoute(map, data, color, fitBounds, clusterGroup) {
    let stepCounter = 1;

    createNumberedMarker(map, data.depart, stepCounter++, color, `<b>Départ:</b> ${data.depart.nom}`, 'left', clusterGroup);

    if(data.sousEtapes) {
        data.sousEtapes.forEach(s => {
            let popup = `<b>📍 ${s.nom}</b>`;
            if(s.heure && s.heure !== "00:00:00") popup += `<br>⏱️ Pause: ${s.heure}`;
            createNumberedMarker(map, s, stepCounter++, color, popup, null, clusterGroup);
        });
    }

    createNumberedMarker(map, data.arrivee, stepCounter, color, `<b>Arrivée:</b> ${data.arrivee.nom}`, 'right', clusterGroup);

    const mode = (data.mode || 'voiture').toLowerCase();

    let coords = `${data.depart.lon},${data.depart.lat}`;
    if(data.sousEtapes) {
        data.sousEtapes.forEach(s => {
            if(s.lon && s.lat) coords += `;${s.lon},${s.lat}`;
        });
    }
    coords += `;${data.arrivee.lon},${data.arrivee.lat}`;

    const json = await fetchViewRoute(coords, mode, 'overview=full&geometries=geojson');

    if (json && json.routes[0]) {
        const style = {
            color: color,
            weight: 5,
            opacity: 0.8,
            dashArray: (mode !== 'voiture') ? '10, 10' : null
        };

        const layer = L.geoJSON(json.routes[0].geometry, style).addTo(map);

        if(fitBounds) map.fitBounds(layer.getBounds(), { padding:[30,30] });
        data.layerBounds = layer.getBounds();
    } else {
        console.warn("Tous les serveurs de routage ont échoué, tracé ligne droite.");
        const points = [[data.depart.lat, data.depart.lon]];
        if(data.sousEtapes) data.sousEtapes.forEach(s => points.push([s.lat, s.lon]));
        points.push([data.arrivee.lat, data.arrivee.lon]);

        L.polyline(points, { color: color, dashArray: '5,10' }).addTo(map);
        if(fitBounds) map.fitBounds(L.latLngBounds(points));
    }
}
/**
 * Creates a circular numbered marker for route steps.
 * @function createNumberedMarker
 * @param {L.Map} map - Leaflet map instance
 * @param {Object} point - Latitude and Longitude object
 * @param {number} number - Step number to display
 * @param {string} color - Background color
 * @param {string} popupText - HTML content for the popup
 * @param {string|null} offset - CSS class for position offset
 * @param {L.MarkerClusterGroup|null} cluster - Cluster group to add to
 */
function createNumberedMarker(map, point, number, color, popupText, offset, cluster) {
    if(!point || !point.lat || !point.lon) return;

    const offsetClass = offset ? `marker-offset-${offset}` : '';

    const icon = L.divIcon({
        className: `custom-marker-number ${offsetClass}`,
        html: `<div class="marker-pin" style="background-color: ${color}; color: white; display:flex; justify-content:center; align-items:center; border-radius:50%; width:100%; height:100%; border:2px solid white; box-shadow:0 2px 5px rgba(0,0,0,0.3); font-weight:bold;">${number}</div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 15],
        popupAnchor: [0, -15]
    });

    const marker = L.marker([point.lat, point.lon], { icon: icon }).bindPopup(popupText);

    if(cluster) cluster.addLayer(marker);
    else marker.addTo(map);
}
/**
 * Toggles the visibility of trip details and local maps.
 * Hides global map when a specific trip is active.
 * @function toggleTrajet
 * @param {string|number} id - Trip ID
 */
window.toggleTrajet = function(id) {
    const content = document.getElementById('sub-steps-' + id);
    const card = document.getElementById('card-' + id);
    const mapGlobal = document.getElementById('map-global');

    if (!content || !card) return;

    const isHidden = content.classList.contains('d-none');

    if (!isHidden) {
        content.classList.add('d-none');
        card.classList.remove('active');
        checkToggleGlobalMap();
    } else {
        content.classList.remove('d-none');
        card.classList.add('active');

        if(mapGlobal) mapGlobal.style.display = 'none';

        setTimeout(() => { initStepMap(id); }, 100);
    }
};

/**
 * Checks if any trip cards are active and toggles the global map accordingly.
 * @function checkToggleGlobalMap
 */
function checkToggleGlobalMap() {
    const active = document.querySelectorAll('.card-vu.active'); // card-vu est la classe que j'ai remise dans le PHP
    const mapGlobal = document.getElementById('map-global');

    if(mapGlobal) {
        if (active.length === 0) {
            mapGlobal.style.display = 'block';
            setTimeout(() => {
                const map = L.map('map-global');
                window.dispatchEvent(new Event('resize'));
            }, 100);
        } else {
            mapGlobal.style.display = 'none';
        }
    }
}
/**
 * Fetches OSRM routing data for all trip cards in parallel.
 * @function calculateAllSegments
 */
function calculateAllSegments() {
    const cards = document.querySelectorAll('.card-vu');
    Promise.all([...cards].map(card => processCardTimes(card)));
}
/**
 * Processes OSRM routing data for a specific card to update UI with distances and times.
 * @async
 * @function processCardTimes
 * @param {HTMLElement} card - The DOM element of the trip card
 * @returns {Promise<void>}
 */
async function processCardTimes(card) {
    const id = card.id.replace('card-', '');
    const data = getTrajetData(id);

    if(!data || !data.depart) return;

    const mode = (data.mode || 'voiture').toLowerCase();

    let coords = `${data.depart.lon},${data.depart.lat}`;
    if(data.sousEtapes) data.sousEtapes.forEach(s => { coords += `;${s.lon},${s.lat}`; });
    coords += `;${data.arrivee.lon},${data.arrivee.lat}`;

    try {
        const json = await fetchViewRoute(coords, mode, 'overview=false&steps=false');

        if (json && json.routes && json.routes[0]) {
            const legs = json.routes[0].legs;

            let currentClock = data.heure_depart || '08:00';

            const segmentInfos = card.querySelectorAll('.segment-info');
            const stepRows = card.querySelectorAll('.step-row');

            legs.forEach((leg, index) => {
                if(segmentInfos[index]) {
                    const segDiv = segmentInfos[index];
                    const distKm = (leg.distance / 1000).toFixed(1);

                    segDiv.querySelector('.segment-distance').textContent = `${distKm} km`;
                    segDiv.querySelector('.segment-time').textContent = formatDuration(leg.duration);

                    segDiv.querySelectorAll('.segment-loader').forEach(el => el.remove());
                }

                const arrivalClock = addTime(currentClock, leg.duration);

                const targetRow = stepRows[index + 1];

                if (targetRow) {
                    const loader = targetRow.querySelector('.horaire-calcule');

                    if(loader) {
                        const stepCard = targetRow.querySelector('.step-card');
                        const pauseStr = stepCard ? stepCard.getAttribute('data-pause') : '00:00';
                        const pauseSec = durationToSeconds(pauseStr);

                        const isLast = (index === legs.length - 1);

                        if (isLast) {
                            loader.innerHTML = `🏁 Arrivée : <strong>${arrivalClock}</strong>`;
                        } else {
                            let html = `⏰ Arrivée : <strong>${arrivalClock}</strong>`;

                            if (pauseSec > 0) {
                                const departureClock = addTime(arrivalClock, pauseSec);
                                html += `<br><span class="horaire-depart-etape" style="color:var(--vert); display:block; margin-top:5px; font-size:0.85em;">🚀 Repart : <strong>${departureClock}</strong></span>`;
                                currentClock = departureClock;
                            } else {
                                currentClock = arrivalClock;
                            }
                            loader.innerHTML = html;
                        }
                    }
                }
            });
        }
    } catch (e) {
        console.error("Erreur calcul temps:", e);
    }
}
/**
 * Adds seconds to a time string.
 * @function addTime
 * @param {string} startTime - Format "HH:mm"
 * @param {number} secondsToAdd - Seconds to add
 * @returns {string} New time string "HH:mm"
 */
function addTime(startTime, secondsToAdd) {
    if(!startTime) return "--:--";
    const parts = startTime.split(':').map(Number);
    const date = new Date();
    date.setHours(parts[0], parts[1], 0, 0);
    date.setSeconds(date.getSeconds() + Math.round(secondsToAdd));
    return date.getHours().toString().padStart(2, '0') + ":" +
        date.getMinutes().toString().padStart(2, '0');
}
/**
 * Converts a duration string (HH:mm:ss or HH:mm) to seconds.
 * @function durationToSeconds
 * @param {string} timeStr - Time string
 * @returns {number} Duration in seconds
 */
function durationToSeconds(timeStr) {
    if(!timeStr) return 0;
    const parts = timeStr.split(':').map(Number);
    let seconds = 0;
    if(parts.length >= 2) seconds += (parts[0] * 3600) + (parts[1] * 60);
    if(parts.length === 3) seconds += parts[2];
    return seconds;
}
/**
 * Converts a duration string (HH:mm:ss or HH:mm) to seconds.
 * @function durationToSeconds
 * @param {string} timeStr - Time string
 * @returns {number} Duration in seconds
 */
function formatDuration(seconds) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    if (h > 0) return `${h}h ${m}min`;
    return `${m} min`;
}
/**
 * Markdown to HTML initialization.
 * Sanitizes and renders markdown content within elements with '.markdown-to-html' class.
 */
document.addEventListener("DOMContentLoaded", function() {
    marked.setOptions({
        breaks: true,
        gfm: true
    });

    document.querySelectorAll('.markdown-to-html').forEach(function(el) {
        const markdownText = el.getAttribute('data-markdown');

        if (markdownText) {
            const rawHtml = marked.parse(markdownText);

            const cleanHtml = DOMPurify.sanitize(rawHtml);

            el.innerHTML = cleanHtml;
        } else {
            el.innerHTML = '';
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-trip-toggle').forEach(function(header) {

        header.addEventListener('click', function() {
            const tripId = this.getAttribute('data-trip-id');
            window.toggleTrajet(tripId);
        });

    });
});
