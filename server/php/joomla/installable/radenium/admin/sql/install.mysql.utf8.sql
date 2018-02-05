-- SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
-- --------------------------------------------------------
-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Jan 31, 2018 at 07:19 PM
-- Server version: 5.5.42
-- PHP Version: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `radenium`
--

-- --------------------------------------------------------

--
-- Table structure for table `#__radenium_ffmpeg`
--
DROP TABLE IF EXISTS `#__radenium_ffmpeg`;
CREATE TABLE `#__radenium_ffmpeg` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `command` text NOT NULL,
  `platform` int(1) NOT NULL DEFAULT '0',
  `category` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__radenium_ffmpeg`
--

INSERT INTO `#__radenium_ffmpeg` (`id`, `name`, `command`, `platform`, `category`) VALUES
(1, 'Apple HLS', '-r 30 -f avfoundation -i {$DEVICES} -pix_fmt yuv420p -s {$OUT_RES} -hls_flags round_durations -hls_time 3 -hls_init_time 3 {$OUT_DIR}{$OUT_NAME_M3U8}', 0, 0),
(2, 'RTSP Push', '-r 30 -f avfoundation -i {$DEVICES} -pix_fmt yuv420p -c:v libx264 -profile:v baseline -level 3.0 -r 24 -g 48 -keyint_min 48 -sc_threshold 0 -vb 310k -c:a mp3 -ab 40k -ar 44100 -ac 2 -f rtsp -muxdelay 0.1 rtsp://{$RTSP_USER_NAME}:{$RTSP_USER_PASSWORD}@{$RTSP_SERVER_URL}:{$RTSP_SERVER_PORT}/{$RTSP_USER_NAME}/{$RTSP_KEY}', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nt4pz_radenium_ffmpeg`
--
ALTER TABLE `#__radenium_ffmpeg`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nt4pz_radenium_ffmpeg`
--
ALTER TABLE `#__radenium_ffmpeg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;


-- --------------------------------------------------------
--
-- Table structure for table `#__radenium_settings`
--

DROP TABLE IF EXISTS `#__radenium_settings`;
CREATE TABLE `#__radenium_settings` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `video_out_size` text NOT NULL,
  `frame_rate` int(11) NOT NULL,
  `remote_url` text NOT NULL,
  `remote_user` text NOT NULL,
  `remote_password` text NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `#__radenium_settings`
--
ALTER TABLE `#__radenium_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `#__radenium_settings`
--
ALTER TABLE `#__radenium_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- --------------------------------------------------------
--
-- Table structure for table `#__radenium_takes`
--

DROP TABLE IF EXISTS `#__radenium_takes`;
CREATE TABLE `#__radenium_takes` (
  `id` int(11) NOT NULL,
  `selectsource` int(11) NOT NULL,
  `vid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `files` int(11) NOT NULL,
  `resolution` varchar(20) NOT NULL,
  `format` int(11) NOT NULL,
  `takedate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `publish` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `#__radenium_takes`
--
ALTER TABLE `#__radenium_takes`
  ADD PRIMARY KEY (`id`);

DROP TABLE IF EXISTS `#__radenium_systemdevices`;
CREATE TABLE `#__radenium_systemdevices` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `type` text NOT NULL,
  `idstr` text NOT NULL,
  `sysid` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `#__radenium_systemdevices`
--
ALTER TABLE `#__radenium_systemdevices`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `#__radenium_systemdevices`
--
ALTER TABLE `#__radenium_systemdevices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
