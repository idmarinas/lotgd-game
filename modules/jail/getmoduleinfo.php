<?php
	$info = array
	(
		"name"		=> "The Jail",
		"author"		=> "<a href='http://www.sixf00t4.com' target=_new>`^Sixf00t4</a>, 
					<a href=http://www.niksolo.com>`^Niksolo</a>, Lonny, and 
					<a href='http://www.rpgee.com' target=_new>`&RPGee.com",
		"version"		=> "20061231",
		"category"		=> "Jail",
		"download"		=> "http://www.rpgee.com/lotgd/jail.zip",  
		"description"	=> "Jail system and court system for the evil-doers.",
		"vertxtloc"		=> "http://www.rpgee.com/lotgd/",
		"requires"		=> array
		(
			"alignment"	=> "1.0|By Lonnyl, available on DragonPrime",
		),
		"settings"	=> array
		(
			"Jail Settings, title",
			"oneloc"		=> "Does the jail only show in one village?, bool|0",
			"jailloc"		=> "`iif yes`i Where does the jail appear?, location|".getsetting("villagename", LOCATION_FIELDS),
			"sheriffname"	=> "Name of sheriff?, text|Andy Griffith",            
			"bond"		=> "Bond price per DK?, int|3000",
			"maxbond"		=> "Max bond price?, int|50000",
			"baillvl"		=> "Bail per level?, int|3000",            
			"baildk"		=> "Bail per dk?, int|1",
			"bailevil"		=> "Percent of evil removed on bail or bond?, int|1",
			"maxtowncries"	=> "How many towncries per newday?, int|1",
			"evilremoved"	=> "Percent of evil removed on new day release?, int|10",
			"usewanted"		=> "Use wanted status for arrest?, bool|1",   
			"useevil"		=> "Use evil for arrest?, bool|1", 
			"minevil"		=> "Minimum amount of evil to be stuck in jail?, int|20",
			"showforum"		=> "Show forum in jail?, bool|1",

//RPGee.com - added in settings to keep players in the jail longer than one day.
			"Jail Time, title",
				"moredays"	=> "Jail holds prisoner for more than 1 day?, bool|0",
				"manydays"	=> "How many days do they stay in?, int|3",
				"runonce"	=> "Decrease days remaining only on server generated new day?, bool|1",
//END RPGee.com
 
			"Courthouse settings, title",
			"eventid"	=> "Account number of village events NPC, int|0",            
			"turnwit"	=> "How many turns to be a witness, int|3", 
			"turnbar"	=> "How many turns to be a barrister, int|5",
			"bardk"	=> "How many DKs above jailed to be a barrister, int|3",            
			"minlvl"	=> "Minimum level to participate in trials, int|4",
		),
		"prefs"	=> array
		(
			"Jail preferences, title",
			"injail"		=> "Is player in jail?, bool|0",
			"wantedlevel"	=> "What is players wanted level?, int|0",
			"playerloc"		=> "Where did they log off?, viewonly",
			"village"		=> "Village of the jail they're in|",

//RPGee.com - added in prefs to keep players in the jail longer than one day.
			"daysin" 		=> "How many days until released?, int|0",
//END RPGee.com

			"Court preferences, title",
			"barrister"			=> "ID of barrister,int|0",            
			"wenttocourt"		=> "Went to court today?,bool|0",
			"witness1"			=> "ID of first witness,int|0", 
			"witness2"			=> "ID of second witness,int|0", 
			"suicideattempts"		=> "Suicide attempts?,int|0",
			"towncries"			=> "How many towncries left?,int|1",
			),	            
		);
?>