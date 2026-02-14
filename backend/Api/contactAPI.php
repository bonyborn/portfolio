<?php
// backend/api/contact.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

// Get posted JSON data
$data = json_decode(file_get_contents("php://input"), true);

// If JSON is empty, fallback to POST
if (empty($data)) {
    $data = $_POST;
}

// Trim inputs
$data = array_map('trim', $data);

// Validate required fields
$required = ['name', 'email', 'subject', 'message'];

foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Missing required field: $field",
            'data' => null
        ]);
        exit;
    }
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => "Invalid email address",
        'data' => null
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Insert message into DB
    $query = "INSERT INTO contact_messages (name, email, subject, message) 
              VALUES (:name, :email, :subject, :message)";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':name', htmlspecialchars(strip_tags($data['name'])));
    $stmt->bindValue(':email', htmlspecialchars(strip_tags($data['email'])));
    $stmt->bindValue(':subject', htmlspecialchars(strip_tags($data['subject'])));
    $stmt->bindValue(':message', htmlspecialchars(strip_tags($data['message'])));

    if ($stmt->execute()) {
        $message_id = $db->lastInsertId();

        // Optional: send email notification
        $to = "alex@morgan.dev";
        $subject = "New Contact Form Submission: " . $data['subject'];
        $message_body = "Name: " . $data['name'] . "\n";
        $message_body .= "Email: " . $data['email'] . "\n";
        $message_body .= "Message:\n" . $data['message'] . "\n";
        $headers = "From: noreply@yourdomain.com\r\n";
        $headers .= "Reply-To: " . $data['email'] . "\r\n";

        // mail($to, $subject, $message_body, $headers); // Enable when ready

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => "Message sent successfully! We'll get back to you soon.",
            'data' => [
                'id' => $message_id,
                'name' => $data['name'],
                'email' => $data['email']
            ]
        ]);
        exit;
    }

} catch (PDOException $e) {
    // Log error for debugging (do not expose in production)
    error_log("Contact API Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "Failed to send message. Please try again later.",
        'data' => null
    ]);
    exit;
}
?>
