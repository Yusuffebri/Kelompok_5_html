<?php
session_start(); // Pastikan session dimulai di sini juga jika halaman ini memerlukan data session
// Koneksi ke database
$host = "localhost";
$user = "root"; // Ganti sesuai konfigurasi MySQL kamu
$password = "";
$dbname = "travel_lovindra";

// Buat koneksi MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data fitur
$stmt_fitur = $conn->prepare("SELECT * FROM fitur");
$stmt_fitur->execute();
$fitur = $stmt_fitur->get_result();

// Ambil data destinasi
$stmt_destinasi = $conn->prepare("SELECT * FROM destinasi_populer");
$stmt_destinasi->execute();
$destinasi = $stmt_destinasi->get_result();

// Ambil data ulasan
$stmt_ulasan = $conn->prepare("SELECT * FROM ulasan ORDER BY tanggal DESC");
$stmt_ulasan->execute();
$ulasan = $stmt_ulasan->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Utama - Travel Lovindra</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            line-height: 1.6;
        }
        
        /* ===== NAVBAR STYLES ===== */
        .navbar {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            padding: 15px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            color: white;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .logo img {
            width: 45px;
            height: 45px;
            margin-right: 15px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .nav-links a.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        /* ===== HEADER STYLES ===== */
        .header {
            background: linear-gradient(rgba(42, 82, 152, 0.8), rgba(30, 60, 114, 0.8)), 
                        url('image/header-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 0 20px;
            margin-top: 80px;
        }
        
        .header h1 {
            font-size: 56px;
            margin-bottom: 24px;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 1s ease-out;
        }
        
        .header p {
            font-size: 22px;
            max-width: 800px;
            margin-bottom: 40px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        .cta-button {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(238, 90, 36, 0.3);
            animation: fadeInUp 1s ease-out 0.6s both;
        }
        
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(238, 90, 36, 0.4);
        }
        
        /* ===== SECTION STYLES ===== */
        .section {
            padding: 100px 0;
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 20px;
            padding-right: 20px;
        }
        
        .section-title {
            text-align: center;
            font-size: 42px;
            color: #2a5298;
            margin-bottom: 20px;
            font-weight: 700;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border-radius: 2px;
        }
        
        .section-subtitle {
            text-align: center;
            font-size: 18px;
            color: #666;
            margin-bottom: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* ===== FEATURES SECTION ===== */
        .features {
            background-color: white;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .feature-card img {
            width: 80px;
            height: 80px;
            margin-bottom: 25px;
            object-fit: contain;
        }
        
        .feature-card h3 {
            color: #2a5298;
            margin-bottom: 15px;
            font-size: 22px;
            font-weight: 600;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.8;
            font-size: 16px;
        }
        
        /* ===== DESTINATIONS SECTION ===== */
        .destinations {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }
        
        .destination-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .destination-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }
        
        .destination-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .destination-card:hover img {
            transform: scale(1.1);
        }
        
        .destination-content {
            padding: 30px;
        }
        
        .destination-card h3 {
            color: #2a5298;
            margin-bottom: 15px;
            font-size: 24px;
            font-weight: 600;
        }
        
        .destination-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .destination-price {
            color: #2a5298;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 20px;
        }
        
        .book-button {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .book-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(42, 82, 152, 0.3);
        }
        
        /* ===== REVIEWS SECTION ===== */
        .reviews {
            background: white;
        }
        
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }
        
        .review-card {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.15);
        }
        
        .reviewer-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .reviewer-info img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .reviewer-details h4 {
            font-weight: 600;
            color: #2a5298;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .reviewer-date {
            color: #888;
            font-size: 14px;
        }
        
        .stars {
            color: #ffc107;
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        .review-destination {
            color: #2a5298;
            font-style: italic;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .review-text {
            color: #555;
            line-height: 1.8;
            font-size: 16px;
        }

        /* New button styling for "Tambahkan Ulasan Anda" */
        .add-review-button-container {
            text-align: center;
            margin-top: 60px; /* Adjust spacing as needed */
        }

        .add-review-button {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(42, 82, 152, 0.3);
        }

        .add-review-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(42, 82, 152, 0.4);
        }
        
        /* ===== FOOTER STYLES ===== */
        .footer {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            padding: 60px 0 30px;
            text-align: center;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            .navbar-container {
                padding: 0 15px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
            }
            
            .nav-links a {
                padding: 8px 15px;
                font-size: 14px;
            }
            
            .logo {
                font-size: 24px;
            }
            
            .logo img {
                width: 35px;
                height: 35px;
            }
            
            .header {
                height: 60vh;
                margin-top: 90px;
            }
            
            .header h1 {
                font-size: 36px;
            }
            
            .header p {
                font-size: 18px;
            }
            
            .section-title {
                font-size: 32px;
            }
            
            .features-grid,
            .destinations-grid,
            .reviews-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
        
        @media (max-width: 480px) {
            .navbar-container {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .nav-links {
                width: 100%;
                justify-content: center;
            }
            
            .header {
                height: 50vh;
                margin-top: 120px;
            }
            
            .section {
                padding: 60px 0;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            .header p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <img src="image/Logo.jpeg" alt="">
                Travel Lovindra
            </div>
            <div class="nav-links">
                <a href="menu_utama.php"class="active">Home</a>
                <a href="katalog.php">Vehicle</a>
                <a href="destinasi.php">Destination</a>
                <a href="katalog_sopir.php">Driver</a>
                <a href="riwayat-transaksi.php">Transaction History</a>
                <a href="katalogadmin.html">Admin</a>
                <a href="index.php">Logout</a>
            </div>
        </div>
    </nav>

    <header class="header">
        <h1>Jelajahi Indonesia Bersama Lovindra</h1>
        <p>Nikmati perjalanan nyaman dengan armada kendaraan kami yang berkualitas dan berpengalaman</p>
        <a href="tentang kami.html" class="cta-button">Tentang Kami</a>
    </header>

    <section class="section features">
        <h2 class="section-title">Keunggulan Kami</h2>
        <p class="section-subtitle">Mengapa memilih Travel Lovindra untuk perjalanan Anda?</p>
        <div class="features-grid">
            <?php while ($f = $fitur->fetch_assoc()): ?>
                <div class="feature-card">
                    <img src="<?= htmlspecialchars($f['gambar']) ?>" alt="<?= htmlspecialchars($f['judul']) ?>">
                    <h3><?= htmlspecialchars($f['judul']) ?></h3>
                    <p><?= htmlspecialchars($f['deskripsi']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="section destinations">
        <h2 class="section-title">Destinasi Populer</h2>
        <p class="section-subtitle">Temukan destinasi impian Anda dengan paket perjalanan terbaik</p>
        <div class="destinations-grid">
            <?php while ($d = $destinasi->fetch_assoc()): ?>
                <div class="destination-card">
                    <img src="<?= htmlspecialchars($d['gambar']) ?>" alt="<?= htmlspecialchars($d['nama']) ?>">
                    <div class="destination-content">
                        <h3><?= htmlspecialchars($d['nama']) ?></h3>
                        <p><?= htmlspecialchars($d['deskripsi']) ?></p>
                        <div class="destination-price">Mulai dari Rp <?= number_format($d['harga'], 0, ',', '.') ?></div>
                        <a href="<?= htmlspecialchars($d['url_detail']) ?>" class="book-button">Lihat Detail</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="section reviews">
        <h2 class="section-title">Ulasan Pengguna</h2>
        <p class="section-subtitle">Apa kata pelanggan kami tentang layanan Travel Lovindra</p>
        <div class="reviews-grid">
            <?php while ($u = $ulasan->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="reviewer-info">
                        <img src="<?= htmlspecialchars($u['foto_profil']) ?>" alt="<?= htmlspecialchars($u['nama_pengulas']) ?>">
                        <div class="reviewer-details">
                            <h4><?= htmlspecialchars($u['nama_pengulas']) ?></h4>
                            <div class="reviewer-date"><?= date("d M Y", strtotime($u['tanggal'])) ?></div>
                        </div>
                    </div>
                    <div class="stars"><?= str_repeat("★", $u['rating']) . str_repeat("☆", 5 - $u['rating']) ?></div>
                    <div class="review-destination"><?= htmlspecialchars($u['destinasi']) ?></div>
                    <div class="review-text"><?= htmlspecialchars($u['ulasan']) ?></div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="add-review-button-container">
            <a href="tambah_ulasan.php" class="add-review-button">Tambahkan Ulasan Anda</a>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2025 Travel Lovindra. Hak Cipta Dilindungi.</p>
            <p>Jelajahi Indonesia dengan nyaman dan aman bersama kami</p>
        </div>
    </footer>
</body>
</html>

<?php
// Tutup statement dan koneksi database setelah selesai digunakan
$stmt_fitur->close();
$stmt_destinasi->close();
$stmt_ulasan->close();
$conn->close();
?>