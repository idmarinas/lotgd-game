/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `titles` DISABLE KEYS */;
INSERT INTO `titles` (`titleid`, `dk`, `ref`, `male`, `female`) VALUES
	(1, 0, '', 'Farmboy', 'Farmgirl'),
	(2, 1, '', 'Page', 'Page'),
	(3, 2, '', 'Squire', 'Squire'),
	(4, 3, '', 'Gladiator', 'Gladiatrix'),
	(5, 4, '', 'Legionnaire', 'Legioness'),
	(6, 5, '', 'Centurion', 'Centurioness'),
	(7, 6, '', 'Sir', 'Madam'),
	(8, 7, '', 'Reeve', 'Reeve'),
	(9, 8, '', 'Steward', 'Steward'),
	(10, 9, '', 'Mayor', 'Mayoress'),
	(11, 10, '', 'Baron', 'Baroness'),
	(12, 11, '', 'Count', 'Countess'),
	(13, 12, '', 'Viscount', 'Viscountess'),
	(14, 13, '', 'Marquis', 'Marchioness'),
	(15, 14, '', 'Chancellor', 'Chancelloress'),
	(16, 15, '', 'Prince', 'Princess'),
	(17, 16, '', 'King', 'Queen'),
	(18, 17, '', 'Emperor', 'Empress'),
	(19, 18, '', 'Angel', 'Angel'),
	(20, 19, '', 'Archangel', 'Archangel'),
	(21, 20, '', 'Principality', 'Principality'),
	(22, 21, '', 'Power', 'Power'),
	(23, 22, '', 'Virtue', 'Virtue'),
	(24, 23, '', 'Dominion', 'Dominion'),
	(25, 24, '', 'Throne', 'Throne'),
	(26, 25, '', 'Cherub', 'Cherub'),
	(27, 26, '', 'Seraph', 'Seraph'),
	(28, 27, '', 'Demigod', 'Demigoddess'),
	(29, 28, '', 'Titan', 'Titaness'),
	(30, 29, '', 'Archtitan', 'Archtitaness'),
	(31, 30, '', 'Undergod', 'Undergoddess'),
	(32, 31, '', 'God', 'Goddess');
/*!40000 ALTER TABLE `titles` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
