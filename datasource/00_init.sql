SELECT 'whiping old tables';

DROP TABLE IF EXISTS `sending_status_type`;
DROP TABLE IF EXISTS `user_image`;
DROP TABLE IF EXISTS `postcard_image`;
DROP TABLE IF EXISTS `postcard`;
DROP TABLE IF EXISTS `learnlanguages_thanks`;
DROP TABLE IF EXISTS `address_waiting_approval`;
DROP TABLE IF EXISTS `address`;
DROP TABLE IF EXISTS `user_blacklist`;
DROP TABLE IF EXISTS `user_preference`;
DROP TABLE IF EXISTS `user_password_recover_secret`;
DROP TABLE IF EXISTS `user_persistent_login`;
DROP TABLE IF EXISTS `user_waiting_approval`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `location_code`;
DROP TABLE IF EXISTS `type_boolean`;

--
SELECT 'creating tables';

SELECT '-- type_boolean';
CREATE TABLE IF NOT EXISTS `type_boolean`
(
	`id` INTEGER PRIMARY KEY,
	`boolean` TEXT NOT NULL CHECK(`boolean` IN ('TRUE','FALSE') )
);
INSERT INTO `type_boolean`(`id`, `boolean`) VALUES (1, 'TRUE');
INSERT INTO `type_boolean`(`id`, `boolean`) VALUES (0, 'FALSE');

SELECT '-- location_code';
CREATE TABLE `location_code`
(
	`id` INTEGER PRIMARY KEY,
	`code` VARCHAR(10) UNIQUE,
	`name` VARCHAR(255),
	`parent` INTEGER DEFAULT NULL,
	
	`iso3166_1_a2` CHAR(2) DEFAULT NULL,
	`iso3166_1_a3` CHAR(3) DEFAULT NULL,
	`iso3166_1_num` CHAR(3) DEFAULT NULL,
	`iso3166_2_ext` VARCHAR(10) DEFAULT NULL,
	`un_m49` CHAR(3) DEFAULT NULL,
	`un_sub` VARCHAR(10) DEFAULT NULL UNIQUE,
	`itu` CHAR(3) DEFAULT NULL,
	`ioc` CHAR(3) DEFAULT NULL,
	`fifa` CHAR(3) DEFAULT NULL,
	`icao` VARCHAR(2) DEFAULT NULL,
	`iata` CHAR(3) DEFAULT NULL,
	
	`map_link` VARCHAR(255),
	`description_link` VARCHAR(255)
);

