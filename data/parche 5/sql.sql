/*## Configuración para el aserradero */
DELETE FROM `module_settings` WHERE `modulename` = 'lumberyard';
INSERT INTO `module_settings` (`modulename`, `setting`, `value`)
VALUES
	('lumberyard', 'alignevil', '3'),
	('lumberyard', 'alloworchard', '1'),
	('lumberyard', 'beargem', '1'),
	('lumberyard', 'chopop', '1'),
	('lumberyard', 'crushgem', '1'),
	('lumberyard', 'crushgold', '250'),
	('lumberyard', 'fingergem', '1'),
	('lumberyard', 'fruitalign', '7'),
	('lumberyard', 'gnomegold', '200'),
	('lumberyard', 'leveladj', '1'),
	('lumberyard', 'levelreq', '2'),
	('lumberyard', 'limitloc', '2'),
	('lumberyard', 'lumberturns', '15'),
	('lumberyard', 'maximumsell', '50'),
	('lumberyard', 'nosuper', '1'),
	('lumberyard', 'perpage', '25'),
	('lumberyard', 'runonce', '1'),
	('lumberyard', 'showFormTabIndex', '3'),
	('lumberyard', 'squarepay', '250'),
	('lumberyard', 'squarepaymax', '350'),
	('lumberyard', 'squarepaymin', '250'),
	('lumberyard', 'usehof', '1'),
	('lumberyard', 'usehofp', '1'),
	('lumberyard', 'woodsold', '0');

/*-- Configuración del aserradero para las ciudades */
DELETE FROM `module_objprefs` WHERE (`modulename` = 'lumberyard' AND `objtype` = 'city');
INSERT INTO `module_objprefs` (`modulename`, `objtype`, `setting`, `objid`, `value`)
VALUES
	('lumberyard', 'city', 'cccount', 1, '0'),
	('lumberyard', 'city', 'cccount', 3, '0'),
	('lumberyard', 'city', 'cccount', 4, '0'),
	('lumberyard', 'city', 'cccount', 5, '0'),
	('lumberyard', 'city', 'cccount', 6, '0'),
	('lumberyard', 'city', 'cccount', 7, '0'),
	('lumberyard', 'city', 'chophere', 1, '0'),
	('lumberyard', 'city', 'chophere', 3, '1'),
	('lumberyard', 'city', 'chophere', 4, '0'),
	('lumberyard', 'city', 'chophere', 5, '0'),
	('lumberyard', 'city', 'chophere', 6, '1'),
	('lumberyard', 'city', 'chophere', 7, '0'),
	('lumberyard', 'city', 'clearcut', 1, '10'),
	('lumberyard', 'city', 'clearcut', 3, '10'),
	('lumberyard', 'city', 'clearcut', 4, '10'),
	('lumberyard', 'city', 'clearcut', 5, '10'),
	('lumberyard', 'city', 'clearcut', 6, '10'),
	('lumberyard', 'city', 'clearcut', 7, '10'),
	('lumberyard', 'city', 'clearcutter', 1, 'Evil Douglas'),
	('lumberyard', 'city', 'clearcutter', 3, 'Amrod Ancalímon'),
	('lumberyard', 'city', 'clearcutter', 4, 'Evil Douglas'),
	('lumberyard', 'city', 'clearcutter', 5, 'Evil Douglas'),
	('lumberyard', 'city', 'clearcutter', 6, 'Malvado Douglas'),
	('lumberyard', 'city', 'clearcutter', 7, 'Evil Douglas'),
	('lumberyard', 'city', 'cutdown', 1, '0'),
	('lumberyard', 'city', 'cutdown', 3, '0'),
	('lumberyard', 'city', 'cutdown', 4, '0'),
	('lumberyard', 'city', 'cutdown', 5, '0'),
	('lumberyard', 'city', 'cutdown', 6, '0'),
	('lumberyard', 'city', 'cutdown', 7, '0'),
	('lumberyard', 'city', 'cutpercent', 1, '50'),
	('lumberyard', 'city', 'cutpercent', 3, '50'),
	('lumberyard', 'city', 'cutpercent', 4, '50'),
	('lumberyard', 'city', 'cutpercent', 5, '50'),
	('lumberyard', 'city', 'cutpercent', 6, '50'),
	('lumberyard', 'city', 'cutpercent', 7, '50'),
	('lumberyard', 'city', 'daygrowth', 1, '10'),
	('lumberyard', 'city', 'daygrowth', 3, '100'),
	('lumberyard', 'city', 'daygrowth', 4, '10'),
	('lumberyard', 'city', 'daygrowth', 5, '10'),
	('lumberyard', 'city', 'daygrowth', 6, '30'),
	('lumberyard', 'city', 'daygrowth', 7, '10'),
	('lumberyard', 'city', 'fullsize', 1, '200'),
	('lumberyard', 'city', 'fullsize', 3, '1000'),
	('lumberyard', 'city', 'fullsize', 4, '200'),
	('lumberyard', 'city', 'fullsize', 5, '200'),
	('lumberyard', 'city', 'fullsize', 6, '300'),
	('lumberyard', 'city', 'fullsize', 7, '200'),
	('lumberyard', 'city', 'plantneed', 1, '100'),
	('lumberyard', 'city', 'plantneed', 3, '300'),
	('lumberyard', 'city', 'plantneed', 4, '100'),
	('lumberyard', 'city', 'plantneed', 5, '100'),
	('lumberyard', 'city', 'plantneed', 6, '150'),
	('lumberyard', 'city', 'plantneed', 7, '100'),
	('lumberyard', 'city', 'remainsize', 1, '200'),
	('lumberyard', 'city', 'remainsize', 3, '1000'),
	('lumberyard', 'city', 'remainsize', 4, '200'),
	('lumberyard', 'city', 'remainsize', 5, '200'),
	('lumberyard', 'city', 'remainsize', 6, '300'),
	('lumberyard', 'city', 'remainsize', 7, '200');

