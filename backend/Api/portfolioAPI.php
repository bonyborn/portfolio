<?php
// backend/api/portfolio.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Fetch featured portfolio projects in order
    $query = "SELECT id, title, description, short_description, image_url, project_url, technologies, featured, display_order, created_at, updated_at 
              FROM portfolio_projects 
              WHERE featured = 1 
              ORDER BY display_order ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();

    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Featured portfolio projects fetched successfully.',
        'count' => count($projects),
        'data' => $projects
    ]);

} catch(PDOException $e) {
    // Log error for server debugging
    error_log("Portfolio API Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching portfolio projects. Please try again later.',
        'data' => null
    ]);
}
?>
