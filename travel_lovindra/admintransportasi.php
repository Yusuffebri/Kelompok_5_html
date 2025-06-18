<?php
// Konfigurasi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_lovindra";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$message = "";

// Menangani request AJAX/POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = array();
    
    // Menambah transportasi baru
    if (isset($_POST['action']) && $_POST['action'] == 'add_transport') {
        $nama_kendaraan = $_POST['namaKendaraan'];
        $kapasitas = $_POST['kapasitas'];
        $bagasi = $_POST['bagasi'];
        $ac = $_POST['ac'];
        $harga = $_POST['harga'];
        $gambar = $_POST['gambar'];

        $stmt = $conn->prepare("INSERT INTO admin_transportasi (nama_kendaraan, kapasitas, bagasi, ac, harga, gambar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nama_kendaraan, $kapasitas, $bagasi, $ac, $harga, $gambar);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Kendaraan baru berhasil ditambahkan!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Mengupdate transportasi dari tabel utama
    if (isset($_POST['action']) && $_POST['action'] == 'update_main_transport') {
        $id = $_POST['id'];
        $nama_kendaraan = $_POST['namaKendaraan'];
        $kapasitas = $_POST['kapasitas'];
        $bagasi = $_POST['bagasi'];
        $ac = $_POST['ac'];
        $harga_clean = preg_replace('/[^0-9]/', '', $_POST['harga']);
        $gambar = $_POST['gambar'];

        $stmt = $conn->prepare("UPDATE transportasi SET nama_kendaraan = ?, kapasitas = ?, bagasi = ?, ac = ?, harga_per_hari = ?, gambar = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $nama_kendaraan, $kapasitas, $bagasi, $ac, $harga_clean, $gambar, $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Transportasi utama berhasil diperbarui!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Update transportasi admin
    if (isset($_POST['action']) && $_POST['action'] == 'update_transport') {
        $id = $_POST['id'];
        $nama_kendaraan = $_POST['namaKendaraan'];
        $kapasitas = $_POST['kapasitas'];
        $bagasi = $_POST['bagasi'];
        $ac = $_POST['ac'];
        $harga = $_POST['harga'];
        $gambar = $_POST['gambar'];

        $stmt = $conn->prepare("UPDATE admin_transportasi SET nama_kendaraan = ?, kapasitas = ?, bagasi = ?, ac = ?, harga = ?, gambar = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $nama_kendaraan, $kapasitas, $bagasi, $ac, $harga, $gambar, $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Transportasi admin berhasil diperbarui!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Delete transportasi utama
    if (isset($_POST['action']) && $_POST['action'] == 'delete_main_transport') {
        $id = $_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM transportasi WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Transportasi utama berhasil dihapus!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Delete transportasi admin
    if (isset($_POST['action']) && $_POST['action'] == 'delete_transport') {
        $id = $_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM admin_transportasi WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Transportasi admin berhasil dihapus!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Legacy handling untuk form submission biasa (non-AJAX)
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $nama_kendaraan = $_POST['namaKendaraan'];
        $kapasitas = $_POST['kapasitas'];
        $bagasi = $_POST['bagasi'];
        $ac = $_POST['ac'];
        $harga = $_POST['harga'];
        $gambar = $_POST['gambar'];

        $stmt = $conn->prepare("INSERT INTO admin_transportasi (nama_kendaraan, kapasitas, bagasi, ac, harga, gambar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nama_kendaraan, $kapasitas, $bagasi, $ac, $harga, $gambar);

        if ($stmt->execute()) {
            $message = "Kendaraan baru berhasil ditambahkan!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM admin_transportasi WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "Kendaraan berhasil dihapus!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Mengambil data transportasi dari database
$sql_main = "SELECT id, nama as nama, kapasitas, bagasi, ac, harga as harga_raw, gambar, 'main' as source_table FROM katalog ORDER BY id DESC";
$result_main = $conn->query($sql_main);

$sql_admin = "SELECT id, nama as nama, kapasitas, bagasi, ac, harga, gambar, 'admin' as source_table FROM admin_transportasi ORDER BY id DESC";
$result_admin = $conn->query($sql_admin);

$transports = [];

// Tambahkan transportasi dari tabel utama
if ($result_main && $result_main->num_rows > 0) {
    while ($row = $result_main->fetch_assoc()) {
        // Format harga untuk transportasi utama
        $row['harga'] = 'Rp ' . number_format($row["harga_raw"], 0, ',', '.') . '/Hari';
        $transports[] = $row;
    }
}

// Tambahkan transportasi dari tabel admin
if ($result_admin && $result_admin->num_rows > 0) {
    while ($row = $result_admin->fetch_assoc()) {
        $transports[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Transportasi - Admin Input</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #2a5298;
            color: white;
            text-align: center;
            padding: 50px;
        }

        h1, h2 {
            margin-bottom: 20px;
        }

        .form-container {
            padding: 30px;
            background: #fff;
            margin: 20px auto;
            max-width: 1000px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        input {
            padding: 10px;
            margin: 10px;
            width: 220px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 20px;
            background-color: #2a5298;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #1e3c72;
        }

        .catalog-container {
            padding: 50px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .vehicle-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 350px;
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
            margin: 0 0 10px;
            color: #2a5298;
        }

        .vehicle-card .specs {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .vehicle-card .price {
            font-weight: bold;
            color: #2a5298;
            margin-bottom: 15px;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .edit-button, .delete-button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 48%;
        }

        .edit-button {
            background-color: #4CAF50;
            color: white;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
        }

        .book-button {
            width: 100%;
            padding: 10px;
            background-color: #2a5298;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .book-button:hover {
            background-color: #1e3c72;
        }

        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            display: none;
        }
    </style>
</head>

<body>
    <header>
        <h1>Katalog Transportasi</h1>
        <p>Admin dapat menambahkan kendaraan baru di bawah ini</p>
    </header>

    <section class="admin-form">
        <h2>Tambah Kendaraan Baru</h2>
        <div id="successMessage" class="success-message">Kendaraan berhasil ditambahkan!</div>
        <form id="formKatalog">
            <input type="hidden" id="kendaraanId">
            <input type="text" id="namaKendaraan" placeholder="Nama Kendaraan" required>
            <input type="text" id="kapasitas" placeholder="Kapasitas (cth: 7 Orang)" required>
            <input type="text" id="bagasi" placeholder="Bagasi (cth: Cukup untuk 4 koper)" required>
            <input type="text" id="ac" placeholder="AC (cth: Aktif)" required>
            <input type="text" id="harga" placeholder="Harga (cth: Rp 500.000/hari)" required>
            <input type="text" id="gambar" placeholder="Path Gambar (cth: image/avanza.jpg)" required><br>
            <button type="submit" id="submitBtn">Tambah Kendaraan</button>
            <button type="button" id="cancelBtn" style="display: none; background-color: #f44336;">Batal</button>
        </form>
    </section>

    <section class="catalog-container" id="catalogContainer">
        <!-- Kendaraan akan ditambahkan di sini -->
    </section>

    <script>
        // Fungsi untuk mendapatkan data kendaraan dari localStorage
        function getVehiclesFromStorage() {
            const vehicles = localStorage.getItem('katalogKendaraan');
            return vehicles ? JSON.parse(vehicles) : [];
        }

        // Fungsi untuk menyimpan data kendaraan ke localStorage
        function saveVehiclesToStorage(vehicles) {
            localStorage.setItem('katalogKendaraan', JSON.stringify(vehicles));
        }

        // Fungsi untuk menampilkan kartu kendaraan
        function displayVehicleCard(vehicle, index) {
            const container = document.getElementById("catalogContainer");
            const card = document.createElement("div");
            card.className = "vehicle-card";
            card.setAttribute("data-id", index);

            card.innerHTML = `
                <img src="${vehicle.gambar}" alt="${vehicle.nama}">
                <div class="content">
                    <h3>${vehicle.nama}</h3>
                    <div class="specs">
                        Kapasitas: ${vehicle.kapasitas}<br>
                        AC: ${vehicle.ac}<br>
                        Bagasi: ${vehicle.bagasi}
                    </div>
                    <div class="price">${vehicle.harga}</div>
                    <button class="book-button">Pesan Sekarang</button>
                    <div class="action-buttons">
                        <button class="edit-button" onclick="editVehicle(${index})">Edit</button>
                        <button class="delete-button" onclick="deleteVehicle(${index})">Hapus</button>
                    </div>
                </div>
            `;

            container.appendChild(card);
        }

        // Fungsi untuk menampilkan semua kendaraan
        function displayAllVehicles() {
            const container = document.getElementById("catalogContainer");
            container.innerHTML = '';
            
            const vehicles = getVehiclesFromStorage();
            vehicles.forEach((vehicle, index) => {
                displayVehicleCard(vehicle, index);
            });
        }

        // Fungsi untuk mengedit data kendaraan
        function editVehicle(index) {
            const vehicles = getVehiclesFromStorage();
            const vehicle = vehicles[index];
            
            document.getElementById("kendaraanId").value = index;
            document.getElementById("namaKendaraan").value = vehicle.nama;
            document.getElementById("kapasitas").value = vehicle.kapasitas;
            document.getElementById("bagasi").value = vehicle.bagasi;
            document.getElementById("ac").value = vehicle.ac;
            document.getElementById("harga").value = vehicle.harga;
            document.getElementById("gambar").value = vehicle.gambar;
            
            document.getElementById("submitBtn").textContent = "Update Kendaraan";
            document.getElementById("cancelBtn").style.display = "inline-block";
            
            // Scroll to form
            document.querySelector(".form-container").scrollIntoView({ behavior: 'smooth' });
        }

        // Fungsi untuk menghapus data kendaraan
        function deleteVehicle(index) {
            if (confirm("Apakah Anda yakin ingin menghapus data kendaraan ini?")) {
                const vehicles = getVehiclesFromStorage();
                vehicles.splice(index, 1);
                saveVehiclesToStorage(vehicles);
                displayAllVehicles();
                
                showSuccessMessage("Kendaraan berhasil dihapus!");
            }
        }

        // Fungsi untuk menampilkan pesan sukses
        function showSuccessMessage(message) {
            const successMessage = document.getElementById("successMessage");
            successMessage.textContent = message;
            successMessage.style.display = "block";
            
            setTimeout(function() {
                successMessage.style.display = "none";
            }, 3000);
        }

        // Fungsi untuk membatalkan edit
        document.getElementById("cancelBtn").addEventListener("click", function() {
            document.getElementById("formKatalog").reset();
            document.getElementById("kendaraanId").value = "";
            document.getElementById("submitBtn").textContent = "Tambah Kendaraan";
            this.style.display = "none";
        });

        // Event listener untuk form submission
        document.getElementById("formKatalog").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const newVehicle = {
                nama: document.getElementById("namaKendaraan").value,
                kapasitas: document.getElementById("kapasitas").value,
                bagasi: document.getElementById("bagasi").value,
                ac: document.getElementById("ac").value,
                harga: document.getElementById("harga").value,
                gambar: document.getElementById("gambar").value
            };
            
            const vehicleId = document.getElementById("kendaraanId").value;
            const vehicles = getVehiclesFromStorage();
            
            if (vehicleId === "") {
                // Tambah kendaraan baru
                vehicles.push(newVehicle);
                showSuccessMessage("Kendaraan berhasil ditambahkan!");
            } else {
                // Update kendaraan yang ada
                vehicles[vehicleId] = newVehicle;
                document.getElementById("submitBtn").textContent = "Tambah Kendaraan";
                document.getElementById("cancelBtn").style.display = "none";
                showSuccessMessage("Kendaraan berhasil diperbarui!");
            }
            
            saveVehiclesToStorage(vehicles);
            displayAllVehicles();
            this.reset();
            document.getElementById("kendaraanId").value = "";
        });

        // Memuat semua kendaraan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            displayAllVehicles();
        });
    </script>
</body>
</html>