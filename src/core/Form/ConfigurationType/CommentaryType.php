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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CommentaryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Clean user posts (filters bad language and splits words over 45 chars long)
            ->add('soap', CheckboxType::class, [
                'required' => false,
                'label'    => 'commentary.soap',
            ])
            // Max # of color changes usable in one comment
            ->add('maxcolors', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min' => 5,
                    'max' => 40,
                ],
                'empty_data'  => 5,
                'label'       => 'commentary.maxcolors',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                ],
            ])
            // Limit posts to let one user post only up to 50% of the last posts (else turn it off)
            ->add('postinglimit', CheckboxType::class, [
                'required' => false,
                'label'    => 'commentary.postinglimit',
            ])
            // Length of the chatline in chars
            ->add('maxcolors', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 5,
                    'max'                   => 1000,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 40,
                'label'       => 'commentary.maxcolors',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                ],
            ])
            // Number of maximum chars for a single chat line
            ->add('maxchars', RangeType::class, [
                'required' => false,
                'attr'     => [
                    'min'                   => 50,
                    'max'                   => 1000,
                    'disable_slider_labels' => true,
                ],
                'empty_data'  => 50,
                'label'       => 'commentary.maxchars',
                'constraints' => [
                    new Assert\DivisibleBy(1),
                ],
            ])
            // Sections to exclude from comment moderation
            ->add('moderateexcludes', TextareaType::class, [
                'required'    => false,
                'empty_data'  => '',
                'label'       => 'commentary.moderateexcludes',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 255]),
                    new Assert\Blank(),
                    // new Assert\AtLeastOneOf([
                    //     'constraints' => [
                    //         new Assert\Length(['min' => 0, 'max' => 255]),
                    //         new Assert\Blank(),
                    //     ],
                    // ]),
                ],
                'filters' => [
                    new StripTags(),
                ],
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
