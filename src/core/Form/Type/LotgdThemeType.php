<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form\Type;

use LotgdKernel;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LotgdThemeType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'attr' => [
                'class' => 'search selection lotgd',
            ],
            'choices' => $this->getThemeList(),
        ]);

        return $resolver;
    }

    /**
     * Get list array.
     */
    private function getThemeList(): array
    {
        $cache = LotgdKernel::get('cache.app');
        $item  = $cache->getItem('lotgd-core-pattern-theme-list');

        if ( ! $item->isHit())
        {
            // A generic way of allowing a theme to be selected.
            $handle = @\opendir('themes');

            // Template directory open failed
            if ( ! $handle)
            {
                return [];
            }

            $skins = [];

            while (false !== ($file = @\readdir($handle)))
            {
                if ('html' == \pathinfo($file, PATHINFO_EXTENSION))
                {
                    $value         = \str_replace(['-', '_'], ' ', \ucfirst(\substr($file, 0, \strpos($file, '.htm'))));
                    $skins[$value] = $file;
                }
            }

            $item->expiresAt(new DateTime('tomorrow'));
            $item->set($skins);
            $cache->save($item);
        }

        return $item->get();
    }
}
