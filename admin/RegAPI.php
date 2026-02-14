<?php
// backend/api/register.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../config/database.php';

$response = [];

/*
|--------------------------------------------------------------------------
| READ INPUT (JSON or FORM)
|--------------------------------------------------------------------------
*/
$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    $data = $_POST;
}

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/
$required = ['full_name', 'email', 'experience_level', 'focus_area', 'preferred_schedule'];

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

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address'
    ]);
    exit;
}

if (empty($data['agreed_to_terms'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'You must agree to the terms and conditions'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT VIA STORED PROCEDURE
|--------------------------------------------------------------------------
*/
try {

    $query = "CALL RegisterForMentoring(
        :program_id, :full_name, :email, :phone, 
        :experience_level, :focus_area, :learning_goals, 
        :preferred_schedule, :agreed_to_terms, :subscribe_newsletter
    )";

    $stmt = $db->prepare($query);

    $stmt->bindValue(':program_id', 1);
    $stmt->bindValue(':full_name', trim(strip_tags($data['full_name'])));
    $stmt->bindValue(':email', trim($data['email']));
    $stmt->bindValue(':phone', $data['phone'] ?? null);
    $stmt->bindValue(':experience_level', $data['experience_level']);
    $stmt->bindValue(':focus_area', $data['focus_area']);
    $stmt->bindValue(':learning_goals', $data['learning_goals'] ?? null);
    $stmt->bindValue(':preferred_schedule', $data['preferred_schedule']);
    $stmt->bindValue(':agreed_to_terms', 1);
    $stmt->bindValue(':subscribe_newsletter', !empty($data['subscribe_newsletter']) ? 1 : 0);

    $stmt->execute();

    http_response_code(201);

    $response = [
        'success' => true,
        'message' => 'Registration successful! We will contact you within 48 hours.',
        'data' => [
            'email' => $data['email']
        ]
    ];

} catch(PDOException $e) {

    // Duplicate email protection
    if ($e->getCode() == 23000) {
        http_response_code(409);
        $response = [
            'success' => false,
            'message' => 'This email is already registered.'
        ];
    } else {
        http_response_code(500);
        $response = [
            'success' => false,
            'message' => 'Registration failed. Please try again later.'
        ];
    }
}

/*
|--------------------------------------------------------------------------
| OUTPUT
|--------------------------------------------------------------------------
*/
echo json_encode($response);
exit;
?>
