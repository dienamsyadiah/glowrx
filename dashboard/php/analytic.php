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
        </div>

        <div class="main-content">
            <header>
                <h1>Analytic</h1>
            </header>
            <div class="charts">
                <div class="row">
                    <div class="chart-item">
                        <h2>Gender</h2>
                        <canvas id="genderChart"></canvas>
                    </div>
                    <div class="chart-item">
                        <h2>Type kulit</h2>
                        <canvas id="skinTypeChart"></canvas>
                    </div>
                    <div class="chart-kulit">
                        <h2>Masalah Kulit Pengguna</h2>
                        <canvas id="problemSkinChart"></canvas>
                    </div>

                </div>
                <div class="row">
                    <div class="chart-produk">
                        <h2>Bahan Poko Produk</h2>
                        <canvas id="BahanChart"></canvas>
                    </div>
                    <div class="rekomAi">
                        <h2>Gunakan Rekomendasi AI Untuk</h2>
                        <div class="isi">
                            <div class="isi1">
                                <p>Klik Rekomendasi AI untuk menggunakan rekomendasi AI secara langsung.</p>
                                <a href="Rekomendasi.php">di sini</a>
                            </div>
                            <div class="isi1">
                                <img class="gambarRokomendasi" src="../img/robot.png" alt="AI recommendation illustration">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/analytic.js"></script>
</body>
</html>
