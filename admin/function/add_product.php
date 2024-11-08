<?php
session_start();
$conn = new mysqli("localhost", "root", "", "toko_elektronik");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST['productName'];
    $productCategory = $_POST['productCategory'];
    $productPrice = $_POST['productPrice'];

    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["productImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["productImage"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['status'] = "File yang diunggah bukan gambar.";
        $uploadOk = 0;
    }

    if ($_FILES["productImage"]["size"] > 2000000) {
        $_SESSION['status'] = "Gambar terlalu besar. Maksimum ukuran adalah 2MB.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        $_SESSION['status'] = "Gambar sudah ada.";
        $uploadOk = 0;
    }

    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        $_SESSION['status'] = "Hanya format JPG, JPEG, PNG, & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    if ($uploadOk === 1) {
        if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO products (thumbnail, product_name, category, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sssd", $target_file, $productName, $productCategory, $productPrice);
                
                if ($stmt->execute()) {
                    $_SESSION['status'] = "Produk berhasil ditambahkan.";
                } else {
                    $_SESSION['status'] = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['status'] = "Error: " . $conn->error;
            }
        } else {
            $_SESSION['status'] = "Terjadi kesalahan saat mengupload gambar.";
        }
    }
    
    $conn->close();
    header("Location: ../dashboard/admin.php");
    exit();
}
?>
