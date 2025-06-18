<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_lovindra";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$message = "";
$error = "";

if ($_POST) {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer-name']);
    $customer_email = mysqli_real_escape_string($conn, $_POST['customer-email']);
    $customer_phone = mysqli_real_escape_string($conn, $_POST['customer-phone']);
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle-type']);
    $destination_type = mysqli_real_escape_string($conn, $_POST['destination-type']);
    $driver_select = mysqli_real_escape_string($conn, $_POST['driver-select']);
    $pickup_date = mysqli_real_escape_string($conn, $_POST['pickup-date']);
    $return_date = mysqli_real_escape_string($conn, $_POST['return-date']);
    $participant_count = intval($_POST['participant-count']);
    
    $pickup = new DateTime($pickup_date);
    $return = new DateTime($return_date);
    $duration = $pickup->diff($return)->days;
    
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
    
    $package_total = $package_prices[$destination_type] * $participant_count;
    $vehicle_total = $vehicle_prices[$vehicle_type] * $duration;
    $total_payment = $package_total + $vehicle_total;
    
    $sql = "INSERT INTO pemesanan (customer_name, customer_email, customer_phone, vehicle_type, destination_type, driver_name, pickup_date, return_date, participant_count, duration, total_payment, booking_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssiiid", $customer_name, $customer_email, $customer_phone, $vehicle_type, $destination_type, $driver_select, $pickup_date, $return_date, $participant_count, $duration, $total_payment);
    
    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;
        $_SESSION['booking_id'] = $booking_id;
        $_SESSION['total_payment'] = $total_payment;
        $message = "Pemesanan berhasil! Total pembayaran: Rp " . number_format($total_payment, 0, ',', '.');
        echo "<script>setTimeout(function(){window.location.href='pembayaran.php';},2000);</script>";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

