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

// Periksa apakah produk sudah diset di request POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['products'])) {
    $products = $_POST['products'];
    // Hitung total harga
    $totalPrice = 0;
    foreach ($products as $product) {
        $totalPrice += $product['harga'] * $product['quantity'];
    }
    $totalPayment = $totalPrice + 17500 + ($totalPrice * 0.1);
    $_SESSION['total_payment'] = $totalPayment;  // Simpan total payment dalam sesi
} else {
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/payment.css">
    <link rel="stylesheet" href="../css/popup.css">
    <title>Payment</title>
    <script src="../js/popup.js"></script>
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
        <div class="cart-container">
            <div class="address-section">
                <h2>Alamat Pengiriman</h2>
                <p><strong>Diena Mukafasyadiah</strong> (+62) 81223291532</p>
                <p>Kost Denok, RT.1/RW.1, Dusun Kimpulan, Desa Umbulmartani, Ngemplak (Belakang warungdenok), KAB. SLEMAN - NGEMPLAK, DI YOGYAKARTA, ID 55584</p>
            </div>
            <div class="products-section">
                <h2>Produk Dipesan</h2>
                <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <div class="store-info">
                        <span class="store-name">GlowRX</span>
                    </div>
                    <div class="product-details">
                        <img src="<?php echo htmlspecialchars($product['url_gambar']); ?>" alt="Product Image">
                        <div class="product-info">
                            <p><?php echo htmlspecialchars($product['nama']); ?></p>
                            <p class="product-price">Harga Satuan: Rp<?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                            <p class="product-quantity">Jumlah: <?php echo htmlspecialchars($product['quantity']); ?></p>
                            <p class="product-subtotal">Subtotal Produk: Rp<?php echo number_format($product['harga'] * $product['quantity'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="payment-section">
                <h2>Metode Pembayaran</h2>
                <div class="payment-method">
                    <p>COD - Cek Dulu</p>
                    <a href="#" class="change-payment">UBAH</a>
                </div>
            </div>
            <div class="total-section">
                <h2>Total Pesanan (<?php echo count($products); ?> Produk):</h2>
                <p>Rp<?php echo number_format($totalPrice, 0, ',', '.'); ?></p>
            </div>
        </div>
        <div class="checkout-container">
            <h2>Ringkasan Pembayaran</h2>
            <div class="summary-section">
                <p>Subtotal untuk Produk: <span class="price">Rp<?php echo number_format($totalPrice, 0, ',', '.'); ?></span></p>
                <p>Total Ongkos Kirim: <span class="price">Rp17.500</span></p>
                <p>Biaya Penanganan: <span class="price">Rp<?php echo number_format($totalPrice * 0.1, 0, ',', '.'); ?></span></p>
                <?php
                $totalPayment = $totalPrice + 17500 + ($totalPrice * 0.1);
                ?>
                <h3>Total Pembayaran: <span class="total-price">Rp<?php echo number_format($totalPayment, 0, ',', '.'); ?></span></h3>
            </div>
            <div class="place-order-section">
                <form action="confirm_payment.php" method="POST" onsubmit="handleFormSubmit(event)">
                    <?php foreach ($products as $product): ?>
                        <input type="hidden" name="products[<?php echo $product['id_produk']; ?>][id_produk]" value="<?php echo htmlspecialchars($product['id_produk']); ?>">
                        <input type="hidden" name="products[<?php echo $product['id_produk']; ?>][nama]" value="<?php echo htmlspecialchars($product['nama']); ?>">
                        <input type="hidden" name="products[<?php echo $product['id_produk']; ?>][deskripsi]" value="<?php echo htmlspecialchars($product['deskripsi']); ?>">
                        <input type="hidden" name="products[<?php echo $product['id_produk']; ?>][url_gambar]" value="<?php echo htmlspecialchars($product['url_gambar']); ?>">
                        <input type="hidden" name="products[<?php echo $product['id_produk']; ?>][harga]" value="<?php echo htmlspecialchars($product['harga']); ?>">
                        <input type="hidden" name="products[<?php echo $product['id_produk']; ?>][quantity]" value="<?php echo htmlspecialchars($product['quantity']); ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="total_payment" value="<?php echo htmlspecialchars($totalPayment); ?>">
                    <button type="submit" class="place-order-btn">Buat Pesanan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Pop-up HTML -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="hidePopup()">&times;</span>
            <p>Pesanan sedang diproses...</p>
        </div>
    </div>
</body>
</html>
<script>
    function showPopup() {
        document.getElementById('popup').style.display = 'block';
    }

    function hidePopup() {
        document.getElementById('popup').style.display = 'none';
    }

    function handleFormSubmit(event) {
        event.preventDefault();
        showPopup();
        setTimeout(function() {
            event.target.submit();
        }, 2000); // Delay for 2 seconds
    }

</script>