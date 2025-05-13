-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Bulan Mei 2025 pada 03.21
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
-- Database: `kelaskita`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `assignments`
--

INSERT INTO `assignments` (`id`, `class_id`, `title`, `description`, `due_date`, `created_at`) VALUES
(1, 1, 'Tugas Membuat Aplikasi CRUD', 'Buat aplikasi CRUD sederhana menggunakan PHP dan MySQL', '2025-09-10', '2025-05-09 03:04:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bootcamps`
--

CREATE TABLE `bootcamps` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `mentor_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bootcamps`
--

INSERT INTO `bootcamps` (`id`, `title`, `description`, `mentor_id`, `created_at`) VALUES
(14, 'Bootcamp Web Development', 'Belajar membuat aplikasi web dari dasar.', 3, '2025-05-08 03:10:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bootcamp_answers`
--

CREATE TABLE `bootcamp_answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bootcamp_answers`
--

INSERT INTO `bootcamp_answers` (`id`, `question_id`, `answer_text`, `is_correct`) VALUES
(1, 1, 'A. UI adalah User Interface', 1),
(2, 1, 'B. Tidak Tau', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `bootcamp_modules`
--

CREATE TABLE `bootcamp_modules` (
  `id` int(11) NOT NULL,
  `bootcamp_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `video_url` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bootcamp_modules`
--

INSERT INTO `bootcamp_modules` (`id`, `bootcamp_id`, `title`, `content`, `created_at`, `video_url`) VALUES
(6, 14, 'Pengenalan UI', 'Materi awal tentang elemen UI', '2025-05-08 03:43:32', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `bootcamp_participants`
--

CREATE TABLE `bootcamp_participants` (
  `id` int(11) NOT NULL,
  `bootcamp_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `progress` int(11) DEFAULT 0,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_modules` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bootcamp_participants`
--

INSERT INTO `bootcamp_participants` (`id`, `bootcamp_id`, `student_id`, `progress`, `joined_at`, `completed_modules`) VALUES
(5, 14, 5, 0, '2025-05-08 03:28:11', 0),
(6, 14, 5, 0, '2025-05-08 07:07:15', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `bootcamp_questions`
--

CREATE TABLE `bootcamp_questions` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bootcamp_questions`
--

INSERT INTO `bootcamp_questions` (`id`, `module_id`, `question`, `created_at`) VALUES
(1, 6, 'Apa itu UI dalam konteks desain UI/UX?', '2025-05-13 01:08:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bootcamp_user_answers`
--

CREATE TABLE `bootcamp_user_answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer_id` int(11) NOT NULL,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bootcamp_user_answers`
--

INSERT INTO `bootcamp_user_answers` (`id`, `student_id`, `question_id`, `selected_answer_id`, `answered_at`) VALUES
(2, 5, 1, 2, '2025-05-13 01:14:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `classes`
--

INSERT INTO `classes` (`id`, `name`, `description`, `teacher_id`, `created_at`) VALUES
(1, 'Rekayasa Perangkat Lunak', 'Kelas untuk siswa SMK belajar dasar RPL', 3, '2025-05-08 07:38:50'),
(2, 'Rekayasa Perangkat Lunak', 'Kelas untuk siswa SMK belajar dasar RPL', 3, '2025-05-09 02:50:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `class_members`
--

CREATE TABLE `class_members` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `class_members`
--

INSERT INTO `class_members` (`id`, `class_id`, `student_id`, `joined_at`) VALUES
(1, 1, 5, '2025-05-09 02:58:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `completed_module_logs`
--

CREATE TABLE `completed_module_logs` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `bootcamp_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_comments`
--

CREATE TABLE `forum_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `forum_comments`
--

INSERT INTO `forum_comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 6, 'Coba pakai Figma, ada banyak template juga di sana.', '2025-05-08 07:20:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `user_id`, `title`, `content`, `created_at`) VALUES
(1, 4, 'Tips belajar UI/UX', 'Apakah ada rekomendasi sumber belajar gratis?', '2025-05-08 07:18:00'),
(2, 4, 'Halo, Example', 'Ini adalah pesan example', '2025-05-08 07:18:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `grades`
--

INSERT INTO `grades` (`id`, `submission_id`, `score`, `feedback`, `graded_at`) VALUES
(1, 1, 92.00, 'Sudah sangat baik, struktur kodenya rapi.', '2025-05-09 03:20:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nis` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `students`
--

INSERT INTO `students` (`id`, `user_id`, `nis`) VALUES
(2, 3, '123456789'),
(5, 6, '123459876');

-- --------------------------------------------------------

--
-- Struktur dari tabel `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file_url` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `submissions`
--

INSERT INTO `submissions` (`id`, `assignment_id`, `student_id`, `file_url`, `submitted_at`) VALUES
(1, 1, 5, 'https://drive.google.com/file/d/abc123/view', '2025-05-09 03:13:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nip` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `nip`) VALUES
(3, 4, '123456');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('teacher','student','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(3, 'Admin', 'admin@example.com', '$2y$10$60Nz0rocoTz7K1W0b/NwneiBNMb4e5aGoZf0gzkytuj.PF/PHV3aS', 'admin', '2025-05-04 06:07:11'),
(4, 'Teacher', 'teacher@example.com', '$2y$10$AaldlxMd4eac0bPwvIvFF.XRlkskQM2pDA/gkr.1wISsA.kKSmqRC', 'teacher', '2025-05-04 06:24:51'),
(6, 'Student', 'student@example.com', '$2y$10$.FCfNgOrvyUJIFZNgXy/QeHs1f.M.7Oh01x5hkbUijxNZt0O9icpu', 'student', '2025-05-04 06:45:17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indeks untuk tabel `bootcamps`
--
ALTER TABLE `bootcamps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentor_id` (`mentor_id`);

--
-- Indeks untuk tabel `bootcamp_answers`
--
ALTER TABLE `bootcamp_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indeks untuk tabel `bootcamp_modules`
--
ALTER TABLE `bootcamp_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bootcamp_id` (`bootcamp_id`);

--
-- Indeks untuk tabel `bootcamp_participants`
--
ALTER TABLE `bootcamp_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bootcamp_id` (`bootcamp_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indeks untuk tabel `bootcamp_questions`
--
ALTER TABLE `bootcamp_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indeks untuk tabel `bootcamp_user_answers`
--
ALTER TABLE `bootcamp_user_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_answer_id` (`selected_answer_id`);

--
-- Indeks untuk tabel `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indeks untuk tabel `class_members`
--
ALTER TABLE `class_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indeks untuk tabel `completed_module_logs`
--
ALTER TABLE `completed_module_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`module_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `bootcamp_id` (`bootcamp_id`);

--
-- Indeks untuk tabel `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indeks untuk tabel `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indeks untuk tabel `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT untuk tabel `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `bootcamps`
--
ALTER TABLE `bootcamps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `bootcamp_answers`
--
ALTER TABLE `bootcamp_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `bootcamp_modules`
--
ALTER TABLE `bootcamp_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `bootcamp_participants`
--
ALTER TABLE `bootcamp_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `bootcamp_questions`
--
ALTER TABLE `bootcamp_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `bootcamp_user_answers`
--
ALTER TABLE `bootcamp_user_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `class_members`
--
ALTER TABLE `class_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `completed_module_logs`
--
ALTER TABLE `completed_module_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `forum_comments`
--
ALTER TABLE `forum_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bootcamps`
--
ALTER TABLE `bootcamps`
  ADD CONSTRAINT `bootcamps_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bootcamp_answers`
--
ALTER TABLE `bootcamp_answers`
  ADD CONSTRAINT `bootcamp_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `bootcamp_questions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bootcamp_modules`
--
ALTER TABLE `bootcamp_modules`
  ADD CONSTRAINT `bootcamp_modules_ibfk_1` FOREIGN KEY (`bootcamp_id`) REFERENCES `bootcamps` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bootcamp_participants`
--
ALTER TABLE `bootcamp_participants`
  ADD CONSTRAINT `bootcamp_participants_ibfk_1` FOREIGN KEY (`bootcamp_id`) REFERENCES `bootcamps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bootcamp_participants_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bootcamp_questions`
--
ALTER TABLE `bootcamp_questions`
  ADD CONSTRAINT `bootcamp_questions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `bootcamp_modules` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bootcamp_user_answers`
--
ALTER TABLE `bootcamp_user_answers`
  ADD CONSTRAINT `bootcamp_user_answers_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bootcamp_user_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `bootcamp_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bootcamp_user_answers_ibfk_3` FOREIGN KEY (`selected_answer_id`) REFERENCES `bootcamp_answers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `class_members`
--
ALTER TABLE `class_members`
  ADD CONSTRAINT `class_members_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_members_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `completed_module_logs`
--
ALTER TABLE `completed_module_logs`
  ADD CONSTRAINT `completed_module_logs_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `completed_module_logs_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `bootcamp_modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `completed_module_logs_ibfk_3` FOREIGN KEY (`bootcamp_id`) REFERENCES `bootcamps` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD CONSTRAINT `forum_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
