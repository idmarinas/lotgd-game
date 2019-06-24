<?php

// translator ready
// addnews ready
// mail ready
function getmount($horse = 0)
{
    $repository = \Doctrine::getRepository('LotgdCore:Mounts');

    return $repository->extractEntity($repository->find($horse));
}
