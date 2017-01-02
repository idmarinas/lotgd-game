<?php

$deathmessagesTable = DB::prefix('deathmessages');

return [
		"INSERT INTO $deathmessagesTable VALUES (0,'`4{goodguyname}`4 has been slain {where} by `4{badguy}`4.',1,0,1,'Nightborn')",
		"INSERT INTO $deathmessagesTable VALUES (0,'`4{goodguyname}`4 has been defeated {where} by `4{badguy}`4.',0,1,1,'Nightborn')",
];