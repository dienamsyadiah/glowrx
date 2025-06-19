<?php
session_start();
// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Redirect ke halaman login jika belum login
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/rekomen.css">
    <title>Document</title>
</head>
<body>
    <header id="header">
        <div class="container-fluid">
            <a href="index.html" class="logo">
                <h1 class="site-name">Glow RX</h1>
            </a>
    
            <nav id="main-nav" class="main-nav">
                <ul>
                    <li><a href="produk.php">Produk</a></li>
                    <li><a href="rekomendasiAI.php">Rekomendasi Produk</a></li>
                    <li><a href="cart.php">Keranjang</a></li>
                    <li><a href="riwayat.php">Riwayat</a></li>
                </ul>
                <a class="profil" href="profile.php" class="profile-link">
                    <img src="../img/profil.png" alt="Profile Picture" class="profile-pic">
                    <?php echo $username; ?>
                </a>
                <i class="mobile-nav-toggle bi bi-list"></i>
            </nav>
        </div>
    </header>
    <div class="main">
        <div class="main-content">
            <header>
                <h1>Recommendation AI</h1>
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
    
</body>
</html>