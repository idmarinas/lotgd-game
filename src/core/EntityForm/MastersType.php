<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\EntityForm;

use Lotgd\Core\Entity\Masters;
use Lotgd\Core\Entity\MastersTranslation;
use Lotgd\Core\EntityForm\Masters as CreaturesFieldType;
use Lotgd\Core\Form\Type\TranslatableFieldType;
use Lotgd\Core\Lib\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MastersType extends AbstractType
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creaturelevel', RangeType::class, [
                'label' => 'creaturelevel',
                'attr'  => [
                    'min'                   => 1,
                    'max'                   => $this->settings->getSetting('maxlevel'),
                    'disable_slider_labels' => false,
                    'step'                  => 1,
                ],
            ])
            ->add('creaturename', TranslatableFieldType::class, [
                'personal_translation' => MastersTranslation::class,
                'widget'               => CreaturesFieldType\NameTranslationType::class,
                'field'                => 'creaturename',
                'label'                => 'creaturename',
            ])
            ->add('creatureweapon', TranslatableFieldType::class, [
                'personal_translation' => MastersTranslation::class,
                'widget'               => CreaturesFieldType\WeaponTranslationType::class,
                'field'                => 'creatureweapon',
                'label'                => 'creatureweapon',
            ])
            ->add('creaturelose', TranslatableFieldType::class, [
                'personal_translation' => MastersTranslation::class,
                'widget'               => CreaturesFieldType\LoseTranslationType::class,
                'field'                => 'creaturelose',
                'label'                => 'creaturelose',
            ])
            ->add('creaturewin', TranslatableFieldType::class, [
                'personal_translation' => MastersTranslation::class,
                'widget'               => CreaturesFieldType\WinTranslationType::class,
                'field'                => 'creaturewin',
                'label'                => 'creaturewin',
            ])
            ->add('save', SubmitType::class, ['label' => 'save.button'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Masters::class,
            'translation_domain' => 'form_core_master',
        ]);
    }
}
