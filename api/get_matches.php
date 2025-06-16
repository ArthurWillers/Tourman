<?php
header('Content-Type: application/json');
require_once '../private/config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = open_connection();
    
    $sql = "SELECT m.*, 
                   t1.name as team1_name, 
                   t2.name as team2_name, 
                   s.name as sport_name, 
                   l.name as location_name 
            FROM `match` m 
            JOIN team t1 ON m.team1_id = t1.id 
            JOIN team t2 ON m.team2_id = t2.id 
            JOIN sport s ON m.sport_id = s.id 
            JOIN location l ON m.location_id = l.id 
            WHERE m.status = 'in_progress'";
    
    $result = mysqli_query($conn, $sql);
    $matches = array();
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $matches[] = $row;
        }
        echo json_encode($matches);
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Database query failed"));
    }
    close_connection($conn);
}
?>
