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

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronjobListType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $files = \glob('cronjob/{*/,}*.php', GLOB_BRACE);

        $defaultChoice = ['none' => null];

        foreach ($files as $file)
        {
            $defaultChoice[$file] = \str_replace('.php', '', $file);
        }

        $resolver->setDefaults([
            'attr' => [
                'class' => 'search selection lotgd',
            ],
            'choices' => $defaultChoice,
        ]);

        return $resolver;
    }
}
