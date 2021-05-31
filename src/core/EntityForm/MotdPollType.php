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

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MotdPollType extends MotdType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('opt', CollectionType::class, [
                'mapped'        => false,
                'label'         => 'poll.choice',
                'entry_type'    => TextType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'delete_empty'  => true,
                'entry_options' => [
                    'label' => 'poll.opt',
                ],
                'data'        => ['', '', ''],
                'constraints' => [
                    new Assert\Count(['min' => 2, 'max' => 50]),
                    new Assert\Unique(),
                ],
            ])
        ;
    }
}
