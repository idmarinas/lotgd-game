<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Tool\Tool;

trait Title
{
    /** Validate DK title */
    public function validDkTitle($title, $dks, $gender): bool
    {
        $repository = $this->repository->getRepository('LotgdCore:Titles');
        $query      = $repository->createQueryBuilder('u');

        $query
            ->where('u.dk <= :dk')
            ->orderBy('u.dk', 'DESC')
        ;

        $query = $repository->createTranslatebleQuery($query);
        $query->setParameter('dk', $dks);
        $result = $query->getResult();

        $d = $result[0]->getDk();

        foreach ($result as $row)
        {
            // Only care about best dk rank for this person
            if ($row->getDk() != $d)
            {
                break;
            }

            if (
                ($gender && ($row->getFemale() == $title))
                || ( ! $gender && ($row->getMale() == $title))
            ) {
                return true;
            }
        }

        return false;
    }

    /** Get a title for a DK */
    public function getDkTitle($dks, $gender, $ref = null): string
    {
        // $ref is an arbitrary string value.  The title picker will try to
        // give the next highest title in the same 'ref', but if it cannot it'll
        // default to a random one of the ones available for the required DK.

        // Figure out which dk value is the right one to use.. The one to use
        // is the closest one below or equal to the players dk number.
        // We will prefer the dk level from the same $ref if we can, but if there
        // is a closer 'any' match, we will use that!

        $repository = $this->doctrine->getRepository('LotgdCore:Titles');

        $refdk = -1;

        if ( ! empty($ref))
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

        // Okay, we now have the right dk target to use, so select a title from
        // any titles available at that level.  We will prefer titles that
        // match the ref if possible.
        $query = $repository->createQueryBuilder('u');
        $query->where('u.dk = :dk')
            ->orderBy('rand()')
        ;
        if ($refdk >= $anydk) {
            $query->andWhere('u.ref = :ref');
        }

        $query = $repository->createTranslatebleQuery($query);

        $targetdk = $anydk;

        if ($refdk >= $anydk)
        {
            $targetdk = $refdk;
            $query->setParameter('ref', $ref);
        }

        $query->setParameter('dk', $targetdk);

        $row = $query->getResult()[0] ?? null;

        if ( ! $row)
        {
            return '';
        }

        if (SEX_FEMALE == $gender)
        {
            return (string) $row->getFemale();
        }

        return (string) $row->getMale();
    }
}
