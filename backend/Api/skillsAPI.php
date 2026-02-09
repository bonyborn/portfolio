<?php
// backend/api/skills.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT * FROM skills ORDER BY display_order ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $skills = $stmt->fetchAll();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $skills
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching skills'
    ]);
}
?>