-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2022 at 05:09 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_form`
--

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `department` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `department`) VALUES
(1, 'IT'),
(2, 'Marketing'),
(3, 'Logistik'),
(4, 'Maintenance'),
(5, 'Keperawatan'),
(6, 'Farmasi'),
(7, 'Finance');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gender`
--

CREATE TABLE `gender` (
  `id` int(11) NOT NULL,
  `gender` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `gender`
--

INSERT INTO `gender` (`id`, `gender`) VALUES
(1, 'Laki-laki'),
(2, 'Wanita');

-- --------------------------------------------------------

--
-- Table structure for table `inventaris`
--

CREATE TABLE `inventaris` (
  `id` int(11) NOT NULL,
  `jenis_inventaris_id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `no_inventaris` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `inventaris`
--

INSERT INTO `inventaris` (`id`, `jenis_inventaris_id`, `nama`, `no_inventaris`, `created_at`, `updated_at`) VALUES
(1, 4, 'Laptop', NULL, '2022-02-03 13:24:17', '2022-03-05 03:30:42'),
(7, 4, 'Printer', NULL, '2022-02-04 08:35:33', '2022-03-05 03:30:36'),
(15, 4, 'Kabel Lan', NULL, '2022-02-26 16:44:08', '2022-02-26 16:44:08'),
(16, 4, 'Vga', NULL, '2022-02-26 16:44:08', '2022-02-26 16:44:08'),
(17, 4, 'Mouse', NULL, '2022-02-26 16:44:08', '2022-02-26 16:44:08'),
(18, 4, 'Monitor', NULL, '2022-02-26 16:44:08', '2022-02-26 16:44:08'),
(19, 4, 'Ram', NULL, '2022-02-26 16:44:08', '2022-02-26 16:44:08'),
(20, 4, 'Kabel Lan', NULL, '2022-02-26 16:44:08', '2022-02-26 16:44:08'),
(21, 4, 'Hapus Data di Tera', NULL, '2022-02-26 16:44:08', '2022-02-26 16:44:08'),
(22, 4, 'Edit Data d iTera', NULL, '2022-02-26 16:44:08', '2022-02-26 16:44:08'),
(23, 4, 'Tambah Data di Tera', NULL, '2022-02-26 16:44:08', '2022-02-26 16:44:08'),
(24, 4, 'Lainnya', NULL, '2022-03-04 11:18:15', '2022-03-04 11:18:15'),
(25, 3, 'Lainnya', NULL, '2022-03-04 11:18:15', '2022-03-04 11:18:15'),
(26, 4, 'Modem', NULL, '2022-03-04 09:02:15', '2022-03-04 09:02:15'),
(27, 3, 'Meja', NULL, '2022-03-05 04:03:58', '2022-03-05 04:03:58'),
(28, 3, 'Rak', NULL, '2022-03-05 04:04:57', '2022-03-05 04:04:57'),
(29, 3, 'Kursi', NULL, '2022-03-05 04:16:20', '2022-03-05 04:16:20'),
(30, 3, 'Ambulan', 'A 123 CDE', '2022-03-05 05:13:19', '2022-03-05 05:13:19'),
(31, 3, 'Mesin Memmert UNB-500', NULL, '2022-03-05 05:44:01', '2022-03-05 05:44:01'),
(32, 3, 'Mesin corona ZTP 83F 1', NULL, '2022-03-05 05:44:23', '2022-03-05 05:44:23'),
(33, 3, 'Mesin steril corona ZTP 80A-7', NULL, '2022-03-05 05:45:38', '2022-03-05 05:45:38'),
(34, 3, 'Mesin Sealling', NULL, '2022-03-05 05:51:05', '2022-03-05 05:51:05');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id` int(11) NOT NULL,
  `jabatan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id`, `jabatan`) VALUES
(1, 'Staff'),
(2, 'Supervisor'),
(3, 'Manager');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_inventaris`
--

CREATE TABLE `jenis_inventaris` (
  `id` int(11) NOT NULL,
  `jenis_inventaris` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jenis_inventaris`
--

INSERT INTO `jenis_inventaris` (`id`, `jenis_inventaris`, `created_at`, `updated_at`) VALUES
(3, 'Maintenance', '2022-02-03 13:21:17', '2022-02-03 13:21:17'),
(4, 'IT', '2022-02-12 14:52:00', '2022-02-12 14:52:00');

-- --------------------------------------------------------

--
-- Table structure for table `keterangan_service`
--

CREATE TABLE `keterangan_service` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id` int(11) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_pasien` varchar(100) NOT NULL,
  `no_rm` int(11) NOT NULL,
  `ruangan` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retur_obat`
--

CREATE TABLE `retur_obat` (
  `id` int(11) NOT NULL,
  `pasien_id` int(11) NOT NULL,
  `obat_alkes` varchar(45) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `no_batch` varchar(50) DEFAULT NULL,
  `expired_date` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `service` text DEFAULT NULL,
  `biaya_service` bigint(20) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `inventaris_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  `teknisi_id` int(11) DEFAULT NULL,
  `tgl_teknisi` datetime DEFAULT NULL,
  `type_permohonan` char(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `status` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `status`) VALUES
(1, 'Active'),
(2, 'In Active'),
(3, 'Menunggu Persetujuan'),
(4, 'Disetujui SPV'),
(5, 'Menunggu Tindakan Finance'),
(6, 'Open'),
(7, 'On Progress'),
(8, 'Completed'),
(9, 'Closed'),
(10, 'Rejected');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `jabatan_id` int(11) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `nik`, `username`, `email`, `password`, `gender_id`, `department_id`, `status_id`, `jabatan_id`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(18, 'alfin', '11223344', 'alfin', 'alfinnurhidayat@gmail.com', '$2y$10$fZ1XxCtLXmPlN3.jZ4slAewspsHnL5KIxYh8VIjIs0nAGokC2zgmy', 1, 1, 1, 3, NULL, 'PYU1InaE2syF72OD6Mh3Ep0HCh6DqmYcfqah1Uk0kc8cfWTTpIoU3li4GJPA', '2022-02-04 01:29:13', '2022-03-08 03:55:18'),
(19, 'Nicky', '1122334455', 'nicky', 'nicky@gmail.com', '$2y$10$g2YAcHHXKR.qcTsio.DMX.7fnvIs5amAGRChxx.QsdE21HygDIGvi', 1, 1, 1, 3, NULL, 'vjpUEEtMumjIKk7nyHdZG4QpfeEUIoQk9MJbUoLkWXgddYGp7YRzTUdBt2qj', '2022-02-04 01:41:49', '2022-02-04 01:41:49'),
(30, 'staff_marketing', '222', 'staff_marketing', 'staffmarketing@gmail.com', '$2y$10$/bw86A2B4tb8cptrz0hUB.hIg0oQeod1oRJ4WaVrWO886/YYnmqi2', 2, 2, 1, 1, NULL, 'VQAtBazK0gf4AU97lDhMAPucm27Bh1MwE7AGvyWrlt4Q8YxyeMmktacrB4Fm', '2022-02-08 02:14:03', '2022-02-08 03:57:39'),
(31, 'spv_marketing', '221', 'spv_marketing', 'spv_marketing@gmail.com', '$2y$10$K.UlZm/tBnLjYOkVjEcBMObl7q94o4TVinxUmUoEP4yi0JoR26Fge', 1, 2, 1, 2, NULL, 'meFT2NqW7YZ0nDo4YQ10A6P8Gw3ss9QWy5YOVsA0qq5XxznHd9a5h3UXDMgu', '2022-02-08 02:15:37', '2022-02-08 02:15:37'),
(32, 'manager_marketing', '220', 'manager_marketing', 'manager_marketing@gmail.com', '$2y$10$sG44GNWWTm12ySQRZ7RhieXJcdtU6Qvctzb7vv5Kbf/U3L3VqgLZG', 1, 2, 1, 3, NULL, 'kTKnNFU6XoVUn89vsOlua4CAT8WzXqVfhjO3Ht82Vy1iCacs8LAtY2rmIK1Y', '2022-02-08 04:23:12', '2022-02-08 04:23:12'),
(33, 'perawat', '3344', 'perawat', 'perawat@gmail.com', '$2y$10$OHh6Eg6jQZvABwpM2PZaG.sTzJQzD0KxBTRdv9bTW4idQ8RrVoWx.', 2, 5, 1, 1, NULL, 'L0r6Ok46VmmFvQCuou7w2R5nMe82EAENITg1bnCFE4XA1JtCn1ML6WT6FbDT', '2022-02-08 04:31:55', '2022-02-08 04:31:55'),
(34, 'ku_perawat', '33441', 'ku_perawat', 'ku_perawat@gmail.com', '$2y$10$fE5/VR0pSWFBOb97dqNd4.O4qeZM6NL0DGyNo0rnQZxoAoggMBw32', 2, 5, 1, 2, NULL, 'vuGHKohxKoxEGCtqMhNprMGM6zrF4iR0N6XoAXYL', '2022-02-08 04:33:09', '2022-02-08 04:33:09'),
(35, 'farmasi', '5566', 'farmasi', 'farmasi@gmail.com', '$2y$10$tfnSLhJceKrsjrawpTyNuuEtL2r.Jpxa8JH9cSdJrkeBXoL4lrfjW', 2, 6, 1, 1, NULL, 'vuGHKohxKoxEGCtqMhNprMGM6zrF4iR0N6XoAXYL', '2022-02-08 04:34:37', '2022-02-08 04:34:37'),
(36, 'maintenance', '23231131', 'maintenance', 'maintenance@gmail.com', '$2y$10$lwfjHuUDpxw6cSDDLNGcEOzCbcDc3WTAmV88eUjyPivO3Cp2eW85m', 1, 4, 1, 1, NULL, '0LhN4KOop8Hw0LV6bD8S4vE7OFNH3I7c1g41VR9SYg3obuKpLCwjQDOBnyZa', '2022-02-12 08:06:46', '2022-02-12 08:06:46'),
(37, 'staff_finance', '6111', 'staff_finance', 'staff_finance@gmail.com', '$2y$10$fs3p3NxN12R0jq6e1zFbjeJRXPRDL8LpAGCqhm5qWcyUr8Ub1WYCC', 2, 7, 1, 1, NULL, 'k3qiVPoZiZoTpZoow36ROkqiHWosxM2kuet4Dh8xuyce8WaZx54edqtkhzUD', '2022-02-23 16:15:25', '2022-02-23 16:15:25'),
(38, 'spv_finance', '6112', 'spv_finance', 'spv_finance@gmail.com', '$2y$10$C0EmNnFVTLJ6oS.yxImhsuHPdw72jWPnDuvyYKPjFW.mFEwGbWJ9S', 1, 7, 1, 2, NULL, 'SULJ3KZvDjM0vk1ZhIHBcWsQLAW113mKuhztYqyRkaCQzjOaXPa4Jpn64ne6', '2022-02-23 16:16:09', '2022-02-23 16:16:09'),
(39, 'manager_finance', '6113', 'manager_finance', 'manager_finance@gmail.com', '$2y$10$Ot5T5wCHdQ1l8NeNrzztEuC2A7phIm2TQKEbF7zjNpoxcDtUoh8bq', 1, 7, 1, 3, NULL, 'mIyW71WokvStOMg008vfuEUg2fQQya40PSlfgROaKA8RoeOjpDOC2ODsPyTY', '2022-02-23 16:17:03', '2022-02-23 16:17:03'),
(40, 'staff_logistik', '71', 'staff_logistik', 'staff_logistik@gmail.com', '$2y$10$2aUoqpq5nLMMkt4CU57iOe/t25nuJUBuwttYZGePYmlPOpG3Y7QFi', 2, 3, 1, 1, NULL, 'KBsolOaIhT3Tn2op5zY4yc8jzlZJSPm0UCcRqi6e2Uw8QZW51HTaVqMD85Kp', '2022-03-01 06:20:46', '2022-03-01 06:20:46'),
(41, 'spv_logistik', '712', 'spv_logistik', 'spv_logistik@gmail.com', '$2y$10$dosvksr2KeISUp90dQXrbuSnKkNntQysU8EwpEZO6S1xFONwl100C', 2, 3, 1, 2, NULL, 'YcnCH7RHPoJVGLlyg28wroh9gFZCantN5Kej0DBwWvhlTN7wALAJyNQ8YAWl', '2022-03-01 06:21:46', '2022-03-01 06:21:46'),
(42, 'manager_logistik', '713', 'manager_logistik', 'manager_logistik@gmail.com', '$2y$10$vfh95QO8KbLDoLd0mjjKCe8kchT1N9OFDdwFZmRVHapCbR/C3CvvK', 1, 3, 1, 3, NULL, '5OohfetJuPowOK2Axa0NTcWmaDwKGaf3gDQsg636hTGY2mLJuFKBsQCZ3ayU', '2022-03-01 06:22:38', '2022-03-01 06:22:38'),
(43, 'denny', '12312312', 'denny', 'denny@gmail.com', '$2y$10$ynuD1SBb/0A2bfHaKy0C..qWLGfGkksB6CCMcvBg0nAkHB90Aem4a', 1, 1, 1, 1, NULL, 'eAbglzfwkEVpycoVR2JNEFwF4RSCNoG81XZkV4MP', '2022-03-04 07:38:46', '2022-03-04 07:38:46'),
(44, 'dimas', '1242343', 'dimas', 'dimas@gmail.com', '$2y$10$8p5lNleIhtK49V3lB7FkYuBEstUyEzgR3YAY1OyyzvuKT7UJlNKYC', 1, 1, 1, 1, NULL, 'WIU7NnmsO11AzuWoQIk8B06XLGEnPvzDKu2lYWenwuUKCIygg6trE6IY83Qy', '2022-03-04 07:41:07', '2022-03-04 07:41:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gender`
--
ALTER TABLE `gender`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventaris`
--
ALTER TABLE `inventaris`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jenis_inventaris_id` (`jenis_inventaris_id`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jenis_inventaris`
--
ALTER TABLE `jenis_inventaris`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keterangan_service`
--
ALTER TABLE `keterangan_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `retur_obat`
--
ALTER TABLE `retur_obat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pasien_id` (`pasien_id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_ibfk_3` (`status_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `inventaris_id` (`inventaris_id`),
  ADD KEY `teknisi_id` (`teknisi_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_ibfk_1` (`gender_id`),
  ADD KEY `users_ibfk_2` (`jabatan_id`),
  ADD KEY `users_ibfk_3` (`department_id`),
  ADD KEY `users_ibfk_4` (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gender`
--
ALTER TABLE `gender`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventaris`
--
ALTER TABLE `inventaris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jenis_inventaris`
--
ALTER TABLE `jenis_inventaris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `keterangan_service`
--
ALTER TABLE `keterangan_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `obat`
--
ALTER TABLE `obat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `retur_obat`
--
ALTER TABLE `retur_obat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventaris`
--
ALTER TABLE `inventaris`
  ADD CONSTRAINT `inventaris_ibfk_1` FOREIGN KEY (`jenis_inventaris_id`) REFERENCES `jenis_inventaris` (`id`);

--
-- Constraints for table `keterangan_service`
--
ALTER TABLE `keterangan_service`
  ADD CONSTRAINT `keterangan_service_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
  ADD CONSTRAINT `keterangan_service_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `pasien`
--
ALTER TABLE `pasien`
  ADD CONSTRAINT `pasien_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `retur_obat`
--
ALTER TABLE `retur_obat`
  ADD CONSTRAINT `retur_obat_ibfk_1` FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`);

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `service_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `service_ibfk_5` FOREIGN KEY (`inventaris_id`) REFERENCES `inventaris` (`id`),
  ADD CONSTRAINT `service_ibfk_6` FOREIGN KEY (`teknisi_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`gender_id`) REFERENCES `gender` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`jabatan_id`) REFERENCES `jabatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
