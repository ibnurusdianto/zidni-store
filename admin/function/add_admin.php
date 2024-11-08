<?php
session_start();
$conn = new mysqli("localhost", "root", "", "toko_elektronik");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['adminUsername'];
    $password = $_POST['adminPassword'];
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['status'] = "Admin berhasil ditambahkan.";
    } else {
        $_SESSION['status'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: ../dashboard/admin.php");
    exit();
}
?>
