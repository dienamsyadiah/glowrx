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
            <header>
                <h1>Produk</h1>
            </header>
            <div class="card-product">
                <h2>List Product</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Penjualan</th>
                                <th>Tanggal Rilis</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $query = "SELECT p.id_produk, p.nama, p.url_gambar, MIN(p.tanggal_rilis) AS tanggal_rilis, COALESCE(SUM(j.jumlah), 0) AS jumlah 
                                    FROM produk p 
                                    LEFT JOIN penjualan j ON p.id_produk = j.id_produk 
                                    GROUP BY p.id_produk
                                    ORDER BY YEAR(MIN(p.tanggal_rilis)) DESC, MONTH(MIN(p.tanggal_rilis)) ASC"; // Mengurutkan berdasarkan tahun terbaru dan bulan terkecil ke terbesar

                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td><img src='../img/{$row['url_gambar']}' alt='{$row['nama']}' width='100'></td>
                                            <td>{$row['nama']}</td>
                                            <td>{$row['jumlah']}</td>
                                            <td>{$row['tanggal_rilis']}</td>
                                            <td><a class='nav-button' href='produk_detail.php?id_produk={$row['id_produk']}'>Detail</a></td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Tidak ada data yang ditemukan</td></tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
