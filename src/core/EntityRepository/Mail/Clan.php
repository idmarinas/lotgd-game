<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\EntityRepository\Mail;

use Doctrine\ORM\Query\Expr\Join;
use Lotgd\Core\Entity as EntityCore;
use Tracy\Debugger;

/**
 * Functions for clan of mail.
 */
trait Clan
{
    /**
     * Delete mail from system by subject.
     *
     * @param string $subject
     * @param int $toId
     *
     * @return int
     */
    public function deleteMailFromSystemBySubj(string $subject, int $toId = 0): int
    {
        $query = $this->_em->createQueryBuilder();
        try
        {
            $query->delete($this->_entityName, 'u')
                ->where('u.subject = :subj AND u.msgfrom = 0 AND u.seen = 0')
                ->setParameter('subj', $subject)
            ;

            if ($toId)
            {
                $query->andWhere('u.msgto = :to')
                    ->setParameter('to', $toId)
                ;
            }

            return $query->getQuery()
                ->execute()
            ;
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            return 0;
        }
    }
}
