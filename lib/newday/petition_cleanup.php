<?php

$repository = \Doctrine::getRepository('LotgdCore:Petitions');

//-- Delte old petitions
if ($repository->deleteOldPetitions())
{
    invalidatedatacache('petition_counts');
}
