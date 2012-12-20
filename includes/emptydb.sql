--
-- Table structure for table `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `sid` int(50) DEFAULT NULL,
  `order` int(50) DEFAULT NULL,
  `header` varchar(50) DEFAULT NULL,
  `menuname` varchar(50) DEFAULT NULL,
  `headline` varchar(100) DEFAULT NULL,
  `context` text,
  `startedby` int(11) DEFAULT NULL,
  `startedby_date` datetime DEFAULT NULL,
  `updatedby` int(11) DEFAULT NULL,
  `updatedby_date` datetime DEFAULT NULL,
  `url` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `global`
--

CREATE TABLE IF NOT EXISTS `global` (
  `id` int(11) NOT NULL,
  `version` text COLLATE latin1_general_ci NOT NULL,
  `site_url` varchar(80) COLLATE latin1_general_ci NOT NULL,
  `site_name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `meta_author` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `meta_description` varchar(500) COLLATE latin1_general_ci NOT NULL,
  `meta_keywords` varchar(500) COLLATE latin1_general_ci NOT NULL,
  `charset` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `timezone` varchar(30) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maker`
--

CREATE TABLE IF NOT EXISTS `maker` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `last_login` varchar(50) DEFAULT NULL,
  `todo` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headline` varchar(150) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `news` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `author` int(11) NOT NULL DEFAULT '0',
  `time` bigint(20) NOT NULL DEFAULT '0',
  `updatedtime` bigint(20) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uid` int(50) NOT NULL,
  `sid` int(50) NOT NULL,
  `permissions` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permissions_extra`
--

CREATE TABLE IF NOT EXISTS `permissions_extra` (
  `id` int(10) unsigned NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE IF NOT EXISTS `quotes` (
  `week01` text NOT NULL,
  `week02` text NOT NULL,
  `week03` text NOT NULL,
  `week04` text NOT NULL,
  `week05` text NOT NULL,
  `week06` text NOT NULL,
  `week07` text NOT NULL,
  `week08` text NOT NULL,
  `week09` text NOT NULL,
  `week10` text NOT NULL,
  `week11` text NOT NULL,
  `week12` text NOT NULL,
  `week13` text NOT NULL,
  `week14` text NOT NULL,
  `week15` text NOT NULL,
  `week16` text NOT NULL,
  `week17` text NOT NULL,
  `week18` text NOT NULL,
  `week19` text NOT NULL,
  `week20` text NOT NULL,
  `week21` text NOT NULL,
  `week22` text NOT NULL,
  `week23` text NOT NULL,
  `week24` text NOT NULL,
  `week25` text NOT NULL,
  `week26` text NOT NULL,
  `week27` text NOT NULL,
  `week28` text NOT NULL,
  `week29` text NOT NULL,
  `week30` text NOT NULL,
  `week31` text NOT NULL,
  `week32` text NOT NULL,
  `week33` text NOT NULL,
  `week34` text NOT NULL,
  `week35` text NOT NULL,
  `week36` text NOT NULL,
  `week37` text NOT NULL,
  `week38` text NOT NULL,
  `week39` text NOT NULL,
  `week40` text NOT NULL,
  `week41` text NOT NULL,
  `week42` text NOT NULL,
  `week43` text NOT NULL,
  `week44` text NOT NULL,
  `week45` text NOT NULL,
  `week46` text NOT NULL,
  `week47` text NOT NULL,
  `week48` text NOT NULL,
  `week49` text NOT NULL,
  `week50` text NOT NULL,
  `week51` text NOT NULL,
  `week52` text NOT NULL,
  `random` tinyint(1) NOT NULL DEFAULT '0',
  KEY `random` (`random`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE IF NOT EXISTS `sections` (
  `sid` int(50) NOT NULL AUTO_INCREMENT,
  `header` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `startedby` int(11) DEFAULT NULL,
  `startedby_date` datetime DEFAULT NULL,
  `updatedby` int(11) DEFAULT NULL,
  `updatedby_date` datetime DEFAULT NULL,
  `order` int(50) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  KEY `sid` (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tracker`
--

CREATE TABLE IF NOT EXISTS `tracker` (
  `IP` varchar(15) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `URL` varchar(500) COLLATE latin1_general_ci NOT NULL,
  `browser` varchar(500) COLLATE latin1_general_ci DEFAULT NULL,
  `referer` varchar(500) COLLATE latin1_general_ci DEFAULT NULL,
  `date_auto` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
