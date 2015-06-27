<?php 
$userinfo = array(
	"Account info,title",
	"acctid"=>"User id,viewonly",
	"login"=>"Login",
	"newpassword"=>"New Password",
	"emailaddress"=>"Email Address",
	"locked"=>"Account Locked,bool",
	"banoverride"=>"Override Bans for this account,bool",
	"referer"=>"ID of player who referred this player," .
		(($session['user']['superuser'] & SU_EDIT_DONATIONS) ? "int" : "viewonly"),
	"refererawarded"=>"Has the referring player been awarded points,viewonly",

	"Basic user info,title",
	"name"=>"Current Display Name (composita),viewhiddenonly",
	"playername"=>"Character Name (Do NOT include ANY title information)",
	"title"=>"Dragonkill Title (prepended to name if Custom Title unset)" . (getsetting("edittitles",1) ? "" : ",hidden"),
	"ctitle"=>"Custom Title (prepended to name if set)",
	"sex"=>"Sex,enum,0,Male,1,Female",
	"age"=>"Days since level 1,int",
	"dragonkills"=>"How many times has slain the dragon,int",
	"dragonage"=>"How old when last killed dragon,int",
	"bestdragonage"=>"Youngest days when killed dragon,int",
	"pk"=>"Has user attacked in pvp?,bool",
	"bio"=>"Bio",

	"Stats,title",
	"level"=>"Level,int",
	"race"=>"Race,enumpretrans,$racesenum",
	"experience"=>"Experience,int",
	"hitpoints"=>"Current Hitpoints,int",
	"maxhitpoints"=>"Max Hitpoints,int",
	"strength"=>"Strength,int",
	"dexterity"=>"Dexterity,int",
	"intelligence"=>"Intelligence,int",
	"constitution"=>"Constitution,int",
	"wisdom"=>"Wisdom,int",
	"charm"=>"Charm,int",
	"attack"=>"Bonus Attack (includes weapon damage),int",
	"defense"=>"Bonus Defense (includes armor defense),int",
	"totalattack"=>"Total Attack (Composita),viewonly",
	"totaldefense"=>"Total Defense (Composita),viewonly",
	
	"More Stats,title",
	"turns"=>"Turns left,int",
	"playerfights"=>"Playerfights left,int",
	"spirits"=>"Spirits (display only),enum,-6,Resurrected,-2,Very Low,-1,Low,0,Normal,1,High,2,Very High",
	"resurrections"=>"Resurrections,int",
	"location"=>"Where is the user currently",

	"Specialty,title",
	"specialty"=>"Specialty,enumpretrans,". $enum,

	"Grave Fights,title",
	"deathpower"=>array("Favor with %s`0,int", getsetting("deathoverlord", '`$Ramius')),
	"gravefights"=>"Grave fights left,int",
	"soulpoints"=>"Soulpoints (HP while dead),int",

	"Gear,title",
	"gems"=>"Gems,int",
	"gold"=>"Gold in hand,int",
	"goldinbank"=>"Gold in bank,int",
	"transferredtoday"=>"Number of transfers today,int",
	"amountouttoday"=>"Total value of transfers from player today,int",
	"weapon"=>"Weapon Name",
	"weapondmg"=>"Damage of weapon,int",
	"weaponvalue"=>"Purchase cost of weapon,int",
	"armor"=>"Armor Name",
	"armordef"=>"Armor defense,int",
	"armorvalue"=>"Purchase cost of armor,int",

	"Special,title",
	"seendragon"=>"Saw dragon today,bool",
	"seenmaster"=>"Seen master,bool",
	"hashorse"=>"Mount,enumpretrans,".$mounts,
	"fedmount"=>"Fed mount today,bool",
	"boughtroomtoday"=>"Bought a room today,bool",
	"marriedto"=>"Is married to the player with AcctID," .
		(($session['user']['superuser'] & SU_MEGAUSER) ? "int" : "viewonly"),

	"Clan Info,title",
	"clanid"=>"Clan,enumpretrans,0,".translate_inline("None"),
	"clanrank"=>"Clan Rank,floatrange,0,31,1",
	"clanjoindate"=>"Clan Join Date",

	"Superuser Flags,title",
	"superuser"=>"Superuser Permissions".
	    "<br/><i>For the most part you can only set flags that you yourself possess;".
		"<br/>if you try to set one that you don't have; it won't stick.</i>,".
		"bitfield,".
		($session['user']['superuser'] | SU_ANYONE_CAN_SET |
		  ($session['user']['superuser']&SU_MEGAUSER ? 0xFFFFFFFF : 0)).",".
		SU_MEGAUSER.        ",MEGA USER (enable all permissions)* <i>(this applies to any future flags as well)</i>".
		"<br/><br/><b>Editors</b>,".
		SU_EDIT_CONFIG.     ",Edit Game Configurations*,".
		SU_EDIT_USERS.      ",Edit Users*,".
		SU_IS_BANMASTER.    ",Edit Bans,".
		SU_EDIT_MOUNTS.     ",Edit Mounts,".
		SU_EDIT_CREATURES.  ",Edit Creatures & Taunts,".
		SU_EDIT_EQUIPMENT.  ",Edit Armor & Weapons,".
		SU_EDIT_RIDDLES.    ",Edit Riddles,".
		SU_MANAGE_MODULES.  ",Manage Modules".
		"<br/><br/><b>Customer Service</b>,".
		SU_IS_GAMEMASTER.   ",Can post comments as gamemaster,".
		SU_EDIT_PETITIONS.  ",Handle Petitions,".
		SU_EDIT_COMMENTS.   ",Moderate Comments,".
		SU_MODERATE_CLANS.  ",Moderate Clan Commentary,".
		SU_AUDIT_MODERATION.",Audit Moderated Comments,".
        SU_OVERRIDE_YOM_WARNING.",Do NOT display YOM warning for this person,".
		SU_POST_MOTD.       ",Post MoTD's".
		"<br/><br/><b>Donations</b>,".
		SU_EDIT_DONATIONS.  ",Manage Donations*,".
		SU_EDIT_PAYLOG.     ",Manage Payment Log".
		"<br/><br/><b>Game Development</b>,".
		SU_INFINITE_DAYS.   ",Infinite Days*,".
		SU_DEVELOPER.       ",Game Developer* (super powers),".
		SU_IS_TRANSLATOR.   ",Enable Translation Tool,".
		SU_DEBUG_OUTPUT.    ",Debug Output,".
		SU_SHOW_PHPNOTICE.  ",See PHP Notices in debug output,".
		SU_RAW_SQL.         ",Execute Raw SQL*,".
		SU_VIEW_SOURCE.     ",View source code,".
		SU_GIVE_GROTTO.     ",Grotto access (only if not granted implicitly in another permission),".
		SU_NEVER_EXPIRE.    ",Account never expires".
		"<br/><br/>* Granting any of these options will hide the user from the HoF",

	"translatorlanguages"=>"Allowed languages to translate (use the 2 chars given in the enum field in the game settings and seperate by comma)",

	"Misc Info,title",
	"regdate"=>"Registered on,viewonly",
	"beta"=>"Willing to participate in beta,bool",
	"laston"=>"Last On (local time),viewonly",
	"lasthit"=>"Last New Day (time is in GMT not local),viewonly",
	"lastmotd"=>"Last MOTD date (local time),viewonly",
	"lastip"=>"Last IP,viewonly",
	"uniqueid"=>"Unique ID,viewonly",
	"gentime"=>"Sum of page gen times,viewonly",
	"gentimecount"=>"Page hits,viewonly",
	"allowednavs"=>"Allowed Navs,viewonly",
	"dragonpointssummary"=>"Dragon points spent (summary),viewonly",
	"dragonpoints"=>"Dragon points spent,viewonly",
	"bufflist"=>"Buff List,viewonly",
	"prefs"=>"Preferences,viewonly",
	"donationconfig"=>"Donation buys,viewonly",
	"Battle Info,title",
	"companions"=>"List of companions,viewonly",
	"badguy"=>"Last Badguy,viewonly"
);
