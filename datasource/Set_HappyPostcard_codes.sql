SELECT '-- Finalising HappyPostcard codes from other codes';

---three letter codes for the country is always good enough for us
UPDATE `location_code` SET `code` = `iso3166_1_a3` WHERE `code` IS NULL;

---with the exception of the 'World' accept UN 3 digit designations as well
UPDATE `location_code` SET `code` = 'SOL3', `name` = 'Earth' WHERE `un_m49`='001';
UPDATE `location_code` SET `code` = `un_m49` WHERE `code` IS NULL;


-- Afghanistan
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,1) || Substr(`un_sub`,4) WHERE `un_sub` IN ('AF-FRA', 'AF-GHA', 'AF-PAN') AND `code` IS NULL;
UPDATE `location_code` SET `code` = Substr(`un_sub`,4) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='AFG') AND `code` IS NULL;

-- Russia - Omskaya obast
UPDATE `location_code` SET `code` = 'OMSK' WHERE `un_sub` IN ('RU-OMS') AND `code` IS NULL;

-- Spain
UPDATE `location_code` SET `code` = 'ESP' || Substr(`un_sub`,4) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='ESP') AND Length(`un_sub`)=4 AND `code` IS NULL;
UPDATE `location_code` SET `code` = 'ESP' || Substr(`un_sub`,4) WHERE `un_sub` IN ('ES-PM', 'ES-PO') AND `code` IS NULL;
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='ESP') AND `code` IS NULL;

-- Azerbaijan
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4) WHERE `un_sub` IN ('AZ-CAB', 'AZ-LER') AND `code` IS NULL;

-- Marshal islands
--- if 2-3 just take 3, leave 2-1 alone
UPDATE `location_code` SET `code` = Substr(`un_sub`,4) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='MHL') AND Length(`un_sub`)=6 AND `code` IS NULL;

-- Trinidad and Tobago
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4,2) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='TTO') AND `code` IS NULL;

-- North Kordofan
UPDATE `location_code` SET `code` = 'SDKN' WHERE `un_sub` IN ('SD-KN10') AND `code` IS NULL;

-- Peru
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4,1) || Substr(`un_sub`,6,1) WHERE `un_sub` IN ('PE-CAJ', 'PE-CAL', 'PE-HUC', 'PE-HUV', 'PE-LAL', 'PE-LAX') AND `code` IS NULL;
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4,2) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='PER') AND `code` IS NULL;

-- Columbia
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4,1) || Substr(`un_sub`,6,1) WHERE `un_sub` IN ('CO-BOL', 'CO-BOY', 'CO-CAL', 'CO-CAQ', 'CO-CAS', 'CO-CAU', 'CO-GUA', 'CO-GUV', 'CO-SAP', 'CO-SAN', 'CO-VAC', 'CO-VAU', 'CO-CUN') AND `code` IS NULL;
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4,2) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='COL') AND `code` IS NULL;

-- Comoros
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='COM') AND `code` IS NULL;

-- Madagascar
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='MDG') AND `code` IS NULL;

-- Taiwan - Taipei
UPDATE `location_code` SET `code` = 'TWNT' WHERE `un_sub` IN ('TW-TPE') AND `code` IS NULL;

-- Mongolia
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,5) WHERE `parent`=(SELECT `id` FROM `location_code` WHERE `iso3166_1_a3`='MNG') AND Substr(`un_sub`, 4, 1) = '0' AND `code` IS NULL;


-- - these codes all start with 0
UPDATE `location_code` SET `code` = (SELECT `iso3166_1_a3` FROM `location_code` AS `l3` WHERE `location_code`.`parent`=`l3`.`id`) || Substr(`un_sub`,5) WHERE `parent` IN (SELECT `parent` FROM `location_code` GROUP BY `parent` having Count(DISTINCT Substr(`un_sub`, 4, 1))=1) AND Length(`un_sub`)=5 AND Substr(`un_sub`, 4, 1)='0' AND `code` IS NULL;


--- random weird codes
--UPDATE `location_code` SET `code`='IEC2' WHERE `un_sub`='IE-C; 2';
--UPDATE `location_code` SET `code`='GNKD' WHERE `un_sub`='GN-KD; 2';

--- Greece latin letters
--UPDATE `location_code` SET `code`='GRC1' WHERE `un_sub`='GR-I';
--UPDATE `location_code` SET `code`='GRC2' WHERE `un_sub`='GR-II';
--UPDATE `location_code` SET `code`='GRC3' WHERE `un_sub`='GR-III';
--UPDATE `location_code` SET `code`='GRC4' WHERE `un_sub`='GR-IV';
--UPDATE `location_code` SET `code`='GRC5' WHERE `un_sub`='GR-V';
--UPDATE `location_code` SET `code`='GRC6' WHERE `un_sub`='GR-VI';
--UPDATE `location_code` SET `code`='GRC7' WHERE `un_sub`='GR-VII';
--UPDATE `location_code` SET `code`='GRC8' WHERE `un_sub`='GR-VIII';
--UPDATE `location_code` SET `code`='GRC9' WHERE `un_sub`='GR-IX';
--UPDATE `location_code` SET `code`='GRC10' WHERE `un_sub`='GR-X';
--UPDATE `location_code` SET `code`='GRC11' WHERE `un_sub`='GR-XI';
--UPDATE `location_code` SET `code`='GRC12' WHERE `un_sub`='GR-XII';
--UPDATE `location_code` SET `code`='GRC13' WHERE `un_sub`='GR-XIII';

---first codes where location is a number
--UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4) WHERE `code` IS NULL AND NOT Substr(`un_sub`,4) GLOB '*[^0-9]*';
---if the extra code is 1, try using 3 letter code of the country
UPDATE `location_code` SET `code` = (SELECT `iso3166_1_a3` FROM `location_code` AS `l3` WHERE `location_code`.`parent`=`l3`.`id`) || Substr(`un_sub`,4) WHERE `code` IS NULL and Length(`un_sub`)=4;
---if the extra code is 4, just use it as is
UPDATE `location_code` SET `code` = Substr(`un_sub`,4) WHERE `code` IS NULL and Length(`un_sub`)=7;
---if the extra code is 3, use the first letter of the 2 leter code + extra
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,1) || Substr(`un_sub`,4) WHERE `code` IS NULL and Length(`un_sub`)=6;
---if the extra code is 2, use the full two letter country code + extra
UPDATE `location_code` SET `code` = Substr(`un_sub`,1,2) || Substr(`un_sub`,4) WHERE `code` IS NULL and Length(`un_sub`)=5;
