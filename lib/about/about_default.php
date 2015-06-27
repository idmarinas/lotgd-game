<?php
$order=array("2","3","1"); //arbitrary, this order the following hooks and whatnot

foreach ($order as $current_rank) {
	switch($current_rank){
	case "3":
		//notes for the +nb Editions
		output("`\$For the original 'Legend of the Green Dragon' #About information check below.`n`n");
		output("`l+nb Edition");
		output("`nBy Oliver Brendel (<a href='http://nb-core.org'>NB Core</a>) `n`n",true);
		output("This version is a forked version of the pre-1.1.1 DP Edition core shortly after I stopped developing for this specific version.`n`nThe reasons were mainly that the path the leading people there wanted to go was not my path at all. Hence this version reflects mostly what *I* wanted to see different *without* having to rewrite the core using modules... and still be stuck with the old structures. Also I don't have to ask for changes with the high probability of getting no answer or a negative one.`n`n");
		output("If you use this core, you need to be aware of the goals of my optimizations:`n`n<ul>",true);
		output("<li>PHP5 is a minimum requirement</li>",true);
		output("<li>MySQL5 is a minimum requirement</li>",true);
		output("<li>Features should be done focussed on avoiding high-load and focussing on many (MMORG) users</li>",true);
		output("<li>Roleplay like in D&D should be possible</li>",true);
		output("</ul>`n`n",true);
		output("For the download of this version please go to <a href='http://nb-core.org'>http://nb-core.org</a> where the latest development version (daily snapshots) and stable versions are hosted.`n`n",true);
		output("`n`nI do not ship modules with it, most modules from 1.x.x DP Editions and previous will work. However there is no guarantee... test them. And be aware that many unbalance gameplay as they give out too much EXP/Buffs/Atk+Def stats.");
		output_notl("`n`n");

		break;
	case "2":
		/* NOTICE
		 * NOTICE Server admins may put their own information here,
		 * NOTICE please leave the main about body untouched.
		 * NOTICE
		 */
		rawoutput("<hr>");
		$imprint = getsetting("impressum", ""); //yes, it's named impressum after the German word. We have to thank somebody for that - w00t
		if ($imprint > "") {
			require_once("lib/nltoappon.php");
			output_notl("%s", nltoappon($imprint),true); //yes, HTML possible
		}
		rawoutput("<br/><br/>");
		break;
	case "1":
		/* NOTICE
		 * NOTICE This section may not be modified, please modify the
		 * NOTICE Server Specific section above.
		 * NOTICE
		 */
		output("`@Legend of the Green Dragon Engine`nBy Eric Stevens & JT Traub`n`n");
		output("`cLoGD version ");
		output_notl("$logd_version`c");
		/*
		 * This section may not be modified, please modify the Server
		 * Specific section above.
		 */
		output("MightyE tells you, \"`2Legend of the Green Dragon is a remake of and homage to the classic BBS Door game, Legend of the Red Dragon (aka LoRD) by <a href='http://www.rtsoft.com' target='_blank'>Seth Able Robinson</a>.`@\"", true);
		output("`n`n`@\"`2LoRD is now owned by Gameport (<a href='http://www.gameport.com/bbs/lord.html' target='_blank'>http://www.gameport.com/bbs/lord.html</a>), and they retain exclusive rights to the LoRD name and game. ", true);
		output("That's why all content in Legend of the Green Dragon is new, with only a very few nods to the original game, such as the buxom barmaid, Violet, and the handsome bard, Seth.`@\"`n`n");
		/*
		 * This section may not be modified, please modify the Server
		 * Specific section above.
		 */
		output("`@\"`2Although serious effort was made to preserve the original feel of the game, numerous departures were taken from the original game to enhance playability, and to adapt it to the web.`@\"`n`n");
		/*
		 * This section may not be modified, please modify the Server
		 * Specific section above.
		 */
		output("`@\"`2LoGD (after version 0.9.7) is released under a <a href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank'>Creative Commons License</a>, which essentially means that the source code to the game, and all derivatives of the game must remain open and available upon request.", true);
		output("Version 0.9.7 and before are still available under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GNU General Public License</a> though 0.9.7 will be the last release under that license.", true);
		output("To use any of the new features requires using the 1.0.0 code.  You may explicitly not place code from versions after 0.9.7 into 0.9.7 and release the combined derivative work under the GPL.`@\"`n`n", true);
		/*
		 * This section may not be modified, please modify the Server
		 * Specific section above.
		 */
		output("`@\"`2You may download the latest official version of LoGD at <a href='http://dragonprime.net/' target='_blank'>DragonPrime</a>  and you can play the Classic version at <a href='http://lotgd.net/'>http://lotgd.net</a>.`@\"`n`n",true);
		//output("`@\"`2The most recent *UNSTABLE* pre-release snapshot is available from <a href='http://dragonprime.net/users/Kendaer/' target='_blank'>http://dragonprime.net/users/Kendaer/</a>.", true);
		output("You should attempt to use this code only if you are comfortable with PHP and MySQL and willing to manually keep your code up to date.`@\"`n`n");
		/*
		 * This section may not be modified, please modify the Server
		 * Specific section above.
		 */
		output("`@\"`2Additionally, there is an active modder community located at <a href='http://dragonprime.net' target='_blank'>DragonPrime</a> which may help you find additional features which you may wish to add to your game.", true);
		output("For these additional features you will find active support within the DragonPrime community.`@\"`n`n");
		/*
		 * This section may not be modified, please modify the Server
		 * Specific section above.
		 */
		output("`@\"`2LoGD is programmed in PHP with a MySQL backend.");
		output("It is known to run on Windows and Linux with appropriate setups.");
		output("The core code has been actively written by Eric Stevens and JT Traub, with some pieces by other authors (denoted in the source at these locations), and the code has been released under a <a href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank'>Creative Commons License</a>.", true);
		output("Users of the source are bound to the terms therein.`n",true);
		output("The DragonPrime Development Team took over responsibility for code development on January 1<sup>st</sup>, 2006 and continues to maintain and add to features of the core code.`@\"`n`n",true);
		/*
		 * This section may not be modified, please modify the Server
		 * Specific section above.
		 */
		output("`@\"`2Users of the source are free to view and modify the source, but original copyright information, and original text from the about page must be preserved, though they may be added to.`@\"`n`n");
		output("`@\"`2We hope you enjoy the game!`@\"");
		/*
		 * This section may not be modified, please modify the Server
		 * Specific section above.
		 */
		break;
	}
	rawoutput("<hr>");
}
addnav("About LoGD");
addnav("Game Setup Info","about.php?op=setup");
addnav("Module Info","about.php?op=listmodules");
addnav("License Info", "about.php?op=license");
modulehook("about");
?>
