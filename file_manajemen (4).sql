-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Nov 2025 pada 13.42
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
-- Struktur dari tabel `favorites`
--

CREATE TABLE `favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `file_id` bigint(20) UNSIGNED DEFAULT NULL,
  `folder_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `file_id`, `folder_id`, `created_at`, `updated_at`) VALUES
(1, 6, 35, NULL, '2025-11-08 00:05:27', '2025-11-08 00:05:27'),
(17, 6, 37, NULL, '2025-11-08 08:47:18', '2025-11-08 08:47:18'),
(18, 6, 38, NULL, '2025-11-08 10:14:45', '2025-11-08 10:14:45'),
(19, 6, 23, NULL, '2025-11-08 10:14:58', '2025-11-08 10:14:58'),
(21, 6, 40, NULL, '2025-11-08 10:52:25', '2025-11-08 10:52:25'),
(22, 6, 98, NULL, '2025-11-11 01:14:59', '2025-11-11 01:14:59'),
(23, 6, 102, NULL, '2025-11-12 05:35:33', '2025-11-12 05:35:33');

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
  `status` enum('Public','Private','Selective') DEFAULT 'Private',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `files`
--

INSERT INTO `files` (`id`, `folder_id`, `uploaded_by`, `divisi`, `file_name`, `file_path`, `file_type`, `file_size`, `mime_type`, `status`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(81, NULL, 6, NULL, 'Hasil edit foto berada di tengah salju lembut dengan jaket puffy pastel.png', 'uploads/files/aRyegYTdhpyd4ElNNPvDVkKKNFsF2MqfOhxA8oMq.png', 'png', 1115755, NULL, 'Private', NULL, '2025-11-10 23:53:50', '2025-11-12 05:01:38', '2025-11-12 05:01:38'),
(99, NULL, 6, NULL, 'WhatsApp Image 2025-11-12 at 10.57.53.jpeg', 'uploads/files/Z3AI9ONLt1vOQtntiu9o8ExcCgTXAJwrx6GeXJJz.jpg', 'jpeg', 195639, NULL, 'Private', NULL, '2025-11-12 05:00:25', '2025-11-12 05:02:14', '2025-11-12 05:02:14'),
(100, 45, 6, NULL, 'Hasil edit foto santai tepi danau.png', 'uploads/Hasil edit foto santai tepi danau.png', 'png', NULL, NULL, 'Private', NULL, '2025-11-12 05:15:11', '2025-11-12 05:35:31', '2025-11-12 05:35:31'),
(101, 46, 6, NULL, 'Screen Recording 2025-11-09 174325.mp4', 'uploads/Screen Recording 2025-11-09 174325.mp4', 'mp4', NULL, NULL, 'Private', NULL, '2025-11-12 05:34:15', '2025-11-12 05:34:15', NULL),
(102, 47, 6, NULL, 'Hasil edit foto night street style.png', 'uploads/Hasil edit foto night street style.png', 'png', NULL, NULL, 'Private', NULL, '2025-11-12 05:34:15', '2025-11-12 05:34:15', NULL);

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
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `folders`
--

INSERT INTO `folders` (`id`, `name`, `parent_id`, `divisi`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`, `sort_order`) VALUES
(17, 'Laravell', NULL, NULL, 'Private', 6, '2025-11-02 05:46:48', '2025-11-12 05:02:26', NULL, 0),
(45, 'New folder (2)', NULL, NULL, 'Private', 6, '2025-11-12 05:15:11', '2025-11-12 05:26:37', NULL, 0),
(46, 'New folder (3)', 17, NULL, 'Private', 6, '2025-11-12 05:34:15', '2025-11-12 05:34:38', NULL, 0),
(47, 'New folder', 45, NULL, 'Private', 6, '2025-11-12 05:34:15', '2025-11-12 05:35:13', NULL, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_11_08_052554_create_notifications_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(6, 'Admin', 'admin@gmail.com', '$2y$10$1u2iyMIauzrfyG94oN7PWOwjdI4CelSViQ9uUMimxRKzYo8YpYJZa', 'admin', '2025-10-17 20:55:07', '2025-11-08 23:38:05', 'profile_photos/6.png'),
(7, 'Diva', 'diva@gmail.com', '$2y$10$lWGRfAu/VtfkICobujE.3.sGdoURnVDD9UlI1GnSoo0XNnCJZQ8TC', 'editor', '2025-10-17 20:57:07', '2025-10-17 20:57:07', '');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

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
-- AUTO_INCREMENT untuk tabel `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
