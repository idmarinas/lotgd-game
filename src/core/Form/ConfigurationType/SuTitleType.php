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
use Lotgd\Core\Form\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class SuTitleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = [
            new Length(['min' => 3, 'max' => 255]),
        ];
        $filters = [
            new StripTags(),
        ];

        $builder
            // Enable chat tags in general
            ->add('enable_chat_tags', CheckboxType::class, [
                'required' => false,
                'label'    => 'su.title.enable_chat_tags.label',
                'help'     => 'su.title.enable_chat_tags.note',
            ])
            // Title for the mega user
            ->add('chat_tag_megauser', TextType::class, [
                'required'    => false,
                'label'       => 'su.title.chat_tag_megauser',
                'constraints' => $constraints,
                'filters'     => $filters,
            ])
            // Name for a GM
            ->add('chat_tag_gm', TextType::class, [
                'required'    => false,
                'label'       => 'su.title.chat_tag_gm',
                'constraints' => $constraints,
                'filters'     => $filters,
            ])
            // Name for a Mod
            ->add('chat_tag_mod', TextType::class, [
                'required'    => false,
                'label'       => 'su.title.chat_tag_mod',
                'constraints' => $constraints,
                'filters'     => $filters,
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
