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
                    <span><?php echo htmlspecialchars($username); ?></span> <br>
                    <span><?php echo htmlspecialchars($status); ?></span>
                </div>
            </div>
        </div>
        <div class="main-content">
            <header>
                <h1>Dashboard</h1>
            </header>
            <section class="stats">
                <div class="stat-card">
                    <div class="logo">
                        <i class='bx bx-user'></i>
                    </div>
                    <div class="isi-stats">
                        <h4>Pengguna Aktif</h4>
                        <?php
                            // Ambil data jumlah user
                            $result = $conn->query("SELECT COUNT(*) AS total_users FROM pengguna");
                            if ($result) {
                                $row = $result->fetch_assoc();
                                echo "<p> " . $row['total_users'] . "</p>";
                            } else {
                                echo "<p>Error retrieving data</p>";
                            }
                        ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="logo">
                        <i class='bx bxl-deezer'></i>
                    </div>
                    <div class="isi-stats">
                        <h4>Produk Terlaris</h4>
                        <?php
                            $result = $conn->query("SELECT p.nama, SUM(j.jumlah) AS total_penjualan 
                                                    FROM penjualan j 
                                                    JOIN produk p ON j.id_produk = p.id_produk 
                                                    GROUP BY p.id_produk 
                                                    ORDER BY total_penjualan DESC 
                                                    LIMIT 1");
                            if ($result) {
                                $row = $result->fetch_assoc();
                                echo "<p>" . $row['nama']  . "</p>";
                            } else {
                                echo "<p>Error retrieving data</p>";
                            }
                        ?>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="logo">
                        <i class='bx bx-basket'></i>
                    </div>
                    <div class="isi-stats">
                        <h4>Total Produk</h4>
                        <?php
                            $result = $conn->query("SELECT COUNT(*) AS total_produk FROM produk");
                            if ($result) {
                                $row = $result->fetch_assoc();
                                echo "<p>" . $row['total_produk'] . "</p>";
                            } else {
                                echo "<p>Error retrieving data</p>";
                            }
                        ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="logo">
                        <i class='bx bxs-star-half'></i>
                    </div>
                    <div class="isi-stats">
                        <h4>Tingkat Kepuasan</h4>
                            <?php
                                $result = $conn->query("SELECT AVG(rating) AS rate_penjualan FROM produk");
                                if ($result) {
                                    $row = $result->fetch_assoc();
                                    echo "<p>" . number_format($row['rate_penjualan'], 2) . "</p>";
                                } else {
                                    echo "<p>Error retrieving data</p>";
                                }
                            ?>
                    </div>
                </div>
            </section>

            <section class="card-penjualan">
                <div class="penjualan">
                    <h2>Penjualan Produk</h2>
                    <label for="filter">Pilih Kategori Produk:</label>
                    <select class="seller" id="filter" onchange="updateChart()">
                        <option value="lama">Produk Paling Awal Diliris</option>
                        <option value="baru">Produk Terbaru</option>
                        <option value="semua">Semua Penjualan</option>
                    </select>
                    
                    <canvas id="chart"></canvas>
                </div>
                <div class="top-produk">
                    <h2>Top Product</h2>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Jumlah Penjualan</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT p.nama, SUM(j.jumlah) AS total_penjualan, p.rating
                                                        FROM penjualan j 
                                                        JOIN produk p ON j.id_produk = p.id_produk 
                                                        GROUP BY p.id_produk 
                                                        ORDER BY total_penjualan DESC 
                                                        LIMIT 5");

                                while($row = $result->fetch_assoc()) {
                                    echo "<tr><td>{$row['nama']}</td><td>{$row['total_penjualan']}</td><td>{$row['rating']}</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
        <script>
           document.addEventListener('DOMContentLoaded', function() {
                var chart;
                var bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                var colors = [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(199, 199, 199, 0.6)',
                    'rgba(83, 102, 255, 0.6)',
                    'rgba(255, 159, 128, 0.6)',
                    'rgba(255, 99, 255, 0.6)'
                ];

                function buatChart(dataProduk, label) {
                    var datasets = dataProduk.map(function(produk, index) {
                        var dataJumlah = [];
                        for (var i = 1; i <= 12; i++) {
                            dataJumlah.push(produk.data[i].jumlah);
                        }

                        var color = colors[index % colors.length];
                        return {
                            label: produk.nama,
                            data: dataJumlah,
                            borderColor: color,
                            backgroundColor: color,
                            borderWidth: 2,
                            fill: false
                        };
                    });

                    var ctx = document.getElementById('chart').getContext('2d');
                    if (chart) {
                        chart.destroy();
                    }
                    chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: bulanLabels,
                            datasets: datasets
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        display: false // Menghilangkan garis kotak-kotak
                                    },
                                    ticks: {
                                        font: {
                                            size: 10 // Ukuran font lebih kecil
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false // Menghilangkan garis kotak-kotak
                                    },
                                    ticks: {
                                        font: {
                                            size: 10 // Ukuran font lebih kecil
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    labels: {
                                        font: {
                                            size: 10 // Ukuran font lebih kecil
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                function updateChart(data) {
                    var filter = document.getElementById('filter').value;
                    if (filter === 'lama') {
                        buatChart(data.produk_lama, 'Produk Paling Lama');
                    } else if (filter === 'baru') {
                        buatChart(data.produk_baru, 'Produk Paling Baru');
                    } else if (filter === 'semua') {
                        buatChart(data.semua_produk, 'Semua Penjualan');
                    }
                }

                function fetchData() {
                    fetch('get_data.php')
                        .then(response => response.json())
                        .then(data => {
                            // Simpan data yang diambil dalam variabel global
                            window.chartData = data;
                            // Inisialisasi chart dengan data default
                            updateChart(data);
                        })
                        .catch(error => console.error('Error fetching data:', error));
                }

                document.getElementById('filter').addEventListener('change', function() {
                    updateChart(window.chartData);
                });

                // Panggil fetchData saat dokumen selesai dimuat
                fetchData();
            });

        </script>
    </div>
</body>
</html>
