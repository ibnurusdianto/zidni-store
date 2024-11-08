<?php
session_start();
$conn = new mysqli("localhost", "root", "", "toko_elektronik");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';

if ($id) {
    $stmt = $conn->prepare("SELECT thumbnail FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->bind_result($thumbnail);
        $stmt->fetch();
        $stmt->close();
        
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if (file_exists($thumbnail)) {
                unlink($thumbnail);
            }
            echo json_encode(['status' => 'success', 'message' => 'Produk berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus produk.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menemukan produk.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID produk tidak valid.']);
}

$conn->close();
?>
