<?php
require_once '../config/database.php';
require_once '../app/models/User.php';

class AuthController {
    private $user;

    public function __construct($pdo) {
        $this->user = new User($pdo);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($this->user->register($username, $email, $password)) {
                header("Location: login.php");
                exit();
            } else {
                echo "Registration failed.";
            }
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            $user = $this->user->login($username, $password);
            if ($user) {
                session_start(); 
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['logged_in'] = true;
                $_SESSION['role'] = $user['role']; 
    
                if (isset($_POST['remember_me'])) {
                    setcookie('username', $username, time() + (86400 * 30), "/"); 
                }
    
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Invalid credentials.";
            }
        }
    }
    

    public function logout() {
        session_start();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}
?>
