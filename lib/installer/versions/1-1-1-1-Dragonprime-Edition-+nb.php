<?php

$tauntsTable = DB::prefix('taunts');
$mastersTable = DB::prefix('masters');

return [
	//since the change of the %w stuff to some more sensible stuff, we need to replace the text in the old taunts to fit in
	"UPDATE $tauntsTable SET taunt=REPLACE(taunt,'%w','{goodguy}')",
	"UPDATE $tauntsTable SET taunt=REPLACE(taunt,'%W','{badguy}')",
	"UPDATE $tauntsTable SET taunt=REPLACE(taunt,'%x','{goodguyweapon}')",
	"UPDATE $tauntsTable SET taunt=REPLACE(taunt,'%X','{badguyweapon}')",
	"UPDATE $tauntsTable SET taunt=REPLACE(taunt,'%a','{goodguyarmor}')",
	"UPDATE $tauntsTable SET taunt=REPLACE(taunt,'%s','{himher}')",
	"UPDATE $tauntsTable SET taunt=REPLACE(taunt,'%p','{hisher}')",
	"UPDATE $tauntsTable SET taunt=REPLACE(taunt,'%o','{heshe}')",
	//masters
	"UPDATE $mastersTable SET creaturewin=REPLACE(creaturewin,'%w','{goodguy}')",
	"UPDATE $mastersTable SET creaturewin=REPLACE(creaturewin,'%W','{badguy}')",
	"UPDATE $mastersTable SET creaturewin=REPLACE(creaturewin,'%x','{goodguyweapon}')",
	"UPDATE $mastersTable SET creaturewin=REPLACE(creaturewin,'%X','{badguyweapon}')",
	"UPDATE $mastersTable SET creaturewin=REPLACE(creaturewin,'%a','{goodguyarmor}')",
	"UPDATE $mastersTable SET creaturewin=REPLACE(creaturewin,'%s','{himher}')",
	"UPDATE $mastersTable SET creaturewin=REPLACE(creaturewin,'%p','{hisher}')",
	"UPDATE $mastersTable SET creaturewin=REPLACE(creaturewin,'%o','{heshe}')",
	//masters part 2
	"UPDATE $mastersTable SET creaturelose=REPLACE(creaturelose,'%w','{goodguy}')",
	"UPDATE $mastersTable SET creaturelose=REPLACE(creaturelose,'%W','{badguy}')",
	"UPDATE $mastersTable SET creaturelose=REPLACE(creaturelose,'%x','{goodguyweapon}')",
	"UPDATE $mastersTable SET creaturelose=REPLACE(creaturelose,'%X','{badguyweapon}')",
	"UPDATE $mastersTable SET creaturelose=REPLACE(creaturelose,'%a','{goodguyarmor}')",
	"UPDATE $mastersTable SET creaturelose=REPLACE(creaturelose,'%s','{himher}')",
	"UPDATE $mastersTable SET creaturelose=REPLACE(creaturelose,'%p','{hisher}')",
	"UPDATE $mastersTable SET creaturelose=REPLACE(creaturelose,'%o','{heshe}')",
];