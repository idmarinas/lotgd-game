<?php

// translator ready
// addnews ready
// mail ready

function valid_dk_title($title, $dks, $gender)
{
    $repository = \Doctrine::getRepository('LotgdCore:Titles');
    $query = $repository->createQueryBuilder('u');

    $result = $query
        ->where('u.dk <= :dk')
        ->orderBy('u.dk', 'DESC')

        ->setParameter('dk', $dks)

        ->getQuery()
        ->getResult()
    ;

    $d = -1;

    foreach ($result as $row)
    {
        if (-1 == $d)
        {
            $d = $row['dk'];
        }
        // Only care about best dk rank for this person
        if ($row->getDk() != $d)
        {
            break;
        }

        if ($gender && ($row->getFemale() == $title))
        {
            return true;
        }

        if (! $gender && ($row->getMale() == $title))
        {
            return true;
        }
    }

    return false;
}

function get_dk_title($dks, $gender, $ref = false)
{
    // $ref is an arbitrary string value.  The title picker will try to
    // give the next highest title in the same 'ref', but if it cannot it'll
    // default to a random one of the ones available for the required DK.

    // Figure out which dk value is the right one to use.. The one to use
    // is the closest one below or equal to the players dk number.
    // We will prefer the dk level from the same $ref if we can, but if there
    // is a closer 'any' match, we will use that!

    $repository = \Doctrine::getRepository('LotgdCore:Titles');

    $refdk = -1;

    if (false !== $ref)
    {
        $query = $repository->createQueryBuilder('u');
        $refdk = $query
            ->select('max(u.dk)')
            ->where('u.dk = :dk AND u.ref = :ref')

            ->setParameter('dk', $dks)
            ->setParameter('ref', $ref)

            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    $query = $repository->createQueryBuilder('u');
    $anydk = $query
        ->select('max(u.dk)')
        ->where('u.dk <= :dk')

        ->setParameter('dk', $dks)

        ->getQuery()
        ->getSingleScalarResult()
    ;

    $targetdk = $anydk;

    if ($refdk >= $anydk)
    {
        $targetdk = $refdk;
    }

    // Okay, we now have the right dk target to use, so select a title from
    // any titles available at that level.  We will prefer titles that
    // match the ref if possible.
    $query = $repository->createQueryBuilder('u');
    $query
        ->select('max(u.dk)')
        ->where('u.dk = :dk')
        ->orderBy('rand()')

        ->setParameter('dk', $targetdk)
    ;

    if ($refdk >= $anydk)
    {
        $query->andWhere('u.ref = :ref')
            ->setParameter('dk', $ref)
        ;
    }

    $row = $query->getQuery()->getResult();

    if (! $row)
    {
        $row = ['male' => 'God', 'female' => 'Goddess'];
    }

    if (SEX_FEMALE == $gender)
    {
        return $row['female'];
    }

    return $row['male'];
}
