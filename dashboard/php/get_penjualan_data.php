<?php
include 'koneksi.php';

$id_produk = intval($_GET['id_produk']);
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

if ($filter === 'years') {
    // Endpoint untuk mendapatkan daftar tahun
    $sql = "SELECT DISTINCT YEAR(tanggal_penjualan) as year FROM penjualan WHERE id_produk = $id_produk AND YEAR(tanggal_penjualan) BETWEEN 2019 AND 2024 ORDER BY year ASC";
    $result = $conn->query($sql);

    $years = [];
    if ($result->num_rows > 0) {
        // Output data setiap baris
        while($row = $result->fetch_assoc()) {
            $years[] = $row['year'];
        }
    }

    // Mengembalikan data dalam format JSON
    header('Content-Type: application/json');
    echo json_encode($years);

} else {
    // Endpoint untuk mendapatkan data penjualan bulanan atau tahunan
    $query = "";
    if ($filter == 'monthly') {
        $year = intval($_GET['year']); // Ambil parameter tahun dari query string
        $query = "SELECT DATE_FORMAT(tanggal_penjualan, '%Y-%m') AS period, SUM(jumlah) AS jumlah 
                  FROM penjualan 
                  WHERE id_produk = $id_produk AND YEAR(tanggal_penjualan) = $year 
                  GROUP BY period 
                  ORDER BY period";
    } else {
        $query = "SELECT DATE_FORMAT(tanggal_penjualan, '%Y') AS period, SUM(jumlah) AS jumlah 
                  FROM penjualan 
                  WHERE id_produk = $id_produk 
                  GROUP BY period 
                  ORDER BY period";
    }

    $result = $conn->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Mengembalikan data dalam format JSON
    header('Content-Type: application/json');
    echo json_encode($data);
}

$conn->close();
?>
