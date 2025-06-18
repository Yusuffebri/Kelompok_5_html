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
    
    // Menambah destinasi baru
    if (isset($_POST['action']) && $_POST['action'] == 'add_destination') {
        $nama_destinasi = $_POST['namaDestinasi'];
        $harga = $_POST['hargaDestinasi'];
        $link_detail = $_POST['linkDestinasi'];
        $gambar = $_POST['gambarDestinasi'];

        $stmt = $conn->prepare("INSERT INTO admin_destinasi (nama_destinasi, harga, link_detail, gambar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama_destinasi, $harga, $link_detail, $gambar);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Destinasi baru berhasil ditambahkan!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Mengupdate destinasi
    if (isset($_POST['action']) && $_POST['action'] == 'update_main_destination') {
        $id = $_POST['id'];
        $nama_destinasi = $_POST['namaDestinasi'];
        // Hapus format Rp dan /Orang untuk mendapatkan angka murni
        $harga_clean = preg_replace('/[^0-9]/', '', $_POST['hargaDestinasi']);
        $link_detail = $_POST['linkDestinasi'];
        $gambar = $_POST['gambarDestinasi'];

        $stmt = $conn->prepare("UPDATE destinasi SET name = ?, price_per_person = ?, detail_page_link = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("sissi", $nama_destinasi, $harga_clean, $link_detail, $gambar, $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Destinasi utama berhasil diperbarui!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Update destinasi admin
    if (isset($_POST['action']) && $_POST['action'] == 'update_destination') {
        $id = $_POST['id'];
        $nama_destinasi = $_POST['namaDestinasi'];
        $harga = $_POST['hargaDestinasi'];
        $link_detail = $_POST['linkDestinasi'];
        $gambar = $_POST['gambarDestinasi'];

        $stmt = $conn->prepare("UPDATE admin_destinasi SET nama_destinasi = ?, harga = ?, link_detail = ?, gambar = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nama_destinasi, $harga, $link_detail, $gambar, $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Destinasi admin berhasil diperbarui!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Delete destinasi utama
    if (isset($_POST['action']) && $_POST['action'] == 'delete_main_destination') {
        $id = $_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM destinasi WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Destinasi utama berhasil dihapus!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Delete destinasi admin
    if (isset($_POST['action']) && $_POST['action'] == 'delete_destination') {
        $id = $_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM admin_destinasi WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Destinasi admin berhasil dihapus!";
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
        $nama_destinasi = $_POST['namaDestinasi'];
        $harga = $_POST['hargaDestinasi'];
        $link_detail = $_POST['linkDestinasi'];
        $gambar = $_POST['gambarDestinasi'];

        $stmt = $conn->prepare("INSERT INTO admin_destinasi (nama_destinasi, harga, link_detail, gambar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama_destinasi, $harga, $link_detail, $gambar);

        if ($stmt->execute()) {
            $message = "Destinasi baru berhasil ditambahkan!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM admin_destinasi WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "Destinasi berhasil dihapus!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Mengambil data destinasi dari database - FIXED: Removed extra $ sign
$sql_main = "SELECT id, name as nama_destinasi, price_per_person as harga_raw, image_path as gambar, detail_page_link as link_detail, 'main' as source_table FROM destinasi ORDER BY id DESC";
$result_main = $conn->query($sql_main);

$sql_admin = "SELECT id, nama_destinasi, harga, gambar, link_detail, 'admin' as source_table FROM admin_destinasi ORDER BY id DESC";
$result_admin = $conn->query($sql_admin);

$destinations = [];

// Tambahkan destinasi dari tabel utama
if ($result_main && $result_main->num_rows > 0) {
    while ($row = $result_main->fetch_assoc()) {
        // Format harga untuk destinasi utama
        $row['harga'] = 'Rp ' . number_format($row["harga_raw"], 0, ',', '.') . '/Orang';
        $destinations[] = $row;
    }
}

// Tambahkan destinasi dari tabel admin
if ($result_admin && $result_admin->num_rows > 0) {
    while ($row = $result_admin->fetch_assoc()) {
        $destinations[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Destinasi - Travel Lovindra</title>
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
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            padding: 10px 20px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .destination-header {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            padding: 50px;
            text-align: center;
        }

        .destination-header h1 {
            font-size: 36px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .admin-form {
            padding: 40px;
            background-color: #ffffff;
            text-align: center;
            max-width: 800px;
            margin: 40px auto;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .admin-form h2 {
            color: #2a5298;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }

        .admin-form input {
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            width: 100%;
            font-size: 16px;
            transition: border-color 0.3s ease;
            margin-bottom: 15px;
        }

        .admin-form input:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
        }

        .admin-form button {
            padding: 15px 30px;
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            margin: 10px 5px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .admin-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 82, 152, 0.3);
        }

        #cancelBtn {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            display: none;
        }

        #cancelBtn:hover {
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3);
        }

        .message {
            padding: 15px;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        }

        .destination-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .destination-card img {
            width: 100%;
            height: 200px;
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

        .destination-card h3 {
            color: #2a5298;
            margin-bottom: 15px;
            font-size: 20px;
            font-weight: 600;
        }

        .destination-card .price {
            margin-bottom: 20px;
            color: #2a5298;
            font-weight: bold;
            font-size: 16px;
        }

        .view-button {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            width: 100%;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 10px;
        }

        .view-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 82, 152, 0.3);
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            gap: 10px;
        }

        .edit-button, .delete-button {
            padding: 8px 15px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 12px;
            flex: 1;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .edit-button {
            background-color: #4CAF50;
            color: white;
        }

        .edit-button:hover {
            background-color: #45a049;
            transform: translateY(-1px);
        }

        .delete-button {
            background-color: #f44336;
            color: white;
        }

        .delete-button:hover {
            background-color: #da190b;
            transform: translateY(-1px);
        }

        .section-title {
            text-align: center;
            color: #2a5298;
            font-size: 32px;
            margin: 40px 0 20px;
            font-weight: 600;
        }

        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            display: none;
        }

        .error-message {
            background-color: #f44336;
            color: white;
            padding: 15px;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }
            
            .destination-header {
                padding: 30px 20px;
            }
            
            .admin-form {
                margin: 20px;
                padding: 30px 20px;
            }
            
            .destination-container {
                padding: 30px 20px;
                gap: 20px;
            }
            
            .destination-card {
                width: 100%;
                max-width: 350px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">Travel Lovindra - Admin</div>
        <div class="nav-links">
            <a href="destinasi.php">Lihat Destinasi</a>
            <a href="menu_utama.php">Home</a>
        </div>
    </nav>

    <header class="destination-header">
        <h1>Admin Destinasi Wisata</h1>
        <p>Kelola destinasi wisata untuk Travel Lovindra</p>
    </header>

    <!-- Message containers for AJAX responses -->
    <div id="successMessage" class="success-message"></div>
    <div id="errorMessage" class="error-message"></div>

    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'berhasil') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <section class="admin-form">
        <h2>Tambah Destinasi Baru</h2>
        <form id="formDestinasi">
            <input type="hidden" id="destinasiId" value="">
            <input type="text" id="namaDestinasi" placeholder="Nama Destinasi" required>
            <input type="text" id="hargaDestinasi" placeholder="Harga (cth: Rp 3.000.000/Orang)" required>
            <input type="text" id="linkDestinasi" placeholder="Link Halaman Detail" required>
            <input type="text" id="gambarDestinasi" placeholder="Path Gambar (cth: image/bali.jpeg)" required>
            <br>
            <button type="submit" id="submitBtn">Tambah Destinasi</button>
            <button type="button" id="cancelBtn">Batal</button>
        </form>
    </section>

    <h2 class="section-title">Destinasi yang Tersedia</h2>

    <section class="destination-container" id="destinationContainer">

        <!-- Dynamic destinations from database -->
        <?php foreach ($destinations as $destination): ?>
<div class="destination-card">
    <img src="<?php echo htmlspecialchars($destination['gambar']); ?>" alt="<?php echo htmlspecialchars($destination['nama_destinasi']); ?>">
    <div class="content">
        <h3><?php echo htmlspecialchars($destination['nama_destinasi']); ?></h3>
        <div class="price"><?php echo htmlspecialchars($destination['harga']); ?></div>
        <a href="<?php echo htmlspecialchars($destination['link_detail']); ?>" class="view-button">Lihat Detail</a>
        <div class="action-buttons">
            <button class="edit-button" onclick="editDestination(<?php echo $destination['id']; ?>, '<?php echo $destination['source_table']; ?>')">Edit</button>
            <button class="delete-button" onclick="deleteDestination(<?php echo $destination['id']; ?>, '<?php echo $destination['source_table']; ?>')">Hapus</button>
        </div>
        <!-- Badge untuk menunjukkan sumber data -->
        <div style="margin-top: 10px;">
            <span style="background: <?php echo $destination['source_table'] == 'main' ? '#4CAF50' : '#FF9800'; ?>; color: white; padding: 3px 8px; border-radius: 10px; font-size: 10px;">
                <?php echo $destination['source_table'] == 'main' ? 'Default' : 'Admin'; ?>
            </span>
        </div>
    </div>
</div>
<?php endforeach; ?>
    </section>

    <script>
        // Destinations data from PHP
        let destinationsData = <?php echo json_encode($destinations); ?>;

        // Fungsi untuk menampilkan pesan
        function showMessage(message, isError = false) {
            const successMessage = document.getElementById("successMessage");
            const errorMessage = document.getElementById("errorMessage");
            
            if (isError) {
                errorMessage.textContent = message;
                errorMessage.style.display = "block";
                successMessage.style.display = "none";
            } else {
                successMessage.textContent = message;
                successMessage.style.display = "block";
                errorMessage.style.display = "none";
            }
            
            setTimeout(function() {
                successMessage.style.display = "none";
                errorMessage.style.display = "none";
            }, 3000);
        }

        // Fungsi untuk mengedit data destinasi
        function editDestination(id, sourceTable) {
            const destination = destinationsData.find(d => d.id == id && d.source_table == sourceTable);
            if (!destination) return;
            
            document.getElementById("destinasiId").value = destination.id;
            document.getElementById("namaDestinasi").value = destination.nama_destinasi;
            
            // Untuk destinasi utama, tampilkan harga yang sudah diformat
            if (sourceTable === 'main') {
                document.getElementById("hargaDestinasi").value = destination.harga;
            } else {
                document.getElementById("hargaDestinasi").value = destination.harga;
            }
            
            document.getElementById("linkDestinasi").value = destination.link_detail;
            document.getElementById("gambarDestinasi").value = destination.gambar;
            
            // Set atribut untuk menandai sumber data
            document.getElementById("formDestinasi").setAttribute('data-source', sourceTable);
            document.getElementById("formDestinasi").setAttribute('data-id', id);
            
            document.getElementById("submitBtn").textContent = sourceTable === 'main' ? "Update Destinasi Default" : "Update Destinasi Admin";
            document.getElementById("cancelBtn").style.display = "inline-block";
            
            // Scroll to form
            document.querySelector(".admin-form").scrollIntoView({ behavior: 'smooth' });
        }

        // Fungsi untuk menghapus data destinasi
        function deleteDestination(id, sourceTable) {
            const confirmMessage = sourceTable === 'main' 
                ? "Apakah Anda yakin ingin menghapus destinasi default ini?" 
                : "Apakah Anda yakin ingin menghapus destinasi ini?";
                
            if (confirm(confirmMessage)) {
                const formData = new FormData();
                
                if (sourceTable === 'main') {
                    formData.append('action', 'delete_main_destination');
                } else {
                    formData.append('action', 'delete_destination');
                }
                
                formData.append('id', id);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showMessage(data.message, true);
                    }
                })
                .catch(error => {
                    showMessage('Terjadi kesalahan: ' + error.message, true);
                });
            }
        }

        // Cancel button functionality
        document.getElementById("cancelBtn").addEventListener("click", function() {
            document.getElementById("formDestinasi").reset();
            document.getElementById("destinasiId").value = "";
            document.getElementById("formDestinasi").removeAttribute('data-source');
            document.getElementById("formDestinasi").removeAttribute('data-id');
            document.getElementById("submitBtn").textContent = "Tambah Destinasi";
            this.style.display = "none";
        });

        // Form submission
        document.getElementById("formDestinasi").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const destinasiId = document.getElementById("destinasiId").value;
            const sourceTable = this.getAttribute('data-source');
            
            if (destinasiId) {
                if (sourceTable === 'main') {
                    formData.append('action', 'update_main_destination');
                } else {
                    formData.append('action', 'update_destination');
                }
                formData.append('id', destinasiId);
            } else {
                formData.append('action', 'add_destination');
            }
            
            formData.append('namaDestinasi', document.getElementById("namaDestinasi").value);
            formData.append('hargaDestinasi', document.getElementById("hargaDestinasi").value);
            formData.append('linkDestinasi', document.getElementById("linkDestinasi").value);
            formData.append('gambarDestinasi', document.getElementById("gambarDestinasi").value);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message);
                    this.reset();
                    document.getElementById("destinasiId").value = "";
                    this.removeAttribute('data-source');
                    this.removeAttribute('data-id');
                    document.getElementById("submitBtn").textContent = "Tambah Destinasi";
                    document.getElementById("cancelBtn").style.display = "none";
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showMessage(data.message, true);
                }
            })
            .catch(error => {
                showMessage('Terjadi kesalahan: ' + error.message, true);
            });
        });
    </script>
</body>

</html>