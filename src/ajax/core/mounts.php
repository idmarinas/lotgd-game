<?php

namespace Lotgd\Ajax\Core;

use Jaxon\Response\Response;

class Mounts
{
    public function getListOfOwners(int $mountId)
    {
        global $session;

        //-- Do nothing if there is no active session
        if (! $session['user']['loggedin'])
        {
            return $response;
        }

        $response = new Response();

        try
        {
            $repository= \Doctrine::getRepository('LotgdCore:Characters');
            $entities = $repository->findBy(['hashorse' => $mountId]);
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            $entities = [];
        }
        // The dialog buttons
        $buttons = [
            [
                'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                'class' => 'ui red deny button'
            ]
        ];

        $content = '';
        foreach($entities as $char)
        {
            $content .= \appoencode($char->getName(), true).'<br>';
        }

        // Show the dialog
        $response->dialog->show('', ['content' => $content ?: '---'], $buttons);

        return $response;
    }
}
