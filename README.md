# Trips& Roads

## Link to the documentation
https://cbulle.github.io/trips-road/
There you can find the documentation of the functions and the installation guide.

## Project Description

RoadTrip Planner is an interactive web application designed to simplify the creation, customization, and sharing of travel itineraries. 

The primary goal of this project is to provide a seamless and responsive mapping interface coupled with powerful editing tools, all while adhering to strict digital accessibility standards. The application recently underwent a major architectural refactoring to ensure the Vanilla JavaScript codebase remains modular, maintainable, and highly performant.

## Key Features

* **Mapping and Dynamic Routing:** Map interface built on Leaflet. Features automatic distance and travel time calculations, and plots routes (car, bike, walking) using the OSRM API.
* **Geocoding and POI Search:** Intelligent city search powered by the Nominatim API and dynamic display of Points of Interest (restaurants, hotels, gas stations) using the Overpass API.
* **Advanced Itinerary Editor:** Step-by-step route creation with automatic timeline generation. Integrates a rich text editor (Markdown) featuring client-side image processing and compression via the Canvas API.
* **Accessibility (A11y):** Persistent user preference management utilizing local storage. Includes a dark theme, a visually impaired mode with high contrast, and specific corrective filters for color blindness (Protanopia, Deuteranopia, Tritanopia).
* **AI Assistant:** Automatic generation of trip ideas (titles, descriptions, step recommendations) via artificial intelligence integration.
* **Export Capabilities:** Asynchronous downloading of itineraries in GPX and PDF formats for offline navigation.

## Technical Architecture & Stack

The project relies on standard web technologies and open-source APIs, with a strong emphasis on frontend performance optimization.

* **Frontend:** HTML5, CSS3, JavaScript (ES6+ Vanilla).
* **Mapping:** Leaflet.js, Leaflet.markercluster.
* **Remote APIs:** OSRM (Open Source Routing Machine), Nominatim (OpenStreetMap), Overpass API.
* **Data Processing:** DOMPurify, Marked.js.

### JavaScript Architecture Principles

The client-side code has been structured according to strict design principles:
* **Separation of Concerns:** Strict isolation between the global application state, business logic (calculations, API calls), and DOM manipulation.
* **Namespacing:** Prevention of global scope collisions through specific, descriptive function naming conventions.
* **Asynchronous Programming:** Systematic use of `async/await` and `Promises` to handle network requests without blocking the UI rendering.

## Codebase Structure

Overview of the main JavaScript modules:

* `map.js`: The core of the roadtrip creation system. Manages the map state, OSRM router calls, segment construction, and the step editor.
* `viewRoadtrip.js`: Dedicated read-only module. Handles the display of multiple trip maps, marker clustering, and asynchronous recalculation of arrival times.
* `index.js`: Logic for the interactive map on the homepage (geolocation, dynamic POI filtering via Overpass).
* `accessibility.js`: Application logic and persistence for visual accessibility preferences.
* `publicRoad.js`: Silent download manager (using hidden iframes) for GPX and PDF exports.
