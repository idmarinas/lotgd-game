<?php

$creaturesTable = DB::prefix('creatures');

return [
    "ALTER TABLE `$creaturesTable`
        DROP creatureexp,
        DROP creatureattack,
        DROP creaturedefense,
        DROP creaturelevel,
        DROP creaturegold,
        DROP creaturehealth;",
    "ALTER TABLE `$creaturesTable`
	    ADD COLUMN `creaturegoldmultiplier` DECIMAL(4,2) UNSIGNED NOT NULL DEFAULT '1.00' AFTER `creatureweapon`"
];
