-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Jul 2025 pada 07.15
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
-- Database: `lowong`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bidang`
--

CREATE TABLE `bidang` (
  `id` int(11) NOT NULL,
  `nama_bidang` varchar(100) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bidang`
--

INSERT INTO `bidang` (`id`, `nama_bidang`, `parent_id`, `gambar`, `deskripsi`) VALUES
(1, 'Keuangan', 0, '683cd344a299b.jpg', 'dibutuhkan karyawan yang bisa microsoft excel, word, dan ppt untuk bidang keuangan ini, umur minimal 20 maksimal 30 tahun'),
(2, 'Informasi Teknologi', 0, '683cd34eb3d5f.png', 'Bidang ini berisikan orang bisa IT'),
(8, 'Web Developer', 2, 'bidang_68674f056a858.png', 'membutuhkan karyawan yang bisa bahasa pemrograman php dan javascript'),
(9, 'assets', 1, 'bidang_6867071d73e3e.png', 'membutuhkan karyawan yang bisa microsoft');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bobot_kriteria`
--

CREATE TABLE `bobot_kriteria` (
  `id` int(11) NOT NULL,
  `kriteria` varchar(50) NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `bidang_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bobot_kriteria`
--

INSERT INTO `bobot_kriteria` (`id`, `kriteria`, `bobot`, `bidang_id`) VALUES
(36, 'pendidikan', 0.33, 9),
(37, 'pengalaman_kerja', 0.15, 9),
(38, 'skill', 0.37, 9),
(39, 'skill', 0.50, 8),
(40, 'pendidikan', 0.20, 8),
(41, 'pengalaman_kerja', 0.30, 8),
(42, 'nilai_ipk', 0.15, 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `calon_karyawan`
--

CREATE TABLE `calon_karyawan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nohp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bidang_id` int(11) DEFAULT NULL,
  `ijazah` varchar(255) NOT NULL,
  `skck` varchar(255) NOT NULL,
  `surat_kesehatan` varchar(255) DEFAULT NULL,
  `ktp` varchar(255) DEFAULT NULL,
  `status` enum('Diproses','Lolos','Tidak Lolos') NOT NULL DEFAULT 'Diproses',
  `diterima` int(11) NOT NULL,
  `tahun_daftar` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_pencocokan`
--

CREATE TABLE `hasil_pencocokan` (
  `id` int(11) NOT NULL,
  `calon_karyawan_id` int(11) NOT NULL,
  `bidang_id` int(11) NOT NULL,
  `skor_kecocokan` decimal(5,2) NOT NULL,
  `peringkat` int(11) DEFAULT NULL,
  `tanggal_pencocokan` datetime DEFAULT current_timestamp(),
  `detail_penjelasan_skor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nik` varchar(50) NOT NULL,
  `nama_karyawan` varchar(255) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `bagian` varchar(255) NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `bidang_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id`, `nik`, `nama_karyawan`, `jabatan`, `bagian`, `tanggal_masuk`, `bidang_id`, `created_at`) VALUES
(49, 'NIK-1751604440', 'Ayyub Abdillah', 'CEO', 'Web Developer', '2025-07-04', 2, '2025-07-04 04:47:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_kandidat`
--

CREATE TABLE `nilai_kandidat` (
  `id` int(11) NOT NULL,
  `calon_karyawan_id` int(11) NOT NULL,
  `kriteria` varchar(100) NOT NULL,
  `nilai` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil_ideal`
--

CREATE TABLE `profil_ideal` (
  `id` int(11) NOT NULL,
  `kriteria` varchar(50) NOT NULL,
  `nilai` int(11) NOT NULL,
  `bidang_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profil_ideal`
--

INSERT INTO `profil_ideal` (`id`, `kriteria`, `nilai`, `bidang_id`) VALUES
(41, 'pendidikan', 5, 9),
(42, 'pengalaman_kerja', 3, 9),
(43, 'skill', 5, 9),
(44, 'skill', 5, 8),
(45, 'pendidikan', 3, 8),
(46, 'pengalaman_kerja', 4, 8),
(47, 'nilai_ipk', 4, 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','perusahaan','karyawan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `nama`, `role`) VALUES
(2, 'sale@gmail.com', '$2y$10$lNEIaFN4PJczHOuKQV0oSuXu1.0SYtJbxx2XqvQXbXix6GLnTZ1.W', 'dafith', 'admin'),
(4, 'dafit@gmail.com', '$2y$10$BbltegyX80wZnEka6FpJw.HJzJEzb4WWpAZft2lvfkcDE.Tf5fK4u', 'dafith', 'perusahaan');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bidang`
--
ALTER TABLE `bidang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_bidang` (`nama_bidang`);

--
-- Indeks untuk tabel `bobot_kriteria`
--
ALTER TABLE `bobot_kriteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bobot_kriteria_bidang` (`bidang_id`);

--
-- Indeks untuk tabel `calon_karyawan`
--
ALTER TABLE `calon_karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `hasil_pencocokan`
--
ALTER TABLE `hasil_pencocokan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calon_karyawan_id` (`calon_karyawan_id`),
  ADD KEY `bidang_id` (`bidang_id`);

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `fk_karyawan_bidang` (`bidang_id`);

--
-- Indeks untuk tabel `nilai_kandidat`
--
ALTER TABLE `nilai_kandidat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calon_karyawan_id` (`calon_karyawan_id`);

--
-- Indeks untuk tabel `profil_ideal`
--
ALTER TABLE `profil_ideal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_profil_ideal_bidang` (`bidang_id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bidang`
--
ALTER TABLE `bidang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `bobot_kriteria`
--
ALTER TABLE `bobot_kriteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `calon_karyawan`
--
ALTER TABLE `calon_karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT untuk tabel `hasil_pencocokan`
--
ALTER TABLE `hasil_pencocokan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `nilai_kandidat`
--
ALTER TABLE `nilai_kandidat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `profil_ideal`
--
ALTER TABLE `profil_ideal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bobot_kriteria`
--
ALTER TABLE `bobot_kriteria`
  ADD CONSTRAINT `fk_bobot_kriteria_bidang` FOREIGN KEY (`bidang_id`) REFERENCES `bidang` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hasil_pencocokan`
--
ALTER TABLE `hasil_pencocokan`
  ADD CONSTRAINT `hasil_pencocokan_ibfk_1` FOREIGN KEY (`calon_karyawan_id`) REFERENCES `calon_karyawan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_pencocokan_ibfk_2` FOREIGN KEY (`bidang_id`) REFERENCES `bidang` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `fk_karyawan_bidang` FOREIGN KEY (`bidang_id`) REFERENCES `bidang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai_kandidat`
--
ALTER TABLE `nilai_kandidat`
  ADD CONSTRAINT `nilai_kandidat_ibfk_1` FOREIGN KEY (`calon_karyawan_id`) REFERENCES `calon_karyawan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `profil_ideal`
--
ALTER TABLE `profil_ideal`
  ADD CONSTRAINT `fk_profil_ideal_bidang` FOREIGN KEY (`bidang_id`) REFERENCES `bidang` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
