<?php

// translator ready
// addnews ready
// mail ready
function getmount($horse = 0)
{
    if (! $horse)
    {
        return null;
    }

    $repository = \Doctrine::getRepository('LotgdCore:Mounts');

    return $repository->extractEntity($repository->find($horse));
}
