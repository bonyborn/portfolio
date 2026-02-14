<?php
// config/database.php
class Database {
    private $host = "localhost";
    private $db_name = "personal_profile_db";
    private $username = "profile_app";
    private $password = "SecurePassword123!";
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Connection error: " . $e->getMessage());
        }
        return $this->conn;
    }
}

// models/Registration.php
require_once '../config/security.php';

class Registration {
    private $conn;
    private $table = "mentoring_registrations";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        // Sanitize all inputs
        $data = Security::sanitize($data);

        // Validate required fields
        $required = ['program_id', 'full_name', 'email', 'experience_level', 'focus_area', 'preferred_schedule', 'agreed_to_terms'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ["success" => false, "message" => "Missing required field: $field"];
            }
        }

        // Validate email
        if (!Security::validateEmail($data['email'])) {
            return ["success" => false, "message" => "Invalid email format"];
        }

        $query = "CALL RegisterForMentoring(
            :program_id, :full_name, :email, :phone, 
            :experience_level, :focus_area, :learning_goals, 
            :preferred_schedule, :agreed_to_terms, :subscribe_newsletter
        )";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(":program_id", $data['program_id']);
        $stmt->bindParam(":full_name", $data['full_name']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":experience_level", $data['experience_level']);
        $stmt->bindParam(":focus_area", $data['focus_area']);
        $stmt->bindParam(":learning_goals", $data['learning_goals']);
        $stmt->bindParam(":preferred_schedule", $data['preferred_schedule']);
        $stmt->bindParam(":agreed_to_terms", $data['agreed_to_terms']);
        $stmt->bindParam(":subscribe_newsletter", $data['subscribe_newsletter']);

        try {
            if ($stmt->execute()) {
                return [
                    "success" => true,
                    "message" => "Registration successful!",
                    "reference_id" => $this->conn->lastInsertId()
                ];
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ["success" => false, "message" => "This email is already registered"];
            }
            Security::logEvent("Registration error: " . $e->getMessage());
            return ["success" => false, "message" => "Registration failed. Please try again later."];
        }
    }
}

// Usage example in registration handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    $database = new Database();
    $db = $database->getConnection();
    $registration = new Registration($db);

    $data = [
        'program_id' => 1,
        'full_name' => $_POST['name'] ?? null,
        'email' => $_POST['email'] ?? null,
        'phone' => $_POST['phone'] ?? null,
        'experience_level' => $_POST['experience'] ?? null,
        'focus_area' => $_POST['focus'] ?? null,
        'learning_goals' => $_POST['goals'] ?? null,
        'preferred_schedule' => $_POST['schedule'] ?? null,
        'agreed_to_terms' => isset($_POST['terms']) ? 1 : 0,
        'subscribe_newsletter' => isset($_POST['newsletter']) ? 1 : 0
    ];

    $result = $registration->create($data);
    echo json_encode($result);
}
?>
