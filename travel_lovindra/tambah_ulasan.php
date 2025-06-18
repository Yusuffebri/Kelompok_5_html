<?php
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$dbname = "travel_lovindra";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$message = "";
$message_type = "";

// Proses form submission
if ($_POST) {
    $nama_pengulas = trim($_POST['nama_pengulas']);
    $destinasi = trim($_POST['destinasi']);
    $rating = (int)$_POST['rating'];
    $ulasan = trim($_POST['ulasan']);
    
    // Validasi input
    if (empty($nama_pengulas) || empty($destinasi) || empty($ulasan) || $rating < 1 || $rating > 5) {
        $message = "Semua field harus diisi dengan benar!";
        $message_type = "error";
    } else {
        // Handle file upload untuk foto profil
        $foto_profil = "image/default-avatar.jpg"; // Default avatar
        
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
            $upload_dir = "image/";
            $file_extension = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = "avatar_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_path)) {
                    $foto_profil = $upload_path;
                }
            }
        }
        
        // Insert ke database
        $stmt = $conn->prepare("INSERT INTO ulasan (nama_pengulas, destinasi, rating, ulasan, foto_profil, tanggal) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssiss", $nama_pengulas, $destinasi, $rating, $ulasan, $foto_profil);
        
        if ($stmt->execute()) {
            $message = "Ulasan berhasil ditambahkan! Terima kasih atas feedback Anda.";
            $message_type = "success";
            
            // Reset form
            $_POST = array();
        } else {
            $message = "Terjadi kesalahan saat menyimpan ulasan. Silakan coba lagi.";
            $message_type = "error";
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ulasan - Travel Lovindra</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .form-container {
            padding: 40px 30px;
        }
        
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-weight: 500;
            text-align: center;
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
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2a5298;
            font-weight: 600;
            font-size: 16px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2a5298;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .rating-container {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 8px;
        }
        
        .star-rating {
            display: flex;
            gap: 5px;
        }
        
        .star {
            font-size: 30px;
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .star:hover,
        .star.active {
            color: #ffc107;
            transform: scale(1.1);
        }
        
        .rating-text {
            margin-left: 15px;
            color: #666;
            font-weight: 500;
        }
        
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: block;
            padding: 15px;
            background-color: #f8f9fa;
            border: 2px dashed #2a5298;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload-label:hover {
            background-color: #e9ecef;
            border-color: #1e3c72;
        }
        
        .file-upload-text {
            color: #2a5298;
            font-weight: 500;
        }
        
        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            min-width: 150px;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(42, 82, 152, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(42, 82, 152, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            .form-container {
                padding: 30px 20px;
            }
            
            .btn-container {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tambah Ulasan</h1>
            <p>Bagikan pengalaman perjalanan Anda dengan Travel Lovindra</p>
        </div>
        
        <div class="form-container">
            <?php if ($message): ?>
                <div class="message <?= $message_type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama_pengulas">Nama Lengkap *</label>
                    <input type="text" id="nama_pengulas" name="nama_pengulas" 
                           value="<?= isset($_POST['nama_pengulas']) ? htmlspecialchars($_POST['nama_pengulas']) : '' ?>" 
                           required placeholder="Masukkan nama lengkap Anda">
                </div>
                
                <div class="form-group">
                    <label for="destinasi">Destinasi yang Dikunjungi *</label>
                    <select id="destinasi" name="destinasi" required>
                        <option value="">Pilih destinasi...</option>
                        <option value="Bali" <?= (isset($_POST['destinasi']) && $_POST['destinasi'] == 'Bali') ? 'selected' : '' ?>>Bali</option>
                        <option value="Yogyakarta" <?= (isset($_POST['destinasi']) && $_POST['destinasi'] == 'Yogyakarta') ? 'selected' : '' ?>>Yogyakarta</option>
                        <option value="Bandung" <?= (isset($_POST['destinasi']) && $_POST['destinasi'] == 'Bandung') ? 'selected' : '' ?>>Bandung</option>
                        <option value="Jakarta" <?= (isset($_POST['destinasi']) && $_POST['destinasi'] == 'Jakarta') ? 'selected' : '' ?>>Jakarta</option>
                        <option value="Surabaya" <?= (isset($_POST['destinasi']) && $_POST['destinasi'] == 'Surabaya') ? 'selected' : '' ?>>Surabaya</option>
                        <option value="Malang" <?= (isset($_POST['destinasi']) && $_POST['destinasi'] == 'Malang') ? 'selected' : '' ?>>Malang</option>
                        <option value="Semarang" <?= (isset($_POST['destinasi']) && $_POST['destinasi'] == 'Semarang') ? 'selected' : '' ?>>Semarang</option>
                        <option value="Solo" <?= (isset($_POST['destinasi']) && $_POST['destinasi'] == 'Solo') ? 'selected' : '' ?>>Solo</option>
                        <option value="Lainnya" <?= (isset($_POST['destinasi']) && $_POST['destinasi'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Rating Layanan *</label>
                    <div class="rating-container">
                        <div class="star-rating">
                            <span class="star" data-rating="1">★</span>
                            <span class="star" data-rating="2">★</span>
                            <span class="star" data-rating="3">★</span>
                            <span class="star" data-rating="4">★</span>
                            <span class="star" data-rating="5">★</span>
                        </div>
                        <span class="rating-text">Pilih rating</span>
                    </div>
                    <input type="hidden" id="rating" name="rating" value="<?= isset($_POST['rating']) ? $_POST['rating'] : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="ulasan">Ulasan Anda *</label>
                    <textarea id="ulasan" name="ulasan" required 
                              placeholder="Ceritakan pengalaman perjalanan Anda dengan Travel Lovindra..."><?= isset($_POST['ulasan']) ? htmlspecialchars($_POST['ulasan']) : '' ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="foto_profil">Foto Profil (Opsional)</label>
                    <div class="file-upload">
                        <input type="file" id="foto_profil" name="foto_profil" accept="image/*">
                        <label for="foto_profil" class="file-upload-label">
                            <div class="file-upload-text">📷 Klik untuk upload foto profil</div>
                        </label>
                    </div>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                    <a href="menu_utama.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Rating star functionality
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');
        const ratingText = document.querySelector('.rating-text');
        
        const ratingTexts = {
            1: 'Sangat Buruk',
            2: 'Buruk', 
            3: 'Cukup',
            4: 'Baik',
            5: 'Sangat Baik'
        };
        
        // Set initial rating if exists
        const initialRating = ratingInput.value;
        if (initialRating) {
            updateStars(parseInt(initialRating));
        }
        
        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                const rating = index + 1;
                ratingInput.value = rating;
                updateStars(rating);
            });
            
            star.addEventListener('mouseover', () => {
                const rating = index + 1;
                highlightStars(rating);
                ratingText.textContent = ratingTexts[rating];
            });
        });
        
        document.querySelector('.star-rating').addEventListener('mouseleave', () => {
            const currentRating = parseInt(ratingInput.value) || 0;
            updateStars(currentRating);
        });
        
        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
            ratingText.textContent = rating > 0 ? ratingTexts[rating] : 'Pilih rating';
        }
        
        function highlightStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        }
        
        // File upload preview
        const fileInput = document.getElementById('foto_profil');
        const fileLabel = document.querySelector('.file-upload-text');
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                fileLabel.textContent = `📷 ${fileName}`;
            } else {
                fileLabel.textContent = '📷 Klik untuk upload foto profil';
            }
        });
    </script>
</body>
</html>