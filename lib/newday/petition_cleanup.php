<?php

$repository = \Doctrine::getRepository('LotgdCore:Petitions');

//-- Delete old petitions
$repository->deleteOldPetitions();
