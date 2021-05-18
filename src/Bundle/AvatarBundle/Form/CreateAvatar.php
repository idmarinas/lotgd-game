<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\AvatarBundle\Form;

use Laminas\Filter;
use Lotgd\Bundle\AvatarBundle\Entity\Avatar;
use Lotgd\Bundle\CoreBundle\Filter\SanitizeLotgdCodes;
use Lotgd\Bundle\CoreBundle\Tool\Censor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreateAvatar extends AbstractType
{
    private $censor;

    public function __construct(Censor $censor)
    {
        $this->censor = $censor;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'   => 'form.field.name',
                'filters' => [
                    new SanitizeLotgdCodes(),
                    new Filter\StripTags(),
                    new Filter\StripNewlines(),
                ],
                'constraints' => [
                    new Assert\Callback(function ($value, ExecutionContextInterface $context)
                    {
                        $filtered = $this->censor->filter($value);

                        if ($filtered !== $value)
                        {
                            $context->addViolation('lotgd_avatar.form.create_avatar.field.name.censor');
                        }
                    }),
                ],
            ])
            ->add('sex', ChoiceType::class, [
                'label'    => 'form.field.sex.label',
                'expanded' => true,
                'choices'  => [
                    'form.field.sex.option.male'   => 0,
                    'form.field.sex.option.female' => 1,
                ],
            ])

            ->add('submit', SubmitType::class, ['label' => 'form.button.submit'])
        ;

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Avatar::class,
            'translation_domain' => 'lotgd_avatar_page_create',
        ]);
    }
}
