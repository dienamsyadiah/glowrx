<?php
session_start();
include 'koneksi.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize product ID (you need to pass this through URL or some form of input)
$id_produk = isset($_GET['id_produk']) ? $_GET['id_produk'] : null;

// Ensure $id_produk has a valid value
if ($id_produk !== null) {
    // Query to fetch product details based on ID
    $query = "SELECT p.id_produk, p.nama, p.deskripsi, p.url_gambar, p.harga, 
                     b.nama AS nama_bahan, 
                     jp.nama_jenis AS jenis_produk, 
                     tk.tipe AS tipe_kulit, 
                     mk.nama_masalah AS masalah_kulit
              FROM produk AS p
              LEFT JOIN bahan AS b ON p.id_bahan = b.id_bahan
              LEFT JOIN jenis_produk AS jp ON p.id_jenis_produk = jp.id_jenis_produk
              LEFT JOIN tipekulit AS tk ON p.id_tipe_kulit = tk.id_tipe_kulit
              LEFT JOIN masalah_kulit AS mk ON p.id_masalah_kulit = mk.id_masalah_kulit
              WHERE p.id_produk = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_produk);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there is exactly one product found
    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
            <link rel="stylesheet" href="../css/detail_produk.css">
            <title>Detail Produk - <?php echo htmlspecialchars($row['nama']); ?></title>
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
                        <a class="profil" href="profile.php">
                            <img src="../img/profil.png" alt="Profile Picture" class="profile-pic">
                            <?php echo htmlspecialchars($username); ?>
                        </a>
                        <i class="mobile-nav-toggle bi bi-list"></i>
                    </nav>
                </div>
            </header>
            <div class="main">
                <a class="back-link" href="produk.php"><i class='bx bx-arrow-back'></i> Back</a>
                <div class="container">
                    <div class="product-detail">
                        <div class="product-image">
                            <img src="../img/<?php echo htmlspecialchars($row['url_gambar']); ?>" alt="Product Image">
                        </div>
                        <div class="product-info">
                            <h2 class="product-name"><?php echo htmlspecialchars($row['nama']); ?></h2>
                            <p class="product-description"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                            <p class="product-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                            <p class="product-detail-info"><strong>Bahan:</strong> <?php echo htmlspecialchars($row['nama_bahan']); ?></p>
                            <p class="product-detail-info"><strong>Jenis Produk:</strong> <?php echo htmlspecialchars($row['jenis_produk']); ?></p>
                            <p class="product-detail-info"><strong>Tipe Kulit:</strong> <?php echo htmlspecialchars($row['tipe_kulit']); ?></p>
                            <p class="product-detail-info"><strong>Masalah Kulit:</strong> <?php echo htmlspecialchars($row['masalah_kulit']); ?></p>
                            <div class="button-group">
                                <form action="payment.php" method="POST" class="btn-buy-form">
                                    <input type="hidden" name="products[0][id_produk]" value="<?php echo htmlspecialchars($row['id_produk']); ?>">
                                    <input type="hidden" name="products[0][nama]" value="<?php echo htmlspecialchars($row['nama']); ?>">
                                    <input type="hidden" name="products[0][deskripsi]" value="<?php echo htmlspecialchars($row['deskripsi']); ?>">
                                    <input type="hidden" name="products[0][url_gambar]" value="<?php echo htmlspecialchars($row['url_gambar']); ?>">
                                    <input type="hidden" name="products[0][harga]" value="<?php echo htmlspecialchars($row['harga']); ?>">
                                    <input type="hidden" name="products[0][quantity]" value="1">
                                    <button type="submit" class="btn-buy">Buy <i class="bx bxs-credit-card"></i></button>
                                </form>
                                <form action="cart.php" method="POST" class="btn-cart-form">
                                    <input type="hidden" name="id_produk" value="<?php echo htmlspecialchars($row['id_produk']); ?>">
                                    <input type="hidden" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>">
                                    <input type="hidden" name="deskripsi" value="<?php echo htmlspecialchars($row['deskripsi']); ?>">
                                    <input type="hidden" name="url_gambar" value="<?php echo htmlspecialchars($row['url_gambar']); ?>">
                                    <input type="hidden" name="harga" value="<?php echo htmlspecialchars($row['harga']); ?>">
                                    <button type="submit" class="btn-cart">Add <i class="bx bx-cart-add"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="recommendation">
                    <h2 class="section-title">Rekomendasi Produk Lainnya</h2>
                    <div class="product-list">
                        <?php
                        // Query to fetch related products based on some criteria (example: category or type)
                        $related_query = "SELECT id_produk, nama, deskripsi, url_gambar, harga 
                                          FROM produk 
                                          WHERE id_produk != ? LIMIT 5";
                        $related_stmt = $conn->prepare($related_query);
                        $related_stmt->bind_param("i", $id_produk);
                        $related_stmt->execute();
                        $related_result = $related_stmt->get_result();

                        // Display related products
                        if ($related_result && $related_result->num_rows > 0) {
                            while ($related_row = $related_result->fetch_assoc()) {
                                ?>
                                <a href="detail_produk.php?id_produk=<?php echo htmlspecialchars($related_row['id_produk']); ?>" class="product-card">
                                    <img src="../img/<?php echo htmlspecialchars($related_row['url_gambar']); ?>" alt="Recommendation Product Image" class="product-image">
                                    <div class="product-details">
                                        <h3 class="product-name"><?php echo htmlspecialchars($related_row['nama']); ?></h3>
                                        <p class="product-description"><?php echo htmlspecialchars($related_row['deskripsi']); ?></p>
                                        <p class="product-price">Rp <?php echo number_format($related_row['harga'], 0, ',', '.'); ?></p>
                                        <div class="button-group">
                                            <form action="payment.php" method="POST" class="btn-buy-form">
                                                <input type="hidden" name="products[0][id_produk]" value="<?php echo htmlspecialchars($related_row['id_produk']); ?>">
                                                <input type="hidden" name="products[0][nama]" value="<?php echo htmlspecialchars($related_row['nama']); ?>">
                                                <input type="hidden" name="products[0][deskripsi]" value="<?php echo htmlspecialchars($related_row['deskripsi']); ?>">
                                                <input type="hidden" name="products[0][url_gambar]" value="<?php echo htmlspecialchars($related_row['url_gambar']); ?>">
                                                <input type="hidden" name="products[0][harga]" value="<?php echo htmlspecialchars($related_row['harga']); ?>">
                                                <input type="hidden" name="products[0][quantity]" value="1">
                                                <button type="submit" class="btn-buy">Buy <i class="bx bxs-credit-card"></i></button>
                                            </form>
                                            <form action="cart.php" method="POST" class="btn-cart-form">
                                                <input type="hidden" name="id_produk" value="<?php echo htmlspecialchars($related_row['id_produk']); ?>">
                                                <input type="hidden" name="nama" value="<?php echo htmlspecialchars($related_row['nama']); ?>">
                                                <input type="hidden" name="deskripsi" value="<?php echo htmlspecialchars($related_row['deskripsi']); ?>">
                                                <input type="hidden" name="url_gambar" value="<?php echo htmlspecialchars($related_row['url_gambar']); ?>">
                                                <input type="hidden" name="harga" value="<?php echo htmlspecialchars($related_row['harga']); ?>">
                                                <button type="submit" class="btn-cart">Add <i class="bx bx-cart-add"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </a>
                                <?php
                            }
                        } else {
                            echo '<p>Tidak ada produk terkait.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo '<p>Produk tidak ditemukan.</p>';
    }

    // Close the database connections
    $stmt->close();
    $related_stmt->close();
    $conn->close();
} else {
    echo '<p>Product ID is missing.</p>';
}
?>
