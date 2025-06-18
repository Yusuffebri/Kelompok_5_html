<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transportasi - Travel Lovindra</title>
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
        }

        .filter-container {
            display: flex;
            gap: 20px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        .filter-group select,
        .filter-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-width: 200px;
        }

        .filter-button {
            background-color: #2a5298;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 24px;
        }

        .filter-button:hover {
            background-color: #1e3c72;
        }

        .catalog-container {
            padding: 50px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .vehicle-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 350px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .vehicle-card:hover {
            transform: translateY(-10px);
        }

        .vehicle-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .vehicle-card .content {
            padding: 20px;
        }

        .vehicle-card h3 {
            color: #2a5298;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .vehicle-card .specs {
            margin-bottom: 15px;
        }

        .vehicle-card .spec {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            color: #555;
        }

        .vehicle-card .spec img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .vehicle-card .price {
            color: #2a5298;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 15px;
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
                <a href="katalog.php"class="active">Vehicle</a>
                <a href="destinasi.php">Destination</a>
                <a href="katalog_sopir.php">Driver</a>
                <a href="riwayat-transaksi.php">Transaction History</a>
                <a href="katalogadmin.html">Admin</a>
                <a href="index.php">Logout</a>
            </div>
        </div>
    </nav>

    <header class="catalog-header">
        <h1>Transportasi</h1>
        <p>Temukan kendaraan yang sesuai dengan kebutuhan perjalanan Anda</p>
    </header>

    <section class="catalog-container" id="catalog-container">
        <!-- Kendaraan akan ditampilkan di sini dari database dan localStorage -->
        <?php
        // --- Bagian Koneksi Database PHP ---
        
        $servername = "localhost"; // Biasanya 'localhost' untuk XAMPP
        $username = "root";        // Username default XAMPP
        $password = "";            // Password default XAMPP (kosong)
        $dbname = "travel_lovindra"; // Nama database yang sudah Anda buat

        // Buat koneksi ke database
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Periksa koneksi
        if ($conn->connect_error) {
            die("Koneksi database gagal: " . $conn->connect_error);
        }

        // --- Bagian Pengambilan Data dari Database ---

        // Query SQL untuk mengambil semua data kendaraan dari tabel 'katalog'
        $sql = "SELECT id, nama, gambar, kapasitas, ac, bagasi, harga FROM katalog";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Jika ada data, tampilkan dalam bentuk vehicle-card
            while($row = $result->fetch_assoc()) {
                echo '<div class="vehicle-card">';
                echo '    <img src="' . htmlspecialchars($row["gambar"]) . '" alt="' . htmlspecialchars($row["nama"]) . '">';
                echo '    <div class="content">';
                echo '        <h3>' . htmlspecialchars($row["nama"]) . '</h3>';
                echo '        <div class="specs">';
                echo '            <div class="spec">';
                echo '                Kapasitas: ' . htmlspecialchars($row["kapasitas"]);
                echo '            </div>';
                echo '            <div class="spec">';
                echo '                AC: ' . htmlspecialchars($row["ac"]);
                echo '            </div>';
                echo '            <div class="spec">';
                echo '                Bagasi: ' . htmlspecialchars($row["bagasi"]);
                echo '            </div>';
                echo '        </div>';
                // Format harga dengan titik sebagai pemisah ribuan
                echo '        <div class="price">Rp ' . number_format($row["harga"], 0, ',', '.') . '/hari</div>';
                
                // Tombol "Pesan Sekarang" dengan data-attributes untuk JavaScript
                echo '        <button class="book-button" ';
                echo '                data-vehicle-name="' . htmlspecialchars($row["nama"]) . '" ';
                echo '                data-vehicle-price="Rp ' . number_format($row["harga"], 0, ',', '.') . '/hari" '; 
                echo '                data-vehicle-image="' . htmlspecialchars($row["gambar"]) . '">';
                echo '            Pesan Sekarang';
                echo '        </button>';
                echo '    </div>';
                echo '</div>';
            }
        } else {
            // Jika tidak ada data di database, tampilkan kendaraan default
            ?>
            <div class="vehicle-card">
                <img src="image/toyota avanza.jpeg" alt="Toyota Avanza">
                <div class="content">
                    <h3>Toyota Avanza</h3>
                    <div class="specs">
                        <div class="spec">
                            Kapasitas: 7 Orang
                        </div>
                        <div class="spec">
                            AC: Aktif
                        </div>
                        <div class="spec">
                            Bagasi: Cukup untuk 3-4 koper
                        </div>
                    </div>
                    <div class="price">Rp 500.000/hari</div>
                    <button class="book-button" 
                            data-vehicle-name="Toyota Avanza" 
                            data-vehicle-price="Rp 500.000/hari" 
                            data-vehicle-image="image/toyota avanza.jpeg">
                        Pesan Sekarang
                    </button>
                </div>
            </div>

            <div class="vehicle-card">
                <img src="image/toyota Hiace.jpeg" alt="Toyota Hiace">
                <div class="content">
                    <h3>Toyota Hiace</h3>
                    <div class="specs">
                        <div class="spec">
                            Kapasitas: 15 Orang
                        </div>
                        <div class="spec">
                            AC: Aktif
                        </div>
                        <div class="spec">
                            Bagasi: Luas untuk banyak koper
                        </div>
                    </div>
                    <div class="price">Rp 1.200.000/hari</div>
                    <button class="book-button" 
                            data-vehicle-name="Toyota Hiace" 
                            data-vehicle-price="Rp 1.200.000/hari" 
                            data-vehicle-image="image/toyota Hiace.jpeg">
                        Pesan Sekarang
                    </button>
                </div>
            </div>

            <div class="vehicle-card">
                <img src="image/Toyota Alphard Hybrid.jpeg" alt="Toyota Alphard">
                <div class="content">
                    <h3>Toyota Alphard</h3>
                    <div class="specs">
                        <div class="spec">
                            Kapasitas: 7 Orang
                        </div>
                        <div class="spec">
                            AC: Aktif
                        </div>
                        <div class="spec">
                            Bagasi: Cukup untuk 4 koper
                        </div>
                    </div>
                    <div class="price">Rp 2.000.000/hari</div>
                    <button class="book-button" 
                            data-vehicle-name="Toyota Alphard" 
                            data-vehicle-price="Rp 2.000.000/hari" 
                            data-vehicle-image="image/Toyota Alphard Hybrid.jpeg">
                        Pesan Sekarang
                    </button>
                </div>
            </div>

            <div class="vehicle-card">
                <img src="image/isuzu.jpeg" alt="Isuzu Elf">
                <div class="content">
                    <h3>Isuzu Elf</h3>
                    <div class="specs">
                        <div class="spec">
                            Kapasitas: 20 Orang
                        </div>
                        <div class="spec">
                            AC: Aktif
                        </div>
                        <div class="spec">
                            Bagasi: Luas untuk banyak koper
                        </div>
                    </div>
                    <div class="price">Rp 1.500.000/hari</div>
                    <button class="book-button" 
                            data-vehicle-name="Isuzu Elf" 
                            data-vehicle-price="Rp 1.500.000/hari" 
                            data-vehicle-image="image/isuzu.jpeg">
                        Pesan Sekarang
                    </button>
                </div>
            </div>

            <div class="vehicle-card">
                <img src="image/brio.jpeg" alt="Honda Brio">
                <div class="content">
                    <h3>Honda Brio</h3>
                    <div class="specs">
                        <div class="spec">
                            Kapasitas: 4 Orang
                        </div>
                        <div class="spec">
                            AC: Aktif
                        </div>
                        <div class="spec">
                            Bagasi: Cukup untuk 2 koper
                        </div>
                    </div>
                    <div class="price">Rp 350.000/hari</div>
                    <button class="book-button" 
                            data-vehicle-name="Honda Brio" 
                            data-vehicle-price="Rp 350.000/hari" 
                            data-vehicle-image="image/brio.jpeg">
                        Pesan Sekarang
                    </button>
                </div>
            </div>

            <div class="vehicle-card">
                <img src="image/Bus.jpeg" alt="Bus Pariwisata">
                <div class="content">
                    <h3>Bus Pariwisata</h3>
                    <div class="specs">
                        <div class="spec">
                            Kapasitas: 50 Orang
                        </div>
                        <div class="spec">
                            AC: Aktif
                        </div>
                        <div class="spec">
                            Bagasi: Bagasi besar di bawah bus
                        </div>
                    </div>
                    <div class="price">Rp 3.500.000/hari</div>
                    <button class="book-button" 
                            data-vehicle-name="Bus Pariwisata" 
                            data-vehicle-price="Rp 3.500.000/hari" 
                            data-vehicle-image="image/Bus.jpeg">
                        Pesan Sekarang
                    </button>
                </div>
            </div>
            <?php
        }

        // Tutup koneksi database
        $conn->close();
        ?>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Fungsi untuk mendapatkan data kendaraan dari localStorage
            function getVehiclesFromStorage() {
                const vehicles = localStorage.getItem('katalogKendaraan');
                return vehicles ? JSON.parse(vehicles) : [];
            }

            // Fungsi untuk menampilkan kendaraan dari localStorage (admin)
            function displayAdminVehicles() {
                const vehicles = getVehiclesFromStorage();
                const container = document.getElementById('catalog-container');

                // Tampilkan setiap kendaraan dari localStorage
                vehicles.forEach(vehicle => {
                    const card = document.createElement('div');
                    card.className = 'vehicle-card';

                    card.innerHTML = `
                        <img src="${vehicle.gambar}" alt="${vehicle.nama}">
                        <div class="content">
                            <h3>${vehicle.nama}</h3>
                            <div class="specs">
                                <div class="spec">
                                    Kapasitas: ${vehicle.kapasitas}
                                </div>
                                <div class="spec">
                                    AC: ${vehicle.ac}
                                </div>
                                <div class="spec">
                                    Bagasi: ${vehicle.bagasi}
                                </div>
                            </div>
                            <div class="price">${vehicle.harga}</div>
                            <button class="book-button" 
                                    data-vehicle-name="${vehicle.nama}" 
                                    data-vehicle-price="${vehicle.harga}" 
                                    data-vehicle-image="${vehicle.gambar}">
                                Pesan Sekarang
                            </button>
                        </div>
                    `;

                    container.appendChild(card);
                });
            }

            // Tampilkan kendaraan dari localStorage
            displayAdminVehicles();

            // Fungsi untuk setup event listener tombol pesan
            const setupBookButtons = function () {
                const bookButtons = document.querySelectorAll('.book-button');

                bookButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        // Ambil data dari data-attributes
                        const vehicleName = this.getAttribute('data-vehicle-name');
                        const vehiclePrice = this.getAttribute('data-vehicle-price');
                        const vehicleImage = this.getAttribute('data-vehicle-image');

                        // Simpan di localStorage
                        localStorage.setItem('selectedVehicle', vehicleName);
                        localStorage.setItem('vehiclePrice', vehiclePrice);
                        localStorage.setItem('vehicleImage', vehicleImage);

                        // Arahkan ke halaman pemesanan
                        window.location.href = 'pemesanan.php';
                    });
                });
            };

            // Setup event listener untuk semua tombol pesan
            setupBookButtons();
        });
    </script>
</body>

</html>