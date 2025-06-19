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
                <h1>Report</h1>
            </header>
            <section class="card-product">
                <h2>Grafik Pemasukan dan Pengeluaran</h2>
                <div id="filter-container">
                    <a href="?filter=bulanan" class="filter-button">Bulanan</a>
                    <a href="?filter=tahunan" class="filter-button">Tahunan</a>
                </div>
                <?php
                        // Sisipkan file koneksi.php untuk mendapatkan koneksi ke database
                        include 'koneksi.php';

                        // Ambil nilai tahun dari parameter URL jika ada
                        $year = isset($_GET['year']) ? $_GET['year'] : date('Y'); // Menggunakan tahun saat ini jika tidak ada parameter

                        // Query untuk mendapatkan tahun-tahun unik dari data
                        $sql = "SELECT DISTINCT YEAR(tanggal_penjualan) AS tahun FROM penjualan ORDER BY tahun DESC";
                        $result = $conn->query($sql);

                        // Buat dropdown tahun
                        ?>
                        <div class="filter-select-container">
                            <label for="year-select">Pilih Tahun:</label>
                            <select id="year-select" class="filter-select" onchange="location = this.value;">
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $tahun = $row['tahun'];
                                        echo '<option value="?filter=bulanan&year=' . $tahun . '"';
                                        if ($year == $tahun) {
                                            echo ' selected';
                                        }
                                        echo '>' . $tahun . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <?php
                        // Tutup koneksi
                        $conn->close();
                        ?>

                <canvas id="reportChart" width="400" height="200"></canvas>
            </section>
        
            <section class="card-product">
                <h2>Pemasukan dan Pengeluaran</h2>
                <div class="buttons">
                    <button id="btn-all" onclick="filterTable('all')">All Data</button>
                    <button id="btn-pemasukan" onclick="filterTable('pemasukan')">Pemasukan</button>
                    <button id="btn-pengeluaran" onclick="filterTable('pengeluaran')">Pengeluaran</button>
                </div>
                <div class="table-responsive" id="tableWrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include 'koneksi.php';

                            $query = "
                                SELECT p.nama, pm.jumlah, pm.tanggal_pemasukan AS tanggal, 'pemasukan' AS tipe, pm.deskripsi 
                                FROM pemasukan pm 
                                JOIN produk p ON pm.id_produk = p.id_produk
                                UNION ALL
                                SELECT p.nama, pg.jumlah, pg.tanggal_pengeluaran AS tanggal, 'pengeluaran' AS tipe, pg.deskripsi 
                                FROM pengeluaran pg 
                                JOIN produk p ON pg.id_produk = p.id_produk
                                ORDER BY tanggal DESC
                            ";

                            $result = $conn->query($query);

                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['nama']}</td>
                                        <td>{$row['jumlah']}</td>
                                        <td>{$row['tanggal']}</td>
                                        <td>{$row['tipe']}</td>
                                        <td>{$row['deskripsi']}</td>
                                    </tr>";
                            }

                            $conn->close();
                            ?>

                        </tbody>
                    </table>
                </div>
                <div class="table-navigation">
                    <button class="nav-button" id="prevButton"><i class='bx bx-chevron-left'></i>Previous</button>
                    <button class="nav-button" id="nextButton">Next<i class='bx bx-chevron-right'></i></button>
                </div>
            </section>

        </div>
    </div>
    
    <script src="../js/report.js"></script>
</body>
</html>
