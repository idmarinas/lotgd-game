<?php
//translator ready
//addnews ready
//mail ready
function get_all_tables()
{
return [
	'accounts'=>[
		'acctid'=>[
			'name'=>'acctid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment' //the unique account ID
		],
		'name'=>[
			'name'=>'name', 'type'=>'varchar(100)' //100 chars as UTF-8 take more space, control that yourself in the module or whatnot where you save name/etc
		],
		'playername'=>[
			'name'=>'playername', 'type'=>'varchar(40)' // this is the raw name of the player to modify, use the lib/names.php to do so, it does the work for you.
		],
		'sex'=>[
			'name'=>'sex', 'type'=>'tinyint(4) unsigned', 'default'=>'0' //use SEX_MALE, SEX_FEMALE constnats to check  this!
		],
		'strength'=>[
			'name'=>'strength', 'type'=>'smallint(4) unsigned', 'default'=>'10' //strength of the user
		],
		'dexterity'=>[
			'name'=>'dexterity', 'type'=>'smallint(4) unsigned', 'default'=>'10' //dexterity of the user
		],
		'intelligence'=>[
			'name'=>'intelligence', 'type'=>'smallint(4) unsigned', 'default'=>'10' //intelligence of the user
		],
		'constitution'=>[
			'name'=>'constitution', 'type'=>'smallint(4) unsigned', 'default'=>'10' //constitution of the user
		],
		'wisdom'=>[
			'name'=>'wisdom', 'type'=>'smallint(4) unsigned', 'default'=>'10' // wisdom of the user
		],
		//these are the main stats. buffed up attack or defense will be reset eventually =)
		'specialty'=>[
			'name'=>'specialty', 'type'=>'varchar(20)', //normally 2 chars are used only
		],
		'experience'=>[
			'name'=>'experience', 'type'=>'bigint(11) unsigned', 'default'=>'0' //the amount of experience
		],
		'gold'=>[
			'name'=>'gold', 'type'=>'int(11) unsigned', 'default'=>'0' //gold on hand
		],
		'weapon'=>[
			'name'=>'weapon', 'type'=>'varchar(50)', 'default'=>'Fists' //note the default value: not translated here, use at own discretion and remember to check after an update of the core game
		],
		'armor'=>[
			'name'=>'armor', 'type'=>'varchar(50)', 'default'=>'T-Shirt' //same here
		],
		'seenmaster'=>[
			'name'=>'seenmaster', 'type'=>'tinyint(4) unsigned', 'default'=>'0' //has he seen his master today?
		],
		'level'=>[
			'name'=>'level', 'type'=>'smallint(4) unsigned', 'default'=>'1' //what level is he? note: reduced to range 0-65535, that's enough.
		],
		'defense'=>[
			'name'=>'defense', 'type'=>'int(11) unsigned', 'default'=>'0' //defensive power the user has which is additional to the base calculated based on stats
		],
		'attack'=>[
			'name'=>'attack', 'type'=>'int(11) unsigned', 'default'=>'0' //offensive power which is additional to the base calculated based on stats
		],
		'alive'=>[
			'name'=>'alive', 'type'=>'tinyint(1) unsigned', 'default'=>'1' //is he alive? redundant as hitpoints<=0 is the exact same thing.
		],
		'goldinbank'=>[
			'name'=>'goldinbank', 'type'=>'int(11)', 'default'=>'0' //gold stored in the bank
		],
		'marriedto'=>[
			'name'=>'marriedto', 'type'=>'int(11) unsigned', 'default'=>'0' //married to? note: core does not allow you to marry players, you need a module that can do that
		],
		'spirits'=>[
			'name'=>'spirits', 'type'=>'int(4)', 'default'=>'0' //in what spirits are you? up from -128 till +127 (I don't remember how SQL calculates that], basically gives the + or - turns a day
		],
		'laston'=>[
			'name'=>'laston', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00' //last on
		],
		'hitpoints'=>[
			'name'=>'hitpoints', 'type'=>'int(11)', 'default'=>'10' //user hitpoints he currently has
		],
		'maxhitpoints'=>[
			'name'=>'maxhitpoints', 'type'=>'int(11) unsigned', 'default'=>'10' //maximum hitpoints he can have
		],
		'gems'=>[
			'name'=>'gems', 'type'=>'int(11) unsigned', 'default'=>'0' //number of gems he has on hand
		],
		'weaponvalue'=>[
			'name'=>'weaponvalue', 'type'=>'int(11) unsigned', 'default'=>'0' //price of his weapon
		],
		'armorvalue'=>[
			'name'=>'armorvalue', 'type'=>'int(11) unsigned', 'default'=>'0' //price of the armour
		],
		'location'=>[
			'name'=>'location', 'type'=>'varchar(50)', 'default'=>'Degolburg' //location he is in
		],
		'turns'=>[
			'name'=>'turns', 'type'=>'int(11) unsigned', 'default'=>'10' //amount of turns he has
		],
		'title'=>[
			'name'=>'title', 'type'=>'varchar(50)' //the dragonkilltitle the user has
		],
		'password'=>[
			'name'=>'password', 'type'=>'varchar(32)' //the password stored as MD5 hash
		],
		'badguy'=>[
			'name'=>'badguy', 'type'=>'text' //well, what guy(s) does he fight / did he fight last
		],
		'companions'=>[
			'name'=>'companions', 'type'=>'text' //what are his companions
		],
		'allowednavs'=>[
			'name'=>'allowednavs', 'type'=>'mediumtext' //what navs is he allowed to access (excluding anonymous ones
		],
		'loggedin'=>[
			'name'=>'loggedin', 'type'=>'tinyint(4) unsigned', 'default'=>'0' //is he currently logged in? (note to check for a timeout too!)
		],
		'resurrections'=>[
			'name'=>'resurrections', 'type'=>'int(11) unsigned', 'default'=>'0' // how often did he resurrect?
		],
		'superuser'=>[
			'name'=>'superuser', 'type'=>'int(11) unsigned', 'default'=>'1' // superuser flags are stored here
		],
		'weapondmg'=>[
			'name'=>'weapondmg', 'type'=>'int(11)', 'default'=>'0' //damage his weapons deals, already added to attack
		],
		'armordef'=>[
			'name'=>'armordef', 'type'=>'int(11)', 'default'=>'0' //defense of the armour, already added to defense
		],
		'age'=>[
			'name'=>'age', 'type'=>'int(11) unsigned', 'default'=>'0' //increases as it is the number of newdays he had since the last DK
		],
		'charm'=>[
			'name'=>'charm', 'type'=>'int(11) unsigned', 'default'=>'0' //amount of charm points he has
		],
		'specialinc'=>[
			'name'=>'specialinc', 'type'=>'varchar(50)' // used to tell the core a special needs to be executed like module:fairy which is used in the forest i.e.
		],
		'specialmisc'=>[
			'name'=>'specialmisc', 'type'=>'varchar(1000)' //put anything in you want, but it can be overwritten by modules that need it!
		],
		'login'=>[
			'name'=>'login', 'type'=>'varchar(50)' //login name ... might differ from playername!
		],
		'lastmotd'=>[
			'name'=>'lastmotd',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		], //explains itself
		'playerfights'=>[
			'name'=>'playerfights', 'type'=>'int(11) unsigned', 'default'=>'3' //number of PvP
		],
		'lasthit'=>[
			'name'=>'lasthit', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00' //last pagehit when?
		],
		'seendragon'=>[
			'name'=>'seendragon', 'type'=>'tinyint(4) unsigned', 'default'=>'0' //already seen the dragon today?
		],
		'dragonkills'=>[
			'name'=>'dragonkills', 'type'=>'int(11) unsigned', 'default'=>'0' //amount of dragonkills
		],
		'locked'=>[
			'name'=>'locked', 'type'=>'tinyint(4) unsigned', 'default'=>'0'
		],
		'restorepage'=>[
			'name'=>'restorepage', 'type'=>'varchar(128)', 'null'=>'1'
		],
		'hashorse'=>[
			'name'=>'hashorse', 'type'=>'tinyint(4) unsigned', 'default'=>'0'
		],
		'bufflist'=>[
			'name'=>'bufflist', 'type'=>'text'
		],
		'gentime'=>[
			'name'=>'gentime', 'type'=>'double unsigned', 'default'=>'0'
		],
		'gentimecount'=>[
			'name'=>'gentimecount', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'lastip'=>[
			'name'=>'lastip', 'type'=>'varchar(40)'
		],
		'uniqueid'=>[
			'name'=>'uniqueid', 'type'=>'varchar(32)', 'null'=>'1'
		],
		'dragonpoints'=>[
			'name'=>'dragonpoints', 'type'=>'text'
		],
		'boughtroomtoday'=>[
			'name'=>'boughtroomtoday', 'type'=>'tinyint(4)', 'default'=>'0'
		],
		'emailaddress'=>[
			'name'=>'emailaddress', 'type'=>'varchar(128)'
		],
		'replaceemail'=>[
			'name'=>'replaceemail', 'type'=>'varchar(128)'
		],
		'emailvalidation'=>[
			'name'=>'emailvalidation', 'type'=>'varchar(32)'
		],
		'forgottenpassowrd'=>[
			'name'=>'forgottenpassword', 'type'=>'varchar(32)'
		],
		'sentnotice'=>[
			'name'=>'sentnotice', 'type'=>'tinyint(1)', 'default'=>'0'
		],
		'prefs'=>[
			'name'=>'prefs', 'type'=>'text'
		],
		'pvpflag'=>[
			'name'=>'pvpflag', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'transferredtoday'=>[
			'name'=>'transferredtoday', 'type'=>'smallint(2) unsigned', 'default'=>'0'
		],
		'soulpoints'=>[
			'name'=>'soulpoints', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'gravefights'=>[
			'name'=>'gravefights', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'hauntedby'=>[
			'name'=>'hauntedby', 'type'=>'varchar(50)'
		],
		'deathpower'=>[
			'name'=>'deathpower', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'gensize'=>[
			'name'=>'gensize', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'recentcomments'=>[
			'name'=>'recentcomments',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		],
		'donation'=>[
			'name'=>'donation', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'donationspent'=>[
			'name'=>'donationspent', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'donationconfig'=>[
			'name'=>'donationconfig', 'type'=>'text'
		],
		'referer'=>[
			'name'=>'referer', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'refererawarded'=>[
			'name'=>'refererawarded', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'bio'=>[
			'name'=>'bio', 'type'=>'varchar(255)'
		],
		'race'=>[
			'name'=>'race', 'type'=>'varchar(50)', 'default'=>'0'
		],
		'biotime'=>[
			'name'=>'biotime', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'banoverride'=>[
			'name'=>'banoverride',
			'type'=>'tinyint(4)',
			'null'=>'1',
			'default'=>'0'
		],
		'translatorlanguages'=>[
			'name'=>'translatorlanguages', 'type'=>'varchar(128)', 'default'=>'en'
		],
		'amountouttoday'=>[
			'name'=>'amountouttoday', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'pk'=>[
			'name'=>'pk', 'type'=>'tinyint(3) unsigned', 'default'=>'0'
		],
		'dragonage'=>[
			'name'=>'dragonage', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'bestdragonage'=>[
			'name'=>'bestdragonage', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'ctitle'=>[
			'name'=>'ctitle', 'type'=>'varchar(25)'
		],
		'beta'=>[
			'name'=>'beta', 'type'=>'tinyint(3) unsigned', 'default'=>'0'
		],
		'slaydragon'=>[
			'name'=>'slaydragon', 'type'=>'tinyint(4) unsigned', 'default'=>'0'
		],
		'fedmount'=>[
			'name'=>'fedmount', 'type'=>'tinyint(4) unsigned', 'default'=>'0'
		],
		'regdate'=>[
			'name'=>'regdate',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		],
		'clanid'=>[
			'name'=>'clanid', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'clanrank'=>[
			'name'=>'clanrank', 'type'=>'tinyint(4) unsigned', 'default'=>'0'
		],
		'clanjoindate'=>[
			'name'=>'clanjoindate',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'acctid'
		],
		'key-name'=>[
			'name'=>'name', 'type'=>'key', 'columns'=>'name'
		],
		'key-level'=>[
			'name'=>'level', 'type'=>'key', 'columns'=>'level'
		],
		'key-login'=>[
			'name'=>'login', 'type'=>'key', 'columns'=>'login'
		],
		'key-alive'=>[
			'name'=>'alive', 'type'=>'key', 'columns'=>'alive'
		],
		'key-laston'=>[
			'name'=>'laston', 'type'=>'key', 'columns'=>'laston'
		],
		'key-lasthit'=>[
			'name'=>'lasthit', 'type'=>'key', 'columns'=>'lasthit'
		],
		'key-emailaddress'=>[
			'name'=>'emailaddress', 'type'=>'key', 'columns'=>'emailaddress'
		],
		'key-clanid'=>[
			'name'=>'clanid', 'type'=>'key', 'columns'=>'clanid'
		],
		'key-locked'=>[
			'name'=>'locked', 'type'=>'key', 'columns'=>'locked,loggedin,laston'
		],
		'key-referer'=>[
			'name'=>'referer', 'type'=>'key', 'columns'=>'referer'
		],
		'key-uniqueid'=>[
			'name'=>'uniqueid', 'type'=>'key', 'columns'=>'uniqueid'
		],
		'key-emailvalidation'=>[
			'name'=>'emailvalidation', 'type'=>'key', 'columns'=>'emailvalidation'
		],
		],
	'accounts_output'=>[
		'acctid'=>[
			'name'=>'acctid', 'type'=>'int(11) unsigned'
		],
		'output'=>[
			'name'=>'output', 'type'=>'mediumtext'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'acctid'
		],
		],
	'companions'=>[
		'companionid'=>[
			'name'=>'companionid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment',
		],
		'name'=>[
			'name'=>'name', 'type'=>'varchar(50)', 'null'=>'0'
		],
		'category'=>[
			'name'=>'category', 'type'=>'varchar(50)', 'null'=>'0'
		],
		'description'=>[
			'name'=>'description', 'type'=>'text', 'null'=>'0'
		],
		'attack'=>[
			'name'=>'attack', 'type'=>'int(6) unsigned', 'null'=>'0', 'default'=>'1'
		],
		'attackperlevel'=>[
			'name'=>'attackperlevel', 'type'=>'int(6) unsigned', 'null'=>'0', 'default'=>'0'
		],
		'defense'=>[
			'name'=>'defense', 'type'=>'int(6) unsigned', 'null'=>'0', 'default'=>'1'
		],
		'defenseperlevel'=>[
			'name'=>'defenseperlevel', 'type'=>'int(6) unsigned', 'null'=>'0', 'default'=>'0'
		],
		'maxhitpoints'=>[
			'name'=>'maxhitpoints', 'type'=>'int(6) unsigned', 'null'=>'0', 'default'=>'10'
		],
		'maxhitpointsperlevel'=>[
			'name'=>'maxhitpointsperlevel', 'type'=>'int(6) unsigned', 'null'=>'0', 'default'=>'10'
		],
		'abilities'=>[
			'name'=>'abilities', 'type'=>'text', 'null'=>'0', 'default'=>''
		],
		'cannotdie'=>[
			'name'=>'cannotdie', 'type'=>'tinyint(4)', 'null'=>'0', 'default'=>'0'
		],
		'cannotbehealed'=>[
			'name'=>'cannotbehealed', 'type'=>'tinyint(4)', 'null'=>'0', 'default'=>'1'
		],
		'companionlocation'=>[
			'name'=>'companionlocation', 'type'=>'varchar(25)', 'default'=>'all'
		],
		'companionactive'=>[
			'name'=>'companionactive', 'type'=>'tinyint(25)', 'default'=>'1'
		],
		'companioncostdks'=>[
			'name'=>'companioncostdks', 'type'=>'tinyint(4)', 'default'=>'0'
		],
		'companioncostgems'=>[
			'name'=>'companioncostgems', 'type'=>'int(6)', 'default'=>'0'
		],
		'companioncostgold'=>[
			'name'=>'companioncostgold', 'type'=>'int(10)', 'default'=>'0'
		],
		'jointext'=>[
			'name'=>'jointext', 'type'=>'text', 'default'=>''
		],
		'dyingtext'=>[
			'name'=>'dyingtext', 'type'=>'varchar(255)', 'default'=>''
		],
		'allowinshades'=>[
			'name'=>'allowinshades', 'type'=>'tinyint(4)', 'default'=>'0'
		],
		'allowinpvp'=>[
			'name'=>'allowinpvp', 'type'=>'tinyint(4)', 'default'=>'0'
		],
		'allowintrain'=>[
			'name'=>'allowintrain', 'type'=>'tinyint(4)', 'default'=>'0'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'companionid'
		],
		],
	'debug'=>[
		'id'=>[
			'name'=>'id',
			'type'=>'bigint(11) unsigned',
			'extra'=>'auto_increment',
		],
		'type'=>[
			'name'=>'type',
			'type'=>'varchar(100)',
			'null'=>'1',
		],
		'category'=>[
			'name'=>'category',
			'type'=>'varchar(100)',
			'null'=>'1',
		],
		'subcategory'=>[
			'name'=>'subcategory',
			'type'=>'varchar(100)',
			'null'=>'1',
		],
		'value'=>[
			'name'=>'value',
			'type'=>'varchar(100)',
			'null'=>'1',
		],
		'key-primary'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'id',
		'key-combikey'=>[
			'name'=>'combikey',
			'type'=>'key',
			'unique'=>'1',
			'columns'=>'type,category,subcategory',
		],
		],
		],

	'deathmessages'=>[
		'id'=>[
			'name'=>'deathmessageid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'deathmessage'=>[
			'name'=>'deathmessage', 'type'=>'varchar(500)', 'null'=>'1'
		],
		'forest'=>[
			'name'=>'forest', 'type'=>'tinyint', 'default'=>'1'
		],
		'graveyard'=>[
			'name'=>'graveyard', 'type'=>'tinyint', 'default'=>'0'
		],
		'taunt'=>[
			'name'=>'taunt', 'type'=>'tinyint', 'default'=>'1'
		],
		'editor'=>[
			'name'=>'editor', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'key-forest'=>[
			'name'=>'forest', 'type'=>'key', 'columns'=>'forest'
		],
		'key-graveyard'=>[
			'name'=>'graveyard', 'type'=>'key', 'columns'=>'graveyard'
		],
		'key-taunt'=>[
			'name'=>'taunt', 'type'=>'key', 'columns'=>'taunt'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'deathmessageid'
			]
		],
	'paylog'=>[
		'payid'=>[
			'name'=>'payid', 'type'=>'int(11)', 'null'=>'0', 'extra'=>'auto_increment'
		],
		'info'=>[
			'name'=>'info', 'type'=>'text'
		],
		'response'=>[
			'name'=>'response', 'type'=>'text', 'null'=>'0'
		],
		'txnid'=>[
			'name'=>'txnid', 'type'=>'varchar(32)', 'null'=>'0'
		],
		'amount'=>[
			'name'=>'amount', 'type'=>'float(9,2)', 'null'=>'0', 'default'=>'0.00'
		],
		'name'=>[
			'name'=>'name', 'type'=>'varchar(50)', 'null'=>'0'
		],
		'acctid'=>[
			'name'=>'acctid', 'type'=>'int(11) unsigned', 'null'=>'0', 'default'=>'0',
		],
		'processed'=>[
			'name'=>'processed', 'type'=>'tinyint(4) unsigned', 'null'=>'0',
			'default'=>'0'
		],
		'filed'=>[
			'name'=>'filed', 'type'=>'tinyint(4) unsigned', 'null'=>'0',
			'default'=>'0'
		],
		'txfee'=>[
			'name'=>'txfee', 'type'=>'float(9,2)', 'null'=>'0',
			'default'=>'0.00'
		],
		'processdate'=>[
			'name'=>'processdate', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'payid'
		],
		'key-txnid'=>[
			'name'=>'txnid', 'type'=>'key', 'columns'=>'txnid'
		],
		],
	'armor'=>[
		'armorid'=>[
			'name'=>'armorid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'armorname'=>[
			'name'=>'armorname', 'type'=>'varchar(128)', 'null'=>'1'
		],
		'value'=>[
			'name'=>'value', 'type'=>'int(11)', 'default'=>'0'
		],
		'defense'=>[
			'name'=>'defense', 'type'=>'int(11)', 'default'=>'1'
		],
		'level'=>[
			'name'=>'level', 'type'=>'int(11)', 'default'=>'0'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'armorid'
		],
		],
	'bans'=>[
		'ipfilter'=>[
			'name'=>'ipfilter', 'type'=>'varchar(15)'
		],
		'uniqueid'=>[
			'name'=>'uniqueid', 'type'=>'varchar(32)'
		],
		'banexpire'=>[
			'name'=>'banexpire', 'type'=>'datetime', 'null'=>'1'
		],
		'banreason'=>[
			'name'=>'banreason', 'type'=>'text'
		],
		'banner'=>[
			'name'=>'banner', 'type'=>'varchar(50)'
		],
		'lasthit'=>[
			'name'=>'lasthit', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'columns'=>'banexpire,uniqueid,ipfilter'
		],
		],
	'clans'=>[
		'clanid'=>[
			'name'=>'clanid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'
		],
		'clanname'=>[
			'name'=>'clanname', 'type'=>'varchar(255)'
		],
		'clanshort'=>[
			'name'=>'clanshort', 'type'=>'varchar(50)'
		],
		'clanmotd'=>[
			'name'=>'clanmotd', 'type'=>'text', 'null'=>'1'
		],
		'clandesc'=>[
			'name'=>'clandesc', 'type'=>'text', 'null'=>'1'
		],
		'motdauthor'=>[
			'name'=>'motdauthor', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'descauthor'=>[
			'name'=>'descauthor', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'customsay'=>[
			'name'=>'customsay', 'type'=>'varchar(15)'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'clanid'
		],
		'key-clanname'=>[
			'name'=>'clanname', 'type'=>'key', 'columns'=>'clanname'
		],
		'key-clanshort'=>[
			'name'=>'clanshort', 'type'=>'key', 'columns'=>'clanshort'
			]
		],
	'commentary'=>[
		'commentid'=>[
			'name'=>'commentid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'section'=>[
			'name'=>'section', 'type'=>'varchar(20)', 'null'=>'1'
		],
		'author'=>[
			'name'=>'author', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'comment'=>[
			'name'=>'comment', 'type'=>'varchar(600)'
		],
		'postdate'=>[
			'name'=>'postdate',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'commentid'
		],
		'key-section'=>[
			'name'=>'section', 'type'=>'key', 'columns'=>'section'
		],
		'key-postdate'=>[
			'name'=>'postdate', 'type'=>'key', 'columns'=>'postdate'
			]
		],
	'creatures'=>[
		'creatureid'=>[
			'name'=>'creatureid', 'type'=>'int(11)', 'extra'=>'auto_increment'
		],
		'creaturename'=>[
			'name'=>'creaturename', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'creaturecategory'=>[
			'name'=>'creaturecategory', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'creaturelevel'=>[
			'name'=>'creaturelevel', 'type'=>'int(11)', 'null'=>'1'
		],
		'creatureweapon'=>[
			'name'=>'creatureweapon', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'creaturelose'=>[
			'name'=>'creaturelose', 'type'=>'varchar(120)', 'null'=>'1'
		],
		'creaturewin'=>[
			'name'=>'creaturewin', 'type'=>'varchar(120)', 'null'=>'1'
		],
		'creaturegold'=>[
			'name'=>'creaturegold', 'type'=>'int(11)', 'null'=>'1'
		],
		'creatureexp'=>[
			'name'=>'creatureexp', 'type'=>'int(11)', 'null'=>'1'
		],
		'oldcreatureexp'=>[
			'name'=>'oldcreatureexp', 'type'=>'int(11)', 'null'=>'1'
		], //this field is obsolete and will be dropped by the installer
		'creaturehealth'=>[
			'name'=>'creaturehealth', 'type'=>'int(11)', 'null'=>'1'
		],
		'creatureattack'=>[
			'name'=>'creatureattack', 'type'=>'int(11)', 'null'=>'1'
		],
		'creaturedefense'=>[
			'name'=>'creaturedefense', 'type'=>'int(11)', 'null'=>'1'
		],
		'creatureaiscript'=>[
			'name'=>'creatureaiscript', 'type'=>'text', 'null'=>'1'
		],
		'createdby'=>[
			'name'=>'createdby', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'forest'=>[
			'name'=>'forest', 'type'=>'tinyint(4)', 'default'=>'0'
		],
		'graveyard'=>[
			'name'=>'graveyard', 'type'=>'tinyint(4)', 'default'=>'0'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'creatureid'
		],
		'key-creaturecategory'=>[
			'name'=>'creaturecategory', 'type'=>'key', 'columns'=>'creaturecategory'
		],
		'key-creaturelevel'=>[
			'name'=>'creaturelevel', 'type'=>'key', 'columns'=>'creaturelevel'
			]
		],
	'debuglog'=>[
		'id'=>[
			'name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'
		],
		'date'=>[
			'name'=>'date', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'actor'=>[
			'name'=>'actor', 'type'=>'int(11) unsigned', 'null'=>'1'
		],
		'target'=>[
			'name'=>'target', 'type'=>'int(11) unsigned', 'null'=>'1'
		],
		'message'=>[
			'name'=>'message', 'type'=>'text'
		],
		'field'=>[
			'name'=>'field', 'type'=>'varchar(20)', 'null'=>'0', 'default'=>''
		],
		'value'=>[
			'name'=>'value', 'type'=>'float(9,2)', 'null'=>'0', 'default'=>'0.00'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'id'
		],
		'key-date'=>[
			'name'=>'date', 'type'=>'key', 'columns'=>'date'
		],
		'key-target'=>[
			'name'=>'target', 'type'=>'key', 'columns'=>'target'
		],
		'key-field'=>[
			'name'=>'field', 'type'=>'key', 'columns'=>'actor,field'
		],
		],
	'debuglog_archive'=>[
		'id'=>[
			'name'=>'id', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'
		],
		'date'=>[
			'name'=>'date', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'actor'=>[
			'name'=>'actor', 'type'=>'int(11) unsigned', 'null'=>'1'
		],
		'target'=>[
			'name'=>'target', 'type'=>'int(11) unsigned', 'null'=>'1'
		],
		'message'=>[
			'name'=>'message', 'type'=>'text'
		],
		'field'=>[
			'name'=>'field', 'type'=>'varchar(20)', 'null'=>'0', 'default'=>''
		],
		'value'=>[
			'name'=>'value', 'type'=>'float(9,2)', 'null'=>'0', 'default'=>'0.00'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'id'
		],
		'key-date'=>[
			'name'=>'date', 'type'=>'key', 'columns'=>'date'
		],
		'key-target'=>[
			'name'=>'target', 'type'=>'key', 'columns'=>'target'
		],
		'key-field'=>[
			'name'=>'field', 'type'=>'key', 'columns'=>'actor,field'
		],
		],
	'faillog'=>[
		'eventid'=>[
			'name'=>'eventid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'date'=>[
			'name'=>'date', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'post'=>[
			'name'=>'post', 'type'=>'tinytext'
		],
		'ip'=>[
			'name'=>'ip', 'type'=>'varchar(40)'
		],
		'acctid'=>[
			'name'=>'acctid', 'type'=>'int(11) unsigned', 'null'=>'1'
		],
		'id'=>[
			'name'=>'id', 'type'=>'varchar(32)'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'eventid'
		],
		'key-date'=>[
			'name'=>'date', 'type'=>'key', 'columns'=>'date'
		],
		'key-acctid'=>[
			'name'=>'acctid', 'type'=>'key', 'columns'=>'acctid'
		],
		'key-ip'=>[
			'name'=>'ip', 'type'=>'key', 'columns'=>'ip'
			]
		],
	'gamelog'=>[
		'logid'=>[
			'name'=>'logid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment',
		],
		'message'=>[
			'name'=>'message',
			'type'=>'text',
		],
		'category'=>[
			'name'=>'category',
			'type'=>'varchar(50)',
		],
		'filed'=>[
			'name'=>'filed',
			'type'=>'tinyint(4)',
			'default'=>'0',
		],
		'date'=>[
			'name'=>'date',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00',
		],
		'who'=>[
			'name'=>'who',
			'type'=>'int(11) unsigned',
			'default'=>'0',
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'logid',
		],
		'key-date'=>[
			'name'=>'date',
			'type'=>'key',
			'columns'=>'category,date',
		],
		],
	'logdnetbans'=>[
		'banid'=>['name'=>'banid','type'=>'int(11) unsigned','extra'=>'auto_increment'],
		'bantype'=>['name'=>'bantype','type'=>'varchar(20)'],
		'banvalue'=>['name'=>'banvalue','type'=>'varchar(255)'],
		'key-PRIMARY'=>['name'=>'PRIMARY','type'=>'PRIMARY KEY','unique'=>'1','columns'=>'banid'],
		],
	'logdnet'=>[
		'serverid'=>[
			'name'=>'serverid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'address'=>[
			'name'=>'address', 'type'=>'varchar(255)'
		],
		'description'=>[
			'name'=>'description', 'type'=>'varchar(255)'
		],
		'priority'=>[
			'name'=>'priority', 'type'=>'double', 'default'=>'100'
		],
		'lastupdate'=>[
			'name'=>'lastupdate',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		],
		'version'=>[
			'name'=>'version', 'type'=>'varchar(255)', 'default'=>'Unknown'
		],
		'admin'=>[
			'name'=>'admin', 'type'=>'varchar(255)', 'default'=>'unknown'
		],
		'lastping'=>[
			'name'=>'lastping',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		],
		'recentips'=>[
			'name'=>'recentips',
			'type'=>'varchar(255)',
			'default'=>'',
		],
		'count'=>[
			'name'=>'count',
			'type'=>'int(11) unsigned',
			'default'=>'0',
		],
		'lang'=>[
			'name'=>'lang',
			'type'=>'varchar(20)',
			'default'=>'',
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'serverid'
			]
		],
	'mail'=>[
		'messageid'=>[
			'name'=>'messageid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'msgfrom'=>[
			'name'=>'msgfrom', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'msgto'=>[
			'name'=>'msgto', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'subject'=>[
			'name'=>'subject', 'type'=>'varchar(255)'
		],
		'body'=>[
			'name'=>'body', 'type'=>'text'
		],
		'sent'=>[
			'name'=>'sent', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'seen'=>[
			'name'=>'seen', 'type'=>'tinyint(1)', 'default'=>'0'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'messageid'
		],
		'key-msgto'=>[
			'name'=>'msgto', 'type'=>'key', 'columns'=>'msgto'
		],
		'key-seen'=>[
			'name'=>'seen', 'type'=>'key', 'columns'=>'seen'
			]
		],
	'masters'=>[
		'creatureid'=>[
			'name'=>'creatureid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'creaturename'=>[
			'name'=>'creaturename', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'creaturelevel'=>[
			'name'=>'creaturelevel', 'type'=>'int(11)', 'null'=>'1'
		],
		'creatureweapon'=>[
			'name'=>'creatureweapon', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'creaturelose'=>[
			'name'=>'creaturelose', 'type'=>'varchar(120)', 'null'=>'1'
		],
		'creaturewin'=>[
			'name'=>'creaturewin', 'type'=>'varchar(120)', 'null'=>'1'
		],
		'creaturegold'=>[
			'name'=>'creaturegold', 'type'=>'int(11)', 'null'=>'1'
		],
		'creatureexp'=>[
			'name'=>'creatureexp', 'type'=>'int(11)', 'null'=>'1'
		],
		'creaturehealth'=>[
			'name'=>'creaturehealth', 'type'=>'int(11)', 'null'=>'1'
		],
		'creatureattack'=>[
			'name'=>'creatureattack', 'type'=>'int(11)', 'null'=>'1'
		],
		'creaturedefense'=>[
			'name'=>'creaturedefense', 'type'=>'int(11)', 'null'=>'1'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'creatureid'
			]
		],
	'moderatedcomments'=>[
		'modid'=>[
			'name'=>'modid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'
		],
		'comment'=>[
			'name'=>'comment', 'type'=>'text', 'null'=>'1'
		],
		'moderator'=>[
			'name'=>'moderator', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'moddate'=>[
			'name'=>'moddate', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'modid'
			]
		],
	'module_event_hooks'=>[
		'event_type'=>[
			'name'=>'event_type', 'type'=>'varchar(20)'
		],
		'modulename'=>[
			'name'=>'modulename', 'type'=>'varchar(50)'
		],
		'event_chance'=>[
			'name'=>'event_chance', 'type'=>'text'
		],
		'key-modulename'=>[
			'name'=>'modulename', 'type'=>'key', 'columns'=>'modulename'
		],
		'key-event_type'=>[
			'name'=>'event_type', 'type'=>'key', 'columns'=>'event_type'
			]
		],
	'module_hooks'=>[
		'modulename'=>[
			'name'=>'modulename', 'type'=>'varchar(50)'
		],
		'location'=>[
			'name'=>'location', 'type'=>'varchar(50)'
		],
		'function'=>[
			'name'=>'function', 'type'=>'varchar(50)'
		],
		'whenactive'=>[
			'name'=>'whenactive', 'type'=>'text'
		],
		'priority'=>[
			'name'=>'priority','type'=>'int(11)','default'=>'50'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'modulename,location,function'
		],
		'key-location'=>[
			'name'=>'location', 'type'=>'key', 'columns'=>'location'
		],
		],
	'module_objprefs'=>[
		'modulename'=>[
			'name'=>'modulename', 'type'=>'varchar(50)'
		],
		'objtype'=>[
			'name'=>'objtype', 'type'=>'varchar(50)'
		],
		'setting'=>[
			'name'=>'setting', 'type'=>'varchar(50)'
		],
		'objid'=>[
			'name'=>'objid', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'value'=>[
			'name'=>'value', 'type'=>'text', 'null'=>'1'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'modulename,objtype,setting,objid'
			]
		],

	'module_settings'=>[
		'modulename'=>[
			'name'=>'modulename', 'type'=>'varchar(50)'
		],
		'setting'=>[
			'name'=>'setting', 'type'=>'varchar(50)'
		],
		'value'=>[
			'name'=>'value', 'type'=>'text', 'null'=>'1'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'modulename,setting'
			]
		],
	'module_userprefs'=>[
		'modulename'=>[
			'name'=>'modulename', 'type'=>'varchar(50)'
		],
		'setting'=>[
			'name'=>'setting', 'type'=>'varchar(50)'
		],
		'userid'=>[
			'name'=>'userid', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'value'=>[
			'name'=>'value', 'type'=>'text', 'null'=>'1'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'modulename,setting,userid'
		],
		'key-modulename'=>[
			'name'=>'modulename', 'type'=>'key', 'columns'=>'modulename,userid'
		],
		],
	'modules'=>[
		'modulename'=>[
			'name'=>'modulename', 'type'=>'varchar(50)'
		],
		'formalname'=>[
			'name'=>'formalname', 'type'=>'varchar(255)'
		],
		'description'=>[
			'name'=>'description', 'type'=>'text'
		],
		'moduleauthor'=>[
			'name'=>'moduleauthor', 'type'=>'varchar(255)'
		],
		'active'=>[
			'name'=>'active', 'type'=>'tinyint(4)', 'default'=>'0'
		],
		'filename'=>[
			'name'=>'filename', 'type'=>'varchar(255)'
		],
		'installdate'=>[
			'name'=>'installdate',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		],
		'installedby'=>[
			'name'=>'installedby', 'type'=>'varchar(50)'
		],
		'filemoddate'=>[
			'name'=>'filemoddate',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		],
		'type'=>[
			'name'=>'type', 'type'=>'tinyint(4)', 'default'=>'0'
		],
		'extras'=>[
			'name'=>'extras', 'type'=>'text', 'null'=>'1'
		],
		'category'=>[
			'name'=>'category', 'type'=>'varchar(50)'
		],
		'infokeys'=>[
			'name'=>'infokeys', 'type'=>'text'
		],
		'version'=>[
			'name'=>'version', 'type'=>'varchar(10)', 'null'=>'1'
		],
		'download'=>[
			'name'=>'download', 'type'=>'varchar(200)', 'null'=>'1'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'modulename'
			]
		],
	'motd'=>[
		'motditem'=>[
			'name'=>'motditem',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'motdtitle'=>[
			'name'=>'motdtitle', 'type'=>'varchar(200)', 'null'=>'1'
		],
		'motdbody'=>[
			'name'=>'motdbody', 'type'=>'text', 'null'=>'1'
		],
		'motddate'=>[
			'name'=>'motddate', 'type'=>'datetime', 'null'=>'1'
		],
		'motdtype'=>[
			'name'=>'motdtype', 'type'=>'tinyint(4) unsigned', 'default'=>'0'
		],
		'motdauthor'=>[
			'name'=>'motdauthor', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'motditem'
			]
		],
	'mounts'=>[
		'mountid'=>[
			'name'=>'mountid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'mountname'=>[
			'name'=>'mountname', 'type'=>'varchar(50)'
		],
		'mountdesc'=>[
			'name'=>'mountdesc', 'type'=>'text', 'null'=>'1'
		],
		'mountcategory'=>[
			'name'=>'mountcategory', 'type'=>'varchar(50)'
		],
		'mountbuff'=>[
			'name'=>'mountbuff', 'type'=>'text', 'null'=>'1'
		],
		'mountcostgems'=>[
			'name'=>'mountcostgems', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'mountcostgold'=>[
			'name'=>'mountcostgold', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'mountactive'=>[
			'name'=>'mountactive', 'type'=>'int(11) unsigned', 'default'=>'1'
		],
		'mountforestfights'=>[
			'name'=>'mountforestfights', 'type'=>'int(11)', 'default'=>'0'
		],
		'newday'=>[
			'name'=>'newday', 'type'=>'text'
		],
		'recharge'=>[
			'name'=>'recharge', 'type'=>'text'
		],
		'partrecharge'=>[
			'name'=>'partrecharge', 'type'=>'text'
		],
		'mountfeedcost'=>[
			'name'=>'mountfeedcost', 'type'=>'int(11) unsigned', 'default'=>'20'
		],
		'mountlocation'=>[
			'name'=>'mountlocation', 'type'=>'varchar(25)', 'default'=>'all'
		],
		'mountdkcost'=>[
			'name'=>'mountdkcost', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'mountid'
		],
		'key-mountid'=>[
			'name'=>'mountid', 'type'=>'key', 'columns'=>'mountid'
			]
		],
	'nastywords'=>[
		'words'=>[
			'name'=>'words', 'type'=>'text', 'null'=>'1'
		],
		'type'=>[
			'name'=>'type', 'type'=>'varchar(10)', 'null'=>'1'
			]
		],
	'news'=>[
		'newsid'=>[
			'name'=>'newsid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'
		],
		'newstext'=>[
			'name'=>'newstext', 'type'=>'text'
		],
		'newsdate'=>[
			'name'=>'newsdate', 'type'=>'date', 'default'=>'0000-00-00'
		],
		'accountid'=>[
			'name'=>'accountid', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'arguments'=>[
			'name'=>'arguments', 'type'=>'text'
		],
		'tlschema'=>[
			'name'=>'tlschema', 'type'=>'varchar(255)', 'default'=>'news'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'newsid,newsdate'
		],
		'key-accountid'=>[
			'name'=>'accountid', 'type'=>'key', 'columns'=>'accountid'
		],
		'key-newsdate'=>[
			'name'=>'newsdate', 'type'=>'key', 'columns'=>'newsdate'
		],
		],
	'petitions'=>[
		'petitionid'=>[
			'name'=>'petitionid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'author'=>[
			'name'=>'author', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'date'=>[
			'name'=>'date', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'
		],
		'status'=>[
			'name'=>'status', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'body'=>[
			'name'=>'body', 'type'=>'text', 'null'=>'1'
		],
		'pageinfo'=>[
			'name'=>'pageinfo', 'type'=>'text', 'null'=>'1'
		],
		'closedate'=>[
			'name'=>'closedate',
			'type'=>'datetime',
			'default'=>'0000-00-00 00:00:00'
		],
		'closeuserid'=>[
			'name'=>'closeuserid', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'ip'=>[
			'name'=>'ip', 'type'=>'varchar(40)', 'default'=>''
		],
		'id'=>[
			'name'=>'id', 'type'=>'varchar(32)', 'default'=>''
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'petitionid'
			]
		],
	'pollresults'=>[
		'resultid'=>[
			'name'=>'resultid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'choice'=>[
			'name'=>'choice', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'account'=>[
			'name'=>'account', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'motditem'=>[
			'name'=>'motditem', 'type'=>'int(11) unsigned', 'default'=>'0'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'resultid'
			]
		],
	'referers'=>[
		// This table needs to be myISAM since pre-4.0.14 mysql cannot index
		// on blob tables under innoDB and we have no way to determine
		// with 100% accuracy (mysql_get_server_info merely returns an
		// arbitrary string) what the version of the database is. :/
		'RequireMyISAM'=>1,
		'refererid'=>[
			'name'=>'refererid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'uri'=>[
			'name'=>'uri', 'type'=>'text', 'null'=>'1'
		],
		'count'=>[
			'name'=>'count', 'type'=>'int(11)', 'null'=>'1'
		],
		'last'=>[
			'name'=>'last', 'type'=>'datetime', 'null'=>'1'
		],
		'site'=>[
			'name'=>'site', 'type'=>'varchar(50)'
		],
		'dest'=>[
			'name'=>'dest', 'type'=>'varchar(255)', 'null'=>'1'
		],
		'ip'=>[
			'name'=>'ip', 'type'=>'varchar(40)', 'null'=>'1'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'refererid'
		],
		'key-uri'=>[
			'name'=>'uri', 'type'=>'key', 'columns'=>'uri(100)'
		],
		'key-site'=>[
			'name'=>'site', 'type'=>'key', 'columns'=>'site'
			]
		],
	'settings'=>[
		'setting'=>[
			'name'=>'setting', 'type'=>'varchar(25)'
		],
		'value'=>[
			'name'=>'value', 'type'=>'varchar(255)'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'setting'
			]
		],
	'settings_extended'=>[
		'setting'=>[
			'name'=>'setting', 'type'=>'varchar(50)'
		],
		'value'=>[
			'name'=>'value', 'type'=>'text'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'setting'
			]
		],
	'taunts'=>[
		'tauntid'=>[
			'name'=>'tauntid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'taunt'=>[
			'name'=>'taunt', 'type'=>'text', 'null'=>'1'
		],
		'editor'=>[
			'name'=>'editor', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'tauntid'
			]
		],
	'untranslated'=>[
		// This table needs to be myISAM since pre-4.0.14 mysql cannot index
		// on blob tables under innoDB and we have no way to determine
		// with 100% accuracy (mysql_get_server_info merely returns an
		// arbitrary string) what the version of the database is. :/
		'RequireMyISAM'=>1,
		'intext'=>[
			'name'=>'intext', 'type'=>'blob', 'null'=>'0'
		],
		'language'=>[
			'name'=>'language', 'type'=>'varchar(10)'
		],
		'namespace'=>[
			'name'=>'namespace', 'type'=>'varchar(255)'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
		'columns'=>'intext(200],language,namespace'
		],
		'key-language'=>[
			'name'=>'language', 'type'=>'key', 'columns'=>'language'
		],
		'key-intext1'=>[
		'name'=>'intext1', 'type'=>'key', 'columns'=>'intext(200],language'
		],
		],
	'translations'=>[
		'tid'=>[
			'name'=>'tid', 'type'=>'int(11)', 'extra'=>'auto_increment'
		],
		'language'=>[
			'name'=>'language', 'type'=>'varchar(10)'
		],
		'uri'=>[
			'name'=>'uri', 'type'=>'varchar(255)'
		],
		'intext'=>[
			'name'=>'intext', 'type'=>'blob'
		],
		'outtext'=>[
			'name'=>'outtext', 'type'=>'blob'
		],
		'author'=>[
			'name'=>'author', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'version'=>[
			'name'=>'version', 'type'=>'varchar(50)', 'null'=>'1'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'tid'
		],
		'key-language'=>[
			'name'=>'language', 'type'=>'key', 'columns'=>'language,uri'
		],
		'key-uri'=>[
			'name'=>'uri', 'type'=>'key', 'columns'=>'uri'
		],
		],
	'weapons'=>[
		'weaponid'=>[
			'name'=>'weaponid',
			'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'weaponname'=>[
			'name'=>'weaponname', 'type'=>'varchar(128)', 'null'=>'1'
		],
		'value'=>[
			'name'=>'value', 'type'=>'int(11)', 'default'=>'0'
		],
		'damage'=>[
			'name'=>'damage', 'type'=>'int(11)', 'default'=>'1'
		],
		'level'=>[
			'name'=>'level', 'type'=>'int(11)', 'default'=>'0'
		],
		'key-PRIMARY'=>[
			'name'=>'PRIMARY',
			'type'=>'primary key',
			'unique'=>'1',
			'columns'=>'weaponid'
			]
		],
	'titles'=>[
		'titleid'=>[
			'name'=>'titleid', 'type'=>'int(11) unsigned',
			'extra'=>'auto_increment'
		],
		'dk'=>[
			'name'=>'dk', 'type'=>'int(11)', 'default'=>'0'
		],
		'ref'=>[
			'name'=>'ref', 'type'=>'varchar(100)', 'null'=>'0', 'default'=>""
		],
		'male'=>[
			'name'=>'male', 'type'=>'varchar(25)', 'null'=>'0', 'default'=>""
		],
		'female'=>[
			'name'=>'female', 'type'=>'varchar(25)', 'null'=>'0', 'default'=>""
		],
		'key-PRIMARY' => [
			'name' => 'PRIMARY',
			'type' => 'primary key',
			'unique' => '1',
			'columns' => 'titleid',
		],
		'key-dk' => [
			'name' => 'dk',
			'type' => 'key',
			'columns' => 'dk',
			],
		],
	];
}
?>

