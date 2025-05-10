<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
include 'sidebar.php';

// Proses transaksi baru
if (isset($_POST['buat_transaksi'])) {
    $total = $_POST['total'];
    $diskon = $_POST['diskon'] ?? 0;
    $total_setelah_diskon = $total - $diskon;
    $metode = $_POST['metode_pembayaran'];
    
    // Insert transaksi
    $stmt = $conn->prepare("INSERT INTO transaksi (total, diskon, total_setelah_diskon, metode_pembayaran) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ddds", $total, $diskon, $total_setelah_diskon, $metode);
    $stmt->execute();
    $id_transaksi = $stmt->insert_id;
    $stmt->close();
    
    // Insert detail transaksi
    foreach ($_POST['produk'] as $produk) {
        $id_produk = $produk['id'];
        $jumlah = $produk['jumlah'];
        $harga = $produk['harga'];
        $subtotal = $jumlah * $harga;
        
        $stmt = $conn->prepare("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiidd", $id_transaksi, $id_produk, $jumlah, $harga, $subtotal);
        $stmt->execute();
        $stmt->close();
        
        // Update stok produk
        $stmt = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
        $stmt->bind_param("ii", $jumlah, $id_produk);
        $stmt->execute();
        $stmt->close();
    }
    
    // Insert pembayaran
    $stmt = $conn->prepare("INSERT INTO pembayaran (id_transaksi, jumlah_bayar, metode) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $id_transaksi, $total_setelah_diskon, $metode);
    $stmt->execute();
    $stmt->close();
    
    header("Location: transaksi.php?sukses=1");
    exit;
}

// Ambil data produk untuk dropdown
$produk_list = $conn->query("SELECT * FROM produk WHERE stok > 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Toko Digital</title>
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
        .form-group input, .form-group select, .form-group textarea {
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
        .produk-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .produk-item select, .produk-item input {
            flex: 1;
        }
        .produk-item .btn {
            flex: 0 0 auto;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Transaksi</h1>
        
        <?php if (isset($_GET['sukses'])): ?>
            <div class="success-message">
                Transaksi berhasil dibuat!
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h3>Buat Transaksi Baru</h3>
            </div>
            <div class="card-body">
                <form method="POST" id="transaksiForm">
                    <div id="produkContainer">
                        <div class="produk-item">
                            <select name="produk[0][id]" class="produk-select" required>
                                <option value="">Pilih Produk</option>
                                <?php while ($produk = $produk_list->fetch_assoc()): ?>
                                    <option value="<?php echo $produk['id_produk']; ?>" 
                                        data-harga="<?php echo $produk['harga']; ?>"
                                        data-stok="<?php echo $produk['stok']; ?>">
                                        <?php echo $produk['nama_produk']; ?> (Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <input type="number" name="produk[0][jumlah]" min="1" value="1" class="jumlah" required>
                            <input type="hidden" name="produk[0][harga]" class="harga">
                            <button type="button" class="btn btn-danger hapus-produk"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    
                    <button type="button" id="tambahProduk" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Produk</button>
                    
                    <div class="form-group">
                        <label for="diskon">Diskon (Rp)</label>
                        <input type="number" id="diskon" name="diskon" min="0" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="metode_pembayaran">Metode Pembayaran</label>
                        <select id="metode_pembayaran" name="metode_pembayaran" required>
                            <option value="tunai">Tunai</option>
                            <option value="kartu_kredit">Kartu Kredit</option>
                            <option value="kartu_debit">Kartu Debit</option>
                            <option value="digital">Digital</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Total</label>
                        <input type="number" id="total" name="total" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Total Setelah Diskon</label>
                        <input type="number" id="total_setelah_diskon" name="total_setelah_diskon" readonly>
                    </div>
                    
                    <button type="submit" name="buat_transaksi" class="btn btn-success"><i class="fas fa-save"></i> Simpan Transaksi</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Riwayat Transaksi</h3>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Diskon</th>
                            <th>Total Setelah Diskon</th>
                            <th>Metode Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM transaksi ORDER BY tanggal_transaksi DESC LIMIT 10";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id_transaksi'] . "</td>";
                                echo "<td>" . $row['tanggal_transaksi'] . "</td>";
                                echo "<td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>";
                                echo "<td>Rp " . number_format($row['diskon'], 0, ',', '.') . "</td>";
                                echo "<td>Rp " . number_format($row['total_setelah_diskon'], 0, ',', '.') . "</td>";
                                echo "<td>" . ucfirst(str_replace('_', ' ', $row['metode_pembayaran'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Tidak ada transaksi</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let produkCounter = 1;
        
        document.getElementById('tambahProduk').addEventListener('click', function() {
            const container = document.getElementById('produkContainer');
            const newItem = document.createElement('div');
            newItem.className = 'produk-item';
            newItem.innerHTML = `
                <select name="produk[${produkCounter}][id]" class="produk-select" required>
                    <option value="">Pilih Produk</option>
                    <?php 
                    $produk_list->data_seek(0);
                    while ($produk = $produk_list->fetch_assoc()): ?>
                        <option value="<?php echo $produk['id_produk']; ?>" 
                            data-harga="<?php echo $produk['harga']; ?>"
                            data-stok="<?php echo $produk['stok']; ?>">
                            <?php echo $produk['nama_produk']; ?> (Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <input type="number" name="produk[${produkCounter}][jumlah]" min="1" value="1" class="jumlah" required>
                <input type="hidden" name="produk[${produkCounter}][harga]" class="harga">
                <button type="button" class="btn btn-danger hapus-produk"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(newItem);
            produkCounter++;
            
            // Add event listeners to new elements
            addEventListenersToNewItem(newItem);
        });
        
        function addEventListenersToNewItem(item) {
            const select = item.querySelector('.produk-select');
            const jumlah = item.querySelector('.jumlah');
            const harga = item.querySelector('.harga');
            const hapusBtn = item.querySelector('.hapus-produk');
            
            select.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    harga.value = selectedOption.dataset.harga;
                    jumlah.max = selectedOption.dataset.stok;
                    hitungTotal();
                }
            });
            
            jumlah.addEventListener('input', hitungTotal);
            
            hapusBtn.addEventListener('click', function() {
                item.remove();
                hitungTotal();
            });
        }
        
        document.getElementById('diskon').addEventListener('input', hitungTotal);
        
        function hitungTotal() {
            let total = 0;
            
            document.querySelectorAll('.produk-item').forEach(item => {
                const select = item.querySelector('.produk-select');
                const jumlah = item.querySelector('.jumlah');
                const harga = item.querySelector('.harga');
                
                if (select.value && jumlah.value && harga.value) {
                    total += parseFloat(harga.value) * parseInt(jumlah.value);
                }
            });
            
            const diskon = parseFloat(document.getElementById('diskon').value) || 0;
            const totalSetelahDiskon = total - diskon;
            
            document.getElementById('total').value = total;
            document.getElementById('total_setelah_diskon').value = totalSetelahDiskon;
        }
        
        // Add event listeners to initial elements
        document.querySelectorAll('.produk-item').forEach(addEventListenersToNewItem);
    </script>
</body>
</html>