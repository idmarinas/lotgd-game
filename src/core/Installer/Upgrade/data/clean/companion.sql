/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `companions` DISABLE KEYS */;
INSERT INTO `companions` (`companionid`, `name`, `category`, `description`, `attack`, `attackperlevel`, `defense`, `defenseperlevel`, `maxhitpoints`, `maxhitpointsperlevel`, `abilities`, `cannotdie`, `cannotbehealed`, `companionlocation`, `companionactive`, `companioncostdks`, `companioncostgems`, `companioncostgold`, `jointext`, `dyingtext`, `allowinshades`, `allowinpvp`, `allowintrain`) VALUES
	(1, 'Mortimer teh javelin man', 'Knight', 'A rough and ready warrior.  Beneath his hardened exterior, one can detect a man of strong honour.', 5, 2, 1, 2, 20, 20, 'a:4:{s:5:"fight";i:1;s:4:"heal";i:0;s:5:"magic";i:0;s:6:"defend";i:0;}', 0, 0, 'Degolburg', 1, 0, 4, 573, '`^Greetings unto thee, my friend.  Let us go forth and conquer the evils of this world together!', '`4Argggggh!  I am slain!  Shuffling off my mortal coil.  Fare thee well, my friends.', 1, 0, 0),
	(2, 'Florenz', 'Healer', 'With a slight build, Florenz is better suited as a healer than a fighter.', 1, 1, 5, 5, 15, 10, 'a:4:{s:4:"heal";i:2;s:5:"magic";i:0;s:5:"fight";i:0;s:6:"defend";i:0;}', 0, 0, 'Degolburg', 1, 0, 3, 1000, 'Thank ye for thy faith in my skills.  I shall endeavour to keep ye away from Ramius\' claws.', 'O Discordia!', 1, 0, 0),
	(3, 'Grizzly Bear', 'Wild Beasts', 'You look at the beast knowing that this Grizzly Bear will provide an effective block against attack with its long curved claws and massive body of silver-tipped fur.', 1, 2, 5, 2, 25, 25, 'a:4:{s:5:"fight";i:0;s:4:"heal";i:0;s:5:"magic";i:0;s:6:"defend";i:1;}', 0, 0, 'Qexelcrag', 1, 0, 4, 600, 'You hear a low, deep belly growl coming from a shadowed corner of the Bestiarium.  Curious you walk over to investigate your purchase. As you approach a large form shuffles on all four legs towards the front of its hewn rock enclosure.`n`nThe hunched shoulders of the largest bear you have ever seen ripple as its front haunches push against the ground causing it to stand on its hind legs.  It makes another low growl before dropping back on all four legs to follow you on your adventure.', 'The grizzly gets scared by the multitude of blows and hits he has to take and flees into the forest.', 1, 0, 0);
/*!40000 ALTER TABLE `companions` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
