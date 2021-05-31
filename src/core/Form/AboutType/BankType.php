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

namespace Lotgd\Core\Form\AboutType;

use Lotgd\Core\Form\Type\ViewOnlyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Max player can borrow per level (val * level for max)
            ->add('borrowperlevel', ViewOnlyType::class, [
                'label'        => 'bank.borrowperlevel',
                'apply_filter' => 'numeral',
            ])
            // Allow players to transfer gold
            ->add('allowgoldtransfer', ViewOnlyType::class, [
                'label'        => 'bank.allowgoldtransfer',
                'apply_filter' => 'yes_no',
            ])
            // Max player can receive from a transfer (val * level)
            ->add('transferperlevel', ViewOnlyType::class, [
                'label'        => 'bank.transferperlevel',
                'apply_filter' => 'numeral',
            ])
            // Min level a player (0 DK's) needs to transfer gold
            ->add('mintransferlev', ViewOnlyType::class, [
                'label'        => 'bank.mintransferlev',
                'apply_filter' => 'numeral',
            ])
            // Total transfers a player can receive in one day
            ->add('transferreceive', ViewOnlyType::class, [
                'label'        => 'bank.transferreceive',
                'apply_filter' => 'numeral',
            ])
            // Amount player can transfer to others (val * level)
            ->add('maxtransferout', ViewOnlyType::class, [
                'label'        => 'bank.maxtransferout',
                'apply_filter' => 'numeral',
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
