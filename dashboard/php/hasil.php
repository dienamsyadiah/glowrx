<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['username']; // Ambil username dari sesi
$status = $_SESSION['status'];
$products = isset($_GET['products']) ? json_decode(urldecode($_GET['products']), true) : [];
$recommendations = isset($_GET['recommendations']) ? urldecode($_GET['recommendations']) : '';

if (isset($recommendations)) {
    // Simpan data ke dalam tabel recommendations
    $stmt = $conn->prepare("INSERT INTO recommendations (username, recommendations) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $recommendations);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Rekomendasi Produk</title>
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
                <div><a class="sidebar-item" href="riwayat.php"><i class='bx bx-history'></i>History</a></div>
                <div><a class="sidebar-item" href="../index.php"><i class='bx bxs-log-out'></i>Logout</a></div>
            </div>
            <div class="user-info">
                <img src="../img/manager.jpg" alt="User">
                <div>
                    <span><?php echo htmlspecialchars($username); ?></span><br>
                    <span><?php echo htmlspecialchars($status); ?></span>
                </div>
            </div>
        </div>
        <div class="main-content">
            <header>
                <h1>Hasil Rekomendasi Produk</h1>
            </header>
            <div class="container">
                <div class="recommendation-info">
                    <h2>Rekomendasi:</h2>
                    <?php
                    if (isset($_GET['recommendations'])) {
                        $recommendations = urldecode($_GET['recommendations']);
                        // Misalkan rekomendasi dipisahkan oleh baris baru
                        $recommendationArray = preg_split('/\r\n|[\r\n]/', $recommendations);
                        foreach ($recommendationArray as $recommendation) {
                            echo '<p>' . htmlspecialchars($recommendation) . '</p>';
                        }
                    } else {
                        echo '<p>Tidak ada rekomendasi.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
