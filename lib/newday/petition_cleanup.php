<?php

$repository = \Doctrine::getRepository('LotgdCore:Petitions');

//-- Delte old petitions
if ($repository->deleteOldPetitions())
{
    LotgdCache::removeItem('petition_counts');
}
