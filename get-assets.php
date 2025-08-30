<?php
require_once '../lib/Database.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $type = $_GET['type'] ?? 'all';
    
    switch($type) {
        case 'plants':
            $plants = $db->fetchAll("SELECT * FROM hydrogen_plants");
            echo json_encode($plants);
            break;
            
        case 'renewables':
            $renewables = $db->fetchAll("SELECT * FROM renewable_sources");
            echo json_encode($renewables);
            break;
            
        case 'infrastructure':
            $infrastructure = $db->fetchAll("SELECT * FROM infrastructure");
            echo json_encode($infrastructure);
            break;
            
        default:
            $result = [
                'plants' => $db->fetchAll("SELECT * FROM hydrogen_plants"),
                'renewables' => $db->fetchAll("SELECT * FROM renewable_sources"),
                'infrastructure' => $db->fetchAll("SELECT * FROM infrastructure")
            ];
            echo json_encode($result);
    }
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
require_once 'php/security-headers.php';
?>