/* ---------------------------------- */

/*## Configuración para las viviendas */
DELETE FROM `module_settings` WHERE `modulename` = 'dwellings';
INSERT INTO `module_settings` (`modulename`, `setting`, `value`)
VALUES
	('dwellings', 'abnperc', '110'),
	('dwellings', 'addcof', '1'),
	('dwellings', 'commwhat', '1'),
	('dwellings', 'delete', '\r\n						2'),
	('dwellings', 'delete2', '\r\n					1'),
	('dwellings', 'demoper', '35'),
	('dwellings', 'descgemcost', '0'),
	('dwellings', 'descgoldcost', '265'),
	('dwellings', 'dumpcof', '0'),
	('dwellings', 'enablecof', '1'),
	('dwellings', 'levelsell', '10'),
	('dwellings', 'listnum', '25'),
	('dwellings', 'logoutlocation', 'Dentro de la vivienda'),
	('dwellings', 'lvlbuy', '0'),
	('dwellings', 'maxcofferdeps', '3'),
	('dwellings', 'maxcoffergems', '50'),
	('dwellings', 'maxcoffergold', '100000'),
	('dwellings', 'maxcofferwiths', '3'),
	('dwellings', 'namegemcost', '2'),
	('dwellings', 'namegoldcost', '250'),
	('dwellings', 'ownergloballimit', '3'),
	('dwellings', 'showFormTabIndex', '2'),
	('dwellings', 'talkl', '1'),
	('dwellings', 'valueper', '90'),
	('dwellings', 'villagenav', 'Viviendas locales'),
	('dwellings', 'windgemcost', '0'),
	('dwellings', 'windgoldcost', '365'),
	('dwellings', 'zerocof', '0');

DELETE FROM `module_settings` WHERE `modulename` = 'dwshacks';
INSERT INTO `module_settings` (`modulename`, `setting`, `value`)
VALUES
	('dwshacks', 'dkreq', '1'),
	('dwshacks', 'dwname', '`qChabola'),
	('dwshacks', 'dwnameplural', '`qChabolas'),
	('dwshacks', 'enablecof', '1'),
	('dwshacks', 'gemcost', '3'),
	('dwshacks', 'gemsxfer', '3'),
	('dwshacks', 'globallimit', '0'),
	('dwshacks', 'goldcost', '2500'),
	('dwshacks', 'goldxfer', '30'),
	('dwshacks', 'maxgems', '10'),
	('dwshacks', 'maxgold', '500'),
	('dwshacks', 'maxkeys', '1'),
	('dwshacks', 'maxsleep', '1'),
	('dwshacks', 'othersleep', '1'),
	('dwshacks', 'ownersleep', '1'),
	('dwshacks', 'showFormTabIndex', '2'),
	('dwshacks', 'turncost', '50'),
	('dwshacks', 'typeid', '1');

DELETE FROM `module_settings` WHERE `modulename` = 'upkeep';
INSERT INTO `module_settings` (`modulename`, `setting`, `value`)
VALUES
	('upkeep', 'repo', '\r\n				5');


