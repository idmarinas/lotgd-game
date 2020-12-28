<?php
/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Ajax\Pattern\Core\Petition;

use Jaxon\Response\Response;
use Tracy\Debugger;

trait Faq
{
    public function faq(?int $faq = null)
    {
        $response = new Response();
        $params   = $this->getParams();

        try
        {
            // Dialog title
            $title = \LotgdTranslator::t('title.faq', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                    'class' => 'ui red deny button',
                ],
            ];

            //-- Options
            $options = [
                'autofocus' => false,
            ];

            if ($faq)
            {
                $faq     = \max(1, \min(4, $faq));
                $content = $this->getTemplate()->renderBlock("petition_faq{$faq}", $params);
            }
            else
            {
                $params['faqList'] = $this->faqToc();

                // Dialog content
                $content = $this->getTemplate()->renderBlock('petition_faq', $params);
            }

            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);
        }

        $response->jQuery('#village-petition-faq')->removeClass('disabled');

        return $response;
    }

    public function primer()
    {
        $response = new Response();
        $params   = $this->getParams();

        try
        {
            // Dialog title
            $title = \LotgdTranslator::t('title.faq', [], $this->getTextDomain());

            // The dialog buttons
            $buttons = [
                [
                    'title' => \LotgdTranslator::t('modal.buttons.cancel', [], 'app-default'),
                    'class' => 'ui red deny button',
                ],
            ];

            //-- Options
            $options = [
                'autofocus' => false,
            ];

            $params['pvp']           = getsetting('pvp', 1);
            $params['deathOverlord'] = getsetting('deathoverlord', '`$Ramius`0');
            $params['pvpImmunity']   = getsetting('pvpimmunity', 5);
            $params['pvpMinExp']     = getsetting('pvpminexp', 1500);
            $params['pvpDeflose']    = getsetting('pvpdeflose', 5);
            $params['pvpAttGain']    = getsetting('pvpattgain', 10);
            $params['pvpAttLose']    = getsetting('pvpattlose', 15);
            $params['pvpDefGain']    = getsetting('pvpdefgain', 10);

            // Dialog content
            $content = $this->getTemplate()->renderBlock('petition_primer', $params);

            $response->dialog->show($title, ['content' => $content, 'isScrollable' => true], $buttons, $options);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);
        }

        $response->jQuery('#village-petition-faq')->removeClass('disabled');

        return $response;
    }

    /**
     * Creaqte list of faqs.
     */
    private function faqToc(): array
    {
        \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_PETITION_FAQ_TOC);

        return modulehook('faq-toc', [
            [
                'onclick' => 'JaxonLotgd.Ajax.Core.Petition.primer()',
                'link'    => [
                    'section.faq.toc.primer',
                    [],
                    $this->getTextDomain(),
                ],
            ],
            [
                'onclick' => 'JaxonLotgd.Ajax.Core.Petition.faq(1)',
                'link'    => [
                    'section.faq.toc.general',
                    [],
                    $this->getTextDomain(),
                ],
            ],
            [
                'onclick' => 'JaxonLotgd.Ajax.Core.Petition.faq(2)',
                'link'    => [
                    'section.faq.toc.spoiler',
                    [],
                    $this->getTextDomain(),
                ],
            ],
            [
                'onclick' => 'JaxonLotgd.Ajax.Core.Petition.faq(3)',
                'link'    => [
                    'section.faq.toc.technical',
                    [],
                    $this->getTextDomain(),
                ],
            ],
        ]);
    }
}
