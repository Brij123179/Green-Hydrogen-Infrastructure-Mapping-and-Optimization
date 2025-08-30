// Analysis engine for site suitability analysis

// Initialize analysis parameters
function initAnalysisEngine() {
    // Set up parameter sliders
    const sliders = {
        'param-solar': 'solar-value',
        'param-wind': 'wind-value',
        'param-water': 'water-value',
        'param-infrastructure': 'infrastructure-value'
    };
    
    Object.keys(sliders).forEach(sliderId => {
        const slider = document.getElementById(sliderId);
        const valueSpan = document.getElementById(sliders[sliderId]);
        
        if (slider && valueSpan) {
            // Set initial value
            valueSpan.textContent = slider.value;
            
            // Update value when slider changes
            slider.addEventListener('input', () => {
                valueSpan.textContent = slider.value;
            });
        }
    });
}

// Run suitability analysis
async function runAnalysis() {
    showNotification('Starting analysis...', 'info');
    
    // Get parameter values
    const params = {
        solar: parseInt(document.getElementById('param-solar').value),
        wind: parseInt(document.getElementById('param-wind').value),
        water: parseInt(document.getElementById('param-water').value),
        infrastructure: parseInt(document.getElementById('param-infrastructure').value)
    };
    
    try {
        // Call the analysis API
        const response = await apiCall('php/api/run-analysis.php', {
            method: 'POST',
            body: JSON.stringify(params)
        });
        
        // Display results
        displayAnalysisResults(response);
        showNotification('Analysis completed successfully', 'success');
    } catch (error) {
        console.error('Analysis failed:', error);
        showNotification('Analysis failed', 'error');
    }
}

// Display analysis results
function displayAnalysisResults(results) {
    const resultsContainer = document.getElementById('analysis-results');
    resultsContainer.innerHTML = '';
    
    if (!results || !results.suitableSites || results.suitableSites.length === 0) {
        resultsContainer.innerHTML = '<p class="placeholder">No suitable sites found with current parameters</p>';
        return;
    }
    
    // Create results header
    const header = document.createElement('h4');
    header.textContent = `Suitable Sites (${results.suitableSites.length} found)`;
    resultsContainer.appendChild(header);
    
    // Add each suitable site
    results.suitableSites.forEach((site, index) => {
        const card = document.createElement('div');
        card.className = 'result-card';
        
        card.innerHTML = `
            <h4>Site #${index + 1}</h4>
            <p>Score: ${site.score.toFixed(2)}/10</p>
            <p>Location: ${site.lat.toFixed(4)}, ${site.lng.toFixed(4)}</p>
            <p>Solar Potential: ${site.factors.solar}/10</p>
            <p>Wind Potential: ${site.factors.wind}/10</p>
            <p>Water Availability: ${site.factors.water}/10</p>
            <p>Infrastructure: ${site.factors.infrastructure}/10</p>
        `;
        
        // Add click event to zoom to location
        card.style.cursor = 'pointer';
        card.addEventListener('click', () => {
            window.greenH2Map.flyTo({
                center: [site.lng, site.lat],
                zoom: 10,
                essential: true
            });
            
            // Add a marker at the location
            new mapboxgl.Marker({ color: '#27ae60' })
                .setLngLat([site.lng, site.lat])
                .setPopup(new mapboxgl.Popup().setHTML(`
                    <div class="asset-popup">
                        <h3>Suitable Site #${index + 1}</h3>
                        <p>Score: ${site.score.toFixed(2)}/10</p>
                        <p>This location is suitable for a green hydrogen plant based on the selected criteria.</p>
                    </div>
                `))
                .addTo(window.greenH2Map)
                .togglePopup();
        });
        
        resultsContainer.appendChild(card);
    });
    
    // Add visualization if we have multiple results
    if (results.suitableSites.length > 1) {
        addResultsVisualization(results.suitableSites);
    }
}

// Add chart visualization of results
function addResultsVisualization(sites) {
    const canvas = document.createElement('canvas');
    canvas.id = 'results-chart';
    canvas.style.marginTop = '15px';
    canvas.style.maxHeight = '200px';
    
    document.getElementById('analysis-results').appendChild(canvas);
    
    // Prepare data for chart
    const labels = sites.map((site, index) => `Site ${index + 1}`);
    const scores = sites.map(site => site.score);
    
    // Create chart
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Suitability Score',
                data: scores,
                backgroundColor: 'rgba(39, 174, 96, 0.7)',
                borderColor: 'rgba(39, 174, 96, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10,
                    title: {
                        display: true,
                        text: 'Score'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Site'
                    }
                }
            }
        }
    });
}

// Initialize when document is loaded
document.addEventListener('DOMContentLoaded', initAnalysisEngine);

// Export functions for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initAnalysisEngine,
        runAnalysis,
        displayAnalysisResults
    };
}