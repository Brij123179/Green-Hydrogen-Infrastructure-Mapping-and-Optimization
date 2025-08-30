// Map layer management for the Green Hydrogen GIS Tool

// Layer configuration
const layerConfig = {
    plants: {
        id: 'plants-layer',
        type: 'circle',
        source: 'plants-source',
        paint: {
            'circle-radius': 10,
            'circle-color': '#e74c3c',
            'circle-stroke-width': 2,
            'circle-stroke-color': '#ffffff'
        }
    },
    solar: {
        id: 'solar-layer',
        type: 'circle',
        source: 'solar-source',
        paint: {
            'circle-radius': 8,
            'circle-color': '#f1c40f',
            'circle-stroke-width': 2,
            'circle-stroke-color': '#ffffff'
        }
    },
    wind: {
        id: 'wind-layer',
        type: 'circle',
        source: 'wind-source',
        paint: {
            'circle-radius': 8,
            'circle-color': '#3498db',
            'circle-stroke-width': 2,
            'circle-stroke-color': '#ffffff'
        }
    },
    hydro: {
        id: 'hydro-layer',
        type: 'circle',
        source: 'hydro-source',
        paint: {
            'circle-radius': 8,
            'circle-color': '#2980b9',
            'circle-stroke-width': 2,
            'circle-stroke-color': '#ffffff'
        }
    }
};

// Initialize layers on the map
function initLayers(map) {
    // Add sources and layers for each asset type
    addAssetSource(map, 'plants');
    addAssetSource(map, 'solar');
    addAssetSource(map, 'wind');
    addAssetSource(map, 'hydro');
    
    // Set up layer toggle event listeners
    setupLayerToggles(map);
}

// Add data source for a specific asset type
function addAssetSource(map, assetType) {
    map.addSource(`${assetType}-source`, {
        type: 'geojson',
        data: {
            type: 'FeatureCollection',
            features: []
        }
    });
    
    // Add layer if configuration exists
    if (layerConfig[assetType]) {
        map.addLayer(layerConfig[assetType]);
    }
}

// Set up layer visibility toggles
function setupLayerToggles(map) {
    const layerCheckboxes = {
        'layer-plants': 'plants-layer',
        'layer-solar': 'solar-layer',
        'layer-wind': 'wind-layer',
        'layer-hydro': 'hydro-layer',
        'layer-pipelines': 'pipelines-layer',
        'layer-storage': 'storage-layer'
    };
    
    Object.keys(layerCheckboxes).forEach(checkboxId => {
        const checkbox = document.getElementById(checkboxId);
        if (checkbox) {
            const layerId = layerCheckboxes[checkboxId];
            
            // Set initial visibility
            if (map.getLayer(layerId)) {
                map.setLayoutProperty(layerId, 'visibility', checkbox.checked ? 'visible' : 'none');
            }
            
            // Add change listener
            checkbox.addEventListener('change', (e) => {
                if (map.getLayer(layerId)) {
                    map.setLayoutProperty(layerId, 'visibility', e.target.checked ? 'visible' : 'none');
                }
            });
        }
    });
}

// Add assets to the map
function addAssetsToMap(assets, assetType) {
    const map = window.greenH2Map;
    if (!map || !map.getSource(`${assetType}-source`)) return;
    
    // Convert assets to GeoJSON features
    const features = assets.map(asset => {
        let properties = { ...asset };
        
        // Add different properties based on asset type
        if (assetType === 'plants') {
            properties.type = 'plant';
            properties.description = `
                <div class="asset-popup">
                    <h3>${asset.name}</h3>
                    <p>Capacity: <span class="capacity">${asset.capacity_mw} MW</span></p>
                    <p>Status: <span class="status ${asset.status}">${asset.status.replace('_', ' ')}</span></p>
                </div>
            `;
        } else if (assetType === 'renewables') {
            properties.type = asset.type;
            properties.description = `
                <div class="asset-popup">
                    <h3>${asset.type.charAt(0).toUpperCase() + asset.type.slice(1)} Source</h3>
                    <p>Capacity: <span class="capacity">${asset.capacity_mw} MW</span></p>
                </div>
            `;
        }
        
        return {
            type: 'Feature',
            geometry: {
                type: 'Point',
                coordinates: [parseFloat(asset.lng), parseFloat(asset.lat)]
            },
            properties: properties
        };
    });
    
    // Update the source data
    map.getSource(`${assetType}-source`).setData({
        type: 'FeatureCollection',
        features: features
    });
    
    // Add click events for popups
    if (map.getLayer(`${assetType}-layer`)) {
        map.on('click', `${assetType}-layer`, (e) => {
            const description = e.features[0].properties.description;
            new mapboxgl.Popup()
                .setLngLat(e.lngLat)
                .setHTML(description)
                .addTo(map);
        });
        
        // Change cursor on hover
        map.on('mouseenter', `${assetType}-layer`, () => {
            map.getCanvas().style.cursor = 'pointer';
        });
        
        map.on('mouseleave', `${assetType}-layer`, () => {
            map.getCanvas().style.cursor = '';
        });
    }
}

// Add GeoJSON data to map (for pipelines, storage, etc.)
function addGeoJSONToMap(geojsonData, layerId, color) {
    const map = window.greenH2Map;
    if (!map) return;
    
    const sourceId = `${layerId}-source`;
    
    // Add or update source
    if (map.getSource(sourceId)) {
        map.getSource(sourceId).setData(geojsonData);
    } else {
        map.addSource(sourceId, {
            type: 'geojson',
            data: geojsonData
        });
        
        map.addLayer({
            id: layerId,
            type: 'line',
            source: sourceId,
            paint: {
                'line-color': color,
                'line-width': 3
            }
        });
    }
}

// Export functions for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initLayers,
        addAssetsToMap,
        addGeoJSONToMap,
        setupLayerToggles
    };
}