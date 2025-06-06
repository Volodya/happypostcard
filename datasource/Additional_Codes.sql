SELECT '-- Additional Codes';

INSERT INTO `location_code`(`code`, `name`, `un_m49`, `map_link`, `description_link`)
VALUES
 ('CIS', 'Commonwealth of Independent States', '172', 'CIS (orthographic projection, only Crimea disputed).svg', 'https://en.wikipedia.org/wiki/Commonwealth_of_Independent_States')
,('AFU', 'African Union', NULL, 'African Union (orthographic projection).svg', 'https://en.wikipedia.org/wiki/African_Union')
,('ARAB', 'Arab League', NULL, 'Arab League member states (orthographic projection).svgs', 'https://en.wikipedia.org/wiki/Arab_League')
;
INSERT INTO `location_code`(`code`, `name`, `parent`, `map_link`, `description_link`)
VALUES
 ('FREE', 'Free Territory (Makhnovshina)',                     NULL, 'Makhnovia.svg', 'https://en.wikipedia.org/wiki/Makhnovshchina')
,('STRC', 'Strandzha Commune',                                 NULL, 'Lokalizacja Strandży.jpg', 'https://en.wikipedia.org/wiki/Strandzha_Commune')
,('KPAM', 'Korean Peopleʼs Association in Manchuria',          NULL, 'Location of Mudanjiang Prefecture within Heilongjiang (China).png', "https://en.wikipedia.org/wiki/Korean_People's_Association_in_Manchuria")
,('RCAT', 'Revolutionary Catalonia',                           NULL, NULL, 'https://en.wikipedia.org/wiki/Revolutionary_Catalonia')
,('CRDA', 'Regional Defence Council of Aragon',                NULL, 'MapaCRDA.png', 'https://en.wikipedia.org/wiki/Regional_Defence_Council_of_Aragon')
,('STPN', 'Stapleton Colony',                                  NULL, NULL, 'https://en.wikipedia.org/wiki/Stapleton_Colony')
,('FOEC', 'Federation of Egalitarian Communities',             NULL, NULL, 'https://en.wikipedia.org/wiki/Federation_of_Egalitarian_Communities')
,('STDN', 'Freetown Christiania',                              NULL, 'Christiania (OpenStreetMap within Copenhagen).png', 'https://en.wikipedia.org/wiki/Freetown_Christiania')
,('LMAI', 'Longo Maï',                                         NULL, NULL, 'https://en.wikipedia.org/wiki/Longo_Ma%C3%AF')
,('KNDN', 'Kommune Niederkaufungen',                           NULL, NULL, 'https://en.wikipedia.org/wiki/Kommune_Niederkaufungen')
,('AKCM', 'Metelkova',                                         NULL, NULL, 'https://en.wikipedia.org/wiki/Metelkova')
,('TBPX', 'Trumbullplex',                                      NULL, NULL, 'https://en.wikipedia.org/wiki/Trumbullplex')
,('ZADN', 'ZAD de Notre-Dame-des-Landes',                      NULL, NULL, 'https://en.wikipedia.org/wiki/ZAD_de_Notre-Dame-des-Landes')
,('ERRE', 'Errekaleor Bizirik',                                NULL, NULL, 'https://en.wikipedia.org/wiki/Errekaleor')
,('KKTZ', 'Kukutza',                                           NULL, NULL, 'https://en.wikipedia.org/wiki/Kukutza')
,('FEJU', 'Federation of Neighborhood Councils-El Alto',       NULL, NULL, 'https://en.wikipedia.org/wiki/Fejuve')
,('MARZ', 'Rebel Zapatista Autonomous Municipalities',         NULL, 'Mexico Chiapas neozapatista map.svg', 'https://en.wikipedia.org/wiki/Rebel_Zapatista_Autonomous_Municipalities')
,('ANES', 'Autonomous Administration of North and East Syria', NULL, 'Map of Rojava cantons march 22.png', 'https://en.wikipedia.org/wiki/Autonomous_Administration_of_North_and_East_Syria')
,('UTPA', 'Utopia (inside Ohio, USA)',                         NULL, 'Utopia-Ohio.png', 'https://en.wikipedia.org/wiki/Utopia,_Ohio')
,('UCMT', 'Utopian Community of Modern Times',                 NULL, NULL, 'https://en.wikipedia.org/wiki/Utopian_Community_of_Modern_Times')
,('HOME', 'Home (inside Washington, USA)',                     NULL, NULL, 'https://en.wikipedia.org/wiki/Home,_Washington')
,('BOCC', 'Equality Colony',                                   NULL, NULL, 'https://en.wikipedia.org/wiki/Equality_Colony')
,('WHIT', 'Whiteway Colony',                                   NULL, NULL, 'https://en.wikipedia.org/wiki/Whiteway_Colony')
,('SRON', 'Soviet Republic of Naissaar',                       NULL, NULL, 'https://en.wikipedia.org/wiki/Naissaar#Soviet_Republic_of_Naissaar')
,('BMSR', 'Bavarian Soviet Republic',                          NULL, 'Map-WR-Bavaria.svg', 'https://en.wikipedia.org/wiki/Bavarian_Soviet_Republic')
,('BRSR', 'Bremen Soviet Republic',                            NULL, 'Bremen in the German Reich (1925).svg', 'https://en.wikipedia.org/wiki/Bremen_Soviet_Republic')
,('WURR', 'Würzburg Soviet Republic',                          NULL, NULL, 'https://en.wikipedia.org/wiki/W%C3%BCrzburg_Soviet_Republic')
,('LALC', 'Life and Labor Commune',                            NULL, NULL, 'https://en.wikipedia.org/wiki/Life_and_Labor_Commune')
,('DROP', 'Drop City',                                         NULL, NULL, 'https://en.wikipedia.org/wiki/Drop_City')
,('FROW', 'Free Republic of Wendland',                         NULL, NULL, 'https://en.wikipedia.org/wiki/Free_Republic_of_Wendland')
,('COMP', 'Paris Commune',                                     NULL, NULL, 'https://en.wikipedia.org/wiki/Paris_Commune')
,('BLBR', 'Black Bear Ranch',                                  NULL, NULL, 'https://en.wikipedia.org/wiki/Black_Bear_Ranch')
,('FARM', 'The Farm (inside Tennessee, USA)',                  NULL, NULL, 'https://en.wikipedia.org/wiki/The_Farm_%28Tennessee%29')
,('AWRA', 'Awra Amba',                                         NULL, NULL, 'https://en.wikipedia.org/wiki/Awra_Amba')
,('TUNI', 'Tenacious Unicorn Ranch',                           NULL, NULL, 'https://en.wikipedia.org/wiki/Tenacious_Unicorn_Ranch')
,('PLND', 'Pooleʼs Land',                                      NULL, NULL, "https://en.wikipedia.org/wiki/Poole's_Land")
,('NAUS', 'New Australia (inside Paraguay)',                   NULL, 'Cosme.jpg', 'https://en.wikipedia.org/wiki/New_Australia')
;

INSERT INTO `location_code`(`code`, `name`, `parent`, `map_link`, `description_link`)
VALUES
 ('ACRN', 'Acorn Community', (SELECT `id` FROM `location_code` WHERE `code`='FOEC'), NULL, 'https://en.wikipedia.org/wiki/Acorn_Community_Farm')
,('EWND', 'East Wind Community', (SELECT `id` FROM `location_code` WHERE `code`='FOEC'), NULL, 'https://en.wikipedia.org/wiki/East_Wind_Community')
,('SAPL', 'Sapling Community', (SELECT `id` FROM `location_code` WHERE `code`='FOEC'), NULL, NULL)
,('MDDN', 'The Midden', (SELECT `id` FROM `location_code` WHERE `code`='FOEC'), NULL, 'http://themidden.wordpress.com/about/')
,('SNDH', 'Sandhill Community', (SELECT `id` FROM `location_code` WHERE `code`='FOEC'), NULL, 'https://www.sandhillfarm.org/about/')
,('TWIN', 'Twin Oaks Community', (SELECT `id` FROM `location_code` WHERE `code`='FOEC'), NULL, 'https://en.wikipedia.org/wiki/Twin_Oaks_Community,_Virginia')
;