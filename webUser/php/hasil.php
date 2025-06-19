<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/hasil.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Hasil Rekomendasi Produk</title>
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
                <i class="mobile-nav-toggle bi bi-list"></i>
            </nav>
        </div>
    </header>
    <div class="main">
        <div class="main-content">
            <header>
                <h1>Hasil Rekomendasi Produk</h1>
            </header>
            <div class="container">
                <div class="content-wrapper">
                    <div class="product-content">
                        <?php
                        if (isset($_GET['recommendations']) && isset($_GET['product'])) {
                            $recommendations = urldecode($_GET['recommendations']);
                            $productName = urldecode($_GET['product']);

                            // Ambil detail produk dari database
                            include "koneksi.php";
                            $queryProduk = "SELECT * FROM produk WHERE nama = ?";
                            $stmtProduk = $conn->prepare($queryProduk);
                            $stmtProduk->bind_param('s', $productName);
                            $stmtProduk->execute();
                            $resultProduk = $stmtProduk->get_result();

                            if ($row = $resultProduk->fetch_assoc()) {
                                echo '<div class="product-info">';
                                echo '<a href="detail_produk.php?id_produk=' . htmlspecialchars($row['id_produk']) . '" class="product-link">';
                                echo '<img src="../img/' . htmlspecialchars($row['url_gambar']) . '" alt="Product Image" class="product-image">';
                                echo '</a>';
                                echo '<div class="product-details">';
                                echo '<h2 class="product-name">' . htmlspecialchars($row['nama']) . '</h2>';
                                echo '<p class="product-price">Rp ' . number_format($row['harga'], 0, ',', '.') . '</p>';
                                echo '<form action="detail_produk.php" method="GET" class="btn-buy-form">';
                                echo '<input type="hidden" name="id_produk" value="' . htmlspecialchars($row['id_produk']) . '">';
                                echo '<button type="submit" class="btn-buy">Buy <i class="bx bxs-credit-card"></i></button>';
                                echo '</form>';
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '<p>Produk tidak ditemukan.</p>';
                            }
                        } else {
                            echo '<p>Tidak ada rekomendasi.</p>';
                        }
                        ?>
                    </div>
                    <div class="main-content-description">
                        <?php
                        if (isset($_GET['recommendations']) && isset($_GET['product'])) {
                            $recommendations = urldecode($_GET['recommendations']);
                            $recommendationArray = preg_split('/\r\n|[\r\n]/', $recommendations);
                            $firstRecommendation = reset($recommendationArray);

                            // Ambil detail produk pertama dari database
                            include "koneksi.php";
                            $queryProduk = "SELECT * FROM produk WHERE nama = ?";
                            $stmtProduk = $conn->prepare($queryProduk);
                            $stmtProduk->bind_param('s', $productName);
                            $stmtProduk->execute();
                            $resultProduk = $stmtProduk->get_result();

                            if ($row = $resultProduk->fetch_assoc()) {
                                echo '<div class="description-info">';
                                echo '<h3>Deskripsi Produk</h3>';
                                echo '<p>' . htmlspecialchars($row['deskripsi']) . '</p>';

                                if (!empty($row['manfaat'])) {
                                    echo '<h3>Manfaat</h3>';
                                    echo '<p>' . htmlspecialchars($row['manfaat']) . '</p>';
                                } else {
                                    echo '<h3>Manfaat</h3>';
                                    echo '<p>Manfaat produk belum tersedia.</p>';
                                }

                                if (!empty($row['cara_penggunaan'])) {
                                    echo '<h3>Cara Penggunaan</h3>';
                                    echo '<p>' . htmlspecialchars($row['cara_penggunaan']) . '</p>';
                                } else {
                                    echo '<h3>Cara Penggunaan</h3>';
                                    echo '<p>Cara penggunaan produk belum tersedia.</p>';
                                }

                                echo '<h3>Rekomendasi AI</h3>';
                                echo '<p>' . nl2br(htmlspecialchars($recommendations)) . '</p>';

                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