$katalog_query = "SELECT * FROM katalog WHERE nama = 'available'";
$katalog_result = $conn->query($katalog_query);
$destinasi_query = "SELECT * FROM destinasi WHERE name = 'active'";
$destinasi_result = $conn->query($destinasi_query);
$katalog_sopir_query = "SELECT * FROM katalog_sopir WHERE nama = 'available'";
$katalog_sopir_result = $conn->query($katalog_sopir_query);
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemesanan - Travel Lovindra</title>
    <link rel="icon" type="image/png" href="image/Logo.jpeg" sizes="16x16">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
        body{background-color:#f5f5f5}
        .navbar{background-color:#2a5298;padding:15px 50px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
        .navbar .logo h1{color:#fff}
        .nav-links a{color:#fff;text-decoration:none;margin:0 15px}
        .booking-container{width:80%;margin:20px auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1)}
        .form-group{margin-bottom:15px}
        .form-group label{display:block;font-weight:bold;margin-bottom:5px}
        .form-group input,.form-group select{width:100%;padding:8px;border:1px solid #ccc;border-radius:4px}
        .form-buttons{text-align:right;margin-top:20px}
        .primary-button{padding:10px 20px;border:none;cursor:pointer;border-radius:4px;background-color:#FF9800;color:#fff}
        footer{text-align:center;padding:10px;background:#2a5298;color:white;margin-top:20px}
        .selected-package,.selected-vehicle,.selected-driver{margin-bottom:20px;padding:15px;border:1px solid #ddd;border-radius:5px;background-color:#f9f9f9}
        .package-preview,.vehicle-preview,.driver-preview{display:flex;align-items:center}
        .package-preview img,.vehicle-preview img{width:120px;height:80px;object-fit:cover;border-radius:5px;margin-right:15px}
        .driver-preview img{width:80px;height:80px;object-fit:cover;border-radius:50%;margin-right:15px}
        .package-details h4,.vehicle-details h4,.driver-details h4{margin-bottom:5px;color:#2a5298}
        .package-details p,.vehicle-details p{font-weight:bold;color:#333}
        .driver-details p{margin-bottom:3px;color:#333}
        .total-payment{margin-top:20px;padding:15px;border:1px solid #ddd;border-radius:5px;background-color:#f0f8ff}
        .total-payment h3{color:#2a5298;margin-bottom:10px}
        .total-amount{font-size:24px;font-weight:bold;color:#FF9800;text-align:right}
        .payment-details{margin-top:10px}
        .payment-row{display:flex;justify-content:space-between;margin-bottom:5px;padding-bottom:5px;border-bottom:1px dashed #ddd}
        .alert{padding:15px;margin-bottom:20px;border:1px solid transparent;border-radius:4px}
        .alert-success{color:#3c763d;background-color:#dff0d8;border-color:#d6e9c6}
        .alert-error{color:#a94442;background-color:#f2dede;border-color:#ebccd1}
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
    
    <div class="booking-container">
        <h2>Formulir Pemesanan</h2>
        <?php if($message):?><div class="alert alert-success"><?php echo $message;?></div><?php endif;?>
        <?php if($error):?><div class="alert alert-error"><?php echo $error;?></div><?php endif;?>
        
        <div class="selected-package" id="selected-package" style="display:none;"></div>
        <div class="selected-vehicle" id="selected-vehicle" style="display:none;"></div>
        <div class="selected-driver" id="selected-driver" style="display:none;"></div>
        
        <form id="booking-form" method="POST" action="">
            <div class="form-group">
                <label for="customer-name">Nama Lengkap</label>
                <input type="text" id="customer-name" name="customer-name" required>
            </div>
            <div class="form-group">
                <label for="customer-email">Email</label>
                <input type="email" id="customer-email" name="customer-email" required>
            </div>
            <div class="form-group">
                <label for="customer-phone">No. Telepon</label>
                <input type="tel" id="customer-phone" name="customer-phone" required>
            </div>
            <div class="form-group">
                <label for="vehicle-type">Pilih Kendaraan</label>
                <select id="vehicle-type" name="vehicle-type" required>
                    <option value="">-- Pilih Kendaraan --</option>
                    <?php 
                    if($vehicles_result && $vehicles_result->num_rows > 0) {
                        while($vehicle = $vehicles_result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($vehicle['name']) . "' data-price='" . $vehicle['price_per_day'] . "' data-image='" . htmlspecialchars($vehicle['image']) . "'>" . htmlspecialchars($vehicle['name']) . "</option>";
                        }
                    } else {
                        echo "<option value='Toyota Avanza'>Toyota Avanza</option>";
                        echo "<option value='Toyota Hiace'>Toyota Hiace</option>";
                        echo "<option value='Toyota Alphard'>Toyota Alphard</option>";
                        echo "<option value='Bus Pariwisata'>Bus Pariwisata</option>";
                        echo "<option value='Isuzu Elf'>Isuzu Elf</option>";
                        echo "<option value='Honda Brio'>Honda Brio</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="destination-type">Pilih Destinasi</label>
                <select id="destination-type" name="destination-type" required>
                    <option value="">-- Pilih Destinasi --</option>
                    <?php 
                    if($destinations_result && $destinations_result->num_rows > 0) {
                        while($destination = $destinations_result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($destination['name']) . "' data-price='" . $destination['price_per_person'] . "' data-image='" . htmlspecialchars($destination['image']) . "'>" . htmlspecialchars($destination['name']) . "</option>";
                        }
                    } else {
                        echo "<option value='Paket Wisata Bali'>Paket Wisata Bali</option>";
                        echo "<option value='Paket Wisata Yogyakarta'>Paket Wisata Yogyakarta</option>";
                        echo "<option value='Paket Wisata Malang'>Paket Wisata Malang</option>";
                        echo "<option value='Paket Wisata Solo'>Paket Wisata Solo</option>";
                        echo "<option value='Paket Wisata Bandung'>Paket Wisata Bandung</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="driver-select">Pilih Sopir</label>
                <select id="driver-select" name="driver-select" required>
                    <option value="">-- Pilih Sopir --</option>
                    <?php 
                    if($drivers_result && $drivers_result->num_rows > 0) {
                        while($driver = $drivers_result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($driver['name']) . "' data-rating='" . htmlspecialchars($driver['rating']) . "' data-experience='" . htmlspecialchars($driver['experience']) . "' data-image='" . htmlspecialchars($driver['image']) . "'>" . htmlspecialchars($driver['name']) . "</option>";
                        }
                    } else {
                        $default_drivers = ["Yeonjun","Haruto","Taehyung","Hanbin","Zhang Hao","Jeno","Jaemin","Jisung","Jake","Jiwoong","Sunghoon","Ricky","Gunwook","Renjun","Jungwon"];
                        foreach($default_drivers as $driver) {
                            echo "<option value='" . $driver . "'>" . $driver . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pickup-date">Tanggal Pengambilan</label>
                <input type="date" id="pickup-date" name="pickup-date" required>
            </div>
            <div class="form-group">
                <label for="return-date">Tanggal Pengembalian</label>
                <input type="date" id="return-date" name="return-date" required>
            </div>
            
            <div class="total-payment" id="total-payment">
                <h3>Total Pembayaran</h3>
                <div class="payment-details">
                    <div class="payment-row">
                        <span>Harga Paket Wisata:</span>
                        <span id="package-price-display">-</span>
                    </div>
                    <div class="payment-row">
                        <span>Harga Sewa Kendaraan:</span>
                        <span id="vehicle-price-display">-</span>
                    </div>
                    <div class="payment-row">
                        <span>Durasi Sewa:</span>
                        <span id="rental-duration">0 hari</span>
                    </div>
                    <div class="payment-row">
                        <span>Jumlah Peserta:</span>
                        <input type="number" id="participant-count" name="participant-count" min="1" value="1" style="width:80px;text-align:right;">
                    </div>
                </div>
                <div class="total-amount">
                    Total: <span id="total-amount-display">Rp 0</span>
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="primary-button" id="confirm-button">Pesan Sekarang</button>
            </div>
        </form>
    </div>
    
    <footer>
        <p>&copy; 2025 Travel Lovindra. Semua hak dilindungi.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const vehicleData = {
                "Toyota Avanza": {price: "Rp 450.000/hari", priceValue: 450000, image: "image/Toyota Avanza.jpeg"},
                "Toyota Hiace": {price: "Rp 900.000/hari", priceValue: 900000, image: "image/Toyota Hiace.jpeg"},
                "Toyota Alphard": {price: "Rp 1.500.000/hari", priceValue: 1500000, image: "image/Toyota Alphard Hybrid.jpeg"},
                "Bus Pariwisata": {price: "Rp 2.000.000/hari", priceValue: 2000000, image: "image/Bus.jpeg"},
                "Isuzu Elf": {price: "Rp 800.000/hari", priceValue: 800000, image: "image/isuzu.jpeg"},
                "Honda Brio": {price: "Rp 350.000/hari", priceValue: 350000, image: "image/Brio.jpeg"}
            };
            
            const destinationData = {
                "Paket Wisata Bali": {price: "Rp 5.000.000/orang", priceValue: 5000000, image: "image/bali.jpeg"},
                "Paket Wisata Yogyakarta": {price: "Rp 3.000.000/orang", priceValue: 3000000, image: "image/jogja.jpeg"},
                "Paket Wisata Malang": {price: "Rp 2.500.000/orang", priceValue: 2500000, image: "image/malang.jpeg"},
                "Paket Wisata Solo": {price: "Rp 2.300.000/orang", priceValue: 2300000, image: "image/solo.jpeg"},
                "Paket Wisata Bandung": {price: "Rp 2.800.000/orang", priceValue: 2800000, image: "image/bandung.jpeg"}
            };
            
            const driverData = {
                "Yeonjun": {rating: "★★★★★ (4.9)", experience: "Pengalaman: 12 tahun", image: "image/yeonjun.jpeg"},
                "Haruto": {rating: "★★★★☆ (4.7)", experience: "Pengalaman: 8 tahun", image: "image/haruto.jpeg"},
                "Taehyung": {rating: "★★★★★ (4.9)", experience: "Pengalaman: 10 tahun", image: "image/taehyung.jpeg"},
                "Hanbin": {rating: "★★★★☆ (4.6)", experience: "Pengalaman: 7 tahun", image: "image/hanbin.jpeg"},
                "Zhang Hao": {rating: "★★★★☆ (4.5)", experience: "Pengalaman: 9 tahun", image: "image/zhang hao.jpeg"},
                "Jeno": {rating: "★★★★★ (5.0)", experience: "Pengalaman: 15 tahun", image: "image/jeno.jpeg"},
                "Jaemin": {rating: "★★★★★ (4.9)", experience: "Pengalaman: 14 tahun", image: "image/jaemin.jpeg"},
                "Jisung": {rating: "★★★★☆ (4.8)", experience: "Pengalaman: 11 tahun", image: "image/jisung.jpeg"},
                "Jake": {rating: "★★★★☆ (4.7)", experience: "Pengalaman: 9 tahun", image: "image/jake.jpeg"},
                "Jiwoong": {rating: "★★★★☆ (4.6)", experience: "Pengalaman: 8 tahun", image: "image/jiwoong.jpeg"},
                "Sunghoon": {rating: "★★★★★ (4.9)", experience: "Pengalaman: 13 tahun", image: "image/sunghoon.jpeg"},
                "Ricky": {rating: "★★★★☆ (4.8)", experience: "Pengalaman: 10 tahun", image: "image/ricky.jpeg"},
                "Gunwook": {rating: "★★★★☆ (4.7)", experience: "Pengalaman: 9 tahun", image: "image/gunwook.jpeg"},
                "Renjun": {rating: "★★★★★ (4.9)", experience: "Pengalaman: 11 tahun", image: "image/renjun.jpeg"},
                "Jungwon": {rating: "★★★★☆ (4.8)", experience: "Pengalaman: 9 tahun", image: "image/jungwon.jpeg"}
            };

            function calculateTotal() {
                const destinationSelect = document.getElementById('destination-type');
                const vehicleSelect = document.getElementById('vehicle-type');
                const pickupDate = new Date(document.getElementById('pickup-date').value);
                const returnDate = new Date(document.getElementById('return-date').value);
                const participantCount = parseInt(document.getElementById('participant-count').value) || 1;
                
                let duration = 0;
                if (!isNaN(pickupDate.getTime()) && !isNaN(returnDate.getTime())) {
                    duration = Math.ceil((returnDate - pickupDate) / (1000 * 60 * 60 * 24));
                    if (duration < 0) duration = 0;
                }
                
                document.getElementById('rental-duration').textContent = duration + ' hari';
                
                let packageTotal = 0;
                const selectedDestination = destinationSelect.value;
                let destinationPrice = 0;
                
                const destinationOption = destinationSelect.options[destinationSelect.selectedIndex];
                if (destinationOption && destinationOption.dataset.price) {
                    destinationPrice = parseInt(destinationOption.dataset.price);
                } else if (destinationData[selectedDestination]) {
                    destinationPrice = destinationData[selectedDestination].priceValue;
                }
                
                if (destinationPrice > 0) {
                    packageTotal = destinationPrice * participantCount;
                    document.getElementById('package-price-display').textContent = 
                        'Rp ' + destinationPrice.toLocaleString('id-ID') + ' × ' + participantCount + ' orang';
                } else {
                    document.getElementById('package-price-display').textContent = '-';
                }
                
                let vehicleTotal = 0;
                const selectedVehicle = vehicleSelect.value;
                let vehiclePrice = 0;
                
                const vehicleOption = vehicleSelect.options[vehicleSelect.selectedIndex];
                if (vehicleOption && vehicleOption.dataset.price) {
                    vehiclePrice = parseInt(vehicleOption.dataset.price);
                } else if (vehicleData[selectedVehicle]) {
                    vehiclePrice = vehicleData[selectedVehicle].priceValue;
                }
                
                if (vehiclePrice > 0 && duration > 0) {
                    vehicleTotal = vehiclePrice * duration;
                    document.getElementById('vehicle-price-display').textContent = 
                        'Rp ' + vehiclePrice.toLocaleString('id-ID') + ' × ' + duration + ' hari';
                } else {
                    document.getElementById('vehicle-price-display').textContent = '-';
                }
                
                const grandTotal = packageTotal + vehicleTotal;
                document.getElementById('total-amount-display').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
            }

            document.getElementById('vehicle-type').addEventListener('change', function() {
                const selectedVehicle = this.value;
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedVehicle) {
                    let vehicleInfo;
                    
                    if (selectedOption.dataset.price && selectedOption.dataset.image) {
                        vehicleInfo = {
                            price: 'Rp ' + parseInt(selectedOption.dataset.price).toLocaleString('id-ID') + '/hari',
                            image: selectedOption.dataset.image
                        };
                    } else if (vehicleData[selectedVehicle]) {
                        vehicleInfo = vehicleData[selectedVehicle];
                    }
                    
                    if (vehicleInfo) {
                        const vehicleInfoDiv = document.getElementById('selected-vehicle');
                        vehicleInfoDiv.style.display = 'block';
                        vehicleInfoDiv.innerHTML = `
                            <h3>Kendaraan Yang Dipilih</h3>
                            <div class="vehicle-preview">
                                <img src="${vehicleInfo.image}" alt="${selectedVehicle}">
                                <div class="vehicle-details">
                                    <h4>${selectedVehicle}</h4>
                                    <p>${vehicleInfo.price}</p>
                                </div>
                            </div>
                        `;
                    }
                } else {
                    document.getElementById('selected-vehicle').style.display = 'none';
                }
                calculateTotal();
            });

            document.getElementById('destination-type').addEventListener('change', function() {
                const selectedDestination = this.value;
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedDestination) {
                    let destinationInfo;
                    
                    if (selectedOption.dataset.price && selectedOption.dataset.image) {
                        destinationInfo = {
                            price: 'Rp ' + parseInt(selectedOption.dataset.price).toLocaleString('id-ID') + '/orang',
                            image: selectedOption.dataset.image
                        };
                    } else if (destinationData[selectedDestination]) {
                        destinationInfo = destinationData[selectedDestination];
                    }
                    
                    if (destinationInfo) {
                        const packageInfoDiv = document.getElementById('selected-package');
                        packageInfoDiv.style.display = 'block';
                        packageInfoDiv.innerHTML = `
                            <h3>Paket Wisata Yang Dipilih</h3>
                            <div class="package-preview">
                                <img src="${destinationInfo.image}" alt="${selectedDestination}">
                                <div class="package-details">
                                    <h4>${selectedDestination}</h4>
                                    <p>${destinationInfo.price}</p>
                                </div>
                            </div>
                        `;
                    }
                } else {
                    document.getElementById('selected-package').style.display = 'none';
                }
                calculateTotal();
            });

            document.getElementById('driver-select').addEventListener('change', function() {
                const selectedDriver = this.value;
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedDriver) {
                    let driverInfo;
                    
                    if (selectedOption.dataset.rating && selectedOption.dataset.experience && selectedOption.dataset.image) {
                        driverInfo = {
                            rating: selectedOption.dataset.rating,
                            experience: selectedOption.dataset.experience,
                            image: selectedOption.dataset.image
                        };
                    } else if (driverData[selectedDriver]) {
                        driverInfo = driverData[selectedDriver];
                    }
                    
                    if (driverInfo) {
                        const driverInfoDiv = document.getElementById('selected-driver');
                        driverInfoDiv.style.display = 'block';
                        driverInfoDiv.innerHTML = `
                            <h3>Sopir Yang Dipilih</h3>
                            <div class="driver-preview">
                                <img src="${driverInfo.image}" alt="${selectedDriver}">
                                <div class="driver-details">
                                    <h4>${selectedDriver}</h4>
                                    <p>${driverInfo.rating}</p>
                                    <p>${driverInfo.experience}</p>
                                </div>
                            </div>
                        `;
                    }
                } else {
                    document.getElementById('selected-driver').style.display = 'none';
                }
            });

            document.getElementById('pickup-date').addEventListener('change', calculateTotal);
            document.getElementById('return-date').addEventListener('change', calculateTotal);
            document.getElementById('participant-count').addEventListener('input', calculateTotal);
        });
    </script>
</body>
</html>