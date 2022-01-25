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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PlacesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = [
            new Assert\Length(['min' => 3, 'max' => 255]),
        ];

        $filters = [
            new StripTags(),
        ];

        $builder
            // Name for the main village
            ->add('villagename', TextType::class, [
                'label'       => 'places.villagename',
                'filters'     => $filters,
                'constraints' => $constraints,
            ])
            // Name of the inn
            ->add('innname', TextType::class, [
                'label'       => 'places.innname',
                'filters'     => $filters,
                'constraints' => $constraints,
            ])
            // Name of the barkeep
            ->add('barkeep', TextType::class, [
                'label'       => 'places.barkeep',
                'filters'     => $filters,
                'constraints' => $constraints,
            ])
            // Name of the barmaid
            ->add('barmaid', TextType::class, [
                'label'       => 'places.barmaid',
                'filters'     => $filters,
                'constraints' => $constraints,
            ])
            // Name of the bard
            ->add('bard', TextType::class, [
                'label'       => 'places.bard',
                'filters'     => $filters,
                'constraints' => $constraints,
            ])
            // Name of the clan registrar
            ->add('clanregistrar', TextType::class, [
                'label'       => 'places.clanregistrar',
                'filters'     => $filters,
                'constraints' => $constraints,
            ])
            // Name of the banker
            ->add('bankername', TextType::class, [
                'label'       => 'places.bankername',
                'filters'     => $filters,
                'constraints' => $constraints,
            ])
            // Name of the death overlord
            ->add('deathoverlord', TextType::class, [
                'label'       => 'places.deathoverlord',
                'filters'     => $filters,
                'constraints' => $constraints,
            ])
        ;

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
