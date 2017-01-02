<?php

$nastywordsTable = DB::prefix('nastywords');
$mastersTable = DB::prefix('masters');
$petitionsTable = DB::prefix('petitions');
$accountsTable = DB::prefix('accounts');
$logdnetTable = DB::prefix('logdnet');

return [
	"UPDATE $petitionsTable SET closedate = date WHERE status=2",

	"UPDATE $mastersTable SET creaturewin='Learn to adapt your style, and you shall prevail.' WHERE creaturename='Sensei Noetha'",

	"UPDATE $logdnetTable SET lastping=lastupdate",

	"UPDATE $accountsTable SET attack=attack-1,race=\"Troll\" WHERE race='1'",
	"UPDATE $accountsTable SET defense=defense-1,race=\"Elf\" WHERE race='2'",
	"UPDATE $accountsTable SET race=\"Human\" WHERE race='3'",
	"UPDATE $accountsTable SET race=\"Dwarf\" WHERE race='4'",
	"UPDATE $accountsTable SET race=\"Horrible Gelatinous Blob\" WHERE race='0'",
	"UPDATE $accountsTable SET location=\"The Boar's Head Inn\" WHERE location='1'",
	"UPDATE $accountsTable SET location=\"Degolburg\" WHERE location='0'",
	"UPDATE $accountsTable SET password=md5(password) WHERE length(password) < 32",
	"UPDATE $accountsTable SET password=md5(password)",

	"UPDATE $nastywordsTable SET type='nasty'",
	];