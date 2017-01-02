<?php

$creaturesTable = DB::prefix('creatures');

return [
	"1|UPDATE $creaturesTable SET forest=1",
	"1|UPDATE $creaturesTable SET graveyard=1 where location=1",
];