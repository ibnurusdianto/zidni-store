<?php
$conn = new mysqli("localhost", "root", "", "toko_elektronik");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT id, product_name, category, price FROM products WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
    } else {
        echo json_encode(false);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['productId']) && isset($_POST['editProductName']) && isset($_POST['editProductCategory']) && isset($_POST['editProductPrice'])) {
        $productId = intval($_POST['productId']);
        $productName = $conn->real_escape_string($_POST['editProductName']);
        $productCategory = $conn->real_escape_string($_POST['editProductCategory']);
        $productPrice = floatval($_POST['editProductPrice']);

        $update_sql = "UPDATE products SET product_name = '$productName', category = '$productCategory', price = $productPrice WHERE id = $productId";
        
        if ($conn->query($update_sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Produk berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat memperbarui produk.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    }
}

$conn->close();
?>
