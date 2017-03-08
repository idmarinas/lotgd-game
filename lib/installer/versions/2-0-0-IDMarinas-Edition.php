<?php

return [
	"INSERT INTO `settings`
		(`setting`, `value`)
	VALUES
		('deathoverlord', '`\$Ramius`0'),
		('villagename', 'Degolburg'),
		('innname', 'The Boar\'s Head Inn'),
		('barkeep', '`tCedrik`0'),
		('barmaid', '`%Violet`0'),
		('bard', '`^Seth`0'),
		('clanregistrar', '`%Karissa`0'),
		('bankername', '`@Elessa`0'),
		('motditems', '5'),
		('petition_types', 'Bug,General'),
		('defaultskin', 'jade.html')
	ON DUPLICATE KEY UPDATE value=VALUES(value);"
];