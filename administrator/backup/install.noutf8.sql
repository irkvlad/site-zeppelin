CREATE TABLE IF NOT EXISTS `#__attachments`
(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`filename` VARCHAR(240) NOT NULL,
	`filename_sys` TEXT NOT NULL,
	`file_type` VARCHAR(30) NOT NULL,
	`file_size` INT(11) UNSIGNED NOT NULL,
	`icon_filename` VARCHAR(60) NOT NULL,
	`display_filename` VARCHAR(240) NOT NULL DEFAULT '',
	`description` TEXT NOT NULL DEFAULT '',
	`url` TEXT NOT NULL,
	`uploader_id` INT(11) NOT NULL,
	`article_id` INT(11) UNSIGNED NOT NULL,
	`published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`user_field_1` TEXT NOT NULL DEFAULT '',
	`user_field_2` TEXT NOT NULL DEFAULT '',
	`user_field_3` TEXT NOT NULL DEFAULT '',
    `create_date` DATETIME DEFAULT NULL,
    `modification_date` DATETIME DEFAULT NULL,
    `download_count` INT(11) UNSIGNED DEFAULT '0',
     PRIMARY KEY (`id`)
);

