<?php
session_start(); // Mulai sesi PHP untuk menyimpan pesan feedback

require_once 'koneksi.php'; // Sertakan file koneksi database Anda

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $full_name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];

    // Validasi input server-side
    if (empty($full_name) || empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Kata sandi dan konfirmasi kata sandi tidak cocok.";
    } elseif (strlen($password) < 6) { // Contoh: minimal 6 karakter
        $error_message = "Kata sandi minimal 6 karakter.";
    } else {
        // Gunakan MySQLi prepared statements secara konsisten
        try {
            // Cek apakah username sudah ada
            $stmt_check_username = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
            $stmt_check_username->bind_param("s", $username); // 's' for string
            $stmt_check_username->execute();
            $result_check_username = $stmt_check_username->get_result(); // Ambil hasilnya
            if ($result_check_username->num_rows > 0) { // Periksa jumlah baris
                $error_message = "Username ini sudah terdaftar. Silakan pilih username lain.";
            } else {
                // Cek apakah email sudah ada
                $stmt_check_email = $koneksi->prepare("SELECT id FROM users WHERE email = ?");
                $stmt_check_email->bind_param("s", $email); // 's' for string
                $stmt_check_email->execute();
                $result_check_email = $stmt_check_email->get_result(); // Ambil hasilnya
                if ($result_check_email->num_rows > 0) { // Periksa jumlah baris
                    $error_message = "Email ini sudah terdaftar. Silakan gunakan email lain atau masuk.";
                } else {
                    // Hash kata sandi sebelum disimpan ke database (SANGAT PENTING!)
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Siapkan query untuk memasukkan data user
                    $stmt_insert = $koneksi->prepare("INSERT INTO users (full_name, email, username, password_hash) VALUES (?, ?, ?, ?)");
                    
                    // 'ssss' menunjukkan semua parameter adalah string
                    $stmt_insert->bind_param("ssss", $full_name, $email, $username, $hashed_password);

                    if ($stmt_insert->execute()) {
                        $_SESSION['success_message'] = "Pendaftaran berhasil! Silakan masuk dengan akun baru Anda.";
                        header("Location: index.php"); // Arahkan ke halaman login (index.php)
                        exit();
                    } else {
                        $error_message = "Terjadi kesalahan saat pendaftaran. Silakan coba lagi.";
                    }
                }
            }
            // Tutup statement
            $stmt_check_username->close();
            $stmt_check_email->close();
            if (isset($stmt_insert)) { // Hanya tutup jika sudah dibuat
                $stmt_insert->close();
            }
        } catch (Exception $e) { // Tangkap Exception umum untuk error MySQLi
            $error_message = "Terjadi kesalahan database: " . $e->getMessage();
            // Dalam lingkungan produksi, sebaiknya log error ini alih-alih menampilkannya langsung ke pengguna.
        }
    }
    // Jika ada error, simpan pesan error ke session untuk ditampilkan di register.php
    if (!empty($error_message)) {
        $_SESSION['error_message'] = $error_message;
        // Penting: Arahkan ke register.php, bukan register.html, agar pesan error bisa ditampilkan
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Travel Lovindra</title>
    <style>
        /* CSS Anda di sini, sama seperti di index.php */
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
        
        .register-container {
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
        
        .login-link {
            margin-top: 20px;
            color: #555;
        }
        
        .login-link a {
            color: #2a5298;
            text-decoration: none;
        }
        
        .login-link a:hover {
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
        .success-message {
            color: green;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1>Daftar Akun Baru</h1>
            <img src="image/Logo.jpeg" alt="Mobil Travel" class="car-image">
        </div>
        
        <?php
        // Tampilkan pesan sukses atau error dari session
        if (isset($_SESSION['success_message'])) {
            echo '<p class="success-message">' . $_SESSION['success_message'] . '</p>';
            unset($_SESSION['success_message']); // Hapus pesan dari session setelah ditampilkan
        }
        if (isset($_SESSION['error_message'])) {
            echo '<p class="error-message">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']); // Hapus pesan dari session setelah ditampilkan
        }
        ?>

        <form method="POST" action="register.php"> 
            <div class="input-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($full_name ?? '') ?>">
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required value="<?= htmlspecialchars($username ?? '') ?>">
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="input-group">
                <label for="confirmPassword">Konfirmasi Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>
            
            <button type="submit">Daftar</button>
        </form>
        
        <p class="login-link">Sudah punya akun? <a href="index.php">Login di sini</a></p>
    </div>
</body>
</html>