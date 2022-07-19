<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\Form\Type;

use Lotgd\Core\Event\Clan;
use Lotgd\Core\Tool\Sanitize;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClanRankType extends ChoiceType
{
    private $eventDispatcher;
    private $sanitize;
    private $translator;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        // Inserted for v1.1.0 Dragonprime Edition to extend clan possibilities
        $ranks = [
            CLAN_APPLICANT      => 'ranks.00',
            CLAN_MEMBER         => 'ranks.010',
            CLAN_OFFICER        => 'ranks.020',
            CLAN_ADMINISTRATIVE => 'ranks.025',
            CLAN_LEADER         => 'ranks.030',
            CLAN_FOUNDER        => 'ranks.031',
        ];
        $ranksResult = new Clan(['ranks' => $ranks, 'textDomain' => 'page_clan', 'clanid' => null]);
        $this->eventDispatcher->dispatch($ranksResult, Clan::RANK_LIST);
        $ranksResult = $ranksResult->getData();
        $ranks       = $ranksResult['ranks'];

        $choices = [];

        foreach ($ranks as $rankId => $rankName)
        {
            $choices[$this->sanitize->fullSanitize($this->translator->trans($rankName, [], $ranksResult['textDomain']))] = $rankId;
        }

        $resolver->setDefaults([
            'attr' => [
                'class' => 'search selection lotgd',
            ],
            'choices' => $choices,
        ]);

        return $resolver;
    }

    /** @required */
    public function setDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $this->eventDispatcher = $dispatcher;

        return $this;
    }

    /** @required */
    public function setSanitize(Sanitize $sanitize): self
    {
        $this->sanitize = $sanitize;

        return $this;
    }

    /** @required */
    public function setTranslator(TranslatorInterface $translatorInterface): self
    {
        $this->translator = $translatorInterface;

        return $this;
    }
}
