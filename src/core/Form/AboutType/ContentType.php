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

namespace Lotgd\Core\Form\AboutType;

use Lotgd\Core\Form\Type\ViewOnlyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Days to keep comments and news?  (0 = infinite)
            ->add('expirecontent', ViewOnlyType::class, [
                'label'        => 'content.expirecontent',
                'apply_filter' => 'numeral',
            ])
            // Days to keep never logged-in accounts? (0 = infinite)
            ->add('expiretrashacct', ViewOnlyType::class, [
                'label'        => 'content.expiretrashacct',
                'apply_filter' => 'numeral',
            ])
            // Days to keep 1 level (0 dragon) accounts? (0 =infinite)
            ->add('expirenewacct', ViewOnlyType::class, [
                'label'        => 'content.expirenewacct',
                'apply_filter' => 'numeral',
            ])
            // Days to keep all other accounts? (0 = infinite)
            ->add('expireoldacct', ViewOnlyType::class, [
                'label'        => 'content.expireoldacct',
                'apply_filter' => 'numeral',
            ])
            // Seconds of inactivity before auto-logoff
            ->add('LOGINTIMEOUT', ViewOnlyType::class, [
                'label'        => 'content.LOGINTIMEOUT',
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
