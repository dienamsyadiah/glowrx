<?php
include 'koneksi.php';

// Query untuk menghitung total penjualan per produk dan mengupdate rating
$query = "
    UPDATE produk p
    LEFT JOIN (
        SELECT id_produk, SUM(jumlah) as total_penjualan
        FROM penjualan
        GROUP BY id_produk
    ) penj
    ON p.id_produk = penj.id_produk
    SET p.rating = CASE
        WHEN penj.total_penjualan > 20 THEN 5
        WHEN penj.total_penjualan > 10 THEN 4
        ELSE 3
    END;
";

if ($conn->query($query) === TRUE) {
    echo "Rating produk berhasil diperbarui berdasarkan penjualan.";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
