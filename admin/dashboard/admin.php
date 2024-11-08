<?php
session_start();

$status = isset($_SESSION['status']) ? $_SESSION['status'] : '';
$admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : '';
unset($_SESSION['status']);

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../login/login.php?logout_message=" . urlencode("Berhasil logout dengan $admin_name, sampai jumpa kembali."));
    exit();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../login/login.php");
    exit();
}

$logout_message = isset($_GET['logout_message']) ? $_GET['logout_message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Toko Elektronik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="">Zidni-Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?action=logout">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <?php if ($status): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $status; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h2 class="text-center mb-4">Daftar Produk Handphone</h2>
    
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Tambah Produk</button>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAdminModal">Tambah Admin</button>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Thumbnail</th>
                <th scope="col">Produk</th>
                <th scope="col">Kategori</th>
                <th scope="col">Harga</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $conn = new mysqli("localhost", "root", "", "toko_elektronik");

            if ($conn->connect_error) {
                die("Koneksi gagal: " . $conn->connect_error);
            }

            $sql = "SELECT id, thumbnail, product_name, category, price FROM products";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='" . htmlspecialchars($row['thumbnail']) . "' alt='" . htmlspecialchars($row['product_name']) . "' class='img-fluid' style='width: 50px;'></td>";
                    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                    echo "<td>Rp. " . number_format($row['price'], 0, ',', '.') . "</td>";
                    echo "<td>
                            <button class='btn btn-warning btn-sm editProductBtn' data-id='" . $row['id'] . "' data-bs-toggle='modal' data-bs-target='#editProductModal'>Edit</button>
                            <button class='btn btn-danger btn-sm deleteProductBtn' data-id='" . $row['id'] . "' data-bs-toggle='modal' data-bs-target='#deleteProductModal'>Hapus</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Tidak ada produk ditemukan.</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteProductModalLabel">Hapus Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus produk ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdminModalLabel">Tambah Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAdminForm" action="../function/add_admin.php" method="POST">
                        <div class="mb-3">
                            <label for="adminUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="adminUsername" name="adminUsername" required>
                        </div>
                        <div class="mb-3">
                            <label for="adminPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah Admin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Status Penambahan Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($status): ?>
                        <p><?php echo $status; ?></p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center">Grafik Penjualan</h2>
    <canvas id="myChart" width="400" height="200"></canvas>
</div>

<div class="container mb-5">
    <h2 class="text-center my-4">Perintah Eksekusi</h2>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Eksekusi Perintah</h5>
            <textarea class="form-control" rows="4" placeholder="Masukkan perintah di sini..."></textarea>
            <button class="btn btn-primary mt-3">Eksekusi</button>
        </div>
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" action="../function/add_product.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="productImage" class="form-label">Thumbnail</label>
                        <input type="file" class="form-control" id="productImage" name="productImage" required>
                    </div>
                    <div class="mb-3">
                        <label for="productName" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="productName" name="productName" required>
                    </div>
                    <div class="mb-3">
                        <label for="productCategory" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="productCategory" name="productCategory" required>
                    </div>
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="productPrice" name="productPrice" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah Produk</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm">
                    <input type="hidden" id="productId" name="productId">
                    <div class="mb-3">
                        <label for="editProductName" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="editProductName" name="editProductName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductCategory" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="editProductCategory" name="editProductCategory" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductPrice" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="editProductPrice" name="editProductPrice" required>
                    </div>
                    <button type="button" class="btn btn-primary" id="updateProductBtn">Perbarui Produk</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.min.js" integrity="sha512-L0Shl7nXXzIlBSUUPpxrokqq4ojqgZFQczTYlGjzONGTDAcLremjwaWv5A+EDLnxhQzY5xUZPWLOLqYRkY0Cbw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    const editProductBtns = document.querySelectorAll('.editProductBtn');
    editProductBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            fetch(`../function/get_product.php?id=${productId}`)
                .then(response => response.json())
                .then(product => {
                    if (product) {
                        document.getElementById('productId').value = product.id;
                        document.getElementById('editProductName').value = product.product_name;
                        document.getElementById('editProductCategory').value = product.category;
                        document.getElementById('editProductPrice').value = product.price;
                    } else {
                        alert('Produk tidak ditemukan.');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    const deleteProductBtns = document.querySelectorAll('.deleteProductBtn');
    let productIdToDelete = null;

    deleteProductBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            productIdToDelete = this.getAttribute('data-id');
        });
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (productIdToDelete) {
            fetch(`../function/delete_product.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: productIdToDelete })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        }
    });

    document.getElementById('updateProductBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('editProductForm'));
        fetch('../function/get_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Handphone 1', 'Handphone 2', 'Handphone 3'],
            datasets: [{
                label: 'Harga',
                data: [300, 400, 450],
                backgroundColor: 'rgba(52, 152, 219, 0.5)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const logoutMessage = "<?php echo $logout_message; ?>";
    if (logoutMessage) {
        const myModal = new bootstrap.Modal(document.getElementById('logoutModal'));
        document.getElementById('logoutMessage').innerText = logoutMessage;
        myModal.show();
    }
</script>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="logoutMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
