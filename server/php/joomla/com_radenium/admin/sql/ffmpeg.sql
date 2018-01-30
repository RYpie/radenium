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
  `command` text NOT NULL,
  `platform` int(1) NOT NULL,
  `category` int(1) NOT NULL
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

