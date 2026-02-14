<?php
// backend/api/skills.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Fetch all skills ordered by display_order
    $query = "SELECT id, skill_name, proficiency_level, skill_type, display_order, created_at 
              FROM skills 
              ORDER BY display_order ASC";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Skills fetched successfully.',
        'count' => count($skills),
        'data' => $skills
    ]);

} catch(PDOException $e) {
    // Log the error for debugging
    error_log("Skills API Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching skills. Please try again later.',
        'data' => null
    ]);
}
?>
