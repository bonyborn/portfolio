<?php
// backend/api/register.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"), true);

// If no JSON data, check POST data
if (empty($data)) {
    $data = $_POST;
}

$response = [];

// Validate required fields
$required = ['full_name', 'email', 'experience_level', 'focus_area', 'preferred_schedule'];
$conn->query("INSERT INTO test_table (name) VALUES ('test')");
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Missing required field: $field"
        ]);
        exit;
    }
}

if (!isset($data['agreed_to_terms']) || !$data['agreed_to_terms']) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'You must agree to the terms and conditions'
    ]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    // Use the stored procedure
    $query = "CALL RegisterForMentoring(
        :program_id, :full_name, :email, :phone, 
        :experience_level, :focus_area, :learning_goals, 
        :preferred_schedule, :agreed_to_terms, :subscribe_newsletter
    )";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    $stmt->bindValue(':program_id', 1); // Default program
    $stmt->bindValue(':full_name', htmlspecialchars(strip_tags($data['full_name'])));
    $stmt->bindValue(':email', htmlspecialchars(strip_tags($data['email'])));
    $stmt->bindValue(':phone', isset($data['phone']) ? htmlspecialchars(strip_tags($data['phone'])) : null);
    $stmt->bindValue(':experience_level', $data['experience_level']);
    $stmt->bindValue(':focus_area', $data['focus_area']);
    $stmt->bindValue(':learning_goals', isset($data['learning_goals']) ? htmlspecialchars(strip_tags($data['learning_goals'])) : null);
    $stmt->bindValue(':preferred_schedule', $data['preferred_schedule']);
    $stmt->bindValue(':agreed_to_terms', 1);
    $stmt->bindValue(':subscribe_newsletter', isset($data['subscribe_newsletter']) ? 1 : 0);
    
    if ($stmt->execute()) {
        http_response_code(201);
        $response = [
            'success' => true,
            'message' => 'Registration successful! We will contact you within 48 hours.',
            'data' => [
                'email' => $data['email'],
                'reference_id' => $db->lastInsertId()
            ]
        ];
    }
    
} catch(PDOException $e) {
    // Check if it's a duplicate email error
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        http_response_code(409);
        $response = [
            'success' => false,
            'message' => 'This email is already registered. Please use a different email or contact us.'
        ];
    } else {
        http_response_code(500);
        $response = [
            'success' => false,
            'message' => 'Registration failed. Please try again later.',
            'error' => $e->getMessage()
        ];
    }
}

echo json_encode($response);
?>