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

namespace Lotgd\Core\EntityForm;

use Lotgd\Core\Entity\Motd;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotdEditType extends MotdType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('changeauthor', CheckboxType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'item.change.author',
            ])
            ->add('changedate', CheckboxType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'item.change.date',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Motd::class,
            'translation_domain' => 'form_core_motd',
        ]);
    }
}
