-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Nov 2025 pada 13.51
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
-- Database: `file_manajemen`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `divisi` varchar(50) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `status` enum('Public','Private') DEFAULT 'Private',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `files`
--

INSERT INTO `files` (`id`, `folder_id`, `uploaded_by`, `divisi`, `file_name`, `file_path`, `file_type`, `file_size`, `mime_type`, `status`, `description`, `created_at`, `updated_at`) VALUES
(20, NULL, 7, NULL, 'Siomay.jpg', 'uploads/files/FTx5jYm383CESptkCK8SzChHtIOupaOar0McEoTb.jpg', 'jpg', 54229, NULL, 'Private', NULL, '2025-11-04 05:30:13', '2025-11-04 05:30:13'),
(21, NULL, 6, NULL, 'pulau bali.webp', 'uploads/files/UJceJX4ipSs4MoE279SYZXQxVK8aOOP36JP4hdKK.webp', 'webp', 32852, NULL, 'Private', NULL, '2025-11-04 05:44:33', '2025-11-04 05:44:33'),
(22, NULL, 6, NULL, 'Minggu-11 25-29 Agustus.docx.pdf', 'uploads/files/zgMEMTyXcH3LIGfsJO9lwvaci0dNjewJz9obFMOF.pdf', 'pdf', 86881, NULL, 'Private', NULL, '2025-11-04 05:45:31', '2025-11-04 05:45:31'),
(23, NULL, 6, NULL, 'Screen Recording 2025-10-21 193620.mp4', 'uploads/files/StM7DFa49KuzbS5OhR52Lj9i6CTwUKrZYWhsEe5W.mp4', 'mp4', 1594244, NULL, 'Private', NULL, '2025-11-04 05:46:02', '2025-11-04 05:46:02'),
(24, NULL, 6, NULL, 'Caption feed.txt', 'uploads/files/ppEllCM4ZDnMVBYyjf7CDPvr9IUOReQQCB9cZEbj.txt', 'txt', 748, NULL, 'Private', NULL, '2025-11-04 05:46:44', '2025-11-04 05:46:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `file_access_logs`
--

CREATE TABLE `file_access_logs` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` enum('view','download','edit','delete','upload') NOT NULL,
  `accessed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `file_shares`
--

CREATE TABLE `file_shares` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `shared_with` int(11) DEFAULT NULL,
  `permission` enum('view','edit','download') DEFAULT 'view',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `divisi` varchar(50) DEFAULT NULL,
  `status` enum('Public','Private') DEFAULT 'Private',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `folders`
--

INSERT INTO `folders` (`id`, `name`, `parent_id`, `divisi`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(17, 'Laravel', NULL, NULL, 'Private', 6, '2025-11-02 05:46:48', '2025-11-02 05:46:48', NULL),
(18, 'Contoh', 17, NULL, 'Private', 6, '2025-11-02 05:46:59', '2025-11-02 05:58:04', NULL),
(19, 'Coba', 18, NULL, 'Private', 6, '2025-11-02 05:47:15', '2025-11-02 05:47:15', NULL),
(20, 'Coba', NULL, NULL, 'Private', 6, '2025-11-04 01:37:29', '2025-11-04 05:40:11', NULL),
(21, 'POLTEK', NULL, NULL, 'Private', 7, '2025-11-04 01:39:55', '2025-11-04 01:39:55', NULL),
(22, 'TEKNIK', NULL, NULL, 'Private', 7, '2025-11-04 01:40:23', '2025-11-04 01:40:23', NULL),
(23, 'COBA SAJA', 22, NULL, 'Private', 7, '2025-11-04 01:40:37', '2025-11-04 01:40:37', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor','viewer') DEFAULT 'viewer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `photo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `photo`) VALUES
(6, 'Admin', 'admin@gmail.com', '$2y$10$1u2iyMIauzrfyG94oN7PWOwjdI4CelSViQ9uUMimxRKzYo8YpYJZa', 'admin', '2025-10-17 20:55:07', '2025-11-04 05:24:11', 'profile_photos/6.jpg'),
(7, 'Diva', 'diva@gmail.com', '$2y$10$lWGRfAu/VtfkICobujE.3.sGdoURnVDD9UlI1GnSoo0XNnCJZQ8TC', 'editor', '2025-10-17 20:57:07', '2025-10-17 20:57:07', '');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `files_folder_fk` (`folder_id`),
  ADD KEY `files_uploaded_by_fk` (`uploaded_by`);

--
-- Indeks untuk tabel `file_access_logs`
--
ALTER TABLE `file_access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `file_shares`
--
ALTER TABLE `file_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `shared_with` (`shared_with`);

--
-- Indeks untuk tabel `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_parent_id` (`parent_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `file_access_logs`
--
ALTER TABLE `file_access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `file_shares`
--
ALTER TABLE `file_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_folder_fk` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `files_uploaded_by_fk` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `file_access_logs`
--
ALTER TABLE `file_access_logs`
  ADD CONSTRAINT `file_access_logs_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_access_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `file_shares`
--
ALTER TABLE `file_shares`
  ADD CONSTRAINT `file_shares_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_shares_ibfk_2` FOREIGN KEY (`shared_with`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `fk_folders_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_parent_folder` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `folders_parent_fk` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
