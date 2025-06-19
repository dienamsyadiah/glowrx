<!DOCTYPE html>
<html>
<head>
  <title>Visualisasi Pengeluaran berdasarkan Kategori</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div style="width: 50%;">
    <!-- Form Filter Bulan dan Tahun -->
    <form method="get">
      <label for="bulan">Pilih Bulan:</label>
      <select id="bulan" name="bulan">
        <option value="00">Semua Bulan</option>
        <option value="01" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '01') echo 'selected'; ?>>Januari</option>
        <option value="02" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '02') echo 'selected'; ?>>Februari</option>
        <option value="03" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '03') echo 'selected'; ?>>Maret</option>
        <option value="04" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '04') echo 'selected'; ?>>April</option>
        <option value="05" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '05') echo 'selected'; ?>>Mei</option>
        <option value="06" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '06') echo 'selected'; ?>>Juni</option>
        <option value="07" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '07') echo 'selected'; ?>>Juli</option>
        <option value="08" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '08') echo 'selected'; ?>>Agustus</option>
        <option value="09" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '09') echo 'selected'; ?>>September</option>
        <option value="10" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '10') echo 'selected'; ?>>Oktober</option>
        <option value="11" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '11') echo 'selected'; ?>>November</option>
        <option value="12" <?php if(isset($_GET['bulan']) && $_GET['bulan'] == '12') echo 'selected'; ?>>Desember</option>
      </select>

      <label for="tahun">Pilih Tahun:</label>
      <select id="tahun" name="tahun">
        <?php
        // Mengenerate pilihan tahun dari 2017 sampai sekarang
        $tahun_sekarang = date("Y");
        for ($tahun = 2017; $tahun <= $tahun_sekarang; $tahun++) {
          echo "<option value='$tahun'";
          if (isset($_GET['tahun']) && $_GET['tahun'] == $tahun) echo ' selected';
          echo ">$tahun</option>";
        }
        ?>
      </select>

      <button type="submit">Filter</button>
    </form>
  </div>

  <!-- Chart.js untuk menampilkan visualisasi -->
  <div style="width: 80%; margin: 20px auto;">
    <canvas id="myChart"></canvas>
  </div>

  <?php
  // Menghubungkan ke file koneksi.php
  require_once('koneksi.php');

  // Mendapatkan bulan dan tahun dari parameter GET
  $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '00'; // Default semua bulan
  $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

  // Query untuk mengambil data pengeluaran per kategori berdasarkan bulan dan tahun
  if ($bulan == '00') {
    // Jika memilih semua bulan, tampilkan data untuk setiap bulan
    $datasets = [];
    for ($i = 1; $i <= 12; $i++) {
      $sql = "SELECT kp.nama AS kategori, SUM(p.jumlah) AS total_pengeluaran
              FROM pengeluaran p
              INNER JOIN kategori_pengeluaran kp ON p.id_kategori_pengeluaran = kp.id
              WHERE MONTH(p.tanggal_pengeluaran) = $i AND YEAR(p.tanggal_pengeluaran) = $tahun
              GROUP BY kp.nama";
      $result = $conn->query($sql);

      $dataPoints = [];
      while($row = $result->fetch_assoc()) {
        $dataPoints[] = array("label" => $row["kategori"], "y" => $row["total_pengeluaran"]);
      }
      $datasets[$i] = $dataPoints;
    }
    echo '<script>';
    echo 'var datasets = ' . json_encode($datasets) . ';';
    echo '</script>';
  } else {
    // Jika memilih bulan tertentu, tampilkan data hanya untuk bulan tersebut
    $sql = "SELECT kp.nama AS kategori, SUM(p.jumlah) AS total_pengeluaran
            FROM pengeluaran p
            INNER JOIN kategori_pengeluaran kp ON p.id_kategori_pengeluaran = kp.id
            WHERE MONTH(p.tanggal_pengeluaran) = $bulan AND YEAR(p.tanggal_pengeluaran) = $tahun
            GROUP BY kp.nama";
    $result = $conn->query($sql);

    $dataPoints = [];
    while($row = $result->fetch_assoc()) {
      $dataPoints[] = array("label" => $row["kategori"], "y" => $row["total_pengeluaran"]);
    }
    echo '<script>';
    echo 'var dataPoints = ' . json_encode($dataPoints, JSON_NUMERIC_CHECK) . ';';
    echo '</script>';
  }
  ?>

  <script>
    // Membuat grafik dengan Chart.js
    var ctx = document.getElementById('myChart').getContext('2d');

    <?php if ($bulan == '00') : ?>
      var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
          datasets: datasets.map((data, index) => ({
            label: 'Total Pengeluaran per Kategori - Bulan ' + index,
            data: data.map(data => data.y),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }))
        },
        options: {
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  var label = context.dataset.label || '';
                  if (label) {
                    label += ': ';
                  }
                  if (context.parsed.y !== null) {
                    label += 'Rp ' + context.parsed.y.toLocaleString();
                  }
                  return label;
                }
              }
            }
          }
        }
      });
    <?php else : ?>
      var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: dataPoints.map(data => data.label),
          datasets: [{
            label: 'Total Pengeluaran per Kategori',
            data: dataPoints.map(data => data.y),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  var label = context.dataset.label || '';
                  if (label) {
                    label += ': ';
                  }
                  if (context.parsed.y !== null) {
                    label += 'Rp ' + context.parsed.y.toLocaleString();
                  }
                  return label;
                }
              }
            }
          }
        }
      });
    <?php endif; ?>
  </script>
</body>
</html>
