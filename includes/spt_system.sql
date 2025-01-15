-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Jan 2025 pada 08.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spt_system`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `no_hp` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id`, `nama_lengkap`, `no_hp`) VALUES
(2, 'Aidil Oktadwifa Prayetno ', '12324'),
(6, 'Alkhafi Kurniawan', '081234567890'),
(7, 'Elsi Primasari', '098765123456');

-- --------------------------------------------------------

--
-- Struktur dari tabel `spt`
--

CREATE TABLE `spt` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `petugas_id` int(11) NOT NULL,
  `tanggal_pergi` date NOT NULL,
  `tanggal_pulang` date NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `spt`
--

INSERT INTO `spt` (`id`, `karyawan_id`, `petugas_id`, `tanggal_pergi`, `tanggal_pulang`, `keterangan`) VALUES
(6, 2, 2, '2025-01-09', '2025-01-15', 'Jambi'),
(9, 7, 2, '2025-01-10', '2025-01-13', 'Jakarta'),
(10, 7, 2, '2025-01-14', '2025-01-15', 'Palembang'),
(11, 2, 2, '2025-01-16', '2025-01-17', 'JAmbi\r\n\r\n'),
(12, 6, 2, '2025-01-10', '2025-01-11', 'Padang'),
(13, 6, 2, '2025-01-12', '2025-01-13', 'Padang'),
(14, 6, 2, '2025-01-14', '2025-01-15', 'Padang'),
(15, 6, 2, '2025-01-16', '2025-01-17', 'Padang'),
(16, 6, 2, '2025-01-18', '2025-01-19', 'Padang'),
(17, 6, 2, '2025-01-20', '2025-01-21', 'Padang'),
(18, 7, 2, '2025-01-20', '2025-01-21', 'Padang');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin', 'admin', 'admin'),
(2, 'petugas', 'petugas', 'petugas123', 'petugas'),
(6, 'MUHAMMAD NADAFFA', 'daffa1', 'daffa123', 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `spt`
--
ALTER TABLE `spt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`),
  ADD KEY `petugas_id` (`petugas_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `spt`
--
ALTER TABLE `spt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `spt`
--
ALTER TABLE `spt`
  ADD CONSTRAINT `spt_ibfk_1` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`),
  ADD CONSTRAINT `spt_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
