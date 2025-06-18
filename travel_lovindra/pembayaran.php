<?php
session_start();

// Cek apakah ada booking_id di session
if (!isset($_SESSION['booking_id']) || !isset($_SESSION['total_payment'])) {
    header("Location: pemesanan.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_lovindra";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil detail pemesanan dari database
$booking_id = $_SESSION['booking_id'];
$sql = "SELECT * FROM pemesanan WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    header("Location: pemesanan.php");
    exit();
}

// Data harga untuk perhitungan ulang
$package_prices = [
    "Paket Wisata Bali" => 5000000,
    "Paket Wisata Yogyakarta" => 3000000,
    "Paket Wisata Malang" => 2500000,
    "Paket Wisata Solo" => 2300000,
    "Paket Wisata Bandung" => 2800000
];

$vehicle_prices = [
    "Toyota Avanza" => 450000,
    "Toyota Hiace" => 900000,
    "Toyota Alphard" => 1500000,
    "Bus Pariwisata" => 2000000,
    "Isuzu Elf" => 800000,
    "Honda Brio" => 350000
];

// Hitung detail pembayaran
$package_price_per_person = $package_prices[$booking['destination_type']];
$vehicle_price_per_day = $vehicle_prices[$booking['vehicle_type']];
$package_total = $package_price_per_person * $booking['participant_count'];
$vehicle_total = $vehicle_price_per_day * $booking['duration'];

// Proses pembayaran jika form disubmit
$message = "";
$error = "";

if ($_POST) {
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $payment_proof = "";
    
    // Handle file upload untuk bukti pembayaran
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $payment_proof = "payment_" . $booking_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $payment_proof;
        
        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_file)) {
            // Update status pembayaran
            $update_sql = "UPDATE pemesanan SET booking_status = 'paid', payment_method = ?, bukti_bayar = ?, payment_date = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssi", $payment_method, $bukti_bayar, $booking_id);
            
            if ($update_stmt->execute()) {
                $message = "Pembayaran berhasil diproses! Tim kami akan segera memverifikasi pembayaran Anda.";
                // Clear session
                unset($_SESSION['booking_id']);
                unset($_SESSION['total_payment']);
                echo "<script>setTimeout(function(){window.location.href='riwayat-transaksi.php';},3000);</script>";
            } else {
                $error = "Gagal memproses pembayaran: " . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            $error = "Gagal mengupload bukti pembayaran.";
        }
    } else {
        $error = "Silakan upload bukti pembayaran.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran - Travel Lovindra</title>
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
        
        .navbar {
            background-color: #2a5298;
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar .logo h1 {
            color: #fff;
        }
        
        .nav-links a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
        }
        
        .payment-container {
            width: 80%;
            margin: 20px auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .booking-details, .payment-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .booking-details h2, .payment-form h2 {
            color: #2a5298;
            margin-bottom: 20px;
            border-bottom: 2px solid #2a5298;
            padding-bottom: 10px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            font-weight: bold;
            color: #333;
        }
        
        .detail-value {
            color: #666;
        }
        
        .payment-breakdown {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #FF9800;
            border-top: 2px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .form-group select, .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        
        .bank-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .bank-info h4 {
            color: #2a5298;
            margin-bottom: 10px;
        }
        
        .bank-detail {
            margin-bottom: 8px;
        }
        
        .primary-button {
            width: 100%;
            padding: 12px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            background-color: #FF9800;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
        }
        
        .primary-button:hover {
            background-color: #f57c00;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        
        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        
        footer {
            text-align: center;
            padding: 10px;
            background: #2a5298;
            color: white;
            margin-top: 20px;
            grid-column: 1 / -1;
        }
        
        @media (max-width: 768px) {
            .payment-container {
                width: 95%;
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><h1>Travel Lovindra</h1></div>
        <div class="nav-links">
            <a href="menu-utama.php">Beranda</a>
            <a href="katalog.php">Transportasi</a>
            <a href="destinasi.php">Destinasi</a>
            <a href="katalog-sopir.php">Sopir</a>
            <a href="riwayat-transaksi.php">Riwayat Transaksi</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
    
    <div class="payment-container">
        <?php if($message): ?>
            <div class="alert alert-success" style="grid-column: 1 / -1;"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-error" style="grid-column: 1 / -1;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Detail Pemesanan -->
        <div class="booking-details">
            <h2>Detail Pemesanan</h2>
            
            <div class="detail-row">
                <span class="detail-label">ID Pemesanan:</span>
                <span class="detail-value">#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Nama Pemesan:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['customer_name']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['customer_email']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">No. Telepon:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['customer_phone']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Destinasi:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['destination_type']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Kendaraan:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['vehicle_type']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Sopir:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['driver_name']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Tanggal Berangkat:</span>
                <span class="detail-value"><?php echo date('d F Y', strtotime($booking['pickup_date'])); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Tanggal Kembali:</span>
                <span class="detail-value"><?php echo date('d F Y', strtotime($booking['return_date'])); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Durasi:</span>
                <span class="detail-value"><?php echo $booking['duration']; ?> hari</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Jumlah Peserta:</span>
                <span class="detail-value"><?php echo $booking['participant_count']; ?> orang</span>
            </div>
            
            <!-- Rincian Pembayaran -->
            <div class="payment-breakdown">
                <h4>Rincian Pembayaran</h4>
                
                <div class="breakdown-row">
                    <span>Paket Wisata:</span>
                    <span>Rp <?php echo number_format($package_price_per_person, 0, ',', '.'); ?> × <?php echo $booking['participant_count']; ?></span>
                </div>
                
                <div class="breakdown-row">
                    <span>Subtotal Paket:</span>
                    <span>Rp <?php echo number_format($package_total, 0, ',', '.'); ?></span>
                </div>
                
                <div class="breakdown-row">
                    <span>Sewa Kendaraan:</span>
                    <span>Rp <?php echo number_format($vehicle_price_per_day, 0, ',', '.'); ?> × <?php echo $booking['duration']; ?> hari</span>
                </div>
                
                <div class="breakdown-row">
                    <span>Subtotal Kendaraan:</span>
                    <span>Rp <?php echo number_format($vehicle_total, 0, ',', '.'); ?></span>
                </div>
                
                <div class="breakdown-row total-amount">
                    <span>Total Pembayaran:</span>
                    <span>Rp <?php echo number_format($booking['total_payment'], 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Form Pembayaran -->
        <div class="payment-form">
            <h2>Pembayaran</h2>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="payment_method">Metode Pembayaran:</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">-- Pilih Metode Pembayaran --</option>
                        <option value="BCA">Transfer Bank BCA</option>
                        <option value="BRI">Transfer Bank BRI</option>
                        <option value="BNI">Transfer Bank BNI</option>
                        <option value="Mandiri">Transfer Bank Mandiri</option>
                        <option value="OVO">OVO</option>
                        <option value="GoPay">GoPay</option>
                        <option value="DANA">DANA</option>
                    </select>
                </div>
                
                <div class="bank-info" id="bank-info" style="display: none;">
                    <!-- Info rekening akan ditampilkan dengan JavaScript -->
                </div>
                
                <div class="form-group">
                    <label for="payment_proof">Bukti Pembayaran:</label>
                    <input type="file" id="payment_proof" name="payment_proof" accept="image/*,.pdf" required>
                    <small style="color: #666;">Upload foto/screenshot bukti transfer (JPG, PNG, PDF)</small>
                </div>
                
                <button type="submit" class="primary-button">Konfirmasi Pembayaran</button>
            </form>
        </div>
        
        <footer>
            <p>&copy; 2025 Travel Lovindra. Semua hak dilindungi.</p>
        </footer>
    </div>
    
    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            const bankInfo = document.getElementById('bank-info');
            const selectedMethod = this.value;
            
            const bankDetails = {
                'BCA': {
                    name: 'Bank BCA',
                    account: '1234567890',
                    holder: 'PT Travel Lovindra'
                },
                'BRI': {
                    name: 'Bank BRI',
                    account: '0987654321',
                    holder: 'PT Travel Lovindra'
                },
                'BNI': {
                    name: 'Bank BNI',
                    account: '1122334455',
                    holder: 'PT Travel Lovindra'
                },
                'Mandiri': {
                    name: 'Bank Mandiri',
                    account: '9988776655',
                    holder: 'PT Travel Lovindra'
                },
                'OVO': {
                    name: 'OVO',
                    account: '081234567890',
                    holder: 'Travel Lovindra'
                },
                'GoPay': {
                    name: 'GoPay',
                    account: '081234567890',
                    holder: 'Travel Lovindra'
                },
                'DANA': {
                    name: 'DANA',
                    account: '081234567890',
                    holder: 'Travel Lovindra'
                }
            };
            
            if (selectedMethod && bankDetails[selectedMethod]) {
                const detail = bankDetails[selectedMethod];
                bankInfo.style.display = 'block';
                bankInfo.innerHTML = `
                    <h4>Informasi Transfer ${detail.name}</h4>
                    <div class="bank-detail"><strong>Nomor Rekening/HP:</strong> ${detail.account}</div>
                    <div class="bank-detail"><strong>Atas Nama:</strong> ${detail.holder}</div>
                    <div class="bank-detail"><strong>Jumlah Transfer:</strong> Rp <?php echo number_format($booking['total_payment'], 0, ',', '.'); ?></div>
                `;
            } else {
                bankInfo.style.display = 'none';
            }
        });
    </script>
</body>
</html>