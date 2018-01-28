-- SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
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
  `title` TEXT NOT NULL
  `notes` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `#__radenium_takes`
--
ALTER TABLE `#__radenium_takes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `#__radenium_takes`
--
ALTER TABLE `#__radenium_takes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

