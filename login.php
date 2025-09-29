<?php
session_start();
include 'database.php'; // db connection

if (isset($_POST['register'])) {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $salt = "any_random_salt_here";
    $password = sha1($salt . $_POST['password']);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please login.');</script>";
    } else {
        echo "<script>alert('Error: Could not register.');</script>";
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password_input = $_POST['password'];
    $salt = "any_random_salt_here"; // MUST be the same as registration
    $hashed_password = sha1($salt . $password_input);

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $hashed_password);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        echo "<script>alert('Login successful');</script>";
        // redirect to dashboard or homepage
        header('Location: dashboard.php'); exit;
    } else {
        echo "<script>alert('Invalid email or password');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login / Register</title>
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body background="assets/images/background.jpg">
  <div class="form-container">
    <div class="form-card">
      
      <!-- Login Form -->
      <form id="login-form" class="active" method="POST" action="">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
        <p class="toggle-text">
          Don't have an account? <span id="show-register">Register</span>
        </p>
      </form>

      <!-- Register Form -->
      <form id="register-form" method="POST" action="">
        <h2>Register</h2>
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" name="register">Register</button>
        <p class="toggle-text">
          Already have an account? <span id="show-login">Login</span>
        </p>
      </form>

    </div>
  </div>

  <script>
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const showRegister = document.getElementById('show-register');
    const showLogin = document.getElementById('show-login');

    showRegister.addEventListener('click', () => {
      loginForm.classList.remove('active');
      registerForm.classList.add('active');
    });

    showLogin.addEventListener('click', () => {
      registerForm.classList.remove('active');
      loginForm.classList.add('active');
    });
  </script>
</body>
</html>
