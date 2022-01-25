<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Repository\Mail;

use Throwable;
use Tracy\Debugger;

/**
 * Functions for clan of mail.
 */
trait Clan
{
    /**
     * Delete mail from system by subject.
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

            if ($toId !== 0)
            {
                $query->andWhere('u.msgto = :to')
                    ->setParameter('to', $toId)
                ;
            }

            return $query->getQuery()
                ->execute()
            ;
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return 0;
        }
    }
}
