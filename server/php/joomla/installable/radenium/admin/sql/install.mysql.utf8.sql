-- SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
-- --------------------------------------------------------
--
-- Table structure for table `#__radenium_ffmpeg`
--

DROP TABLE IF EXISTS `#__radenium_ffmpeg`;
CREATE TABLE `#__radenium_ffmpeg` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `command` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `#__radenium_ffmpeg`
--
ALTER TABLE `#__radenium_ffmpeg`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `#__radenium_ffmpeg`
--
ALTER TABLE `#__radenium_ffmpeg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


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
  `resolution` int(11) NOT NULL,
  `format` int(11) NOT NULL,
  `takedate` text NOT NULL,
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
