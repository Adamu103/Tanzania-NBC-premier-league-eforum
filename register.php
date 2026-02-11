<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row["password"])) {

            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"] = $row["role"]; // muhimu sana

            // 🔥 Redirect based on role
            if ($row["role"] === "admin") {
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
    <title>Register | NBC Premier League Forum</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">

        <div class="logo-wrapper">
            <img src="image.jpg" class="logo-main">
        </div>

        <h2>Create Account</h2>
        <p class="subtitle">Join NBC Premier League Forum</p>

        <form method="POST" action="">

            <div class="input-group">
                <span class="icon">👤</span>
                <input type="text"
                       name="username"
                       id="regUsername"
                       placeholder="Username"
                       required>
            </div>

            <div class="input-group">
                <span class="icon">🔒</span>
                <input type="password"
                       name="password"
                       id="regPassword"
                       placeholder="Password"
                       required>
            </div>

            <div class="input-group">
                <span class="icon">✔</span>
                <input type="password"
                       name="confirm_password"
                       id="confirmPassword"
                       placeholder="Confirm Password"
                       required>
            </div>

            <button type="submit" class="primary-btn">Register</button>

        </form>

        <p class="switch-link">
            Already have an account?
            <a href="login.php">Login</a>
        </p>

    </div>
</div>

<script src="auth.js"></script>
</body>
</html>

