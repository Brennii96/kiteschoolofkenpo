let googleMapsApiPromise = null;

function loadGoogleMapsApi(apiKey) {
    if (googleMapsApiPromise) {
        return googleMapsApiPromise;
    }

    googleMapsApiPromise = new Promise((resolve, reject) => {
        if (typeof window.google !== 'undefined' && typeof window.google.maps !== 'undefined') {
            resolve(window.google.maps);
            return;
        }

        if (!window.googleMapsApiLoadedCallback) {
            window.googleMapsApiLoadedCallback = () => {
                if (window.google && window.google.maps) {
                    resolve(window.google.maps);
                } else {
                    reject(new Error("Google Maps API loaded but 'google.maps' not found."));
                }
                delete window.googleMapsApiLoadedCallback;
            };
        } else {
            // console.warn("Google Maps API callback already defined. Waiting for existing load process.");
        }

        const existingScript = document.querySelector('script[src*="maps.googleapis.com"]');
        if (existingScript) {
            // console.log("Google Maps API script tag already exists. Waiting for it to load.");
            return;
        }

        const script = document.createElement('script');
        const libraries = ['maps', 'marker'];
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&v=beta&libraries=${libraries.join(',')}&callback=googleMapsApiLoadedCallback`;
        script.async = true;
        script.defer = true;
        script.onerror = (error) => {
            console.error("Google Maps API script failed to load.", error);
            googleMapsApiPromise = null; // Reset promise on failure
            reject(new Error("Google Maps API script failed to load."));
        };
        document.head.appendChild(script);
    });

    return googleMapsApiPromise;
}


async function initializeMap(mapElement) {
    const apiKey = mapElement.dataset.apiKey;
    const lat = parseFloat(mapElement.dataset.lat);
    const lng = parseFloat(mapElement.dataset.lng);
    const title = mapElement.dataset.title;
    const mapId = mapElement.id;

    if (!apiKey || isNaN(lat) || isNaN(lng)) {
        mapElement.innerHTML = `<p class='p-4 text-center text-red-600'>Error: Map configuration missing.</p>`;
        return;
    }

    try {
        const googleMaps = await loadGoogleMapsApi(apiKey);
        const position = {lat: lat, lng: lng};
        const {Map} = await google.maps.importLibrary("maps");
        const {AdvancedMarkerElement} = await googleMaps.importLibrary("marker");

        mapElement.innerHTML = '';

        const map = new Map(mapElement, {
            center: position,
            zoom: 16,
            mapId: mapId || 'DEMO_MAP_ID',
        });

        const marker = new AdvancedMarkerElement({
            map: map,
            position: position,
            title: title,
        });
    } catch (error) {
        console.error(`Map: Failed to initialize.`, error);
        mapElement.innerHTML = `<p class='p-4 text-center text-red-600'>Error: Map could not be loaded</p>`;
    }
}

export function initializeGoogleMapsOnPage() {
    const mapElements = document.querySelectorAll('[data-google-map]');
    if (mapElements.length > 0) {
        mapElements.forEach(initializeMap);
    } else {
        console.log("No Google Map elements found on this page.");
    }
}
