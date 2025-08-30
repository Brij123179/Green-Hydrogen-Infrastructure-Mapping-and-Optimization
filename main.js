// Main application logic for the Green Hydrogen GIS Tool

// Global map instance
let greenH2Map;

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Mapbox map
    mapboxgl.accessToken = 'pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4M29iazA2Z2gycXA4N2pmbDZmangifQ.-g_vE53SD2WrJ6tFX7QHmA';
    greenH2Map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/light-v10',
        center: [-95.7129, 37.0902],
        zoom: 4
    });
    
    // Store map instance globally
    window.greenH2Map = greenH2Map;
    
    // Add navigation controls
    greenH2Map.addControl(new mapboxgl.NavigationControl(), 'top-right');
    
    // Load layers when map is ready
    greenH2Map.on('load', function() {
        initLayers(greenH2Map);
        loadAssets();
        
        // Set up event listeners
        document.getElementById('run-analysis').addEventListener('click', function() {
            document.getElementById('analysis-params').classList.toggle('hidden');
        });
        
        document.getElementById('start-analysis').addEventListener('click', runAnalysis);
        
        // Map controls
        document.getElementById('zoom-in').addEventListener('click', () => greenH2Map.zoomIn());
        document.getElementById('zoom-out').addEventListener('click', () => greenH2Map.zoomOut());
        document.getElementById('reset-view').addEventListener('click', () => {
            greenH2Map.flyTo({
                center: [-95.7129, 37.0902],
                zoom: 4,
                essential: true
            });
        });
        
        // Current location button
        document.getElementById('current-location').addEventListener('click', getCurrentLocation);
    });
});

// Load assets from server
async function loadAssets() {
    try {
        // In a real application, we would fetch from the API
        // For this demo, we'll use sample data
        const sampleData = {
            plants: [
                { id: 1, name: "GreenH2 Valley", capacity_mw: 50, status: "planned", lat: 34.0522, lng: -118.2437 },
                { id: 2, name: "Sunshine Electrolyzer", capacity_mw: 25.5, status: "under_construction", lat: 33.9806, lng: -117.3755 },
                { id: 3, name: "Coastal Hydrogen Facility", capacity_mw: 100, status: "operational", lat: 32.7157, lng: -117.1611 }
            ],
            renewables: [
                { id: 1, type: "solar", capacity_mw: 150, lat: 35.1420, lng: -118.4550 },
                { id: 2, type: "wind", capacity_mw: 75.5, lat: 34.5128, lng: -117.3233 },
                { id: 3, type: "solar", capacity_mw: 45.25, lat: 33.7922, lng: -116.5410 },
                { id: 4, type: "wind", capacity_mw: 120, lat: 32.9346, lng: -115.5408 },
                { id: 5, type: "hydro", capacity_mw: 80, lat: 37.7749, lng: -122.4194 }
            ]
        };
        
        // Add assets to map
        addAssetsToMap(sampleData.plants, 'plants');
        addAssetsToMap(sampleData.renewables, 'renewables');
        
        // Add sample pipeline data
        const pipelineData = {
            type: 'FeatureCollection',
            features: [
                {
                    type: 'Feature',
                    geometry: {
                        type: 'LineString',
                        coordinates: [
                            [-118.2437, 34.0522],
                            [-117.3755, 33.9806],
                            [-116.5410, 33.7922]
                        ]
                    },
                    properties: {
                        name: 'Southern California H2 Pipeline'
                    }
                }
            ]
        };
        
        addGeoJSONToMap(pipelineData, 'pipelines', '#9b59b6');
        
    } catch (error) {
        console.error('Error loading assets:', error);
        showNotification('Error loading map data', 'error');
    }
}

// Get user's current location
function getCurrentLocation() {
    if (!navigator.geolocation) {
        showNotification('Geolocation is not supported by your browser', 'error');
        return;
    }
    
    showNotification('Getting your location...', 'info');
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const { latitude, longitude } = position.coords;
            
            // Fly to location
            greenH2Map.flyTo({
                center: [longitude, latitude],
                zoom: 10,
                essential: true
            });
            
            // Add a marker
            new mapboxgl.Marker({ color: '#3498db' })
                .setLngLat([longitude, latitude])
                .setPopup(new mapboxgl.Popup().setHTML(`
                    <div class="asset-popup">
                        <h3>Your Location</h3>
                        <p>Latitude: ${latitude.toFixed(4)}</p>
                        <p>Longitude: ${longitude.toFixed(4)}</p>
                    </div>
                `))
                .addTo(greenH2Map);
                
            showNotification('Location found', 'success');
        },
        (error) => {
            console.error('Geolocation error:', error);
            showNotification('Unable to get your location', 'error');
        },
        {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        }
    );
}