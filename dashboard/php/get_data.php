<?php
include 'koneksi.php'; // Pastikan menyertakan koneksi ke database

// Mengambil Data dari Database
$sql = "SELECT produk.id_produk, produk.nama, produk.tanggal_rilis, 
               penjualan.jumlah, penjualan.total_harga, 
               DATE_FORMAT(penjualan.tanggal_penjualan, '%Y-%m') AS bulan
        FROM penjualan 
        JOIN produk ON penjualan.id_produk = produk.id_produk
        ORDER BY produk.tanggal_rilis ASC, penjualan.tanggal_penjualan ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Query Error: " . $conn->error);
}

$produk = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tanggal_rilis = strtotime($row['tanggal_rilis']);
        $produk_id = $row['id_produk'];

        if (!isset($produk[$produk_id])) {
            $produk[$produk_id] = [
                'nama' => $row['nama'],
                'tanggal_rilis' => $tanggal_rilis,
                'data' => array_fill(1, 12, ['jumlah' => 0, 'total_harga' => 0])
            ];
        }

        $bulan = (int)date('n', strtotime($row['bulan']));
        $produk[$produk_id]['data'][$bulan]['jumlah'] += (int)$row['jumlah'];
        $produk[$produk_id]['data'][$bulan]['total_harga'] += (float)$row['total_harga'];
    }

    usort($produk, function($a, $b) {
        return $a['tanggal_rilis'] - $b['tanggal_rilis'];
    });

    // Mendapatkan 5 produk paling lama
    $produk_lama = array_slice($produk, 0, 5);

    // Mendapatkan 5 produk paling baru
    $produk_baru = array_slice(array_reverse($produk), 0, 5);

    // Menggabungkan data untuk semua produk
    $semua_produk = [
        'nama' => 'Semua Produk',
        'data' => array_fill(1, 12, ['jumlah' => 0, 'total_harga' => 0])
    ];

    foreach ($produk as $produk_item) {
        for ($i = 1; $i <= 12; $i++) {
            $semua_produk['data'][$i]['jumlah'] += $produk_item['data'][$i]['jumlah'];
        }
    }
}

// Menghasilkan JSON
echo json_encode([
    'produk_lama' => array_values($produk_lama),
    'produk_baru' => array_values($produk_baru),
    'semua_produk' => [$semua_produk]
]);

$conn->close();
?>
