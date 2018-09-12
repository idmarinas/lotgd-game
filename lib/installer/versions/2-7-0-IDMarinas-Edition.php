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
        ADD COLUMN `creaturegoldbonus` DECIMAL(4,2) UNSIGNED NOT NULL DEFAULT '1.00' AFTER `creatureweapon`,
	    ADD COLUMN `creatureattackbonus` DECIMAL(4,2) UNSIGNED NOT NULL DEFAULT '1.00' AFTER `creaturegoldbonus`,
	    ADD COLUMN `creaturedefensebonus` DECIMAL(4,2) UNSIGNED NOT NULL DEFAULT '1.00' AFTER `creatureattackbonus`,
	    ADD COLUMN `creaturehealthbonus` DECIMAL(4,2) UNSIGNED NOT NULL DEFAULT '1.00' AFTER `creaturedefensebonus`"
];
