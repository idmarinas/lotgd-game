<?php

$accountsTable = DB::prefix('accounts');
$settingsTable = DB::prefix('settings');

return [
	"UPDATE $accountsTable SET clanrank = clanrank * 10",
	"INSERT INTO $settingsTable VALUES ('newdaycron', '0')",
	"INSERT INTO $settingsTable VALUES ('charset', 'UTF-8')",
	"INSERT INTO $settingsTable VALUES ('allowspecialswitch', '1')",
];