<?php
include 'koneksi.php';
session_start();

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $email = $_POST['email'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $id_tipe_kulit = $_POST['id_tipe_kulit'];
    $id_masalah_kulit = $_POST['id_masalah_kulit'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    // Check if passwords match
    if ($password !== $password2) {
        header("Location: login.php?error=Password does not match");
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement
    $sql = "INSERT INTO pengguna (nama, tanggal_lahir, email, jenis_kelamin, id_tipe_kulit, id_masalah_kulit, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssssiss", $nama, $tanggal_lahir, $email, $jenis_kelamin, $id_tipe_kulit, $id_masalah_kulit, $hashed_password);

    // Execute statement and check for success
    if ($stmt->execute()) {
        header("Location: login.php?success=Account created successfully");
        exit();
    } else {
        header("Location: login.php?error=Failed to create account");
        exit();
    }
    $stmt->close();
}

// Login logic
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM pengguna WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['id_pengguna'] = $row['id_pengguna'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['username'] = $row['nama']; // Ensure 'username' is set
            header("Location: ../php/produk.php");
        } else {
            echo "Kata sandi salah!";
        }
    } else {
        echo "Email tidak ditemukan!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glow RX - Login/Registrasi</title>
    <link rel="stylesheet" href="login.css">
    <link rel="shortcut icon" href="../Logo/logo.png"/>
    <style>
        .form-container { display: none; }
        .form-container.active { display: block; }
    </style>
</head>
<body>
    <main>
        <div id="particles-js"></div>
        <div class="box">
            <div class="inner-box">
                <div class="forms-wrap">
                    <!-- Form Login -->
                    <form action="login.php" method="post" autocomplete="off" class="form-container active" id="login-form">
                        <div class="logo">
                            <img src="img/logo.png" alt="GLOW RX" />
                            <h4>GLOW RX</h4>
                        </div>
                        <div class="heading">
                            <h2>Selamat Datang Kembali</h2>
                            <h6>Sudah punya akun?</h6>
                            <a href="#" onclick="toggleForm('register-form')" class="toggle">Daftar</a>
                        </div>
                        <div class="actual-form">
                            <div class="input-wrap">
                                <input type="email" class="input-field" autocomplete="off" required name="email"/>
                                <label>Email</label>
                            </div>
                            <div class="input-wrap">
                                <input type="password" minlength="4" class="input-field" autocomplete="off" required name="password"/>
                                <label>Kata Sandi</label>
                            </div>
                            <input type="submit" name="login" value="Masuk" class="sign-btn" />
                            <p class="text">
                                Lupa kata sandi atau detail login anda?
                                <a href="#">Dapatkan Bantuan</a>
                            </p>
                        </div>
                    </form>

                    <!-- Form Register -->
                    <form action="login.php" method="post" autocomplete="off" class="form-container" id="register-form">
                        <div class="logo">
                            <img src="img/logo.png" alt="GLOW RX" />
                            <h4>GLOW RX</h4>
                        </div>
                        <div class="heading">
                            <h2>Registrasi Now!</h2>
                            <h6>Sudah punya akun?</h6>
                            <a href="#" onclick="toggleForm('login-form')" class="toggle">Masuk</a>
                        </div>
                        <div class="actual-form">
                            <div class="input-wrap">
                                <input name="nama" type="text" class="input-field" required/>
                                <label>Nama</label>
                            </div>
                            <div class="input-wrap">
                                <input name="tanggal_lahir" type="date" class="input-field" required/>  <!-- Corrected name attribute -->
                                <label>Tanggal Lahir</label>
                            </div>
                            <div class="input-wrap">
                                <input name="email" type="email" class="input-field" required/>
                                <label>Email</label>
                            </div>
                            <div class="input-wrap">
                                <select name="jenis_kelamin" class="input-field" required>
                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <label>Jenis Kelamin</label>
                            </div>
                            <div class="input-wrap">
                                <select name="id_tipe_kulit" class="input-field" required>
                                    <option value="" disabled selected>Pilih Tipe Kulit</option>
                                    <?php
                                    $sql = "SELECT * FROM tipekulit";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='".$row['id_tipe_kulit']."'>".$row['tipe']."</option>";
                                    }
                                    ?>
                                </select>
                                <label>Tipe Kulit</label>
                            </div>
                            <div class="input-wrap">
                                <select name="id_masalah_kulit" class="input-field" required>
                                    <option value="" disabled selected>Pilih Masalah Kulit</option>
                                    <?php
                                    $sql = "SELECT * FROM masalah_kulit";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='".$row['id_masalah_kulit']."'>".$row['nama_masalah']."</option>";
                                    }
                                    ?>
                                </select>
                                <label>Masalah Kulit</label>
                            </div>
                            <div class="input-wrap">
                                <input name="password" type="password" minlength="4" class="input-field" required/>
                                <label>Kata Sandi</label>
                            </div>
                            <div class="input-wrap">
                                <input name="password2" type="password" minlength="4" class="input-field" required/>
                                <label>Konfirmasi Kata Sandi</label>
                            </div>
                            <input name="register" type="submit" value="Daftar" class="sign-btn"/>
                        </div>
                    </form>
                </div>
                <div class="carousel">
                    <div class="images-wrapper">
                        <img src="img/image4.jpg" class="image img-1 show" alt="" />
                        <img src="img/image2.jpg" class="image img-2" alt="" />
                        <img src="img/image3.jpg" class="image img-3" alt="" />
                    </div>
                    <div class="text-slider">
                        <div class="text-wrap">
                            <div class="text-group">
                                <h3>Pintu Gerbang Menuju Sistem Informasi yang Seru</h3>
                                <h3>Eksplorasi Data</h3>
                                <h3>Analisis Berbagai Produk Kulit Anda</h3>
                            </div>
                        </div>
                        <div class="bullets">
                            <span class="active" data-value="1"></span>
                            <span data-value="2"></span>
                            <span data-value="3"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script type="text/javascript" src="particles.js"></script>
    <script type="text/javascript" src="app.js"></script>
    <script src="login.js"></script>
    <script>
        function toggleForm(formId) {
            document.getElementById('login-form').classList.remove('active');
            document.getElementById('register-form').classList.remove('active');
            document.getElementById(formId).classList.add('active');
        }
    </script>
</body>
</html>
