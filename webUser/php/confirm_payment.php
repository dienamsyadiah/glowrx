<?php
session_start();
require 'koneksi.php'; // Pastikan koneksi.php di-include dengan benar

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$id_pengguna = $_SESSION['id_pengguna'];
$totalPayment = $_SESSION['total_payment'];

// Simpan produk yang dipesan ke dalam session
$_SESSION['products'] = $_POST['products'];

// Simpan pesanan ke dalam riwayat
if (isset($_SESSION['products']) && is_array($_SESSION['products'])) {
    $products = $_SESSION['products'];

    // Insert ke dalam tabel riwayat_pesanan menggunakan MySQLi
    $sql_insert_riwayat = "INSERT INTO riwayat_pesanan (id_pengguna, total_pembayaran) VALUES (?, ?)";
    $stmt_insert_riwayat = $conn->prepare($sql_insert_riwayat);
    $stmt_insert_riwayat->bind_param('id', $id_pengguna, $totalPayment);
    $stmt_insert_riwayat->execute();
    $id_riwayat = $stmt_insert_riwayat->insert_id;

    // Insert detail produk ke dalam tabel detail_pesanan menggunakan MySQLi
    foreach ($products as $product) {
        $sql_insert_detail = "INSERT INTO detail_pesanan (id_riwayat, id_produk, nama_produk, harga, jumlah) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert_detail = $conn->prepare($sql_insert_detail);
        $stmt_insert_detail->bind_param('iisdi', $id_riwayat, $product['id_produk'], $product['nama'], $product['harga'], $product['quantity']);
        $stmt_insert_detail->execute();
    }

    // Clear session setelah berhasil disimpan
    unset($_SESSION['products']);
    unset($_SESSION['total_payment']);

    // Redirect ke halaman riwayat.php
    header("Location: riwayat.php");
    exit();
} else {
    // Jika tidak ada produk, bisa diarahkan ke halaman lain atau tampilkan pesan error
    die("Error: Produk tidak ditemukan dalam session.");
}
?>
