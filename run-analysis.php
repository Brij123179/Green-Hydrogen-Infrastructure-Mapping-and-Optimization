<?php
require_once '../lib/Database.php';
require_once '../lib/Geospatial.php';
require_once '../lib/AnalysisModel.php';

header('Content-Type: application/json');

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }
    
    // Create analysis model and run analysis
    $analysis = new AnalysisModel();
    $results = $analysis->runSuitabilityAnalysis($input);
    
    echo json_encode($results);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
require_once 'php/security-headers.php';
?>