<?php

class ServerFunctions
{
    public static function isTheServerFull()
    {
        if (abs(getsetting('OnlineCountLast', 0) - strtotime('now')) > 60)
        {
            $repository = \Doctrine::getRepository('LotgdCore:Accounts');
            $counter = $repository->count([ 'locked' => 0, 'loggedin' => 1 ]);

            savesetting('OnlineCount', $counter);
            savesetting('OnlineCountLast', strtotime('now'));
        }

        $onlinecount = (int) getsetting('OnlineCount', 0);

        if ($onlinecount >= getsetting('maxonline', 0) && 0 != getsetting('maxonline', 0))
        {
            return true;
        }

        return false;
    }

    public static function resetAllDragonkillPoints($acctid = false)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Characters');
        $query = $repository->createQueryBuilder('u');

        $query->where("u.dragonpoints <> ''");

        if (is_numeric($acctid))
        {
            $query->addWhere('u.acct = :acct')
                ->setParamater('acct', $acctid)
            ;
        }
        elseif (is_array($acctid))
        {
            $query->addWhere('u.acct IN (:acct)')
                ->setParamater('acct', $acctid)
            ;
        }

        $result = $query->getQuery()->getResult();

        //this is ugly, but fortunately only needed out of the ordinary
        foreach ($result as $entity)
        {
            $dkpoints = $entity->getDragonpoints();

            if (empty($dkpoints))
            {
                continue;
            } //-- Not do nothing if is an empty array

            $distribution = array_count_values($dkpoints);

            $entity->setDragonpoints([])
                ->setStrength($entity->getStrength() - (int) $distribution['str'])
                ->setConstitution($entity->getConstitution() - (int) $distribution['con'])
                ->setIntelligence($entity->getIntelligence() - (int) $distribution['int'])
                ->setWisdom($entity->getWisdom() - (int) $distribution['wis'])
                ->setDexterity($entity->getDexterity() - (int) $distribution['dex'])
            ;

            \Doctrine::persist($entity);
        }

        \Doctrine::flush();

        //adding a hook, nasty, but you don't call this too often
        modulehook('dragonpointreset', [$result]);
    }
}
