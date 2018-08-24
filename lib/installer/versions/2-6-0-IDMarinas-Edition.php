<?php

$creaturesTable = DB::prefix('creatures');

return [
    "ALTER TABLE `$creaturesTable`
    ADD COLUMN `creatureimage` VARCHAR(250) NOT NULL AFTER `creaturecategory`,
	ADD COLUMN `creaturedescription` TEXT NOT NULL AFTER `creatureimage`;"
];
