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

];
