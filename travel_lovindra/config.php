<?php
// config.php - Konfigurasi koneksi database

$host = 'localhost';
$username = 'root'; // sesuaikan dengan username MySQL Anda
$password = ''; // sesuaikan dengan password MySQL Anda
$database = 'travel_lovindra';

// Membuat koneksi MySQLi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset untuk menghindari masalah encoding
$conn->set_charset("utf8");

// Fungsi untuk membersihkan input
if (!function_exists('clean_input')) {
    function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}
}

// Fungsi untuk format rupiah
if (!function_exists('format_rupiah')) {
    function format_rupiah($amount) {
    return "Rp " . number_format($amount, 0, ',', '.');
}
}

// Fungsi untuk format tanggal Indonesia
if (!function_exists(function: 'format_tanggal')) {
    function format_tanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    );
}
    
    $date = date_create($tanggal);
    $hari = date_format($date, 'j');
    $bulan_nama = $bulan[date_format($date, 'n')];
    $tahun = date_format($date, 'Y');
    
    return $hari . ' ' . $bulan_nama . ' ' . $tahun;
}

// Fungsi untuk generate kode transaksi
function generate_kode_transaksi() {
    $tanggal = date('Ymd');
    
    // Cari nomor urut terakhir untuk hari ini
    global $conn;
    $query = "SELECT MAX(CAST(SUBSTRING(kode_transaksi, -3) AS UNSIGNED)) as max_no 
              FROM transaksi 
              WHERE kode_transaksi LIKE 'TRX-$tanggal-%'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    
    $no_urut = ($row['max_no'] ?? 0) + 1;
    $kode = 'TRX-' . $tanggal . '-' . str_pad($no_urut, 3, '0', STR_PAD_LEFT);
    
    return $kode;
}
?>