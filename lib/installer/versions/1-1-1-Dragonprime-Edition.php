<?php

$creaturesTable = DB::prefix('creatures');
$companionsTable = DB::prefix('companions');
$settingsTable = DB::prefix('settings');
$moduleNavhooksTable = DB::prefix('module_navhooks');
$modulePagehooksTable = DB::prefix('module_pagehooks');
$moduleSettingdescTable = DB::prefix('module_settingdesc');
$modulePrefdescTable = DB::prefix('module_prefdesc');

$creaturefields = "creatureid,creaturename,creaturelevel,creatureweapon,creaturelose,creaturewin,creaturegold,creatureexp,creaturehealth,creatureattack,creaturedefense,creatureaiscript,createdby,forest,graveyard";

return [
	"INSERT INTO $settingsTable VALUES ('allowclans','1')",
	"INSERT INTO $settingsTable VALUES ('resurrectionturns','-6')",

	"DROP TABLE IF EXISTS $moduleNavhooksTable;",
	"DROP TABLE IF EXISTS $modulePagehooksTable;",
	"DROP TABLE IF EXISTS $moduleSettingdescTable;",
	"DROP TABLE IF EXISTS $modulePrefdescTable;",
	// "DROP TABLE IF EXISTS " . db_prefix("items") . ";", //-- This delete table from module inventory - Not want this

	"ALTER TABLE $creaturesTable DROP oldcreatureexp;",

	"INSERT INTO $settingsTable VALUES ('serverlanguages','en,English,fr,Français,dk,Danish,de,Deutsch,es,Español,it,Italian')",
	"INSERT INTO $creaturesTable ($creaturefields) VALUES (320, 'Gypsy Bandit', 14, 'Gemmed Dagger', 'You''re dead, he''s free to take what he will.', 'That will put an end to his thieving days.', 499, 172, 145, 27, 20, 'global \$badguy, \$session;\r\n\r\nif (!isset(\$badguy[\\'spellpoints\\'])) {\r\n	\$badguy[\\'spellpoints\\'] = 1;\r\n}\r\n\r\n\$gold = round(\$session[\\'user\\'][\\'gold\\'] * 0.2);\r\nif (e_rand(0,7) == 0 && \$gold > 200 && \$badguy[\\'spellpoints\\'] == 1) {\r\n	rawoutput(\"<br /><b><span style=''color: white''>The pickpocket takes <span style=''color: gold''>\$gold gold</span>!</span></b><br /><br />\");\r\n	\$session[\\'user\\'][\\'gold\\'] -= \$gold;\r\n	\$badguy[\\'creaturegold\\'] += \$gold;\r\n	\$badguy[\\'spellpoints\\']--;\r\n}', 'Talisman', 1, 0)",

	"INSERT INTO $companionsTable (`companionid`, `name`, `category`, `description`, `attack`, `attackperlevel`, `defense`, `defenseperlevel`, `maxhitpoints`, `maxhitpointsperlevel`, `abilities`, `cannotdie`, `cannotbehealed`, `companionlocation`, `companionactive`, `companioncostdks`, `companioncostgems`, `companioncostgold`, `jointext`, `dyingtext`, `allowinshades`, `allowinpvp`, `allowintrain`) VALUES (1, 'Mortimer teh javelin man', 'Knight', 'A rough and ready warrior.  Beneath his hardened exterior, one can detect a man of strong honour.', 5, 2, 1, 2, 20, 20, 'a:4:{s:5:\"fight\";s:1:\"1\";s:4:\"heal\";s:1:\"0\";s:5:\"magic\";s:1:\"0\";s:6:\"defend\";b:0;}', 0, 0, '".getsetting("villagename", LOCATION_FIELDS)."', 1, 0, 4, 573, '`^Greetings unto thee, my friend.  Let us go forth and conquer the evils of this world together!', '`4Argggggh!  I am slain!  Shuffling off my mortal coil.  Fare thee well, my friends.', 1, 0, 0)",
	"INSERT INTO $companionsTable (`companionid`, `name`, `category`, `description`, `attack`, `attackperlevel`, `defense`, `defenseperlevel`, `maxhitpoints`, `maxhitpointsperlevel`, `abilities`, `cannotdie`, `cannotbehealed`, `companionlocation`, `companionactive`, `companioncostdks`, `companioncostgems`, `companioncostgold`, `jointext`, `dyingtext`, `allowinshades`, `allowinpvp`, `allowintrain`) VALUES (2, 'Florenz', 'Healer', 'With a slight build, Florenz is better suited as a healer than a fighter.', 1, 1, 5, 5, 15, 10, 'a:4:{s:4:\"heal\";s:1:\"2\";s:5:\"magic\";s:1:\"0\";s:5:\"fight\";b:0;s:6:\"defend\";b:0;}', 0, 0, '".getsetting("villagename", LOCATION_FIELDS)."', 1, 0, 3, 1000, 'Thank ye for thy faith in my skills.  I shall endeavour to keep ye away from Ramius'' claws.', 'O Discordia!', 1, 0, 0)",
];