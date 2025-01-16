
<?php
// /classes/User.php
class User {
    private $conn;
    private $table_name = 'users';

    public $id;
    public $email;
    public $name;
    public $password;
    public $login_code;
    public $phone;
    public $address;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET email = :email, name = :name, password = :password, login_code = :login_code, phone = :phone, address = :address, created_at = :created_at";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->login_code = htmlspecialchars(strip_tags($this->login_code));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));

        // Bind
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':name', $this->name);
        // Hash the password
        $hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':login_code', $this->login_code);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':created_at', $this->created_at);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Generate a unique 6-digit login code
    public function generateLoginCode() {
        return rand(100000, 999999);
    }

    // Verify login credentials
    public function verifyLogin($code, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE login_code = :code LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $code = htmlspecialchars(strip_tags($code));
        $stmt->bindParam(':code', $code);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                // Set user properties
                $this->id = $row['id'];
                $this->email = $row['email'];
                $this->name = $row['name'];
                $this->phone = $row['phone'];
                $this->address = $row['address'];
                $this->login_code = $row['login_code'];
                return true;
            }
        }

        return false;
    }
}
?>