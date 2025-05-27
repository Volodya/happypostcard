SELECT '-- No longer countries';

INSERT INTO `location_code`(`code`, `name`, `parent`, `iso3166_1_a2`, `iso3166_1_a3`, `iso3166_1_num`, `iso3166_2_ext`, `un_m49`, `itu`, `ioc`, `fifa`, `icao`, `iata`, `map_link`, `description_link`)
VALUES
 ('128', 'Canton and Enderbury Islands', NULL, NULL, NULL, NULL, NULL, '128', NULL, NULL, NULL, NULL, NULL, 'LocationCantonIsl.png', 'https://en.wikipedia.org/wiki/Canton_and_Enderbury_Islands')
,('200', 'Czechoslovakia', NULL, NULL, NULL, NULL, NULL,'200', NULL, NULL, NULL, NULL, NULL, 'Czechoslovakia location map.svg', 'https://en.wikipedia.org/wiki/Czechoslovakia')
,('720', 'Peopleʼs Democratic Republic of Yemen', NULL, NULL, NULL, NULL, NULL,'720', NULL, NULL, NULL, NULL, NULL, 'Aden Yemen on the globe (South Yemen centered orthographic projection).svg', 'https://en.wikipedia.org/wiki/South_Yemen')
,('FRG', 'Federal Republic of Germany', NULL, NULL, NULL, NULL, NULL,'280', NULL, NULL, NULL, NULL, NULL, 'West Germany 1956-1990.svg', 'https://en.wikipedia.org/wiki/West_Germany')
,('274', 'Gaza Strip', NULL, NULL, NULL, NULL, NULL,'', NULL, NULL, NULL, NULL, NULL, 'Gz-map2.png', 'https://en.wikipedia.org/wiki/Gaza_Strip')
,('DDR', 'German Democratic Republic', NULL, NULL, NULL, NULL, NULL,'278', NULL, NULL, NULL, NULL, NULL, 'East Germany 1956-1990.svg', 'https://en.wikipedia.org/wiki/East_Germany')
,('396', 'Kalama Atoll', NULL, NULL, NULL, NULL, NULL,'396', NULL, NULL, NULL, NULL, NULL, '', 'https://en.wikipedia.org/wiki/Johnston_Atoll')
,('488', 'Midway Atoll', NULL, NULL, NULL, NULL, NULL,'488', NULL, NULL, NULL, NULL, NULL, '', 'https://en.wikipedia.org/wiki/Midway_Atoll')
,('TTPI', 'Trust Territory of the Pacific Islands', NULL, NULL, NULL, NULL, NULL,'582', NULL, NULL, NULL, NULL, NULL, 'TTPI-locatormap.png', 'https://en.wikipedia.org/wiki/Trust_Territory_of_the_Pacific_Islands')
,('SFRY', 'SFR Yugoslavia', NULL, NULL, NULL, NULL, NULL,'890', NULL, NULL, NULL, NULL, NULL, 'Yugoslavia 1956-1990.svg', 'https://en.wikipedia.org/wiki/Socialist_Federal_Republic_of_Yugoslavia')
,('USSR', 'Union of Soviet Socialist Republics', NULL, 'SU', 'SUN', NULL, NULL,'810', NULL, NULL, NULL, NULL, NULL, 'Union of Soviet Socialist Republics (orthographic projection).svg', 'https://en.wikipedia.org/wiki/Soviet_Union')
,('872', 'Wake Atoll', NULL, NULL, NULL, NULL, NULL,'872', NULL, NULL, NULL, NULL, NULL, 'Wake Island NASA photo map.jpg', 'https://en.wikipedia.org/wiki/Wake_Island')
;