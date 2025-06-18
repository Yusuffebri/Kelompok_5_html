<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_lovindra";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Handle adding new driver
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_driver') {
    $nama_sopir = $_POST['namaSopir'];
    $pengalaman = $_POST['pengalaman'];
    $bahasa = $_POST['bahasa'];
    $keahlian = $_POST['keahlian'];
    $rating = $_POST['rating'];
    $gambar = $_POST['gambar'];
    $tag_lokasi = $_POST['tag'];
    
    // Extract numeric rating from string like "★★★★★ 4.8"
    $numeric_rating = preg_replace('/[^0-9.]/', '', $rating);
    if (!is_numeric($numeric_rating)) {
        $numeric_rating = 5.0; // default rating
    }
    
    // Extract experience years from string like "10 tahun"
    $experience_years = preg_replace('/[^0-9]/', '', $pengalaman);
    if (!is_numeric($experience_years)) {
        $experience_years = 1; // default experience
    }

    // Insert into katalog_sopir table (the one used by catalog)
    $stmt = $conn->prepare("INSERT INTO katalog_sopir (nama, pengalaman, bahasa, keahlian, rating, foto, kota, spesialisasi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssss", $nama_sopir, $experience_years, $bahasa, $keahlian, $numeric_rating, $gambar, $tag_lokasi, $keahlian);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Sopir berhasil ditambahkan!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Handle updating driver
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_driver') {
    $id = $_POST['id'];
    $nama_sopir = $_POST['namaSopir'];
    $pengalaman = $_POST['pengalaman'];
    $bahasa = $_POST['bahasa'];
    $keahlian = $_POST['keahlian'];
    $rating = $_POST['rating'];
    $gambar = $_POST['gambar'];
    $tag_lokasi = $_POST['tag'];
    
    // Extract numeric rating from string like "★★★★★ 4.8"
    $numeric_rating = preg_replace('/[^0-9.]/', '', $rating);
    if (!is_numeric($numeric_rating)) {
        $numeric_rating = 5.0;
    }
    
    // Extract experience years from string like "10 tahun"
    $experience_years = preg_replace('/[^0-9]/', '', $pengalaman);
    if (!is_numeric($experience_years)) {
        $experience_years = 1;
    }

    $stmt = $conn->prepare("UPDATE katalog_sopir SET nama=?, pengalaman=?, bahasa=?, keahlian=?, rating=?, foto=?, kota=?, spesialisasi=? WHERE id=?");
    $stmt->bind_param("sissssssi", $nama_sopir, $experience_years, $bahasa, $keahlian, $numeric_rating, $gambar, $tag_lokasi, $keahlian, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Sopir berhasil diperbarui!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Handle deleting driver
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_driver') {
    $id = $_POST['id'];
    
    $stmt = $conn->prepare("DELETE FROM katalog_sopir WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Sopir berhasil dihapus!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Fetch driver data from the database
$sql = "SELECT id, nama, pengalaman, bahasa, keahlian, rating, foto, kota, spesialisasi FROM katalog_sopir ORDER BY rating DESC";
$result = $conn->query($sql);

$drivers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $drivers[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Input Sopir</title>
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

        header {
            background-color: #2a5298;
            color: white;
            text-align: center;
            padding: 50px;
        }

        h1, h2 {
            margin-bottom: 20px;
        }

        .form-section {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        input {
            padding: 10px;
            margin: 10px;
            width: 250px;
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

        .driver-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px;
            width: 300px;
            transition: transform 0.3s;
        }

        .driver-card:hover {
            transform: translateY(-10px);
        }

        .driver-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .driver-card .content {
            padding: 20px;
        }

        .driver-card h3 {
            margin: 0 0 10px;
            color: #2a5298;
        }

        .driver-card .details {
            color: #555;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .driver-card .rating {
            margin-bottom: 10px;
            color: #ffc107;
        }

        .specialty {
            display: inline-block;
            background: #e6f0ff;
            color: #2a5298;
            padding: 5px 10px;
            margin: 3px;
            border-radius: 15px;
            font-size: 12px;
        }

        .catalog-container {
            padding: 50px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            gap: 5px;
        }

        .edit-button, .delete-button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            flex: 1;
        }

        .edit-button {
            background-color: #4CAF50;
            color: white;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
        }

        .edit-button:hover {
            background-color: #45a049;
        }

        .delete-button:hover {
            background-color: #da190b;
        }

        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            display: none;
        }

        .error-message {
            background-color: #f44336;
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
        <h1>Manajemen Sopir</h1>
        <p>Admin dapat menambahkan data sopir baru di bawah ini</p>
    </header>

    <section class="form-section">
        <h2>Form Tambah Sopir</h2>
        <div id="successMessage" class="success-message"></div>
        <div id="errorMessage" class="error-message"></div>
        <form id="formSopir">
            <input type="hidden" id="sopirId">
            <input type="text" id="namaSopir" placeholder="Nama Sopir" required>
            <input type="text" id="pengalaman" placeholder="Pengalaman (cth: 10 tahun)" required>
            <input type="text" id="bahasa" placeholder="Bahasa (cth: Indonesia, Inggris)" required>
            <input type="text" id="keahlian" placeholder="Keahlian (cth: Wisata Budaya Bali)" required>
            <input type="text" id="rating" placeholder="Rating (cth: ★★★★★ 4.8)" required>
            <input type="text" id="gambar" placeholder="Path Gambar (cth: image/sopir1.jpg)" required>
            <input type="text" id="tag" placeholder="Tag Lokasi (cth: Bali)" required>
            <br>
            <button type="submit" id="submitBtn">Tambah Sopir</button>
            <button type="button" id="cancelBtn" style="display: none; background-color: #f44336;">Batal</button>
        </form>
    </section>

    <section class="catalog-container" id="driverContainer">
        <?php foreach ($drivers as $driver): ?>
            <div class="driver-card" data-id="<?php echo $driver['id']; ?>">
                <img src="<?php echo htmlspecialchars($driver['foto']); ?>" alt="<?php echo htmlspecialchars($driver['nama']); ?>">
                <div class="content">
                    <h3><?php echo htmlspecialchars($driver['nama']); ?></h3>
                    <div class="details">
                        Pengalaman: <?php echo $driver['pengalaman']; ?> tahun<br>
                        Bahasa: <?php echo htmlspecialchars($driver['bahasa']); ?><br>
                        Keahlian: <?php echo htmlspecialchars($driver['keahlian']); ?>
                    </div>
                    <div class="rating">
                        Rating: <?php 
    $bintang_isi = min(5, floor($driver['rating'])); // Maksimal 5 bintang
    $bintang_kosong = max(0, 5 - $bintang_isi); // Minimal 0 bintang kosong
    echo str_repeat('★', $bintang_isi) . str_repeat('☆', $bintang_kosong) . ' ' . $driver['rating']; 
?>
                    </div>
                    <div class="specialties">
                        <span class="specialty"><?php echo htmlspecialchars($driver['kota']); ?></span>
                    </div>
                    <div class="action-buttons">
                        <button class="edit-button" onclick="editDriver(<?php echo $driver['id']; ?>)">Edit</button>
                        <button class="delete-button" onclick="deleteDriver(<?php echo $driver['id']; ?>)">Hapus</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <script>
        // Drivers data from PHP
        let driversData = <?php echo json_encode($drivers); ?>;

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

        // Fungsi untuk mengedit data sopir
        function editDriver(id) {
            const driver = driversData.find(d => d.id == id);
            if (!driver) return;
            
            document.getElementById("sopirId").value = driver.id;
            document.getElementById("namaSopir").value = driver.nama;
            document.getElementById("pengalaman").value = driver.pengalaman + " tahun";
            document.getElementById("bahasa").value = driver.bahasa;
            document.getElementById("keahlian").value = driver.keahlian;
            document.getElementById("rating").value = "★★★★★ " + driver.rating;
            document.getElementById("gambar").value = driver.foto;
            document.getElementById("tag").value = driver.kota;
            
            document.getElementById("submitBtn").textContent = "Update Sopir";
            document.getElementById("cancelBtn").style.display = "inline-block";
            
            // Scroll to form
            document.querySelector(".form-section").scrollIntoView({ behavior: 'smooth' });
        }

        // Fungsi untuk menghapus data sopir
        function deleteDriver(id) {
            if (confirm("Apakah Anda yakin ingin menghapus data sopir ini?")) {
                const formData = new FormData();
                formData.append('action', 'delete_driver');
                formData.append('id', id);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message);
                        // Refresh page after successful deletion
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

        // Event listener untuk inisialisasi halaman
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk membatalkan edit
            document.getElementById("cancelBtn").addEventListener("click", function() {
                document.getElementById("formSopir").reset();
                document.getElementById("sopirId").value = "";
                document.getElementById("submitBtn").textContent = "Tambah Sopir";
                this.style.display = "none";
            });

            // Event listener untuk form submission
            document.getElementById("formSopir").addEventListener("submit", function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                const sopirId = document.getElementById("sopirId").value;
                
                if (sopirId) {
                    formData.append('action', 'update_driver');
                    formData.append('id', sopirId);
                } else {
                    formData.append('action', 'add_driver');
                }
                
                formData.append('namaSopir', document.getElementById("namaSopir").value);
                formData.append('pengalaman', document.getElementById("pengalaman").value);
                formData.append('bahasa', document.getElementById("bahasa").value);
                formData.append('keahlian', document.getElementById("keahlian").value);
                formData.append('rating', document.getElementById("rating").value);
                formData.append('gambar', document.getElementById("gambar").value);
                formData.append('tag', document.getElementById("tag").value);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message);
                        this.reset();
                        document.getElementById("sopirId").value = "";
                        document.getElementById("submitBtn").textContent = "Tambah Sopir";
                        document.getElementById("cancelBtn").style.display = "none";
                        
                        // Refresh page after successful submission
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
        });
    </script>
</body>

</html>