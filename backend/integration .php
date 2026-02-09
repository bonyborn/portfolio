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
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            error_log("Connection error: " . $e->getMessage());
        }
        return $this->conn;
    }
}

// models/Registration.php
class Registration {
    private $conn;
    private $table = "mentoring_registrations";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
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
            if($stmt->execute()) {
                return ["success" => true, "message" => "Registration successful!"];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}

// Example usage in registration handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $registration = new Registration($db);
    
    $data = [
        'program_id' => 1,
        'full_name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'experience_level' => $_POST['experience'],
        'focus_area' => $_POST['focus'],
        'learning_goals' => $_POST['goals'],
        'preferred_schedule' => $_POST['schedule'],
        'agreed_to_terms' => isset($_POST['terms']) ? 1 : 0,
        'subscribe_newsletter' => isset($_POST['newsletter']) ? 1 : 0
    ];
    
    $result = $registration->create($data);
    echo json_encode($result);
}
?>