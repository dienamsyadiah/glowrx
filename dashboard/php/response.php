<?php
session_start();
include "koneksi.php"; // Pastikan ini mengarah ke file koneksi yang benar

// Masukkan autoload Composer
require '../vendor/autoload.php';

use LucianoTonet\GroqPHP\Groq;

$response = '';

try {
    $groq = new Groq('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');

    if (!empty($_POST['message'])) {
        $userMessage = htmlspecialchars($_POST['message']);

        // Ambil user_id dari sesi jika tersedia
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

        // Ambil data dari database sesuai dengan pertanyaan
        $contextData = '';

        // Cek apakah pertanyaan mengandung kata-kata kunci tertentu dan ambil data sesuai dengan kebutuhan
        if (strpos($userMessage, 'produk termurah') !== false || strpos($userMessage, 'harga termurah') !== false) {
            // Mengambil data produk dengan harga termurah
            $stmt = $conn->prepare("SELECT nama, deskripsi, harga FROM produk ORDER BY harga ASC LIMIT 1");
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $contextData = "Produk termurah: Nama Produk: " . $row['nama'] . ", Deskripsi: " . $row['deskripsi'] . ", Harga: " . $row['harga'] . ";\n";
                
                // Debug: Cetak hasil kueri
                error_log("Debug: Produk termurah - " . json_encode($row));
            } else {
                $contextData = "Tidak ada produk yang ditemukan.";
                
                // Debug: Cetak jika tidak ada produk yang ditemukan
                error_log("Debug: Tidak ada produk yang ditemukan.");
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'produk') !== false) {
            // Mengambil data produk
            $stmt = $conn->prepare("SELECT nama, deskripsi, harga FROM produk");
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $contextData .= "Nama Produk: " . $row['nama'] . ", Deskripsi: " . $row['deskripsi'] . ", Harga: " . $row['harga'] . ";\n";
                }
                
                // Debug: Cetak hasil kueri
                error_log("Debug: Produk - " . json_encode($result->fetch_all(MYSQLI_ASSOC)));
            } else {
                $contextData = "Tidak ada produk yang ditemukan.";
                
                // Debug: Cetak jika tidak ada produk yang ditemukan
                error_log("Debug: Tidak ada produk yang ditemukan.");
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'penjualan') !== false) {
            // Mengambil data penjualan
            $stmt = $conn->prepare("SELECT p.nama, pj.tanggal_penjualan, pj.jumlah, pj.total_harga FROM penjualan pj JOIN produk p ON pj.id_produk = p.id_produk WHERE pj.id_pengguna = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $contextData .= "Nama Produk: " . $row['nama'] . ", Tanggal Penjualan: " . $row['tanggal_penjualan'] . ", Jumlah: " . $row['jumlah'] . ", Total Harga: " . $row['total_harga'] . ";\n";
                }
                
                // Debug: Cetak hasil kueri
                error_log("Debug: Penjualan - " . json_encode($result->fetch_all(MYSQLI_ASSOC)));
            } else {
                $contextData = "Tidak ada penjualan yang ditemukan.";
                
                // Debug: Cetak jika tidak ada penjualan yang ditemukan
                error_log("Debug: Tidak ada penjualan yang ditemukan.");
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'pengguna') !== false) {
            // Mengambil data pengguna
            $stmt = $conn->prepare("SELECT nama, email, jenis_kelamin FROM pengguna WHERE id_pengguna = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $contextData .= "Nama: " . $row['nama'] . ", Email: " . $row['email'] . ", Jenis Kelamin: " . $row['jenis_kelamin'] . ";\n";
                }
                
                // Debug: Cetak hasil kueri
                error_log("Debug: Pengguna - " . json_encode($result->fetch_all(MYSQLI_ASSOC)));
            } else {
                $contextData = "Tidak ada data pengguna yang ditemukan.";
                
                // Debug: Cetak jika tidak ada data pengguna yang ditemukan
                error_log("Debug: Tidak ada data pengguna yang ditemukan.");
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'pengeluaran') !== false) {
            // Mengambil data pengeluaran
            $stmt = $conn->prepare("SELECT tanggal_pengeluaran, jumlah, deskripsi FROM pengeluaran WHERE id_pengguna = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $contextData .= "Tanggal Pengeluaran: " . $row['tanggal_pengeluaran'] . ", Jumlah: " . $row['jumlah'] . ", Deskripsi: " . $row['deskripsi'] . ";\n";
                }
                
                // Debug: Cetak hasil kueri
                error_log("Debug: Pengeluaran - " . json_encode($result->fetch_all(MYSQLI_ASSOC)));
            } else {
                $contextData = "Tidak ada data pengeluaran yang ditemukan.";
                
                // Debug: Cetak jika tidak ada data pengeluaran yang ditemukan
                error_log("Debug: Tidak ada data pengeluaran yang ditemukan.");
            }
            $stmt->close();
        } elseif (strpos($userMessage, 'tipe kulit') !== false) {
            // Mengambil data tipe kulit
            $stmt = $conn->prepare("SELECT tipe FROM tipekulit WHERE id_tipe_kulit = (SELECT id_tipe_kulit FROM pengguna WHERE id_pengguna = ?)");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $contextData .= "Tipe Kulit: " . $row['tipe'] . ";\n";
                }
                
                // Debug: Cetak hasil kueri
                error_log("Debug: Tipe Kulit - " . json_encode($result->fetch_all(MYSQLI_ASSOC)));
            } else {
                $contextData = "Tidak ada data tipe kulit yang ditemukan.";
                
                // Debug: Cetak jika tidak ada data tipe kulit yang ditemukan
                error_log("Debug: Tidak ada data tipe kulit yang ditemukan.");
            }
            $stmt->close();
        }

        // Log context data for debugging
        error_log("Context Data: " . $contextData);

        // Gabungkan data konteks dengan pesan pengguna
        $prompt = $contextData . " Pertanyaan pengguna: " . $userMessage;

        // Log prompt for debugging
        error_log("Prompt: " . $prompt);

        // Kirim prompt ke API Groq
        $chatCompletion = $groq->chat()->completions()->create([
            'model'    => 'mixtral-8x7b-32768',
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => $prompt
                ],
            ]
        ]);

        $response = $chatCompletion['choices'][0]['message']['content'];

        // Log response for debugging
        error_log("Response: " . $response);

    }
} catch (Exception $e) {
    $response = 'Kesalahan: ' . $e->getMessage();
    error_log("Error: " . $e->getMessage()); // Log error
}

echo json_encode(['response' => $response]);

$conn->close(); // Tutup koneksi
?>
