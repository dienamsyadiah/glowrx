<?php
include 'koneksi.php';

// Mengatur rentang waktu default (bulanan)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'bulanan';
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Tentukan rentang tahun berdasarkan data yang ada di database
$sql_min_max_year = "SELECT MIN(YEAR(tanggal_pemasukan)) AS min_year, MAX(YEAR(tanggal_pemasukan)) AS max_year FROM pemasukan";
$result_min_max_year = $conn->query($sql_min_max_year);
$row_min_max_year = $result_min_max_year->fetch_assoc();
$min_year = intval($row_min_max_year['min_year']);
$max_year = intval($row_min_max_year['max_year']);

// Pastikan tahun yang dipilih tidak kurang dari tahun minimum atau lebih besar dari tahun maksimum
$year = max($min_year, min($max_year, $year));

// Query untuk mendapatkan data pemasukan dan pengeluaran berdasarkan filter
if ($filter == 'tahunan') {
    $sql_pemasukan = "SELECT DATE_FORMAT(tanggal_pemasukan, '%Y') AS periode, SUM(jumlah) AS total FROM pemasukan WHERE YEAR(tanggal_pemasukan) BETWEEN $min_year AND $max_year GROUP BY periode ORDER BY periode";
    $sql_pengeluaran = "SELECT DATE_FORMAT(tanggal_pengeluaran, '%Y') AS periode, SUM(jumlah) AS total FROM pengeluaran WHERE YEAR(tanggal_pengeluaran) BETWEEN $min_year AND $max_year GROUP BY periode ORDER BY periode";
} else {
    $sql_pemasukan = "SELECT DATE_FORMAT(tanggal_pemasukan, '%Y-%m') AS periode, SUM(jumlah) AS total FROM pemasukan WHERE YEAR(tanggal_pemasukan) = $year GROUP BY periode ORDER BY periode";
    $sql_pengeluaran = "SELECT DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') AS periode, SUM(jumlah) AS total FROM pengeluaran WHERE YEAR(tanggal_pengeluaran) = $year GROUP BY periode ORDER BY periode";
}

$result_pemasukan = $conn->query($sql_pemasukan);
$result_pengeluaran = $conn->query($sql_pengeluaran);

$dates = [];
$pemasukan = [];
$pengeluaran = [];

// Inisialisasi array dengan nol untuk semua periode yang mungkin
if ($filter == 'tahunan') {
    for ($y = $min_year; $y <= $max_year; $y++) {
        $dates[] = (string)$y;
        $pemasukan[] = 0;
        $pengeluaran[] = 0;
    }
} else {
    for ($m = 1; $m <= 12; $m++) {
        $dates[] = sprintf('%04d-%02d', $year, $m);
        $pemasukan[] = 0;
        $pengeluaran[] = 0;
    }
}

if ($result_pemasukan->num_rows > 0) {
    while($row = $result_pemasukan->fetch_assoc()) {
        $index = array_search($row['periode'], $dates);
        if ($index !== false) {
            $pemasukan[$index] = $row['total'];
        }
    }
}

if ($result_pengeluaran->num_rows > 0) {
    while($row = $result_pengeluaran->fetch_assoc()) {
        $index = array_search($row['periode'], $dates);
        if ($index !== false) {
            $pengeluaran[$index] = $row['total'];
        }
    }
}

// Ubah label bulan jika filter adalah bulanan
if ($filter == 'bulanan') {
    $months = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    foreach ($dates as &$date) {
        $date_parts = explode('-', $date);
        $date = $months[$date_parts[1]] . ' ' . $date_parts[0];
    }
    unset($date);
}

$data = [
    'dates' => $dates,
    'pemasukan' => $pemasukan,
    'pengeluaran' => $pengeluaran
];

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
