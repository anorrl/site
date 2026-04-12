-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 12, 2026 at 02:06 PM
-- Server version: 12.2.2-MariaDB
-- PHP Version: 8.5.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anorrldb`
--
CREATE DATABASE IF NOT EXISTS `anorrldb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `anorrldb`;

-- --------------------------------------------------------

--
-- Table structure for table `accesskeys`
--

DROP TABLE IF EXISTS `accesskeys`;
CREATE TABLE `accesskeys` (
  `key` varchar(50) NOT NULL,
  `discorduid` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_players`
--

DROP TABLE IF EXISTS `active_players`;
CREATE TABLE `active_players` (
  `id` varchar(256) NOT NULL,
  `serverid` varchar(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `teamcreate` int(1) NOT NULL DEFAULT 0,
  `timestarted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_servers`
--

DROP TABLE IF EXISTS `active_servers`;
CREATE TABLE `active_servers` (
  `id` varchar(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `jobid` varchar(255) NOT NULL,
  `placeid` int(11) NOT NULL,
  `playercount` int(11) NOT NULL DEFAULT 0,
  `maxcount` int(11) NOT NULL,
  `port` varchar(5) NOT NULL,
  `teamcreate` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
CREATE TABLE `activity` (
  `userid` int(11) NOT NULL,
  `action` text NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `public` int(11) NOT NULL DEFAULT 0,
  `favourites_count` int(11) NOT NULL DEFAULT 0,
  `comments_enabled` int(11) NOT NULL DEFAULT 1,
  `onsale` int(11) NOT NULL DEFAULT 0,
  `cones` int(11) NOT NULL DEFAULT 0,
  `lights` int(11) NOT NULL DEFAULT 0,
  `sales_count` int(11) NOT NULL DEFAULT 0,
  `relatedid` int(11) DEFAULT NULL,
  `currentversion` int(11) NOT NULL DEFAULT 1,
  `nevershow` int(11) NOT NULL DEFAULT 0,
  `lastedited` timestamp NOT NULL DEFAULT current_timestamp(),
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

DROP TABLE IF EXISTS `places`;
CREATE TABLE `places` (
  `id` int(11) NOT NULL,
  `copylocked` int(11) NOT NULL DEFAULT 1,
  `serversize` int(11) NOT NULL DEFAULT 12,
  `visit_count` int(11) NOT NULL DEFAULT 0,
  `currently_playing_count` int(11) NOT NULL DEFAULT 0,
  `teamcreate_enabled` int(1) NOT NULL DEFAULT 0,
  `original` int(1) NOT NULL DEFAULT 0,
  `gears_enabled` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_versions`
--

DROP TABLE IF EXISTS `asset_versions`;
CREATE TABLE `asset_versions` (
  `id` int(11) NOT NULL,
  `assetid` int(11) NOT NULL,
  `md5sig` varchar(50) NOT NULL,
  `md5thumb` varchar(50) NOT NULL DEFAULT 'sound',
  `subid` int(11) NOT NULL DEFAULT 1,
  `publishdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bodycolours`
--

DROP TABLE IF EXISTS `bodycolours`;
CREATE TABLE `bodycolours` (
  `userid` int(11) NOT NULL,
  `head` int(11) NOT NULL DEFAULT 24,
  `torso` int(11) NOT NULL DEFAULT 23,
  `leftarm` int(11) NOT NULL DEFAULT 24,
  `rightarm` int(11) NOT NULL DEFAULT 24,
  `leftleg` int(11) NOT NULL DEFAULT 119,
  `rightleg` int(11) NOT NULL DEFAULT 119
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cloudeditors`
--

DROP TABLE IF EXISTS `cloudeditors`;
CREATE TABLE `cloudeditors` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `placeid` int(11) NOT NULL,
  `added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` varchar(11) NOT NULL,
  `parent` varchar(13) NOT NULL,
  `poster` int(11) NOT NULL,
  `content` varchar(256) NOT NULL,
  `postdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `datastores`
--

DROP TABLE IF EXISTS `datastores`;
CREATE TABLE `datastores` (
  `dkey` text NOT NULL,
  `universeId` int(11) NOT NULL,
  `type` text NOT NULL,
  `scope` text NOT NULL,
  `target` text NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

DROP TABLE IF EXISTS `favourites`;
CREATE TABLE `favourites` (
  `assetid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `assettype` int(2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

DROP TABLE IF EXISTS `follows`;
CREATE TABLE `follows` (
  `follower` int(20) NOT NULL,
  `followed` int(20) NOT NULL,
  `followed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
CREATE TABLE `friends` (
  `sender` varchar(20) NOT NULL,
  `reciever` varchar(20) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `time_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `userid` int(11) NOT NULL,
  `assetid` int(11) NOT NULL,
  `assettype` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `outfits`
--

DROP TABLE IF EXISTS `outfits`;
CREATE TABLE `outfits` (
  `outfit_id` varchar(15) NOT NULL,
  `outfit_creator` int(11) NOT NULL,
  `outfit_colours` text NOT NULL,
  `outfit_assets` text NOT NULL,
  `outfit_renderhash` text NOT NULL,
  `outfit_public` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profilebadges`
--

DROP TABLE IF EXISTS `profilebadges`;
CREATE TABLE `profilebadges` (
  `id` int(11) NOT NULL,
  `badgeid` int(2) NOT NULL,
  `userid` int(10) NOT NULL,
  `recieved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
CREATE TABLE `statuses` (
  `id` varchar(20) NOT NULL,
  `poster` int(10) NOT NULL,
  `content` varchar(64) NOT NULL,
  `posted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `userid` int(11) NOT NULL,
  `lastpaytime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` varchar(15) NOT NULL,
  `userid` int(11) NOT NULL,
  `assetcreator` int(11) DEFAULT NULL,
  `asset` varchar(15) DEFAULT NULL,
  `method` int(1) NOT NULL,
  `cost` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `blurb` varchar(1000) NOT NULL,
  `discord` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `security` varchar(255) NOT NULL,
  `lastprofileupdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `setprofilepicture` int(1) NOT NULL DEFAULT 0,
  `currentappearancemd5` varchar(255) NOT NULL DEFAULT 'e729ef49ab16651b0826febda215862b',
  `online` int(1) NOT NULL DEFAULT 0,
  `joindate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_settings`
--

DROP TABLE IF EXISTS `users_settings`;
CREATE TABLE `users_settings` (
  `userid` int(11) NOT NULL,
  `randoms` int(1) NOT NULL DEFAULT 1,
  `teto` int(1) NOT NULL DEFAULT 1,
  `emotesounds` int(1) NOT NULL DEFAULT 1,
  `accessbility` int(1) NOT NULL DEFAULT 0,
  `headshots` int(1) NOT NULL DEFAULT 1,
  `nightbg` int(1) NOT NULL DEFAULT 0,
  `bgm` int(11) NOT NULL DEFAULT -1,
  `css` text NOT NULL,
  `loadingscreens` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

DROP TABLE IF EXISTS `visits`;
CREATE TABLE `visits` (
  `place` int(11) NOT NULL,
  `player` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesskeys`
--
ALTER TABLE `accesskeys`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `active_players`
--
ALTER TABLE `active_players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `active_servers`
--
ALTER TABLE `active_servers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `asset_versions`
--
ALTER TABLE `asset_versions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bodycolours`
--
ALTER TABLE `bodycolours`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `cloudeditors`
--
ALTER TABLE `cloudeditors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `datastores`
--
ALTER TABLE `datastores`
  ADD PRIMARY KEY (`dkey`(100));

--
-- Indexes for table `profilebadges`
--
ALTER TABLE `profilebadges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_settings`
--
ALTER TABLE `users_settings`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_versions`
--
ALTER TABLE `asset_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cloudeditors`
--
ALTER TABLE `cloudeditors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profilebadges`
--
ALTER TABLE `profilebadges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