SELECT '-- user';
CREATE TABLE `user`
(
	`id` INTEGER PRIMARY KEY,
	`login` VARCHAR(80) UNIQUE NOT NULL,
	`polite_name` VARCHAR(255) DEFAULT '',
	`pass_hash` VARCHAR(255) NOT NULL,
	`email` VARCHAR(255),
	
	`registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`loggedin_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	
	`deleted_at` TIMESTAMP DEFAULT NULL,
	`blocked_at` TIMESTAMP DEFAULT NULL,
	`disabled_at` TIMESTAMP DEFAULT NULL,
	
	`address_changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`confirmed_as_sender_at` TIMESTAMP DEFAULT NULL,
	`confirmed_as_receiver_at` TIMESTAMP DEFAULT NULL,
	
	`home_location_id` INTEGER DEFAULT NULL REFERENCES `location_code`(`id`),
	`travelling_location_id` INTEGER DEFAULT NULL REFERENCES `location_code`(`id`),
	
	`active_profile_id` INTEGER DEFAULT NULL UNIQUE
);

SELECT '-- user_profile';
CREATE TABLE `user_profile`
(
	`id` INTEGER PRIMARY KEY,
	`user_id` INTEGER NOT NULL,
	
	`birthday` DATE DEFAULT NULL,
	
	`about` TEXT DEFAULT '',
	`desires` TEXT DEFAULT '',
	`hobbies` TEXT DEFAULT '',
	`phobias` TEXT DEFAULT '',
	`languages` TEXT DEFAULT '',
	
	`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT '-- user_waiting_approval';
CREATE TABLE `user_waiting_approval`
(
	`id` INTEGER PRIMARY KEY,
	`user_id` INTEGER NOT NULL UNIQUE,
	`reason` VARCHAR(255) DEFAULT 'initial confirmation'
);

SELECT '-- user_persistent_login';
CREATE TABLE `user_persistent_login`
(
	`id` INTEGER PRIMARY KEY,
	`user_id` INTEGER NOT NULL UNIQUE,
	`secret` CHAR(256) NOT NULL UNIQUE
);

SELECT '-- user_password_recover_secret';
CREATE TABLE `user_password_recover_secret`
(
	`user_id` INTEGER NOT NULL PRIMARY KEY,
	`secret_code` CHAR(40) NOT NULL UNIQUE
);

SELECT '-- user_preference';
CREATE TABLE `user_preference`
(
	`id` INTEGER PRIMARY KEY,
	`user_id` INTEGER NOT NULL,
	`key` VARCHAR(255) NOT NULL,
	`val` VARCHAR(255)
);

SELECT '-- user_blacklist';
CREATE TABLE `user_blacklist`
(
	`id` INTEGER PRIMARY KEY,
	`user_id` INTEGER NOT NULL,
	`enemy_user_id` INTEGER NOT NULL,
	`added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT '-- address';
CREATE TABLE `address`
(
	`id` INTEGER PRIMARY KEY,
	`user_id` INTEGER NOT NULL,
	`language_code` VARCHAR(10) DEFAULT 'en',
	`addr` TEXT NOT NULL
);

SELECT '-- address_waiting_approval';
CREATE TABLE `address_waiting_approval`
(
	`id` INTEGER PRIMARY KEY,
	`user_id` INTEGER NOT NULL,
	`address_id` INTEGER NOT NULL UNIQUE,
	`reason` VARCHAR(255) -- 'address changed', 'address added'
);

SELECT '-- learnlanguages_thanks';
CREATE TABLE `learnlanguages_thanks`
(
	`id` INTEGER PRIMARY KEY,
	`language` VARCHAR(255) NOT NULL,
	`language_code` CHAR(3) NOT NULL,
	`phrase` VARCHAR(255) NOT NULL
);

SELECT '-- postcard';
CREATE TABLE `postcard`
(
	`id` INTEGER PRIMARY KEY,
	`num` INTEGER,
	`year` int,
	`code` VARCHAR(255) UNIQUE,
	`sender_id` INTEGER NOT NULL,
	`send_location_id` INTEGER NOT NULL,
	`sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`receiver_id` INTEGER NOT NULL,
	`receive_location_id` INTEGER NOT NULL,
	`received_at` TIMESTAMP DEFAULT NULL,
	`type` INTEGER DEFAULT 0
);

SELECT '-- postcard_image';
CREATE TABLE `postcard_image`
(
	`id` INTEGER PRIMARY KEY,
	`postcard_id` INTEGER NOT NULL,
	`uploader_profile_id` INTEGER NOT NULL,
	`sender_default` BOOLEAN DEFAULT FALSE,
	`receiver_default` BOOLEAN DEFAULT FALSE,
	`uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`hash` VARCHAR(255) NOT NULL,
	`extension` VARCHAR(10) NOT NULL,
	`mime` VARCHAR(64) NOT NULL
);

SELECT '-- user_image';
CREATE TABLE `user_image`
(
	`id` INTEGER PRIMARY KEY,
	`user_id` INTEGER NOT NULL,
	`default` BOOLEAN DEFAULT FALSE,
	`num` INTEGER DEFAULT 1,
	`uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`hash` VARCHAR(255) NOT NULL,
	`extension` VARCHAR(10) NOT NULL,
	`mime` VARCHAR(64) NOT NULL
);

--
SELECT 'registering v010dya';
INSERT INTO `user`(`login`, `polite_name`, `pass_hash`, `email`, `about`, `desires`, `hobbies`, `phobias`, `languages`)
	VALUES('v010dya', 'Volodya', '$2y$12$yYfVBrGcJLXS1emftJnYCO6.Cw6xtg4CWyALAfM0P5yP3.3mA.f52', 'EchoOfFreedom@riseup.net', 'I am a postcrosser from Anapa', 'I want weird cards', 'gardening, poetry, genealogy', '', 'English, Russian');
SELECT '-- setting up addresses for v010dya';
INSERT INTO `address`(`user_id`,`language_code`,`addr`)
	VALUES((SELECT `id` FROM `user` WHERE `login`='v010dya'), 'en', "Volodya"||CHAR(10)||"ul Mayakovskogo d 146A"||CHAR(10)||"Anapa, Krasnoarskiy kray"||CHAR(10)||"353451"||CHAR(10)||"Russia");
INSERT INTO `address`(`user_id`,`language_code`,`addr`)
	VALUES((SELECT `id` FROM `user` WHERE `login`='v010dya'), 'ru', "Володя"||CHAR(10)||"ул Маяковского д 146А"||CHAR(10)||"Анапа, Краснодарский край"||CHAR(10)||"353451"||CHAR(10)||"Russia");
UPDATE `user` SET `home_location_id` = (SELECT `id` FROM `location_code` WHERE `code`='RKDA') WHERE `login`='v010dya';

--
SELECT 'load data';
.read '/home/va/www/happypostcard/datasource/01_learnlanguages.sql'
-- .read '/home/va/www/happypostcard/datasource/02_all_geo_slow.sql'
.read '/home/va/www/happypostcard/datasource/02_all_geo.sql'

--
SELECT 'load test data';
INSERT INTO `user_preference`(`user_id`,`key`,`val`)
	VALUES((SELECT `id` FROM `user` WHERE `login`='v010dya'), 'home_location', (SELECT `code` FROM `location_code` WHERE `name`='Krasnodarskiy kray'));

--INSERT INTO `user`(`login`, `polite_name`, `pass_hash`, `email`, `about`, `desires`, `hobbies`, `phobias`, `languages`)
--	VALUES('bob', 'Bob', '$2y$12$yYfVBrGcJLXS1emftJnYCO6.Cw6xtg4CWyALAfM0P5yP3.3mA.f52', 'bob@bob.com', 'I am a fictional postcrosser.', 'I want any cards', 'running', '', 'English');
--INSERT INTO `address`(`user_id`,`language_code`,`addr`)
--	VALUES((SELECT `id` FROM `user` WHERE `login`='bob'), 'en', "Bob"||CHAR(10)||"Bob's streets"||CHAR(10)||"Afghanistan");

--INSERT INTO `postcard`(`num`, `year`, `code`, `sender_id`, `send_location_id`, `receiver_id`, `receive_location_id`, `received_at`)
--	VALUES (1, 1981, 'RKDA-USA-1981-1', 1, 3711, 2, 236, CURRENT_TIMESTAMP);
--INSERT INTO `postcard_image`(`postcard_id`, `uploader_profile_id`, `hash`, `extension`, `mime`)
--	VALUES(1, 1, '01', 'png', 'image/png');
--INSERT INTO `postcard_image`(`postcard_id`, `uploader_profile_id`, `hash`, `extension`, `mime`)
--	VALUES(1, 1, '9c0fcdd27fc5ef7687aa82e7d942cf506604fdc54d4a6db6a76f6c8417c27fe2', 'webp', 'image/webp');
--INSERT INTO `postcard`(`num`, `year`, `code`, `sender_id`, `send_location_id`, `receiver_id`, `receive_location_id`, `received_at`)
--	VALUES (2, 1981, 'SOL3-USA-1981-2', 1, 4822, 2, 236, CURRENT_TIMESTAMP);
--INSERT INTO `postcard_image`(`postcard_id`, `uploader_profile_id`, `hash`, `extension`, `mime`)
--	VALUES(2, 1, '02', 'png', 'image/png');
--INSERT INTO `postcard`(`num`, `year`, `code`, `sender_id`, `send_location_id`, `receiver_id`, `receive_location_id`, `received_at`)
--	VALUES (3, 1981, 'RKDA-USA-1981-3', 1, 3711, 2, 236, NULL);
