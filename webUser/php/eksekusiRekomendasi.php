<?php
session_start();
use LucianoTonet\GroqPHP\Groq;

require '../vendor/autoload.php'; // Pastikan path ke autoload.php sesuai
include "koneksi.php"; // Tambahkan koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form rekomendasi.php
    $productType = $_POST['product-type'] ?? '';
    $skinProblems = $_POST['skin-problem'] ?? [];
    $mainIngredient = $_POST['main-ingredient'] ?? '';
    $productDescription = $_POST['product-description'] ?? '';

    // Debug: Log input yang diterima dari form
    error_log("Product Type: " . $productType);
    error_log("Skin Problems: " . implode(", ", $skinProblems));
    error_log("Main Ingredient: " . $mainIngredient);

    // Ambil nama tipe kulit dari database
    $queryTipeKulit = "SELECT tipe FROM tipekulit WHERE tipe = ?";
    $stmtTipeKulit = $conn->prepare($queryTipeKulit);
    $stmtTipeKulit->bind_param('s', $productType);
    $stmtTipeKulit->execute();
    $resultTipeKulit = $stmtTipeKulit->get_result();
    $tipeKulitRow = $resultTipeKulit->fetch_assoc();
    $tipeKulit = $tipeKulitRow ? $tipeKulitRow['tipe'] : 'Unknown';

    // Debug: Cek apakah tipe kulit berhasil diambil
    error_log("Tipe Kulit: " . $tipeKulit);

    // Ambil nama masalah kulit dari database
    $skinProblemsNames = [];
    foreach ($skinProblems as $problemName) {
        $queryMasalahKulit = "SELECT nama_masalah FROM masalah_kulit WHERE nama_masalah = ?";
        $stmtMasalahKulit = $conn->prepare($queryMasalahKulit);
        $stmtMasalahKulit->bind_param('s', $problemName);
        $stmtMasalahKulit->execute();
        $resultMasalahKulit = $stmtMasalahKulit->get_result();
        $masalahKulitRow = $resultMasalahKulit->fetch_assoc();
        if ($masalahKulitRow) {
            $skinProblemsNames[] = $masalahKulitRow['nama_masalah'];
        } else {
            $skinProblemsNames[] = 'Unknown';
        }
    }

    // Debug: Cek apakah masalah kulit berhasil diambil
    error_log("Masalah Kulit: " . implode(", ", $skinProblemsNames));

    // Ambil data produk yang sesuai dari database
    $queryProduk = "SELECT nama FROM produk WHERE id_jenis_produk = (SELECT id_jenis_produk FROM tipekulit WHERE tipe = ?) AND id_bahan = (SELECT id_bahan FROM bahan WHERE nama = ?) LIMIT 1";
    $stmtProduk = $conn->prepare($queryProduk);
    $stmtProduk->bind_param('ss', $productType, $mainIngredient);
    $stmtProduk->execute();
    $resultProduk = $stmtProduk->get_result();
    $produkName = $resultProduk->fetch_assoc()['nama'] ?? '';

    // Debug: Cek apakah produk berhasil diambil
    error_log("Produk: " . $produkName);

    // Jika produkName kosong, beri pesan debugging
    if (empty($produkName)) {
        error_log("No products found for the given criteria.");
    }

    // Buat prompt untuk Groq berdasarkan input pengguna
    $prompt = "Rekomendasikan produk yang sesuai berdasarkan jenis kulit $tipeKulit yang berbahan utama $mainIngredient dan memiliki masalah kulit " . implode(", ", $skinProblemsNames) . ". Produk yang tersedia dalam database adalah: " . $produkName . ".";

    // Debug: Cek prompt yang dikirim ke API Groq
    error_log("Prompt: " . $prompt);

    try {
        // Inisialisasi Groq client dengan API key Anda
        $groq = new Groq('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'); // Ganti dengan API key Groq Anda

        // Lakukan permintaan kompletasi obrolan
        $chatCompletion = $groq->chat()->completions()->create([
            'model'    => 'mixtral-8x7b-32768', // Ganti dengan model yang Anda inginkan
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => $prompt
                ],
            ]
        ]);

        // Ekstrak rekomendasi dari respons
        $recommendations = $chatCompletion['choices'][0]['message']['content'];

        // Debug: Cek hasil rekomendasi dari API Groq
        error_log("Rekomendasi: " . $recommendations);

        // Redirect ke hasil.php dengan rekomendasi
        header("Location: hasil.php?recommendations=" . urlencode($recommendations) . "&product=" . urlencode($produkName));
        exit();
    } catch (Exception $e) {
        $errorMessage = 'Error: ' . $e->getMessage();
        header("Location: rekomendasi.php?error=" . urlencode($errorMessage));
        exit();
    }
} else {
    // Redirect jika metode permintaan bukan POST
    header("Location: rekomendasi.php?error=invalid_method");
    exit();
}
?>
