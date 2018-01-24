-- SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
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