/*-- Configuración de las viviendas para las ciudades */
DELETE FROM `module_objprefs` WHERE (`modulename` = 'dwellings' AND `objtype` = 'city');
INSERT INTO `module_objprefs` (`modulename`, `objtype`, `setting`, `objid`, `value`)
VALUES
	('dwellings', 'city', 'allcitylimit', 1, '200'),
	('dwellings', 'city', 'allcitylimit', 3, '100'),
	('dwellings', 'city', 'allcitylimit', 4, '100'),
	('dwellings', 'city', 'allcitylimit', 5, '100'),
	('dwellings', 'city', 'allcitylimit', 6, '150'),
	('dwellings', 'city', 'allcitylimit', 7, '1'),
	('dwellings', 'city', 'ownercitylimit', 1, '1'),
	('dwellings', 'city', 'ownercitylimit', 3, '1'),
	('dwellings', 'city', 'ownercitylimit', 4, '1'),
	('dwellings', 'city', 'ownercitylimit', 5, '1'),
	('dwellings', 'city', 'ownercitylimit', 6, '1'),
	('dwellings', 'city', 'ownercitylimit', 7, '1'),;
	
DELETE FROM `module_objprefs` WHERE (`modulename` = 'dwshacks' AND `objtype` = 'city');
INSERT INTO `module_objprefs` (`modulename`, `objtype`, `setting`, `objid`, `value`)
VALUES
	('dwshacks', 'city', 'loclimitdwshacks', 7, '0'),
	('dwshacks', 'city', 'showdwshacks', 1, '1'),
	('dwshacks', 'city', 'showdwshacks', 3, '1'),
	('dwshacks', 'city', 'showdwshacks', 6, '1'),
	('dwshacks', 'city', 'showdwshacks', 7, '0'),
	('dwshacks', 'city', 'showFormTabIndex', 7, '1'),
	('dwshacks', 'city', 'userloclimitdwshacks', 7, '1');

DELETE FROM `module_objprefs` WHERE (`modulename` = 'citypropvalue' AND `objtype` = 'city');
INSERT INTO `module_objprefs` (`modulename`, `objtype`, `setting`, `objid`, `value`)
VALUES
	('citypropvalue', 'city', 'gemchange', 1, '2'),
	('citypropvalue', 'city', 'gemchange', 3, '1.6'),
	('citypropvalue', 'city', 'gemchange', 4, '1.2'),
	('citypropvalue', 'city', 'gemchange', 5, '1.5'),
	('citypropvalue', 'city', 'gemchange', 6, '1.4'),
	('citypropvalue', 'city', 'goldchange', 1, '1.9'),
	('citypropvalue', 'city', 'goldchange', 3, '1.5'),
	('citypropvalue', 'city', 'goldchange', 4, '1.2'),
	('citypropvalue', 'city', 'goldchange', 5, '1.4'),
	('citypropvalue', 'city', 'goldchange', 6, '1.3');


DELETE FROM `module_objprefs` WHERE (`modulename` = 'dwcostwood');
INSERT INTO `module_objprefs` (`modulename`, `objtype`, `setting`, `objid`, `value`)
VALUES
	('dwcostwood', 'dwellings', 'woodspent', 1, '25'),
	('dwcostwood', 'dwellingtypes', 'woodcost', 1, '25');

DELETE FROM `module_objprefs` WHERE (`modulename` = 'upkeep');
INSERT INTO `module_objprefs` (`modulename`, `objtype`, `setting`, `objid`, `value`)
VALUES
	('upkeep', 'dwellings', 'exempt', 1, '0'),
	('upkeep', 'dwellings', 'upkeepdays', 1, '7'),
	('upkeep', 'dwellings', 'upkeepgems', 1, '2'),
	('upkeep', 'dwellings', 'upkeepgold', 1, '250'),
	('upkeep', 'dwellings', 'upkeepturns', 1, '5'),
	('upkeep', 'dwellingtypes', 'upkeepdays', 1, '14'),
	('upkeep', 'dwellingtypes', 'upkeepgems', 1, '2'),
	('upkeep', 'dwellingtypes', 'upkeepgemsgain', 1, '1'),
	('upkeep', 'dwellingtypes', 'upkeepgemsloss', 1, '5'),
	('upkeep', 'dwellingtypes', 'upkeepgold', 1, '250'),
	('upkeep', 'dwellingtypes', 'upkeepgoldgain', 1, '25'),
	('upkeep', 'dwellingtypes', 'upkeepgoldloss', 1, '350'),
	('upkeep', 'dwellingtypes', 'upkeepturns', 1, '5'),
	('upkeep', 'dwellingtypes', 'useupkeep', 1, '1');

DELETE FROM `module_objprefs` WHERE (`modulename` = 'coffer_ban');
INSERT INTO `module_objprefs` (`modulename`, `objtype`, `setting`, `objid`, `value`)
VALUES
	('coffer_ban', 'dwellings', 'banlist', 1, 'a:1:{i:3;s:1:\"3\";}'),
	('coffer_ban', 'dwellingtypes', 'allow-ban', 1, '1');
