-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Bulan Mei 2025 pada 03.36
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_digital`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`, `harga_satuan`, `subtotal`) VALUES
(1, 1, 1, 1, 10.00, 10.00),
(2, 2, 2, 1, 99999999.99, 99999999.99),
(3, 3, 3, 1, 99999999.99, 99999999.99),
(4, 4, 4, 2, 100.00, 200.00);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `laporan_keuangan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `laporan_keuangan` (
`tanggal_bayar` datetime
,`metode` enum('tunai','kartu_kredit','kartu_debit','digital')
,`total_pembayaran` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `laporan_penjualan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `laporan_penjualan` (
`id_transaksi` int(11)
,`tanggal_transaksi` datetime
,`total_penjualan` decimal(32,2)
,`diskon` decimal(10,2)
,`total_setelah_diskon` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `laporan_stok`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `laporan_stok` (
`id_produk` int(11)
,`nama_produk` varchar(100)
,`stok` int(11)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `metode` enum('tunai','kartu_kredit','kartu_debit','digital') NOT NULL,
  `tanggal_bayar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_transaksi`, `jumlah_bayar`, `metode`, `tanggal_bayar`) VALUES
(1, 1, 10.00, 'tunai', '2025-05-08 11:41:16'),
(2, 2, 99999994.99, 'kartu_kredit', '2025-05-08 12:14:49'),
(3, 3, 99999099.99, 'kartu_debit', '2025-05-08 12:17:59'),
(4, 4, 140.00, 'tunai', '2025-05-08 13:56:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `deskripsi`, `harga`, `stok`, `tanggal_dibuat`) VALUES
(1, 'kipas', 'mengeluarkan angin sejuk', 10.00, 99, '2025-05-08 04:39:12'),
(2, 'mobil', 'mobil sport dengan merek lamborgini', 99999999.99, 11, '2025-05-08 05:13:48'),
(3, 'motor', 'motor sport dengan merek ducati', 99999999.99, 18, '2025-05-08 05:16:00'),
(4, 'baju', 'baju dengan motif batik asli solo', 100.00, 7998, '2025-05-08 06:55:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `tanggal_transaksi` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `diskon` decimal(10,2) DEFAULT 0.00,
  `total_setelah_diskon` decimal(10,2) NOT NULL,
  `metode_pembayaran` enum('tunai','kartu_kredit','kartu_debit','digital') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tanggal_transaksi`, `total`, `diskon`, `total_setelah_diskon`, `metode_pembayaran`) VALUES
(1, '2025-05-08 11:41:16', 10.00, 0.00, 10.00, 'tunai'),
(2, '2025-05-08 12:14:48', 99999999.99, 5.00, 99999994.99, 'kartu_kredit'),
(3, '2025-05-08 12:17:59', 99999999.99, 900.00, 99999099.99, 'kartu_debit'),
(4, '2025-05-08 13:56:05', 200.00, 60.00, 140.00, 'tunai');

-- --------------------------------------------------------

--
-- Struktur untuk view `laporan_keuangan`
--
DROP TABLE IF EXISTS `laporan_keuangan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `laporan_keuangan`  AS SELECT `p`.`tanggal_bayar` AS `tanggal_bayar`, `p`.`metode` AS `metode`, sum(`p`.`jumlah_bayar`) AS `total_pembayaran` FROM `pembayaran` AS `p` GROUP BY `p`.`tanggal_bayar`, `p`.`metode` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `laporan_penjualan`
--
DROP TABLE IF EXISTS `laporan_penjualan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `laporan_penjualan`  AS SELECT `t`.`id_transaksi` AS `id_transaksi`, `t`.`tanggal_transaksi` AS `tanggal_transaksi`, sum(`d`.`subtotal`) AS `total_penjualan`, `t`.`diskon` AS `diskon`, `t`.`total_setelah_diskon` AS `total_setelah_diskon` FROM (`transaksi` `t` join `detail_transaksi` `d` on(`t`.`id_transaksi` = `d`.`id_transaksi`)) GROUP BY `t`.`id_transaksi` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `laporan_stok`
--
DROP TABLE IF EXISTS `laporan_stok`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `laporan_stok`  AS SELECT `produk`.`id_produk` AS `id_produk`, `produk`.`nama_produk` AS `nama_produk`, `produk`.`stok` AS `stok` FROM `produk` ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
