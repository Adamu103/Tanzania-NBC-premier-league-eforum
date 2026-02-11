<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];

            if ($user['role_id'] == 1) {
                header("Location: admin.php");
            } else {
                header("Location: home.php");
            }
            exit();

        } else {
            echo "Wrong password!";
        }

    } else {
        echo "User not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | NBC Premier League Forum</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">

        <div class="logo-wrapper">
            <img src="image.jpg" class="logo-main">
        </div>

        <h2>Welcome Back</h2>
        <p class="subtitle">Login to continue discussion</p>

        <form method="POST" action="">

            <div class="input-group">
                <span class="icon">👤</span>
                <input type="text" 
                       name="username"
                       id="loginUsername"
                       placeholder="Username"
                       required>
            </div>

            <div class="input-group">
                <span class="icon">🔒</span>
                <input type="password"
                       name="password"
                       id="loginPassword"
                       placeholder="Password"
                       required>
            </div>

            <button type="submit" class="primary-btn">Login</button>

        </form>

        <p class="switch-link">
            Don't have an account?
            <a href="register.php">Register</a>
        </p>

    </div>
</div>

<script src="auth.js"></script>
</body>
</html>

