CREATE TABLE IF NOT EXISTS `jos_portfolios` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `logo_path` varchar(256) NOT NULL DEFAULT '../images/porfolio/no_user.png',
  `fio` varchar(40) NOT NULL,
  `date_rojjdeniy` varchar(10) NOT NULL,
  `telefon` varchar(16) NOT NULL,
  `email` varchar(20) NOT NULL,
  `student` text NOT NULL,
  `worcked` text NOT NULL,
  `photo_path` text NOT NULL,
  `date_reg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) unsigned NOT NULL,
  `agroup` varchar(8) NOT NULL DEFAULT '',
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=61 ;