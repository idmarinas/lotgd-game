<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.8.0
 */

namespace Lotgd\Core\Form;

use Laminas\Filter\StripTags;
use Lotgd\Core\Form\Type\PetitionTypesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PetitionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('charname', TextType::class, [
                'label'       => 'charname',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 100]),
                ],
                'filters' => [
                    new StripTags(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'email',
                'constraints' => [
                    new Assert\Email(),
                ],
            ])
            ->add('problem_type', PetitionTypesType::class, [
                'label' => 'petition.type',
            ])
            ->add('description', TextareaType::class, [
                'label'       => 'description',
                'constraints' => [
                    new Assert\Length(['min' => 0, 'max' => 65000]),
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
            'translation_domain' => 'form_core_petition',
        ]);
    }
}
