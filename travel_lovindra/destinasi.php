<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_lovindra";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fungsi untuk mengambil semua destinasi (gabungan dari kedua tabel)
function getAllDestinations($conn) {
    $destinations = [];
    
    // Ambil dari tabel destinasi utama
    $sql1 = "SELECT id, name as nama_destinasi, price_per_person as harga, image_path as gambar, detail_page_link as link_detail, 'main' as source FROM destinasi";
    $result1 = $conn->query($sql1);
    
    if ($result1->num_rows > 0) {
        while($row = $result1->fetch_assoc()) {
            // Format harga untuk destinasi utama
            $row['harga'] = 'Rp ' . number_format($row["harga"], 0, ',', '.') . '/Orang';
            $destinations[] = $row;
        }
    }
    
    // Ambil dari tabel admin_destinasi
    $sql2 = "SELECT ad.id, ad.nama_destinasi, ad.harga, ad.gambar, ad.link_detail, 'admin' as source 
             FROM admin_destinasi ad 
             WHERE NOT EXISTS (
                 SELECT 1 FROM destinasi d 
                 WHERE d.name = ad.nama_destinasi
             )";
    $result2 = $conn->query($sql2);
    
    if ($result2->num_rows > 0) {
        while($row = $result2->fetch_assoc()) {
            $destinations[] = $row;
        }
    }
    
    return $destinations;
}

$allDestinations = getAllDestinations($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Destinasi - Travel Lovindra</title>
    <link rel="icon" type="image/png" href="image/Logo.jpeg" sizes="16x16">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
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
            font-size: 24px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            text-decoration: none;
        }

        .logo img {
            width: 40px;
            height: 40px;
            margin-right: 12px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            margin-right: 12px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .logo-icon::before {
            content: '';
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            opacity: 0.9;
        }

        .nav-links {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            padding: 12px 24px;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .nav-links a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .nav-links a:hover::before {
            left: 100%;
        }

        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .nav-links a.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
        }

        .mobile-menu-toggle span {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 3px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .navbar::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        }

        .logo:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .logo:hover .logo-icon {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .destination-header {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            padding: 120px 50px 50px;
            text-align: center;
            margin-top: 80px;
        }

        .destination-header h1 {
            font-size: 42px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .destination-header p {
            font-size: 18px;
            opacity: 0.9;
        }

        .destination-container {
            padding: 50px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .destination-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            width: 320px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            position: relative;
        }

        .destination-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .destination-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .destination-card:hover img {
            transform: scale(1.05);
        }

        .destination-card .content {
            padding: 25px;
            text-align: center;
        }

        .destination-card .price {
            margin-bottom: 20px;
            color: #2a5298;
            font-weight: bold;
            font-size: 18px;
        }

        .destination-card h3 {
            color: #2a5298;
            margin-bottom: 15px;
            font-size: 22px;
            font-weight: 600;
        }

        .view-button {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .view-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 82, 152, 0.3);
        }

        /* Badge untuk menandai sumber data */
        .source-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .badge-main {
            background-color: #4CAF50;
        }

        .badge-admin {
            background-color: #FF9800;
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
                <a href="menu_utama.php">Home</a>
                <a href="katalog.php">Vehicle</a>
                <a href="destinasi.php"class="active">Destination</a>
                <a href="katalog_sopir.php">Driver</a>
                <a href="riwayat-transaksi.php">Transaction History</a>
                <a href="katalogadmin.html">Admin</a>
                <a href="index.php">Logout</a>
            </div>
        </div>
    </nav>

    <header class="destination-header">
        <h1>Destinasi Wisata</h1>
        <p>Temukan tempat-tempat menarik untuk perjalanan Anda</p>
    </header>

    <section class="destination-container">
        <?php
        if (count($allDestinations) > 0) {
            // Output data untuk setiap destinasi
            foreach($allDestinations as $destination) {
                echo '<div class="destination-card">';
                
                // Tambahkan badge untuk menandai sumber data
                if ($destination['source'] == 'main') {
                    echo '<div class="source-badge badge-main">Default</div>';
                } else {
                    echo '<div class="source-badge badge-admin">Admin</div>';
                }
                
                echo '<img src="' . $destination["gambar"] . '" alt="' . $destination["nama_destinasi"] . '">';
                echo '<div class="content">';
                echo '<h3>' . $destination["nama_destinasi"] . '</h3>';
                echo '<div class="price">' . $destination["harga"] . '</div>';
                echo '<a href="' . $destination["link_detail"] . '" class="view-button">Lihat Detail</a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<p style='text-align: center; padding: 50px; color: #666;'>Tidak ada destinasi ditemukan.</p>";
        }
        ?>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Setup event listener untuk tombol lihat detail
            const viewButtons = document.querySelectorAll('.view-button');

            viewButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    
                    // Ambil data destinasi dari parent card
                    const card = this.closest('.destination-card');
                    const destinationName = card.querySelector('h3').textContent;
                    const destinationPrice = card.querySelector('.price').textContent;
                    const destinationImage = card.querySelector('img').src;
                    const detailLink = this.getAttribute('href');

                    // Simpan di localStorage untuk digunakan di halaman detail
                    localStorage.setItem('selectedDestination', destinationName);
                    localStorage.setItem('destinationPrice', destinationPrice);
                    localStorage.setItem('destinationImage', destinationImage);

                    // Arahkan ke halaman detail
                    window.location.href = detailLink;
                });
            });

            // Mobile menu toggle
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            const navLinks = document.querySelector('.nav-links');

            if (mobileToggle && navLinks) {
                mobileToggle.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                    mobileToggle.classList.toggle('active');
                });
            }
        });
    </script>
</body>

</html>