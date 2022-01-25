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

namespace Lotgd\Core\Form\ConfigurationType;

use Laminas\Filter\StripTags;
use Laminas\Filter;
use Lotgd\Core\Form\Type\CheckboxType;
use Lotgd\Core\Form\Type\LotgdThemeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class HomeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('homeskinselect', CheckboxType::class, [
            'required' => false,
            'label'    => 'home.homeskinselect',
        ]);
        $builder->add('homecurtime', CheckboxType::class, [
            'required' => false,
            'label'    => 'home.homecurtime',
        ]);
        $builder->add('homenewdaytime', CheckboxType::class, [
            'required' => false,
            'label'    => 'home.homenewdaytime',
        ]);
        $builder->add('homenewestplayer', CheckboxType::class, [
            'required' => false,
            'label'    => 'home.homenewestplayer',
        ]);
        $builder->add('defaultskin', LotgdThemeType::class, [
            'label' => 'home.defaultskin',
        ]);
        $builder->add('impressum', TextareaType::class, [
            'required'    => false,
            'label'       => 'home.impressum',
            'empty_data'  => '',
            'constraints' => [
                new Assert\Length(['min' => 0, 'max' => 255, 'allowEmptyString' => true]),
            ],
            'filters' => [
                new StripTags(),
            ],
        ]);

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'translation_domain' => 'form_core_configuration',
        ]);
    }
}
