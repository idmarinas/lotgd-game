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

namespace Lotgd\Ajax\Core;

use Doctrine;
use Jaxon\Response\Response;
use Lotgd\Core\AjaxAbstract;
use LotgdFormat;
use LotgdTranslator;
use Throwable;
use Tracy\Debugger;
use function Jaxon\jaxon;

class Bans extends AjaxAbstract
{
    public function showAffected($ip, $id)
    {
        global $session;

        $response = jaxon()->getResponse();

        try
        {
            $query = Doctrine::createQuery("SELECT c.name FROM Lotgd\Core\Entity\Bans b, LotgdCore:User a
            LEFT JOIN LotgdCore:Avatar c WITH c.acct = a.acctid
            WHERE
                (b.ipfilter = :ip AND b.uniqueid = :id) AND
                ( (substring(a.lastip,1,length(b.ipfilter)) = b.ipfilter AND b.ipfilter != '') OR (a.uniqueid = b.uniqueid AND b.uniqueid != '') )
            ");

            $query->setParameter('id', $id)
                ->setParameter('ip', $ip)
            ;

            $result = $query->execute();
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            $result = [];
        }
        // The dialog buttons
        $buttons = [
            [
                'title' => LotgdTranslator::t('modal.buttons.cancel', [], 'app_default'),
                'class' => 'ui red deny button',
            ],
        ];

        $content = '';

        foreach ($result as $acct)
        {
            $content .= LotgdFormat::colorize($acct['name'], true).'<br>';
        }

        // Show the dialog
        $response->dialog->show('', ['content' => $content ?: '---'], $buttons);

        return $response;
    }
}
