<?php
// Koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_lovindra";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data sopir
$sql = "SELECT * FROM katalog_sopir ORDER BY rating DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Sopir - Travel Lovindra</title>
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

        .catalog-header {
            background-color: #2a5298;
            color: white;
            padding: 50px;
            text-align: center;
        }

        .catalog-header h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .catalog-header p {
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto;
        }

        .filter-section {
            background-color: white;
            padding: 20px 50px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-button {
            background-color: white;
            color: #2a5298;
            border: 1px solid #2a5298;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .filter-button:hover,
        .filter-button.active {
            background-color: #2a5298;
            color: white;
        }

        .catalog-container {
            padding: 50px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .driver-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 350px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .driver-card:hover {
            transform: translateY(-10px);
        }

        .driver-card img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            object-position: center top;
        }

        .driver-card .content {
            padding: 20px;
        }

        .driver-card h3 {
            color: #2a5298;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .driver-card .specs {
            margin-bottom: 15px;
        }

        .driver-card .spec {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            color: #555;
        }

        .driver-rating {
            color: #ffc107;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .driver-specialties {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 15px;
        }

        .specialty-tag {
            display: inline-block;
            background-color: #e6f0ff;
            color: #2a5298;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
        }

        .book-button {
            background-color: #2a5298;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            font-size: 16px;
        }

        .book-button:hover {
            background-color: #1e3c72;
        }

        .livechat-icon {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
        }

        .livechat-icon img {
            width: 30px;
            height: 30px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 30px 0;
        }

        .pagination button {
            padding: 10px 15px;
            border: 1px solid #2a5298;
            background-color: white;
            color: #2a5298;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .pagination button.active,
        .pagination button:hover {
            background-color: #2a5298;
            color: white;
        }

        footer {
            background-color: #2a5298;
            color: white;
            padding: 50px;
            text-align: center;
        }

        footer p {
            margin-top: 20px;
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
                <a href="destinasi.php">Destination</a>
                <a href="katalog_sopir.php"class="active">Driver</a>
                <a href="riwayat-transaksi.php">Transaction History</a>
                <a href="katalogadmin.html">Admin</a>
                <a href="index.php">Logout</a>
            </div>
        </div>
    </nav>

    <header class="catalog-header">
        <h1>Katalog Sopir Profesional</h1>
        <p>Kenyamanan perjalanan Anda didukung oleh sopir-sopir berpengalaman dan berkualitas</p>
    </header>

    <section class="filter-section">
        <button class="filter-button active" data-filter="all">Semua</button>
        <button class="filter-button" data-filter="solo">Solo</button>
        <button class="filter-button" data-filter="malang">Malang</button>
        <button class="filter-button" data-filter="bali">Bali</button>
        <button class="filter-button" data-filter="yogyakarta">Yogyakarta</button>
        <button class="filter-button" data-filter="bandung">Bandung</button>
        <button class="filter-button" data-filter="5star">Rating 5 Bintang</button>
    </section>

    <section class="catalog-container">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Menentukan bintang rating
                // Menentukan bintang rating
$bintang_isi = min(5, floor($row['rating'])); // Maksimal 5 bintang
$rating_stars = str_repeat('★', $bintang_isi);

// Tambah setengah bintang kalau perlu
if (($row['rating'] - floor($row['rating']) >= 0.5) && $bintang_isi < 5) {
    $rating_stars .= '☆';
    $bintang_total = $bintang_isi + 1;
} else {
    $bintang_total = $bintang_isi;
}

// Tambah bintang kosong
$bintang_kosong = max(0, 5 - $bintang_total);
$rating_stars .= str_repeat('☆', $bintang_kosong);
                
                // Menentukan data kategori untuk filter
                $categories = strtolower($row['kota']);
                if ($row['rating'] >= 4.9) {
                    $categories .= ' 5star';
                }
                
                // Memecah spesialisasi menjadi array
                $specialties = explode(',', $row['spesialisasi']);
                
                echo '<div class="driver-card" data-categories="' . $categories . '">';
                echo '<img src="' . $row['foto'] . '" alt="' . $row['nama'] . '">';
                echo '<div class="content">';
                echo '<h3>' . $row['nama'] . '</h3>';
                echo '<div class="driver-rating">' . $rating_stars . ' (' . $row['rating'] . ')</div>';
                echo '<div class="specs">';
                echo '<div class="spec">Pengalaman: ' . $row['pengalaman'] . ' tahun</div>';
                echo '<div class="spec">Bahasa: ' . $row['bahasa'] . '</div>';
                echo '<div class="spec">Keahlian: ' . $row['keahlian'] . '</div>';
                echo '</div>';
                echo '<div class="driver-specialties">';
                foreach($specialties as $specialty) {
                    echo '<span class="specialty-tag">' . trim($specialty) . '</span>';
                }
                echo '</div>';
                echo '<button class="book-button" data-driver-id="' . $row['id'] . '">Pesan Sekarang</button>';
                echo '<a href="roomchat.html?driver=' . $row['nama'] . '" class="livechat-icon">';
                echo '<img src="image/livechat-icon.jpg" alt="Live Chat">';
                echo '</a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>Tidak ada sopir tersedia.</p>';
        }
        ?>
    </section>

    <footer>
        <div class="logo">
            <img src="image/Logo.jpeg" alt="Logo">
            Travel Lovindra
        </div>
        <p>&copy; 2025 Travel Lovindra. Hak Cipta Dilindungi.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Filter functionality
            const filterButtons = document.querySelectorAll('.filter-button');
            const driverCards = document.querySelectorAll('.driver-card');

            filterButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));

                    // Add active class to clicked button
                    this.classList.add('active');

                    // Get filter value
                    const filterValue = this.getAttribute('data-filter');

                    // Show/hide driver cards based on filter
                    driverCards.forEach(card => {
                        if (filterValue === 'all') {
                            card.style.display = 'block';
                        } else {
                            const categories = card.getAttribute('data-categories');
                            if (categories.includes(filterValue)) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        }
                    });
                });
            });

            // Book button functionality
            const bookButtons = document.querySelectorAll('.book-button');
            bookButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const driverCard = this.closest('.driver-card');
                    const driverName = driverCard.querySelector('h3').textContent;
                    const driverImage = driverCard.querySelector('img').src;
                    const driverRating = driverCard.querySelector('.driver-rating').textContent;
                    const driverExperience = driverCard.querySelector('.specs .spec:nth-child(1)').textContent.trim();
                    const driverId = this.getAttribute('data-driver-id');

                    // Save driver info to sessionStorage (menghindari localStorage)
                    sessionStorage.setItem('selectedDriver', driverName);
                    sessionStorage.setItem('driverImage', driverImage);
                    sessionStorage.setItem('driverRating', driverRating);
                    sessionStorage.setItem('driverExperience', driverExperience);
                    sessionStorage.setItem('driverId', driverId);

                    // Redirect to booking page
                    window.location.href = 'pemesanan.php';
                });
            });
        });
    </script>
</body>

</html>

<?php
$conn->close();
?>