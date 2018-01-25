-- SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
-- --------------------------------------------------------
--
-- Table structure for table `#__radenium_systemdevices`
--

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

