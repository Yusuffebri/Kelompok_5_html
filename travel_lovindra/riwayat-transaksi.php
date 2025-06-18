<?php
session_start();

// Cek apakah user sudah login (sesuaikan dengan sistem login Anda)
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_lovindra";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Filter untuk pencarian dan status
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Query untuk mengambil riwayat transaksi
$sql = "SELECT * FROM pemesanan WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (customer_name LIKE ? OR customer_email LIKE ? OR destination_type LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if (!empty($status_filter)) {
    $sql .= " AND booking_status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Data harga untuk perhitungan (sama seperti di pembayaran.php)
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

// Fungsi untuk mendapatkan status badge
function getStatusBadge($status) {
    switch($status) {
        case 'pending':
            return '<span class="status-badge status-pending">Menunggu Pembayaran</span>';
        case 'paid':
            return '<span class="status-badge status-paid">Sudah Dibayar</span>';
        case 'confirmed':
            return '<span class="status-badge status-confirmed">Dikonfirmasi</span>';
        case 'cancelled':
            return '<span class="status-badge status-cancelled">Dibatalkan</span>';
        case 'completed':
            return '<span class="status-badge status-completed">Selesai</span>';
        default:
            return '<span class="status-badge status-pending">Tidak Diketahui</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Transaksi - Travel Lovindra</title>
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
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2a5298, #3f6bb3);
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        
        .header h1 {
            margin-bottom: 10px;
        }
        
        .filter-section {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .filter-form {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .filter-btn {
            padding: 8px 20px;
            background: #2a5298;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .filter-btn:hover {
            background: #1e3d72;
        }
        
        .reset-btn {
            padding: 8px 20px;
            background: #6c757d;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .reset-btn:hover {
            background: #5a6268;
        }
        
        .transactions-table {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #2a5298;
            color: #fff;
            padding: 15px 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #1e3d72;
        }
        
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .transaction-id {
            font-weight: bold;
            color: #2a5298;
        }
        
        .customer-info {
            line-height: 1.4;
        }
        
        .customer-name {
            font-weight: bold;
            color: #333;
        }
        
        .customer-email {
            color: #666;
            font-size: 12px;
        }
        
        .trip-info {
            line-height: 1.4;
        }
        
        .destination {
            font-weight: bold;
            color: #FF9800;
        }
        
        .vehicle-driver {
            color: #666;
            font-size: 12px;
        }
        
        .date-info {
            line-height: 1.4;
            font-size: 13px;
        }
        
        .booking-date {
            color: #333;
            font-weight: bold;
        }
        
        .travel-dates {
            color: #666;
        }
        
        .amount {
            font-weight: bold;
            color: #FF9800;
            text-align: right;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-paid {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #b3d4fc;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-completed {
            background: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }
        
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .btn {
            padding: 4px 8px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-detail {
            background: #17a2b8;
            color: #fff;
        }
        
        .btn-detail:hover {
            background: #138496;
        }
        
        .btn-cancel {
            background: #dc3545;
            color: #fff;
        }
        
        .btn-cancel:hover {
            background: #c82333;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-data img {
            width: 100px;
            opacity: 0.3;
            margin-bottom: 20px;
        }
        
        .pagination {
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            background: #2a5298;
            color: white;
            margin-top: 20px;
        }
        
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
                <a href="katalog_sopir.php">Driver</a>
                <a href="riwayat-transaksi.php"class="active">Transaction History</a>
                <a href="katalogadmin.html">Admin</a>
                <a href="index.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="header">
            <h1>Riwayat Transaksi</h1>
            <p>Kelola dan pantau semua transaksi pemesanan travel</p>
        </div>
        
        <div class="filter-section">
            <form class="filter-form" method="GET">
                <div class="filter-group">
                    <label for="search">Cari Transaksi:</label>
                    <input type="text" id="search" name="search" placeholder="Nama, email, atau destinasi..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Menunggu Pembayaran</option>
                        <option value="paid" <?php echo $status_filter == 'paid' ? 'selected' : ''; ?>>Sudah Dibayar</option>
                        <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>Dikonfirmasi</option>
                        <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Dibatalkan</option>
                        <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">Filter</button>
                <a href="riwayat-transaksi.php" class="reset-btn">Reset</a>
            </form>
        </div>
        
        <div class="transactions-table">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Customer</th>
                            <th>Detail Perjalanan</th>
                            <th>Tanggal</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="transaction-id">#<?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                
                                <td class="customer-info">
                                    <div class="customer-name"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                                    <div class="customer-email"><?php echo htmlspecialchars($row['customer_email']); ?></div>
                                    <div class="customer-email"><?php echo htmlspecialchars($row['customer_phone']); ?></div>
                                </td>
                                
                                <td class="trip-info">
                                    <div class="destination"><?php echo htmlspecialchars($row['destination_type']); ?></div>
                                    <div class="vehicle-driver"><?php echo htmlspecialchars($row['vehicle_type']); ?></div>
                                    <div class="vehicle-driver">Sopir: <?php echo htmlspecialchars($row['driver_name']); ?></div>
                                    <div class="vehicle-driver"><?php echo $row['participant_count']; ?> peserta, <?php echo $row['duration']; ?> hari</div>
                                </td>
                                
                                <td class="date-info">
                                    <div class="booking-date">Booking: <?php echo date('d/m/Y', strtotime($row['created_at'])); ?></div>
                                    <div class="travel-dates">
                                        <?php echo date('d/m/Y', strtotime($row['pickup_date'])); ?> - 
                                        <?php echo date('d/m/Y', strtotime($row['return_date'])); ?>
                                    </div>
                                    <?php if($row['payment_date']): ?>
                                        <div class="travel-dates">Bayar: <?php echo date('d/m/Y', strtotime($row['payment_date'])); ?></div>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="amount">Rp <?php echo number_format($row['total_payment'], 0, ',', '.'); ?></td>
                                
                                <td><?php echo getStatusBadge($row['booking_status']); ?></td>
                                
                                <td class="action-buttons">
                                    <a href="detail-transaksi.php?id=<?php echo $row['id']; ?>" class="btn btn-detail">Detail</a>
                                    <?php if($row['booking_status'] == 'pending'): ?>
                                        <a href="pembayaran.php?booking_id=<?php echo $row['id']; ?>" class="btn btn-detail">Bayar</a>
                                        <button onclick="cancelBooking(<?php echo $row['id']; ?>)" class="btn btn-cancel">Batal</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <h3>Tidak Ada Data Transaksi</h3>
                    <p>Belum ada transaksi yang ditemukan atau coba ubah filter pencarian.</p>
                    <a href="pemesanan.php" class="btn btn-detail" style="margin-top: 15px; display: inline-block; padding: 10px 20px;">Buat Pemesanan Baru</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2025 Travel Lovindra. Semua hak dilindungi.</p>
    </footer>
    
    <script>
        function cancelBooking(bookingId) {
            if (confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')) {
                fetch('cancel-booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'booking_id=' + bookingId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pemesanan berhasil dibatalkan');
                        location.reload();
                    } else {
                        alert('Gagal membatalkan pemesanan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat membatalkan pemesanan');
                });
            }
        }
        
        // Auto refresh setiap 30 detik untuk update status real-time
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>