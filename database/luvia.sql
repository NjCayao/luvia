-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-05-2025 a las 02:37:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `luvia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `media_type` enum('photo','video') NOT NULL,
  `filename` varchar(255) NOT NULL,
  `order_num` int(11) DEFAULT 0,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `media`
--

INSERT INTO `media` (`id`, `profile_id`, `media_type`, `filename`, `order_num`, `is_primary`, `created_at`) VALUES
(1, 1, 'photo', 'photo_6826a38b3f0a5.jpg', 0, 0, '2025-05-16 02:31:39'),
(3, 1, 'video', 'video_6826a4c21fd00.mp4', 0, 0, '2025-05-16 02:36:50'),
(4, 1, 'photo', 'photo_6826ad48944fc.jpg', 1, 1, '2025-05-16 03:13:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'PEN',
  `payment_method` varchar(50) NOT NULL COMMENT 'card, yape, etc',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','processing','completed','failed','refunded') DEFAULT 'pending',
  `izipay_session_id` varchar(100) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `user_type` enum('advertiser','visitor') NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in days',
  `price` decimal(10,2) NOT NULL,
  `max_photos` int(11) DEFAULT NULL,
  `max_videos` int(11) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plans`
--

INSERT INTO `plans` (`id`, `name`, `user_type`, `duration`, `price`, `max_photos`, `max_videos`, `featured`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Plan Básico', 'advertiser', 30, 50.00, 2, 1, 0, 'Plan básico para anunciantes\nIdeal para comenzar', '2025-05-14 19:37:56', '2025-05-14 19:37:56'),
(2, 'Plan Premium', 'advertiser', 30, 100.00, 5, 2, 1, 'Plan premium con mayores beneficios\nPerfil destacado en búsquedas', '2025-05-14 19:37:56', '2025-05-14 19:37:56'),
(3, 'Plan VIP', 'advertiser', 30, 150.00, 8, 3, 0, 'Plan exclusivo con todos los beneficios\nMáxima visibilidad y prioridad', '2025-05-14 19:37:56', '2025-05-14 19:37:56'),
(4, 'Plan Trimestral', 'advertiser', 90, 250.00, 12, 5, 0, 'Plan por 3 meses con descuento\r\nMismos beneficios que el Premiumssss', '2025-05-14 19:37:56', '2025-05-16 02:51:56'),
(5, 'Acceso Básico', 'visitor', 15, 5.00, NULL, NULL, 1, 'Acceso básico por 15 días\r\nIdeal para probar el servicio', '2025-05-14 19:37:56', '2025-05-15 19:41:20'),
(6, 'Acceso Mensual', 'visitor', 30, 15.00, NULL, NULL, 1, 'Acceso completo por 30 días\nMejor relación calidad-precio', '2025-05-14 19:37:56', '2025-05-14 19:37:56'),
(7, 'Acceso Trimestral', 'visitor', 90, 35.00, NULL, NULL, 0, 'Acceso completo por 90 días\nAhorra con esta suscripción', '2025-05-14 19:37:56', '2025-05-14 19:37:56'),
(9, 'Plan Gratuito', 'advertiser', 14, 0.00, 1, 0, 0, 'Plan Gratuito por 14 días. ', '2025-05-16 02:58:42', '2025-05-16 02:59:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` enum('female','male','trans') NOT NULL,
  `description` text DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `whatsapp_clicks` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `name`, `gender`, `description`, `whatsapp`, `city`, `location`, `schedule`, `is_verified`, `views`, `whatsapp_clicks`, `created_at`, `updated_at`) VALUES
(1, 2, 'María López', 'female', 'Hola, soy María. Ofrezco compañía de calidad para caballeros respetuosos. Contacta para más detalles.', '982226893', 'Lima', 'Miraflores', 'Lunes a Viernes: 10am - 8pm\r\nSábado: 12pm - 6pm', 1, 67, 0, '2025-05-14 19:38:05', '2025-05-17 19:38:03'),
(2, 3, 'Juan Pérez', 'male', 'Soy Juan, ofrezco compañía masculina de calidad. Discreto y respetuoso.', '51952345678', 'Lima', 'San Isidro', 'Lunes a Sábado: 2pm - 10pm', 1, 0, 0, '2025-05-14 19:38:05', '2025-05-14 19:38:05'),
(3, 4, 'Alex Vargas', 'trans', 'Hola, soy Alex. Bella trans ofreciendo experiencias inolvidables.', '51953456789', 'Lima', 'San Borja', 'Todos los días: 12pm - 12am', 0, 0, 0, '2025-05-14 19:38:05', '2025-05-16 00:39:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rates`
--

CREATE TABLE `rates` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `rate_type` enum('hour','half_hour','extra') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rates`
--

INSERT INTO `rates` (`id`, `profile_id`, `rate_type`, `description`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 'hour', '1/2 Hora', 100.00, '2025-05-14 19:38:13', '2025-05-17 15:31:21'),
(2, 1, 'half_hour', '1 Hora', 150.00, '2025-05-14 19:38:13', '2025-05-17 15:31:21'),
(3, 1, 'extra', 'Servicios especiales', 50.00, '2025-05-14 19:38:13', '2025-05-14 19:38:13'),
(4, 2, 'hour', 'Hora completa', 120.00, '2025-05-14 19:38:13', '2025-05-14 19:38:13'),
(5, 2, 'half_hour', 'Media hora', 80.00, '2025-05-14 19:38:13', '2025-05-14 19:38:13'),
(6, 3, 'hour', 'Hora completa', 180.00, '2025-05-14 19:38:13', '2025-05-14 19:38:13'),
(7, 3, 'half_hour', 'Media hora', 110.00, '2025-05-14 19:38:13', '2025-05-14 19:38:13'),
(8, 3, 'extra', 'Servicios especiales', 70.00, '2025-05-14 19:38:13', '2025-05-14 19:38:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stats`
--

CREATE TABLE `stats` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `action_type` enum('view','whatsapp_click') NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `visitor_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `status` enum('trial','active','expired','cancelled') DEFAULT 'trial',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `auto_renew` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `plan_id`, `payment_id`, `status`, `start_date`, `end_date`, `auto_renew`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, 'active', '2025-05-14 14:38:22', '2025-06-23 14:38:22', 0, '2025-05-14 19:38:22', '2025-05-17 19:34:52'),
(2, 5, 5, NULL, 'active', '2025-05-14 14:38:22', '2025-05-26 14:38:22', 0, '2025-05-14 19:38:22', '2025-05-17 19:35:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','advertiser','visitor') NOT NULL,
  `status` enum('pending','active','suspended','deleted') DEFAULT 'pending',
  `phone_verified` tinyint(1) DEFAULT 0,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(10) DEFAULT NULL,
  `verification_expires` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `phone`, `email`, `password`, `user_type`, `status`, `phone_verified`, `email_verified`, `verification_code`, `verification_expires`, `last_login`, `created_at`, `updated_at`) VALUES
(1, '999999999', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 1, 1, NULL, NULL, '2025-05-17 14:36:33', '2025-05-14 19:37:20', '2025-05-17 19:36:33'),
(2, '951234567', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'advertiser', 'active', 1, 1, NULL, NULL, '2025-05-17 10:31:01', '2025-05-14 19:37:35', '2025-05-17 15:31:01'),
(3, '952345678', 'juan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'advertiser', 'active', 1, 1, NULL, NULL, NULL, '2025-05-14 19:37:35', '2025-05-14 19:37:35'),
(4, '953456789', 'alex@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'advertiser', 'active', 1, 1, NULL, NULL, NULL, '2025-05-14 19:37:35', '2025-05-16 00:48:40'),
(5, '959876543', 'cliente@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'visitor', 'active', 1, 1, NULL, NULL, '2025-05-17 14:33:56', '2025-05-14 19:37:45', '2025-05-17 19:33:56'),
(6, '982226835', 'nilson.jhonny@gmail.com', '$2y$10$gO1MOlMRrzXRQdtsCB3BvOC6mwaByh4KfJ/4j3yxnPHHVLFfFhTaO', 'visitor', 'pending', 0, 0, '761761', '2025-05-20 19:36:14', NULL, '2025-05-20 00:36:14', '2025-05-20 00:36:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `verification_logs`
--

CREATE TABLE `verification_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `verification_type` enum('id_card','selfie','document') NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indices de la tabla `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indices de la tabla `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indices de la tabla `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `visitor_id` (`visitor_id`);

--
-- Indices de la tabla `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `verification_logs`
--
ALTER TABLE `verification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `verified_by` (`verified_by`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `rates`
--
ALTER TABLE `rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `stats`
--
ALTER TABLE `stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `verification_logs`
--
ALTER TABLE `verification_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`);

--
-- Filtros para la tabla `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rates`
--
ALTER TABLE `rates`
  ADD CONSTRAINT `rates_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `stats`
--
ALTER TABLE `stats`
  ADD CONSTRAINT `stats_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stats_ibfk_2` FOREIGN KEY (`visitor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`),
  ADD CONSTRAINT `subscriptions_ibfk_3` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`);

--
-- Filtros para la tabla `verification_logs`
--
ALTER TABLE `verification_logs`
  ADD CONSTRAINT `verification_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `verification_logs_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
