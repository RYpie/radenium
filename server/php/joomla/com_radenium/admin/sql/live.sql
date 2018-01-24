-- SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
-- --------------------------------------------------------
--
-- Table structure for table `#__radenium_live`
--

DROP TABLE IF EXISTS `#__radenium_live`;
CREATE TABLE `#__radenium_live` (
  `id` int(11) NOT NULL,
  `key` text NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `#__radenium_live`
--
ALTER TABLE `#__radenium_live`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `#__radenium_live`
--
ALTER TABLE `#__radenium_live`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

