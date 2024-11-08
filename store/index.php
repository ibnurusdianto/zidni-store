<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zidni Store</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "toko_elektronik");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$product_name = "";
$sql = "SELECT * FROM products";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_term'])) {
    $product_name = $conn->real_escape_string($_POST['search_term']);
    $sql = "SELECT * FROM products WHERE product_name LIKE '%$product_name%'";
}
$result = $conn->query($sql);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="">Zidni Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="">Home</a>
                </li>
                <li class="nav-item">
                    <?php
                    if (isset($_SESSION['username'])) {
                        echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">' . htmlspecialchars($_SESSION['username']) . '</a>';
                    } else {
                        echo '<a class="nav-link" href="../admin/login/login.php">Login</a>';
                    }
                    ?>
                </li>
            </ul>
            <form class="d-flex" role="search" method="post">
                <input class="form-control me-2" type="search" name="search_term" placeholder="Cari handphone..." aria-label="Search" value="<?php echo htmlspecialchars($product_name); ?>">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>
<section class="text-center my-5">
    <div class="container d-flex align-items-center">
        <div class="me-4">
            <h2 class="mb-4">Selamat Datang di Toko Elektronik Kami</h2>
            <p class="lead">Kami menyediakan berbagai pilihan handphone terbaru dengan harga yang kompetitif. Temukan perangkat yang sesuai dengan kebutuhan Anda di toko kami.</p>
            <a href="alamat-toko.html" class="btn btn-primary">Kunjungi Toko Kami</a>
        </div>
        <img src="img/zidni.jpg" alt="Gambar Toko Elektronik" class="img-fluid custom-img">
    </div>
</section>
<div class="container mt-5">
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="card">';
                echo '<img src="../admin/uploads/' . htmlspecialchars($row['thumbnail']) . '" class="card-img-top" alt="' . htmlspecialchars($row['product_name']) . '">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['product_name']) . '</h5>';
                echo '<p class="card-text">Harga: Rp. ' . number_format($row['price'], 0, ',', '.') . '</p>';
                echo '</div>';
                echo '<ul class="list-group list-group-flush">';
                echo '<li class="list-group-item">Kategori: ' . htmlspecialchars($row['category']) . '</li>';
                echo '</ul>';
                echo '<div class="card-body">';
                echo '<button type="button" class="btn btn-success">Detail</button>';
                echo '<button type="button" class="btn btn-warning">Tambah ke Keranjang</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="col-12 text-center">Tidak ada produk ditemukan</div>';
        }
        $conn->close();
        ?>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin keluar?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="" method="post">
                    <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>
