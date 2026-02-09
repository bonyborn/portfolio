<?php
// backend/api/contact.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data)) {
    $data = $_POST;
}

// Validate
if (empty($data['name']) || empty($data['email']) || empty($data['subject']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required'
    ]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    $query = "INSERT INTO contact_messages (name, email, subject, message) 
              VALUES (:name, :email, :subject, :message)";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindValue(':name', htmlspecialchars(strip_tags($data['name'])));
    $stmt->bindValue(':email', htmlspecialchars(strip_tags($data['email'])));
    $stmt->bindValue(':subject', htmlspecialchars(strip_tags($data['subject'])));
    $stmt->bindValue(':message', htmlspecialchars(strip_tags($data['message'])));
    
    if ($stmt->execute()) {
        // Send email notification (optional)
        $to = "alex@morgan.dev";
        $subject = "New Contact Form Submission: " . $data['subject'];
        $message = "Name: " . $data['name'] . "\n";
        $message .= "Email: " . $data['email'] . "\n";
        $message .= "Message:\n" . $data['message'] . "\n";
        $headers = "From: " . $data['email'] . "\r\n";
        
        // Uncomment to enable email
        // mail($to, $subject, $message, $headers);
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully! We\'ll get back to you soon.'
        ]);
    }
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message. Please try again.'
    ]);
}
?>