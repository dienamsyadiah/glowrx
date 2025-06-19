<?php
session_start();
require('php/koneksi.php'); // Sesuaikan dengan nama file koneksi Anda

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unique_code = $_POST['unique_code'];
    
    // Query untuk memeriksa keberadaan kode unik
    $sql = "SELECT * FROM users WHERE unique_code = '$unique_code'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Kode unik ditemukan, arahkan ke dashboard
        $row = $result->fetch_assoc();
        $_SESSION['logged_in'] = true;
        $_SESSION['id'] = $row['id']; 
        $_SESSION['username'] = $row['username']; // Simpan username ke dalam sesi
        $_SESSION['status'] = $row['status']; 
        header("Location: php/homepage.php"); // Sesuaikan dengan nama file dashboard Anda
        exit();
    } else {
        // Kode unik tidak valid
        $error = "Kode unik tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/index.css">
    <title>Login Glow Rx</title>
</head>
<body>
    <div class="login-container">
        <div class="gambar">
            <img src="./img/banner.png" alt="">
        </div>
        <h1>Please enter your code</h1>
        <p>You should have received a unique access code from GlowRx. If you don't have one, please reach out to your account manager.</p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="unique_code"></label>
            <input type="password" id="unique_code" name="unique_code" required>
            <input type="submit" value="Login">
        </form>
        <?php if(!empty($error)) { echo '<div class="error-message">' . $error . '</div>'; } ?>
    </div>
</body>
</html>