<?php
class Geospatial {
    // Calculate distance between two points using Haversine formula
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        return $distance;
    }
    
    // Generate a bounding box around a point
    public static function getBoundingBox($lat, $lng, $radiusKm) {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        // Calculate lat/lng offsets
        $deltaLat = $radiusKm / $earthRadius * (180 / M_PI);
        $deltaLng = $radiusKm / ($earthRadius * cos(deg2rad($lat))) * (180 / M_PI);
        
        return [
            'minLat' => $lat - $deltaLat,
            'maxLat' => $lat + $deltaLat,
            'minLng' => $lng - $deltaLng,
            'maxLng' => $lng + $deltaLng
        ];
    }
    
    // Check if a point is within a polygon
    public static function pointInPolygon($point, $polygon) {
        $intersections = 0;
        $pointsCount = count($polygon);
        
        for ($i = 1; $i < $pointsCount; $i++) {
            $point1 = $polygon[$i - 1];
            $point2 = $polygon[$i];
            
            if ($point1['lng'] == $point2['lng'] &&
                $point1['lng'] == $point['lng'] &&
                $point['lat'] > min($point1['lat'], $point2['lat']) &&
                $point['lat'] <= max($point1['lat'], $point2['lat'])) {
                return true;
            }
            
            if ($point1['lng'] != $point2['lng']) {
                $m = ($point2['lat'] - $point1['lat']) / ($point2['lng'] - $point1['lng']);
                $b = $point1['lat'] - $m * $point1['lng'];
                $x = ($point['lat'] - $b) / $m;
                
                if ($x > $point['lng'] && $x >= min($point1['lng'], $point2['lng']) && $x <= max($point1['lng'], $point2['lng'])) {
                    $intersections++;
                }
            }
        }
        
        return ($intersections % 2) != 0;
    }
}
?>