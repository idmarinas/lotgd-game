/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `cronjob` DISABLE KEYS */;
INSERT INTO `cronjob` (`name`, `command`, `schedule`, `mailer`, `maxRuntime`, `smtpHost`, `smtpPort`, `smtpUsername`, `smtpPassword`, `smtpSender`, `smtpSenderName`, `smtpSecurity`, `runAs`, `environment`, `runOnHost`, `output`, `dateFormat`, `enabled`, `haltDir`, `debug`, `id`) VALUES
	('lotgdCharcleanup', 'cronjob/charcleanup', '*/30 * * * *', 'sendmail', NULL, NULL, NULL, NULL, NULL, 'jobby@localhost', 'Jobby', NULL, NULL, NULL, NULL, NULL, 'Y-m-d H:i:s', 1, NULL, 0, 2),
	('lotgdCommentcleanup', 'cronjob/commentcleanup', '* * */7 * *', 'sendmail', NULL, NULL, NULL, NULL, NULL, 'jobby@localhost', 'Jobby', NULL, NULL, NULL, NULL, NULL, 'Y-m-d H:i:s', 1, NULL, 0, 3),
	('lotgdDbcleanup', 'cronjob/dbcleanup', '0 4 * * *', 'sendmail', NULL, NULL, NULL, NULL, NULL, 'jobby@localhost', 'Jobby', NULL, NULL, NULL, NULL, NULL, 'Y-m-d H:i:s', 0, NULL, 0, 4),
	('lotgdLogoutAccounts', 'cronjob/logout-accts', '*/15 * * * *', 'sendmail', NULL, NULL, NULL, NULL, NULL, 'jobby@localhost', 'Jobby', NULL, NULL, NULL, NULL, NULL, 'Y-m-d H:i:s', 1, NULL, 0, 5),
	('lotgdNewday', 'cronjob/newday', '0 0,6,12,18 * * *', 'sendmail', NULL, NULL, NULL, NULL, NULL, 'jobby@localhost', 'Jobby', NULL, NULL, NULL, NULL, NULL, 'Y-m-d H:i:s', 1, NULL, 0, 6);
/*!40000 ALTER TABLE `cronjob` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
