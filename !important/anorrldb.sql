-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 15, 2026 at 02:17 AM
-- Server version: 10.11.14-MariaDB-0+deb12u2
-- PHP Version: 8.4.17

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
  `access_key` varchar(50) NOT NULL,
  `access_discorduid` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_players`
--

DROP TABLE IF EXISTS `active_players`;
CREATE TABLE `active_players` (
  `session_id` varchar(256) NOT NULL,
  `session_serverid` varchar(11) NOT NULL,
  `session_playerid` int(11) NOT NULL,
  `session_status` int(1) NOT NULL,
  `session_teamcreate` int(1) NOT NULL DEFAULT 0,
  `session_timestarted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `active_servers`
--

DROP TABLE IF EXISTS `active_servers`;
CREATE TABLE `active_servers` (
  `server_id` varchar(11) NOT NULL,
  `server_pid` int(11) NOT NULL,
  `server_jobid` varchar(255) NOT NULL,
  `server_placeid` int(11) NOT NULL,
  `server_year` varchar(4) NOT NULL DEFAULT '2016',
  `server_playercount` int(11) NOT NULL DEFAULT 0,
  `server_maxcount` int(11) NOT NULL,
  `server_port` varchar(5) NOT NULL,
  `server_teamcreate` int(1) NOT NULL DEFAULT 0
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
  `asset_id` int(11) NOT NULL,
  `asset_creator` int(11) NOT NULL,
  `asset_type` int(11) NOT NULL,
  `asset_name` varchar(128) NOT NULL,
  `asset_description` text NOT NULL,
  `asset_public` int(11) NOT NULL DEFAULT 0,
  `asset_favourites_count` int(11) NOT NULL DEFAULT 0,
  `asset_comments_enabled` int(11) NOT NULL DEFAULT 1,
  `asset_year` int(1) NOT NULL DEFAULT 0,
  `asset_onsale` int(11) NOT NULL DEFAULT 0,
  `asset_sales_count` int(11) NOT NULL DEFAULT 0,
  `asset_relatedid` int(11) DEFAULT NULL,
  `asset_currentversion` int(11) NOT NULL DEFAULT 1,
  `asset_nevershow` int(11) NOT NULL DEFAULT 0,
  `asset_lastedited` timestamp NOT NULL DEFAULT current_timestamp(),
  `asset_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assetversions`
--

DROP TABLE IF EXISTS `assetversions`;
CREATE TABLE `assetversions` (
  `version_id` int(11) NOT NULL,
  `version_assetid` int(11) NOT NULL,
  `version_md5sig` varchar(50) NOT NULL,
  `version_md5thumb` varchar(50) NOT NULL DEFAULT 'sound',
  `version_subid` int(11) NOT NULL DEFAULT 1,
  `version_assettype` int(11) NOT NULL DEFAULT 1,
  `version_publishdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_packages`
--

DROP TABLE IF EXISTS `asset_packages`;
CREATE TABLE `asset_packages` (
  `package_id` int(11) NOT NULL,
  `package_items` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_places`
--

DROP TABLE IF EXISTS `asset_places`;
CREATE TABLE `asset_places` (
  `place_id` int(11) NOT NULL,
  `place_copylocked` int(11) NOT NULL DEFAULT 1,
  `place_serversize` int(11) NOT NULL DEFAULT 12,
  `place_visit_count` int(11) NOT NULL DEFAULT 0,
  `place_currently_playing` int(11) NOT NULL DEFAULT 0,
  `place_teamcreate_enabled` int(1) NOT NULL DEFAULT 0,
  `place_original` int(1) NOT NULL DEFAULT 0,
  `place_gears_enabled` int(1) NOT NULL DEFAULT 0,
  `place_youtube_id` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bodycolours`
--

DROP TABLE IF EXISTS `bodycolours`;
CREATE TABLE `bodycolours` (
  `colours_userid` int(11) NOT NULL,
  `colours_head` int(11) NOT NULL DEFAULT 24,
  `colours_torso` int(11) NOT NULL DEFAULT 23,
  `colours_leftarm` int(11) NOT NULL DEFAULT 24,
  `colours_rightarm` int(11) NOT NULL DEFAULT 24,
  `colours_leftleg` int(11) NOT NULL DEFAULT 119,
  `colours_rightleg` int(11) NOT NULL DEFAULT 119
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cloudeditors`
--

DROP TABLE IF EXISTS `cloudeditors`;
CREATE TABLE `cloudeditors` (
  `cloudeditor_id` int(11) NOT NULL,
  `cloudeditor_userid` int(11) NOT NULL,
  `cloudeditor_placeid` int(11) NOT NULL,
  `cloudeditor_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `comment_id` varchar(11) NOT NULL,
  `comment_parent` varchar(13) NOT NULL,
  `comment_poster` int(11) NOT NULL,
  `comment_content` varchar(256) NOT NULL,
  `comment_postdate` timestamp NOT NULL DEFAULT current_timestamp()
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
  `fav_assetid` int(11) NOT NULL,
  `fav_userid` int(11) NOT NULL,
  `fav_assettype` int(2) NOT NULL,
  `fav_date` timestamp NOT NULL DEFAULT current_timestamp()
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
  `inv_userid` int(11) NOT NULL,
  `inv_assetid` int(11) NOT NULL,
  `inv_assettype` int(11) NOT NULL
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
-- Table structure for table `persistenceblobs`
--

DROP TABLE IF EXISTS `persistenceblobs`;
CREATE TABLE `persistenceblobs` (
  `blob_placeid` int(11) NOT NULL,
  `blob_playerid` int(11) NOT NULL,
  `blob_data` text NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profilebadges`
--

DROP TABLE IF EXISTS `profilebadges`;
CREATE TABLE `profilebadges` (
  `badge_id` int(2) NOT NULL,
  `badge_userid` int(10) NOT NULL,
  `badge_admincorecore` int(1) NOT NULL,
  `badge_recieved` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profilebadges_info`
--

DROP TABLE IF EXISTS `profilebadges_info`;
CREATE TABLE `profilebadges_info` (
  `pbadge_id` int(11) NOT NULL,
  `pbadge_name` varchar(64) NOT NULL,
  `pbadge_description` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
CREATE TABLE `statuses` (
  `status_id` varchar(20) NOT NULL,
  `status_poster` int(10) NOT NULL,
  `status_content` varchar(64) NOT NULL,
  `status_posted` timestamp NOT NULL DEFAULT current_timestamp()
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
  `ta_id` varchar(15) NOT NULL,
  `ta_userid` int(11) NOT NULL,
  `ta_assetcreator` int(11) DEFAULT NULL,
  `ta_asset` varchar(15) DEFAULT NULL,
  `ta_assettype` text DEFAULT NULL,
  `ta_showsupatall` int(11) NOT NULL DEFAULT 1,
  `ta_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `user_blurb` varchar(1000) NOT NULL DEFAULT '',
  `user_discord` varchar(256) NOT NULL,
  `user_password` varchar(256) NOT NULL,
  `user_security` varchar(255) NOT NULL,
  `user_lastprofileupdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_setprofilepicture` int(1) NOT NULL DEFAULT 0,
  `user_currentappearancemd5` varchar(255) NOT NULL DEFAULT 'e729ef49ab16651b0826febda215862b', -- noob avatar
  `user_css` text NOT NULL DEFAULT '',
  `user_online` int(1) NOT NULL DEFAULT 0,
  `user_joindate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_settings`
--

DROP TABLE IF EXISTS `users_settings`;
CREATE TABLE `users_settings` (
  `settings_userid` int(11) NOT NULL,
  `settings_randoms` int(1) NOT NULL DEFAULT 1,
  `settings_teto` int(1) NOT NULL DEFAULT 1,
  `settings_emotesounds` int(1) NOT NULL DEFAULT 1,
  `settings_accessbility` int(1) NOT NULL DEFAULT 0,
  `settings_headshots` int(1) NOT NULL DEFAULT 1,
  `settings_nightbg` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visit`
--

DROP TABLE IF EXISTS `visit`;
CREATE TABLE `visit` (
  `visit_place` int(11) NOT NULL,
  `visit_player` int(11) NOT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_players`
--
ALTER TABLE `active_players`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `active_servers`
--
ALTER TABLE `active_servers`
  ADD PRIMARY KEY (`server_id`);

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indexes for table `assetversions`
--
ALTER TABLE `assetversions`
  ADD PRIMARY KEY (`version_id`);

--
-- Indexes for table `asset_packages`
--
ALTER TABLE `asset_packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `asset_places`
--
ALTER TABLE `asset_places`
  ADD PRIMARY KEY (`place_id`);

--
-- Indexes for table `bodycolours`
--
ALTER TABLE `bodycolours`
  ADD PRIMARY KEY (`colours_userid`);

--
-- Indexes for table `cloudeditors`
--
ALTER TABLE `cloudeditors`
  ADD PRIMARY KEY (`cloudeditor_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `datastores`
--
ALTER TABLE `datastores`
  ADD PRIMARY KEY (`dkey`(100));

--
-- Indexes for table `profilebadges_info`
--
ALTER TABLE `profilebadges_info`
  ADD PRIMARY KEY (`pbadge_id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`ta_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users_settings`
--
ALTER TABLE `users_settings`
  ADD PRIMARY KEY (`settings_userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assetversions`
--
ALTER TABLE `assetversions`
  MODIFY `version_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cloudeditors`
--
ALTER TABLE `cloudeditors`
  MODIFY `cloudeditor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profilebadges_info`
--
ALTER TABLE `profilebadges_info`
  MODIFY `pbadge_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
