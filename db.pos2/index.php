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
    <title>Dashboard - Toko Digital</title>
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
        .card-body {
            padding: 10px 0;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Dashboard</h1>
        
        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <h3>Total Produk</h3>
                </div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(*) as total FROM produk";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    echo "<h2>" . $row['total'] . "</h2>";
                    ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Total Transaksi Hari Ini</h3>
                </div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    echo "<h2>" . $row['total'] . "</h2>";
                    ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Pendapatan Hari Ini</h3>
                </div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT SUM(total_setelah_diskon) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    echo "<h2>Rp " . number_format($row['total'], 0, ',', '.') . "</h2>";
                    ?>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Transaksi Terakhir</h3>
            </div>
            <div class="card-body">
                <table border="1" width="100%" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Diskon</th>
                            <th>Total Setelah Diskon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM transaksi ORDER BY tanggal_transaksi DESC LIMIT 5";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id_transaksi'] . "</td>";
                                echo "<td>" . $row['tanggal_transaksi'] . "</td>";
                                echo "<td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>";
                                echo "<td>Rp " . number_format($row['diskon'], 0, ',', '.') . "</td>";
                                echo "<td>Rp " . number_format($row['total_setelah_diskon'], 0, ',', '.') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Tidak ada transaksi</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>