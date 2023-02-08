/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `cron_job` DISABLE KEYS */;
REPLACE INTO `cron_job` (`id`, `name`, `command`, `schedule`, `description`, `enabled`) VALUES
	(1, 'lotgd_newday', 'lotgd:cron:game:newday', '0 0,6,12,18 * * *', 'Generate a new game day', 1),
	(2, 'lotgd_avatar_clean', 'lotgd:cron:avatar:clean', '*/30 * * * *', 'Clean expire avatars and backup it.', 1),
	(3, 'lotgd_content_clean', 'lotgd:cron:content:clean', '* * */7 * *', 'Clean old content and comments of data base.', 1),
	(4, 'lotgd_petition_clean', 'lotgd:cron:game:petition:clean', '0 * * * *', 'Remove old closed petitions.', 1);
/*!40000 ALTER TABLE `cron_job` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
