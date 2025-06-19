<?php 
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username']; // Ambil username dari sesi
$status = $_SESSION['status'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowRX Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/collect.js/4.36.1/collect.min.js" integrity="sha512-aub0tRfsNTyfYpvUs0e9G/QRsIDgKmm4x59WRkHeWUc3CXbdiMwiMQ5tTSElshZu2LCq8piM/cbIsNwuuIR4gA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0/dist/chartjs-plugin-datalabels.min.js"></script>
</head>
<body>
    <div class="page">
        <div class="sidebar">
            <div class="logo">
                <img src="../img/logo.png" alt="">
                <span>GlowRX</span>
            </div>
            <div class="menu">
                <div><a class="sidebar-item" href="homepage.php"><i class='bx bxs-dashboard'></i>Dashboard</a></div>
                <div><a class="sidebar-item" href="analytic.php"><i class='bx bx-bar-chart-alt-2'></i>Analytics</a></div>
                <div><a class="sidebar-item" href="produk.php"><i class='bx bx-store-alt'></i>Product</a></div>
                <div><a class="sidebar-item" href="ai.php"><i class='bx bx-bulb'></i>Recommendation AI</a></div>
                <div><a class="sidebar-item" href="riwayat.php"><i class='bx bx-history'></i>History</a></div>
                <div><a class="sidebar-item" href="report.php"><i class='bx bx-bar-chart-alt'></i>Report</a></div>
                <div><a class="sidebar-item" href="../index.php"><i class='bx bxs-log-out'></i>Logout</a></div>
            </div>

            <div class="user-info">
                <img src="../img/manager.jpg" alt="User">
                <div>
                    <span><?php echo htmlspecialchars($username); ?></span>
                    <span><?php echo htmlspecialchars($status); ?></span>
                </div>
            </div>
        </div>
        <div class="main-content">
            <a class="nav-button" href="ai.php">Chat Bot</a>
            <header>
                <h1>Recommendation AI</h1>
                <a class="riwayat" href="riwayat.php">Riwayat</a>
            </header>
            <div class="container">
            <form method="POST" action="eksekusiRekomendasi.php">
                    <div class="form-section"> 
                        <!-- type kulit kulit -->
                        <label for="product-type">Jenis kulit</label>
                        <select id="product-type" name="product-type">
                            <option>Berminyak</option>
                            <option>Kering</option>
                            <option>Kombinasi</option>
                            <option>Normal</option>
                        </select>
                    </div>
                    <div class="form-section"> 
                        <label for="main-ingredient">Bahan Utama Produk</label>
                        <select id="main-ingredient" name="main-ingredient">
                            <option value="Vitamin C">Vitamin C</option>
                            <option value="Asam Hialuronat">Asam Hialuronat</option>
                            <option value="Niacinamide">Niacinamide</option>
                            <option value="Ekstrak Aloe Vera">Ekstrak Aloe Vera</option>
                            <option value="Retinol">Retinol</option>
                            <option value="Asam Salisilat">Asam Salisilat</option>
                            <option value="Ekstrak Teh Hijau">Ekstrak Teh Hijau</option>
                            <option value="Shea Butter">Shea Butter</option>
                            <option value="Peptida">Peptida</option>
                            <option value="Ceramides">Ceramides</option>
                        </select>
                    </div>

                    <div class="form-section">
                        <!-- masalah kulit -->
                        <label for="skin-problem">Masalah Kulit</label>
                        <div class="checkbox-group" id="skin-problem">
                            <label>
                                <input type="checkbox" id="jerawat" name="skin-problem[]" value="Jerawat"> Jerawat
                            </label>
                            
                            <label>
                                <input type="checkbox" id="kemerahan" name="skin-problem[]" value="Kemerahan"> Kemerahan
                            </label>
                            
                            <label>
                                <input type="checkbox" id="kulitkusam" name="skin-problem[]" value="KulitKusam"> Kulit Kusam
                            </label>
                            
                            <label>
                                <input type="checkbox" id="penuaan" name="skin-problem[]" value="Penuaan"> Penuaan
                            </label>
                            
                            <label>
                                <input type="checkbox" id="kulitsensitif" name="skin-problem[]" value="KulitSensitif"> Kulit Sensitif
                            </label>
                            
                            <label>
                                <input type="checkbox" id="berkomedo" name="skin-problem[]" value="Berkomedo"> Berkomedo
                            </label>
                            
                            <label>
                                <input type="checkbox" id="kulittidakmerata" name="skin-problem[]" value="KulitTidakMerata"> Kulit Tidak Merata
                            </label>
                            
                            <label>
                                <input type="checkbox" id="flekhitam" name="skin-problem[]" value="FlekHitam"> Flek Hitam
                            </label>
                            
                            <label>
                                <input type="checkbox" id="keriput" name="skin-problem[]" value="Keriput"> Keriput
                            </label>
                        </div>
                    </div>
                    <div class="form-section">
                        <label for="product-description">Deskripsi Produk</label>
                        <textarea id="product-description" name="product-description"></textarea>
                    </div>
                    <button type="submit" class="nav-button">Generate</button>
                </form>
                <!-- <a class="generate-button" href="ai.html">Generate</a> -->
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>