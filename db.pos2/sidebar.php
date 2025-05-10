<div class="sidebar">
    <div class="sidebar-header">
        <h3>Toko Digital</h3>
    </div>
    <ul class="sidebar-menu">
        <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="produk.php"><i class="fas fa-box"></i> Produk</a></li>
        <li><a href="transaksi.php"><i class="fas fa-shopping-cart"></i> Transaksi</a></li>
        <li><a href="laporan.php"><i class="fas fa-chart-bar"></i> Laporan</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<style>
.sidebar {
    width: 250px;
    background: #343a40;
    color: #fff;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
}

.sidebar-header {
    padding: 20px;
    background: #2c3136;
    text-align: center;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li a {
    display: block;
    padding: 15px 20px;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s;
}

.sidebar-menu li a:hover {
    background: #495057;
    padding-left: 25px;
}

.sidebar-menu li a i {
    margin-right: 10px;
}
</style>