<?php
session_start(); // Start a PHP session to manage user login state and messages
require_once 'koneksi.php'; // Include your database connection file

$error_message = ""; // Variable to store login error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use MySQLi for prepared statements to prevent SQL injection
    try {
        // Prepare SQL statement to fetch user by username
        $stmt = $koneksi->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->bind_param("s", $username); // "s" for string type
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc(); // Fetch the user data

        if ($user) {
            // Verify the submitted password against the hashed password from the database
            if (password_verify($password, $user['password_hash'])) {
                // Password is correct, set session variables and redirect
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: menu_utama.php"); // Redirect to your main menu PHP page
                exit();
            } else {
                $error_message = "Username atau password salah!";
            }
        } else {
            $error_message = "Username atau password salah!";
        }
        $stmt->close(); // Close the statement
    } catch (Exception $e) { // Catch general Exception for MySQLi errors
        $error_message = "Terjadi kesalahan database: " . $e->getMessage();
        // In a production environment, you might log the error instead of displaying it.
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Travel Lovindra</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            width: 400px;
            padding: 40px;
            text-align: center;
        }
        
        .logo {
            margin-bottom: 20px;
        }
        
        h1 {
            color: #2a5298;
            margin-bottom: 30px;
        }
        
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input:focus {
            border-color: #2a5298;
            outline: none;
        }
        
        button {
            background-color: #2a5298;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #1e3c72;
        }
        
        .register-link {
            margin-top: 20px;
            color: #555;
        }
        
        .register-link a {
            color: #2a5298;
            text-decoration: none;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .car-image {
            width: 200px;
            margin: 0 auto 20px;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Travel Lovindra</h1>
            <img src="image/Logo.jpeg" alt="Mobil Travel" class="car-image">
        </div>
        
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php"> 
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <p class="register-link">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
    </div>

    <script>
        // document.getElementById('loginForm').addEventListener('submit', function(e) {
        //     e.preventDefault();
            
        //     const username = document.getElementById('username').value;
        //     const password = document.getElementById('password').value;
            
        //     // This client-side validation is now handled by PHP on the server
        //     if (username === 'admin' && password === 'admin123') {
        //         window.location.href = 'menu-utama.html';
        //     } else {
        //         alert('Username atau password salah!');
        //     }
        // });
    </script>
</body>
</html>