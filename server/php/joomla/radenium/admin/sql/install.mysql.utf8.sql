-- --------------------------------------------------------

-- SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
-- --------------------------------------------------------
--
-- Table structure for table `#__radenium_encdck_encodetasks`
--

DROP TABLE IF EXISTS `#__radenium_encdck_encodetasks`;
CREATE TABLE `#__radenium_encdck_encodetasks` (
  `id` int(11) NOT NULL,
  `publish` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `vid` text NOT NULL,
  `aid` text NOT NULL,
  `prog_id_str` text NOT NULL,
  `format` text NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `#__radenium_encdck_encodetasks`
--
ALTER TABLE `#__radenium_encdck_encodetasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `#__radenium_encdck_encodetasks`
--
ALTER TABLE `#__radenium_encdck_encodetasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

