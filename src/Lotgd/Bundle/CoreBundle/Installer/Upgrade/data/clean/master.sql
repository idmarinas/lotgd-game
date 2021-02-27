/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `masters` DISABLE KEYS */;
INSERT INTO `masters` (`creatureid`, `creaturename`, `creaturelevel`, `creatureweapon`, `creaturelose`, `creaturewin`) VALUES
	(1, 'Mireraband', 1, 'Small Dagger', 'Well done {goodguy}`&, I should have guessed you\'d grown some.', 'As I thought, {goodguy}`^, your skills are no match for my own!'),
	(2, 'Fie', 2, 'Short Sword', 'Well done {goodguy}`&, you really know how to use your {goodguyweapon}.', 'You should have known you were no match for my {badguyweapon}'),
	(3, 'Glynyc', 3, 'Hugely Spiked Mace', 'Aah, defeated by the likes of you!  Next thing you know, Mireraband will be hunting me down!', 'Haha, maybe you should go back to Mireraband\'s class.'),
	(4, 'Guth', 4, 'Spiked Club', 'Ha!  Hahaha, excellent fight {goodguy}`&!  Haven\'t had a battle like that since I was in the RAF!', 'Back in the RAF, we\'d have eaten the likes of you alive!  Go work on your skills some old boy!'),
	(5, 'Unélith', 5, 'Thought Control', 'Your mind is greater than mine.  I concede defeat.', 'Your mental powers are lacking.  Meditate on this failure and perhaps some day you will defeat me.'),
	(6, 'Adwares', 6, 'Dwarven Battle Axe', 'Ach!  Y\' do hold yer {goodguyweapon} with skeel!', 'Har!  Y\' do be needin moore praktise y\' wee cub!'),
	(7, 'Gerrard', 7, 'Battle Bow', 'Hmm, mayhaps I underestimated you.', 'As I thought.'),
	(8, 'Ceiloth', 8, 'Orkos Broadsword', 'Well done {goodguy}`&, I can see that great things lie in the future for you!', 'You are becoming powerful, but not yet that powerful.'),
	(9, 'Dwiredan', 9, 'Twin Swords', 'Perhaps I should have considered your {goodguyweapon}...', 'Perhaps you\'ll reconsider my twin swords before you try that again?'),
	(10, 'Sensei Noetha', 10, 'Martial Arts Skills', 'Your style was superior, your form greater.  I bow to you.', 'Learn to adapt your style, and you shall prevail.'),
	(11, 'Celith', 11, 'Throwing Halos', 'Wow, how did you dodge all those halos?', 'Watch out for that last halo, it\'s coming back this way!'),
	(12, 'Gadriel the Elven Ranger', 12, 'Elven Long Bow', 'I can accept that you defeated me, because after all elves are immortal while you are not, so the victory will be mine.', 'Do not forget that elves are immortal.  Mortals will likely never defeat one of the fey.'),
	(13, 'Adoawyr', 13, 'Gargantuan Broad Sword', 'If I could have picked up this sword, I probably would have done better!', 'Haha, I couldn\'t even pick the sword UP and I still won!'),
	(14, 'Yoresh', 14, 'Death Touch', 'Well, you evaded my touch.  I salute you!', 'Watch out for my touch next time!');
/*!40000 ALTER TABLE `masters` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
