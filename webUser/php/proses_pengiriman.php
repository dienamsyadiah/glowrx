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

// Periksa apakah session 'products' telah di-set
if (!isset($_SESSION['products']) || !is_array($_SESSION['products'])) {
    // Jika tidak ada produk, bisa diarahkan ke halaman lain atau tampilkan pesan error
    die("Error: Produk tidak ditemukan dalam session.");
}

$products = $_SESSION['products'];
$totalPayment = $_SESSION['total_payment'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/pengiriman.css">
    <title>Proses Pengiriman</title>
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
        <h2>Proses Pengiriman</h2>
        <p>Pesanan Anda sedang diproses. Terima kasih telah berbelanja di Glow RX!</p>
        <div class="order-status">
            <h3>Status Pengiriman</h3>
            <ul>
                <li>Pemesanan diterima</li>
                <li>Packing pesanan</li>
                <li>Pesanan dikirim</li>
                <li>Pesanan sampai tujuan</li>
            </ul>
        </div>
        <div class="shipping-info">
            <h3>Informasi Pengiriman</h3>
            <p><strong>Diena Mukafasyadiah</strong> (+62) 81223291532</p>
            <p>Kost Denok, RT.1/RW.1, Dusun Kimpulan, Desa Umbulmartani, Ngemplak (Belakang warungdenok), KAB. SLEMAN - NGEMPLAK, DI YOGYAKARTA, ID 55584</p>
        </div>
        <div class="order-summary">
            <h3>Ringkasan Pesanan</h3>
            <ul>
                <?php foreach ($products as $product): ?>
                <li>
                    <p><?php echo htmlspecialchars($product['nama']); ?></p>
                    <p>Jumlah: <?php echo htmlspecialchars($product['quantity']); ?></p>
                    <p>Harga Satuan: Rp<?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total Pembayaran: Rp<?php echo number_format($totalPayment, 0, ',', '.'); ?></strong></p>
        </div>
    </div>
</body>
</html>
