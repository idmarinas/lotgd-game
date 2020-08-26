<?php

namespace Lotgd\Ajax\Core;

use Lotgd\Core\AjaxAbstract;

class Bans extends AjaxAbstract
{
    public function showAffected($ip, $id)
    {
        global $session;

        $response = new \Jaxon\Response\Response();

        try
        {
            $query = \Doctrine::createQuery("SELECT c.name FROM Lotgd\Core\Entity\Bans b, Lotgd\Core\Entity\Accounts a
            LEFT JOIN Lotgd\Core\Entity\Characters c WITH c.acct = a.acctid
            WHERE
                (b.ipfilter = :ip AND b.uniqueid = :id) AND
                ( (substring(a.lastip,1,length(b.ipfilter)) = b.ipfilter AND b.ipfilter != '') OR (a.uniqueid = b.uniqueid AND b.uniqueid != '') )
            ");

            $query->setParameter('id', $id)
                ->setParameter('ip', $ip)
            ;

            $result = $query->execute();
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            $result = [];
        }
        // The dialog buttons
        $buttons = [
            [
                'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                'class' => 'ui red deny button'
            ]
        ];

        $content = '';
        foreach($result as $acct)
        {
            $content .= \appoencode($acct['name'], true).'<br>';
        }

        // Show the dialog
        $response->dialog->show('', ['content' => $content ?: '---'], $buttons);

        return $response;
    }
}
