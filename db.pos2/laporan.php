<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Toko Digital</title>
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
        .form-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .form-filter input, .form-filter select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Laporan</h1>
        
        <div class="card">
            <div class="card-header">
                <h3>Laporan Penjualan</h3>
            </div>
            <div class="card-body">
                <form method="GET" class="form-filter">
                    <input type="date" name="dari_tanggal">
                    <input type="date" name="sampai_tanggal">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>Total Penjualan</th>
                            <th>Diskon</th>
                            <th>Total Setelah Diskon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $where = "";
                        if (isset($_GET['dari_tanggal']) && !empty($_GET['dari_tanggal'])) {
                            $where .= " AND t.tanggal_transaksi >= '" . $_GET['dari_tanggal'] . "'";
                        }
                        if (isset($_GET['sampai_tanggal']) && !empty($_GET['sampai_tanggal'])) {
                            $where .= " AND t.tanggal_transaksi <= '" . $_GET['sampai_tanggal'] . " 23:59:59'";
                        }
                        
                        $sql = "SELECT * FROM laporan_penjualan";
                        if (!empty($where)) {
                            $sql = "SELECT 
                                t.id_transaksi,
                                t.tanggal_transaksi,
                                SUM(d.subtotal) AS total_penjualan,
                                t.diskon,
                                t.total_setelah_diskon
                            FROM transaksi t
                            JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi
                            WHERE 1=1 $where
                            GROUP BY t.id_transaksi";
                        }
                        
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id_transaksi'] . "</td>";
                                echo "<td>" . $row['tanggal_transaksi'] . "</td>";
                                echo "<td>Rp " . number_format($row['total_penjualan'], 0, ',', '.') . "</td>";
                                echo "<td>Rp " . number_format($row['diskon'], 0, ',', '.') . "</td>";
                                echo "<td>Rp " . number_format($row['total_setelah_diskon'], 0, ',', '.') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Tidak ada data penjualan</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Laporan Stok Produk</h3>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>ID Produk</th>
                            <th>Nama Produk</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM laporan_stok");
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $warna = $row['stok'] < 5 ? 'style="color: red; font-weight: bold;"' : '';
                                echo "<tr>";
                                echo "<td>" . $row['id_produk'] . "</td>";
                                echo "<td>" . $row['nama_produk'] . "</td>";
                                echo "<td $warna>" . $row['stok'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Tidak ada data produk</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Laporan Keuangan</h3>
            </div>
            <div class="card-body">
                <form method="GET" class="form-filter">
                    <input type="date" name="dari_tanggal_keuangan">
                    <input type="date" name="sampai_tanggal_keuangan">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Metode Pembayaran</th>
                            <th>Total Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $where = "";
                        if (isset($_GET['dari_tanggal_keuangan']) && !empty($_GET['dari_tanggal_keuangan'])) {
                            $where .= " AND tanggal_bayar >= '" . $_GET['dari_tanggal_keuangan'] . "'";
                        }
                        if (isset($_GET['sampai_tanggal_keuangan']) && !empty($_GET['sampai_tanggal_keuangan'])) {
                            $where .= " AND tanggal_bayar <= '" . $_GET['sampai_tanggal_keuangan'] . " 23:59:59'";
                        }
                        
                        $sql = "SELECT * FROM laporan_keuangan";
                        if (!empty($where)) {
                            $sql = "SELECT 
                                tanggal_bayar as tanggal_bayar,
                                metode,
                                SUM(jumlah_bayar) AS total_pembayaran
                            FROM pembayaran
                            WHERE 1=1 $where
                            GROUP BY tanggal_bayar, metode";
                        }
                        
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['tanggal_bayar'] . "</td>";
                                echo "<td>" . ucfirst(str_replace('_', ' ', $row['metode'])) . "</td>";
                                echo "<td>Rp " . number_format($row['total_pembayaran'], 0, ',', '.') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Tidak ada data keuangan</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>