<?php
// classes/EmailVerification.php
class EmailVerification {
    private $conn;
    private $table_name = 'email_verification';

    public $email;
    public $token;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generateToken() {
        return bin2hex(random_bytes(16)); // Generate a 32-character token
    }

    public function saveToken() {
        $query = "INSERT INTO " . $this->table_name . " SET email=:email, token=:token, created_at=:created_at";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->token = htmlspecialchars(strip_tags($this->token));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        
        // Debug: Output the current date and time
    echo "Current date and time: " . $this->created_at;

        // Bind values
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":token", $this->token);
        $stmt->bindParam(":created_at", $this->created_at);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function verifyToken($token) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE token = :token AND created_at >= NOW() - INTERVAL 3 MINUTE";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $token = htmlspecialchars(strip_tags($token));

        // Bind
        $stmt->bindParam(":token", $token);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>
