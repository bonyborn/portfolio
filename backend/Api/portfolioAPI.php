<?php
// backend/api/portfolio.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT * FROM portfolio_projects WHERE featured = 1 ORDER BY display_order ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $projects = $stmt->fetchAll();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $projects,
        'count' => count($projects)
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching portfolio projects',
        'error' => $e->getMessage()
    ]);
}
?>