<?php

namespace Lotgd\Ajax\Core;

use Lotgd\Core\AjaxAbstract;
use Jaxon\Response\Response;

class Mounts extends AjaxAbstract
{
    public function getListOfOwners(int $mountId)
    {
        $check = $this->checkLoggedInRedirect();

        if (true !== $check)
        {
            return $check;
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
