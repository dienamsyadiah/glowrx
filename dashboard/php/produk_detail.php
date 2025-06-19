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
                    <span><?php echo htmlspecialchars($username); ?></span><br>
                    <span><?php echo htmlspecialchars($status); ?></span>
                </div>
            </div>
        </div>

        <div class="main-content">
            <header>
                <h2>Detail Product</h2>
            </header>

            <div class="card-product">
                <?php
                $id_produk = intval($_GET['id_produk']); // Sanitize input
                $result = $conn->query("SELECT * FROM produk WHERE id_produk = $id_produk");
                $produk = $result->fetch_assoc();
                ?>
                <div class="product-container">
                    <div class="product-image">
                        <?php echo "<img src='../img/{$produk['url_gambar']}' alt='{$produk['nama']}' width='250'>"; ?>
                    </div>
                    <div class="product-details">
                        <?php
                        echo "<h2>{$produk['nama']}</h2>";
                        echo "<p><b>Deskripsi:</b> {$produk['deskripsi']}</p>";
                        echo "<p><b>Tanggal Rilis:</b> {$produk['tanggal_rilis']}</p>";
                        echo "<p><b>Harga:</b> {$produk['harga']}</p>";

                        $result = $conn->query("SELECT SUM(jumlah) AS total_penjualan FROM penjualan WHERE id_produk = $id_produk");
                        $penjualan = $result->fetch_assoc();
                        echo "<p><b>Total Penjualan:</b> {$penjualan['total_penjualan']}</p>";
                        ?>
                    </div>
                </div>
            </div>

            <div class="card-product">
                <h3>Grafik Penjualan</h3>
                <label for="filter">Filter: </label>
                <select id="filter" onchange="updateFilterOptions()">
                    <option value="yearly">Tahunan</option>
                    <option value="monthly">Bulanan</option>
                </select>
                <select id="yearFilter" style="display:none;" onchange="updateChart()"></select>
                <canvas id="penjualanChart" width="400" height="200"></canvas>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        updateChart(); // Initial chart update when the page loads
                    });

                    function updateFilterOptions() {
                        const filter = document.getElementById('filter').value;
                        const yearFilter = document.getElementById('yearFilter');
                        if (filter === 'monthly') {
                            fetchYears();
                            yearFilter.style.display = 'inline'; // Show the year dropdown
                        } else {
                            yearFilter.style.display = 'none'; // Hide the year dropdown
                            updateChart(); // Directly update the chart for yearly filter
                        }
                    }

                    function fetchYears() {
                        const id_produk = '<?php echo intval($_GET['id_produk']); ?>';
                        fetch(`get_penjualan_data.php?id_produk=${id_produk}&filter=years`)
                            .then(response => response.json())
                            .then(years => {
                                const yearFilter = document.getElementById('yearFilter');
                                yearFilter.innerHTML = ''; // Clear existing options
                                years.forEach(year => {
                                    const option = document.createElement('option');
                                    option.value = year;
                                    option.textContent = year;
                                    yearFilter.appendChild(option);
                                });
                                if (years.length > 0) {
                                    yearFilter.value = years[0]; // Set the first year as selected
                                    updateChart(); // Update chart with the first available year
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching years:', error);
                            });
                    }

                    function updateChart() {
                        const filter = document.getElementById('filter').value;
                        const id_produk = '<?php echo intval($_GET['id_produk']); ?>';
                        let url = `get_penjualan_data.php?id_produk=${id_produk}&filter=${filter}`;
                        if (filter === 'monthly') {
                            const year = document.getElementById('yearFilter').value;
                            url += `&year=${year}`;
                        }
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                console.log('Fetched data:', data); // Debugging line

                                const labels = [];
                                const values = [];

                                data.forEach(item => {
                                    if (filter === 'monthly') {
                                        labels.push(convertMonth(item.period));
                                    } else {
                                        labels.push(item.period);
                                    }
                                    values.push(item.jumlah);
                                });

                                const ctx = document.getElementById('penjualanChart').getContext('2d');
                                if (window.myLineChart) {
                                    window.myLineChart.destroy(); // Destroy existing chart if it exists
                                }
                                window.myLineChart = new Chart(ctx, {
                                    type: filter === 'monthly' ? 'line' : 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: filter === 'monthly' ? 'Jumlah Penjualan Bulanan' : 'Jumlah Penjualan Tahunan',
                                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                            borderColor: 'rgba(75, 192, 192, 1)',
                                            data: values,
                                            fill: false,
                                            pointRadius: filter === 'monthly' ? 3 : 0, // Points on line chart, none on bar chart
                                            pointHoverRadius: 5, // Hover points size
                                            pointBackgroundColor: 'rgba(75, 192, 192, 1)', // Point background color
                                            pointBorderColor: 'rgba(75, 192, 192, 1)' // Point border color
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        scales: {
                                            x: {
                                                grid: {
                                                    display: false // Hide grid lines on the x-axis
                                                }
                                            },
                                            y: {
                                                beginAtZero: true,
                                                grid: {
                                                    display: false // Hide grid lines on the y-axis
                                                },
                                                ticks: {
                                                    callback: function(value) {
                                                        return new Intl.NumberFormat('id-ID').format(value); // Format number without currency symbol
                                                    }
                                                }
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                display: false // Hide the legend
                                            },
                                            tooltip: {
                                                enabled: true,
                                                callbacks: {
                                                    label: function(context) {
                                                        let label = context.dataset.label || '';
                                                        if (label) {
                                                            label += ': ';
                                                        }
                                                        if (context.parsed.y !== null) {
                                                            label += new Intl.NumberFormat('id-ID').format(context.parsed.y); // Format number without currency symbol
                                                        }
                                                        return label;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            })
                            .catch(error => {
                                console.error('Error fetching data:', error);
                            });
                    }

                    function convertMonth(period) {
                        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        const [year, month] = period.split('-');
                        return `${monthNames[parseInt(month) - 1]} ${year}`;
                    }
                </script>
            </div>
        </div>
    </div>
</body>
</html>
