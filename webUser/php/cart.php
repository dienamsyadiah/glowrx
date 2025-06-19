<?php
session_start();
require 'koneksi.php';

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle addition of items to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_produk'])) {
    $id_produk = $_POST['id_produk'];
    if (isset($_SESSION['cart'][$id_produk])) {
        $_SESSION['cart'][$id_produk]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$id_produk] = ['quantity' => 1];
    }
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $id_produk = $_POST['id_produk'];
    $new_quantity = $_POST['quantity'];
    if ($new_quantity > 0) {
        $_SESSION['cart'][$id_produk]['quantity'] = $new_quantity;
    } else {
        unset($_SESSION['cart'][$id_produk]);
    }
}

// Handle removal of items from cart
if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    unset($_SESSION['cart'][$removeId]);
}

// Fetch product details from the database
$productIds = array_keys($_SESSION['cart']);
$products = [];

if (!empty($productIds)) {
    $ids = implode(',', array_map('intval', $productIds));
    $sql = "SELECT * FROM produk WHERE id_produk IN ($ids)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[$row['id_produk']] = $row;
        }
    }
}


// Calculate total price
$totalPrice = 0;
foreach ($_SESSION['cart'] as $id => $item) {
    $totalPrice += $item['quantity'] * $products[$id]['harga'];
}
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
    <link rel="stylesheet" href="../css/cart.css">
    <title>Shopping Cart</title>
    <script>
        function updateQuantity(id, operation) {
            const quantityInput = document.getElementById('quantity-' + id);
            let quantity = parseInt(quantityInput.value);

            if (operation === 'increment') {
                quantity += 1;
            } else if (operation === 'decrement' && quantity > 1) {
                quantity -= 1;
            }

            quantityInput.value = quantity;
            document.getElementById('form-' + id).submit();
        }
    </script>
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
        <div class="container">
            <div class="cart">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga Satuan</th>
                            <th>Kuantitas</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $products[$id]['url_gambar']; ?>" alt="Product Image">
                                <div class="item-details">
                                    <div class="store-name"><?php echo $products[$id]['nama']; ?></div>
                                    <div class="product-name"><?php echo $products[$id]['deskripsi']; ?></div>
                                </div>
                            </td>
                            <td>Rp<?php echo number_format($products[$id]['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <div class="item-quantity">
                                    <form id="form-<?php echo $id; ?>" action="cart.php" method="POST">
                                        <input type="hidden" name="id_produk" value="<?php echo $id; ?>">
                                        <input type="hidden" name="update_quantity" value="1">
                                        <button type="button" onclick="updateQuantity('<?php echo $id; ?>', 'decrement')">-</button>
                                        <input type="text" id="quantity-<?php echo $id; ?>" name="quantity" value="<?php echo $item['quantity']; ?>" readonly>
                                        <button type="button" onclick="updateQuantity('<?php echo $id; ?>', 'increment')">+</button>
                                    </form>
                                </div>
                            </td>
                            <td>Rp<?php echo number_format($item['quantity'] * $products[$id]['harga'], 0, ',', '.'); ?></td>
                            <td><a href="cart.php?remove=<?php echo $id; ?>">Hapus</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="cart-summary">
                <div class="total-price">Total (<?php echo count($_SESSION['cart']); ?> produk): Rp<?php echo number_format($totalPrice, 0, ',', '.'); ?></div>
                <form action="payment.php" method="POST">
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <input type="hidden" name="products[<?php echo $id; ?>][id_produk]" value="<?php echo $id; ?>">
                        <input type="hidden" name="products[<?php echo $id; ?>][nama]" value="<?php echo $products[$id]['nama']; ?>">
                        <input type="hidden" name="products[<?php echo $id; ?>][deskripsi]" value="<?php echo $products[$id]['deskripsi']; ?>">
                        <input type="hidden" name="products[<?php echo $id; ?>][url_gambar]" value="<?php echo $products[$id]['url_gambar']; ?>">
                        <input type="hidden" name="products[<?php echo $id; ?>][harga]" value="<?php echo $products[$id]['harga']; ?>">
                        <input type="hidden" name="products[<?php echo $id; ?>][quantity]" value="<?php echo $item['quantity']; ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="checkout-button">Checkout</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
