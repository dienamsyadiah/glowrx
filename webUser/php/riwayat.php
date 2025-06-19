<?php
session_start();
require 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$id_pengguna = $_SESSION['id_pengguna'];

// Query untuk mengambil riwayat pesanan
$sql_riwayat = "SELECT rp.id_riwayat, rp.total_pembayaran, dp.id_produk, dp.nama_produk, dp.harga, dp.jumlah, p.url_gambar
                FROM riwayat_pesanan rp
                JOIN detail_pesanan dp ON rp.id_riwayat = dp.id_riwayat
                JOIN produk p ON dp.id_produk = p.id_produk
                WHERE rp.id_pengguna = ?";
$stmt_riwayat = $conn->prepare($sql_riwayat);
$stmt_riwayat->bind_param('i', $id_pengguna);
$stmt_riwayat->execute();
$result_riwayat = $stmt_riwayat->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    <link rel="stylesheet" href="../css/riwayat.css">
</head>
<body>
    <header id="header">
        <div class="container-fluid">
            <a href="index.php" class="logo">
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
        <h2>Riwayat Pesanan</h2>
        <div class="riwayat-container">
            <?php while ($row_riwayat = $result_riwayat->fetch_assoc()): ?>
                <div class="riwayat-item">
                    <div class="riwayat-content">
                        <h3>Pesanan ID: <?php echo $row_riwayat['id_riwayat']; ?></h3>
                        <p>Total Pembayaran: Rp<?php echo number_format($row_riwayat['total_pembayaran'], 0, ',', '.'); ?></p>

                        <!-- Detail pesanan -->
                        <div class="detail-pesanan">
                            <h4>Detail Pesanan:</h4>
                            <ul>
                                <li>
                                    <p><?php echo htmlspecialchars($row_riwayat['nama_produk']); ?></p>
                                    <p>Jumlah: <?php echo $row_riwayat['jumlah']; ?></p>
                                    <p>Harga Satuan: Rp<?php echo number_format($row_riwayat['harga'], 0, ',', '.'); ?></p>
                                </li>
                                <!-- Tambahkan perulangan atau pengulangan untuk setiap produk -->
                            </ul>
                        </div>
                    </div>
                    <!-- Tampilkan gambar produk -->
                    <img src="<?php echo $row_riwayat['url_gambar']; ?>" alt="Product Image" class="product-image">
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
