<?php

$mastersTable = DB::prefix('masters');

return [
	"REPLACE INTO $mastersTable VALUES (1,'Mireraband',1,'Small Dagger','Well done %w`&, I should have guessed you\\'d grown some.','As I thought, %w`^, your skills are no match for my own!',NULL,NULL,12,2,2)",
	"REPLACE INTO $mastersTable VALUES (2,'Fie',2,'Short Sword','Well done %w`&, you really know how to use your %x.','You should have known you were no match for my %X',NULL,NULL,22,4,4)",
	"REPLACE INTO $mastersTable VALUES (4,'Guth',4,'Spiked Club','Ha!  Hahaha, excellent fight %w`&!  Haven\\'t had a battle like that since I was in the RAF!','Back in the RAF, we\\'d have eaten the likes of you alive!  Go work on your skills some old boy!',NULL,NULL,44,8,8)",
	"REPLACE INTO $mastersTable VALUES (6,'Adwares',6,'Dwarven Battle Axe','Ach!  Y\\' do hold yer %x with skeel!','Har!  Y\\' do be needin moore praktise y\\' wee cub!',NULL,NULL,66,12,12)",
	"REPLACE INTO $mastersTable VALUES (8,'Ceiloth',8,'Orkos Broadsword','Well done %w`&, I can see that great things lie in the future for you!','You are becoming powerful, but not yet that powerful.',NULL,NULL,88,16,16)",
	"REPLACE INTO $mastersTable VALUES (9,'Dwiredan',9,'Twin Swords','Perhaps I should have considered your %x...','Perhaps you\\'ll reconsider my twin swords before you try that again?',NULL,NULL,99,18,18)",
];