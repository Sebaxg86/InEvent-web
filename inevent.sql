-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-05-2025 a las 07:17:46
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

--
-- Base de datos: `inevent`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `event_date` datetime NOT NULL,
  `venue` varchar(150) NOT NULL,
  `type` varchar(60) DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `total_seats` int(11) NOT NULL,
  `img` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'paypal',
  `payment_status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) 

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `seat_id` int(11) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL
) 

ALTER TABLE order_items
  ADD COLUMN event_id INT NULL AFTER seat_id;

UPDATE order_items oi
JOIN seats s ON oi.seat_id = s.id
SET oi.event_id = s.event_id
WHERE oi.seat_id IS NOT NULL;

ALTER TABLE order_items
  ADD CONSTRAINT fk_order_items_event
    FOREIGN KEY (event_id) REFERENCES events(id);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seats`
--

CREATE TABLE `seats` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `seat_label` varchar(10) DEFAULT NULL,
  `is_sold` tinyint(1) DEFAULT 0
)

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `pass_hash` varchar(255) DEFAULT NULL,
  `is_guest` tinyint(1) DEFAULT 0,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
)

