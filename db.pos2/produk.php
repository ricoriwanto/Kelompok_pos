<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
include 'sidebar.php';

// Proses CRUD
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $stmt = $conn->prepare("INSERT INTO produk (nama_produk, deskripsi, harga, stok) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $nama, $deskripsi, $harga, $stok);
    $stmt->execute();
    $stmt->close();
    
    header("Location: produk.php");
    exit;
}

if (isset($_POST['update'])) {
    $id = $_POST['id_produk'];
    $nama = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $stmt = $conn->prepare("UPDATE produk SET nama_produk=?, deskripsi=?, harga=?, stok=? WHERE id_produk=?");
    $stmt->bind_param("ssdii", $nama, $deskripsi, $harga, $stok, $id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: produk.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    $stmt = $conn->prepare("DELETE FROM produk WHERE id_produk=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: produk.php");
    exit;
}

// Ambil data untuk edit
$produk_edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM produk WHERE id_produk=$id");
    $produk_edit = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Toko Digital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }
        .btn-primary {
            background: #007bff;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-success {
            background: #28a745;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-buttons a {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Manajemen Produk</h1>
        
        <div class="card">
            <div class="card-header">
                <h3><?php echo isset($produk_edit) ? 'Edit Produk' : 'Tambah Produk'; ?></h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if (isset($produk_edit)): ?>
                        <input type="hidden" name="id_produk" value="<?php echo $produk_edit['id_produk']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk" required 
                            value="<?php echo isset($produk_edit) ? $produk_edit['nama_produk'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi"><?php echo isset($produk_edit) ? $produk_edit['deskripsi'] : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input type="number" id="harga" name="harga" required 
                            value="<?php echo isset($produk_edit) ? $produk_edit['harga'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="number" id="stok" name="stok" required 
                            value="<?php echo isset($produk_edit) ? $produk_edit['stok'] : ''; ?>">
                    </div>
                    
                    <?php if (isset($produk_edit)): ?>
                        <button type="submit" name="update" class="btn btn-primary">Update Produk</button>
                        <a href="produk.php" class="btn btn-danger">Batal</a>
                    <?php else: ?>
                        <button type="submit" name="tambah" class="btn btn-success">Tambah Produk</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Daftar Produk</h3>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM produk ORDER BY id_produk DESC";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id_produk'] . "</td>";
                                echo "<td>" . $row['nama_produk'] . "</td>";
                                echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                                echo "<td>" . $row['stok'] . "</td>";
                                echo "<td>" . $row['tanggal_dibuat'] . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<a href='produk.php?edit=" . $row['id_produk'] . "' class='btn btn-primary'><i class='fas fa-edit'></i></a>";
                                echo "<a href='produk.php?hapus=" . $row['id_produk'] . "' class='btn btn-danger' onclick='return confirm(\"Yakin ingin menghapus?\")'><i class='fas fa-trash'></i></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Tidak ada produk</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>