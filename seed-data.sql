-- Create database
CREATE DATABASE IF NOT EXISTS green_hydrogen_gis;
USE green_hydrogen_gis;

-- Table for hydrogen plants
CREATE TABLE IF NOT EXISTS hydrogen_plants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    capacity_mw DECIMAL(10,2) NOT NULL,
    status ENUM('operational', 'planned', 'under_construction') DEFAULT 'planned',
    lat DECIMAL(10,8) NOT NULL,
    lng DECIMAL(11,8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX location_index (lat, lng)  -- Changed from SPATIAL INDEX to regular INDEX
);

-- Table for renewable energy sources
CREATE TABLE IF NOT EXISTS renewable_sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('solar', 'wind', 'hydro') NOT NULL,
    capacity_mw DECIMAL(10,2) NOT NULL,
    lat DECIMAL(10,8) NOT NULL,
    lng DECIMAL(11,8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX location_index (lat, lng)  -- Changed from SPATIAL INDEX to regular INDEX
);

-- Table for infrastructure (pipelines, storage)
CREATE TABLE IF NOT EXISTS infrastructure (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('pipeline', 'storage') NOT NULL,
    name VARCHAR(255) NOT NULL,
    capacity DECIMAL(10,2),
    geojson_path TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for analysis results
CREATE TABLE IF NOT EXISTS analysis_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parameters TEXT NOT NULL,
    result_path TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO hydrogen_plants (name, capacity_mw, status, lat, lng) VALUES
('GreenH2 Valley', 50.00, 'planned', 34.052235, -118.243683),
('Sunshine Electrolyzer', 25.50, 'under_construction', 33.980600, -117.375490),
('Coastal Hydrogen Facility', 100.00, 'operational', 32.715736, -117.161087);

INSERT INTO renewable_sources (type, capacity_mw, lat, lng) VALUES
('solar', 150.00, 35.142000, -118.455000),
('wind', 75.50, 34.512800, -117.323300),
('solar', 45.25, 33.792200, -116.541000),
('wind', 120.00, 32.934600, -115.540800),
('hydro', 80.00, 37.774900, -122.419400);

INSERT INTO infrastructure (type, name, capacity, geojson_path) VALUES
('pipeline', 'Southern California H2 Pipeline', 500.00, '/data/geojson/pipeline_sca.json'),
('storage', 'Los Angeles Hydrogen Storage', 250.00, '/data/geojson/storage_la.json');