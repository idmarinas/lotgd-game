/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`setting`, `value`) VALUES
	('allowclans', '1'),
	('allowcreation', '1'),
	('allowspecialswitch', '1'),
	('autofight', '0'),
	('automaster', '1'),
	('bankername', '`@Elessa`0'),
	('bard', '`^Seth`0'),
	('barkeep', '`tCedrik`0'),
	('barmaid', '`%Violet`0'),
	('beta', '0'),
	('betaperplayer', '1'),
	('cachetranslations', '0'),
	('charset', 'UTF-8'),
	('clanregistrar', '`%Karissa`0'),
	('collecttexts', '0'),
	('companionslevelup', '1'),
	('daysperday', '4'),
	('deathoverlord', '`$Ramius`0'),
	('debug', '0'),
	('defaultlanguage', 'en'),
	('defaultskin', 'jade.html'),
	('disablebonuses', '1'),
	('displaymasternews', '1'),
	('dpointspercurrencyunit', '100'),
	('dropmingold', '0'),
	('edittitles', '1'),
	('enablecompanions', '1'),
	('enabletranslation', '1'),
	('exp-array', '100,400,1002,1912,3140,4707,6641,8985,11795,15143,19121,23840,29437,36071,43930'),
	('expirecontent', '180'),
	('expiredebuglog', '18'),
	('expirefaillog', '15'),
	('expiregamelog', '30'),
	('expirenewacct', '10'),
	('expireoldacct', '45'),
	('expiretrashacct', '1'),
	('fightsforinterest', '4'),
	('forestchance', '15'),
	('forestcreaturebar', '0'),
	('forestgemchance', '25'),
	('forestpowerattackchance', '10'),
	('forestpowerattackmulti', '3'),
	('fullmaintenance', '0'),
	('gameoffsetseconds', '0'),
	('gametime', 'g:i a'),
	('gravefightsperday', '10'),
	('homecurtime', '1'),
	('homenewdaytime', '1'),
	('homenewestplayer', '1'),
	('homeskinselect', '1'),
	('inboxlimit', '50'),
    ('maxonline', '0'),
    ('suicidedk', '10'),
    ('companionsallowed', '0'),
	('servername', 'Legend of the Green Dragon'),
	('innname', 'The Boar\'s Head Inn'),
	('instantexp', '0'),
	('logdnet', '0'),
	('loginbanner', '*BETA* This is a BETA of this website, things are likely to change now and again, as it is under active development *BETA*'),
	('LOGINTIMEOUT', '900'),
	('maintenance', '0'),
	('maxattacks', '4'),
	('maxinterest', '10'),
	('maxlevel', '15'),
	('maxlistsize', '100'),
	('mininterest', '1'),
	('moneydecimalpoint', '.'),
	('moneythousandssep', ','),
	('motditems', '5'),
	('multicategory', '0'),
	('multifightdk', '10'),
	('multimaster', '1'),
	('newdaycron', '0'),
	('notifydaysbeforedeletion', '5'),
	('oldmail', '14'),
	('paypalcurrency', 'USD'),
	('paypalemail', ''),
	('paypaltext', 'Legend of the Green Dragon DP Donation from '),
	('permacollect', '0'),
	('petition_types', 'petition.types.general,petition.types.report.bug,petition.types.suggestion,petition.types.comment,petition.types.other'),
	('pvp', '1'),
	('pvpday', '3'),
	('requireemail', '0'),
	('resurrectionturns', '-6'),
	('selfdelete', '0'),
	('serverlanguages', 'en,fr,de,es,it'),
	('specialtybonus', '1'),
	('suicide', '0'),
	('superuseryommessage', 'Asking an admin for gems, gold, weapons, armor, or anything else which you have not earned will not be honored.  If you are experiencing problems with the game, please use the \'Petition for Help\' link instead of contacting an admin directly.'),
	('systemload_lastcheck', '0'),
	('systemload_lastload', '0'),
	('tl_maxallowed', '0'),
	('turns', '10'),
	('villagechance', '0'),
	('villagename', 'Degolburg');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
