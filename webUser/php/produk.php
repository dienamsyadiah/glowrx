<?php
session_start();
// Periksa apakah pengguna sudah login
if (!isset($_SESSION['nama'])) {
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/produk.css">
    <title>Produk GlowRx</title>
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
                    <?php echo htmlspecialchars($username); ?>
                </a>
                <i class="mobile-nav-toggle bi bi-list"></i>
            </nav>
        </div>
    </header>
    <div class="main">
        <h1 class="main-title">Produk GlowRx</h1>
        <div class="main-produk">
            <?php
            // Include the database connection file
            include 'koneksi.php';

            // Query to fetch product data
            $query = "SELECT id_produk, nama, deskripsi, url_gambar, harga FROM produk";
            $result = $conn->query($query);

            // Check if there are any products
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<a href="detail_produk.php?id_produk='.htmlspecialchars($row['id_produk']).'" class="product-link">';
                    echo '<img src="../img/'.htmlspecialchars($row['url_gambar']).'" alt="Product Image" class="product-image">';
                    echo '<div class="product-details">';
                    echo '<h2 class="product-name">'.htmlspecialchars($row['nama']).'</h2>';
                    echo '<p class="product-description">'.htmlspecialchars($row['deskripsi']).'</p>';
                    echo '<p class="product-price">Rp '.number_format($row['harga'], 0, ',', '.').'</p>';
                    echo '</div>';
                    echo '</a>';
                    echo '<div class="button-group">';
                    echo '<form action="payment.php" method="POST" class="btn-buy-form">';
                    echo '<input type="hidden" name="products[0][id_produk]" value="'.htmlspecialchars($row['id_produk']).'">';
                    echo '<input type="hidden" name="products[0][nama]" value="'.htmlspecialchars($row['nama']).'">';
                    echo '<input type="hidden" name="products[0][deskripsi]" value="'.htmlspecialchars($row['deskripsi']).'">';
                    echo '<input type="hidden" name="products[0][url_gambar]" value="'.htmlspecialchars($row['url_gambar']).'">';
                    echo '<input type="hidden" name="products[0][harga]" value="'.htmlspecialchars($row['harga']).'">';
                    echo '<input type="hidden" name="products[0][quantity]" value="1">';
                    echo '<button type="submit" class="btn-buy">Buy <i class="bx bxs-credit-card"></i></button>';
                    echo '</form>';
                    echo '<form action="cart.php" method="POST" class="btn-cart-form">';
                    echo '<input type="hidden" name="id_produk" value="'.htmlspecialchars($row['id_produk']).'">';
                    echo '<button type="submit" class="btn-cart">Add <i class="bx bx-cart-add"></i></button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }

            // Close the database connection
            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
