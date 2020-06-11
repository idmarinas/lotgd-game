<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.2.0
 */

namespace Lotgd\Core\EntityForm\Creatures;

use Lotgd\Core\Entity\CreaturesTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DescriptionTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('locale', HiddenType::class)
            ->add('field', HiddenType::class)
            ->add('content', TextareaType::class, [
                'constraints' => [new Assert\Length(['min' => 0, 'max' => 65535, 'allowEmptyString' => true])],
                'error_bubbling' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreaturesTranslation::class
        ]);
    }
}
