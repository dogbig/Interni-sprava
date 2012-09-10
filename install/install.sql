SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- --------------------------------------------------------

--
-- Struktura tabulky `dd`
--

CREATE TABLE IF NOT EXISTS `dd` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `is_actions`
--

CREATE TABLE IF NOT EXISTS `is_actions` (
  `action_id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `textnote` text NOT NULL,
  `subject` varchar(30) NOT NULL,
  KEY `action_id` (`action_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `is_anservices`
--

CREATE TABLE IF NOT EXISTS `is_anservices` (
  `anservice_id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL,
  `datestart` date NOT NULL,
  `textnote` text NOT NULL,
  `subject` varchar(30) NOT NULL,
  KEY `action_id` (`anservice_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `is_customers`
--

CREATE TABLE IF NOT EXISTS `is_customers` (
  `notes` varchar(800) collate utf8_czech_ci NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(80) character set utf8 default NULL,
  `adress` varchar(150) character set utf8 default NULL,
  `tel` varchar(18) character set utf8 default NULL,
  `tel2` varchar(18) collate utf8_czech_ci NOT NULL,
  `hwkeynum` varchar(20) collate utf8_czech_ci NOT NULL,
  `ic` varchar(12) character set utf8 default NULL,
  `email` varchar(50) character set utf8 default NULL,
  `dic` varchar(17) character set utf8 default NULL,
  `trcenter` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=901 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `is_customersold`
--

CREATE TABLE IF NOT EXISTS `is_customersold` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `ic` int(15) NOT NULL,
  `dic` char(20) NOT NULL,
  `adress` varchar(100) NOT NULL,
  `tel` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `notes` varchar(200) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=530 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `is_distributions`
--

CREATE TABLE IF NOT EXISTS `is_distributions` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `sw_num` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `note` varchar(250) NOT NULL,
  KEY `id` (`id`),
  KEY `customer_f` (`company_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1410 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `is_products`
--

CREATE TABLE IF NOT EXISTS `is_products` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `version` varchar(10) NOT NULL,
  `note` varchar(200) character set utf8 collate utf8_czech_ci default NULL,
  KEY `ID` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `is_todo`
--

CREATE TABLE IF NOT EXISTS `is_todo` (
  `todo_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `prevuser_id` int(11) default NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `tobedone` timestamp NOT NULL default '0000-00-00 00:00:00',
  `subject` varchar(35) NOT NULL,
  `notes` varchar(500) NOT NULL,
  `done` tinyint(1) default NULL,
  `hiden` int(2) NOT NULL,
  PRIMARY KEY  (`todo_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `is_users`
--

CREATE TABLE IF NOT EXISTS `is_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(12) NOT NULL,
  `password` varchar(50) NOT NULL,
  `acl` varchar(10) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

INSERT INTO `is_users` (`id`, `username`, `password`, `acl`) VALUES
(29, 'admin', '21f9fa3d89291fb68c718e737079a02e', 'sa');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
