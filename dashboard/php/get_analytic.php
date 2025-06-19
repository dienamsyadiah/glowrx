<?php
include "koneksi.php";

$functionName = htmlspecialchars($_GET['functionName']);

switch ($functionName) {
    case 'getDataGender':
        getDataGender();
        break;
    case 'getDataSkin':
        getDataSkin();
        break;
    case 'getDataMaterialDistribution':
        getDataMaterialDistribution();
        break;
    case 'getSalesData':
        getSalesData();
        break;
    case 'getBeautyIngredientsData':
        getBeautyIngredientsData();
        break;
    case 'getDataSkinProblems':
        getDataSkinProblems();
        break;
    default:
        echo json_encode([]);
        break;
}

function getDataGender() {
    global $conn;
    $data = [];
    $query = mysqli_query($conn, "SELECT jenis_kelamin FROM pengguna");

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Failed to fetch gender data']);
    }
}

function getDataSkin() {
    global $conn;
    $data = [];
    $query = mysqli_query($conn, "SELECT tipekulit.tipe 
                                  FROM pengguna 
                                  JOIN tipekulit ON pengguna.id_tipe_kulit = tipekulit.id_tipe_kulit");

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Failed to fetch skin data']);
    }
}

function getDataMaterialDistribution() {
    global $conn;
    $data = [];
    $query = mysqli_query($conn, "SELECT produk.nama AS produk, bahan.nama AS bahan, produk_bahan.jumlah 
                                FROM produk_bahan JOIN produk ON produk_bahan.id_produk = produk.id_produk JOIN bahan ON produk_bahan.id_bahan = bahan.id_bahan");

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Failed to fetch material distribution data']);
    }
}

function getSalesData() {
    global $conn;
    $sql = "SELECT nama_kampanye, SUM(penjualan_sales) as total_penjualan, tanggal_kampanye 
            FROM sales 
            GROUP BY nama_kampanye, tanggal_kampanye";
    $result = $conn->query($sql);

    if ($result) {
        $salesData = [];
        while ($row = $result->fetch_assoc()) {
            $salesData[] = $row;
        }
        echo json_encode($salesData);
    } else {
        echo json_encode(['error' => 'Failed to fetch sales data']);
    }
}

function getBeautyIngredientsData() {
    global $conn;
    $sql = "SELECT b.nama AS nama_bahan, COUNT(p.id_produk) AS jumlah_produk
            FROM produk p
            JOIN bahan b ON p.id_bahan = b.id_bahan
            GROUP BY b.id_bahan, b.nama
            ORDER BY jumlah_produk DESC";

    $result = $conn->query($sql);

    if ($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Failed to fetch beauty ingredients data']);
    }
}

function getDataSkinProblems() {
    global $conn;
    $sql = "SELECT nama_masalah, COUNT(id_pengguna) as jumlah_pengguna
            FROM pengguna
            JOIN masalah_kulit ON pengguna.id_masalah_kulit = masalah_kulit.id_masalah_kulit
            GROUP BY nama_masalah";

    $result = $conn->query($sql);

    if ($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Failed to fetch skin problems data']);
    }
}

$conn->close();
?>
