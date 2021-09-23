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

use Lotgd\Core\Entity\Avatar;
use Lotgd\Core\Form\Type\ClanRankType;
use Lotgd\Core\Form\Type\RaceType;
use Lotgd\Core\Form\Type\SpecialtyType;
use Lotgd\Core\Lib\Settings;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CharactersType extends AbstractType
{
    protected $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('acct', EntityType::class, [
                'label'        => 'char.acct',
                'class'        => 'LotgdCore:User',
                'choice_value' => 'acctid',
                'choice_label' => function ($account)
                {
                    return $account->getAcctid().') '.$account->getLogin();
                },
                'attr' => [
                    'class' => 'clearable',
                ],
            ])
            ->add('lasthit', DateType::class, ['label' => 'char.lasthit', 'required' => false, 'disabled' => true])
            ->add('name', TextType::class, [
                'label'    => 'char.name',
                'required' => false,
                'disabled' => true,
            ])
            ->add('title', TextType::class, [
                'label'      => 'char.title',
                'required'   => false,
                'empty_data' => '',
                'disabled'   => ! ((bool) $this->settings->getSetting('edittitles', 1)),
            ])
            ->add('ctitle', TextType::class, [
                'label'      => 'char.ctitle',
                'required'   => false,
                'empty_data' => '',
            ])
            ->add('playername', TextType::class, ['label' => 'char.playername'])
            ->add('sex', ChoiceType::class, [
                'label'   => 'char.sex.label',
                'choices' => [
                    'char.sex.option.male'   => 0,
                    'char.sex.option.female' => 1,
                ],
            ])
            ->add('dragonkills', NumberType::class, [
                'label'      => 'char.dragonkills',
                'empty_data' => 0,
                'required'   => false,
            ])
            ->add('age', NumberType::class, ['label' => 'char.age', 'empty_data' => 0, 'required' => false])
            ->add('dragonage', NumberType::class, ['label' => 'char.dragonage', 'empty_data' => 0, 'required' => false])
            ->add('bestdragonage', NumberType::class, ['label' => 'char.bestdragonage', 'empty_data' => 0, 'required' => false])
            ->add('pk', CheckboxType::class, ['label' => 'char.pk', 'required' => false])

            ->add('level', NumberType::class, ['label' => 'char.level'])
            ->add('race', RaceType::class, ['label' => 'char.race'])
            ->add('experience', NumberType::class, ['empty_data' => 0, 'label' => 'char.experience'])
            ->add('hitpoints', NumberType::class, ['empty_data' => 0, 'label' => 'char.hitpoints'])
            ->add('maxhitpoints', NumberType::class, ['empty_data' => 0, 'label' => 'char.maxhitpoints'])
            ->add('permahitpoints', NumberType::class, ['empty_data' => 0, 'label' => 'char.permahitpoints'])
            ->add('strength', NumberType::class, ['empty_data' => 0, 'label' => 'char.strength'])
            ->add('dexterity', NumberType::class, ['empty_data' => 0, 'label' => 'char.dexterity'])
            ->add('intelligence', NumberType::class, ['empty_data' => 0, 'label' => 'char.intelligence'])
            ->add('constitution', NumberType::class, ['empty_data' => 0, 'label' => 'char.constitution'])
            ->add('wisdom', NumberType::class, ['empty_data' => 0, 'label' => 'char.wisdom'])
            ->add('charm', NumberType::class, ['empty_data' => 0, 'label' => 'char.charm'])
            ->add('attack', NumberType::class, ['empty_data' => 0, 'label' => 'char.attack'])
            ->add('defense', NumberType::class, ['empty_data' => 0, 'label' => 'char.defense'])

            ->add('turns', NumberType::class, ['empty_data' => 0, 'label' => 'char.turns'])
            ->add('playerfights', NumberType::class, ['empty_data' => 0, 'label' => 'char.playerfights'])
            ->add('spirits', ChoiceType::class, [
                'label'   => 'char.spirits.label',
                'choices' => [
                    'char.spirits.option.resurrected' => -6,
                    'char.spirits.option.very_low'    => -2,
                    'char.spirits.option.low'         => -1,
                    'char.spirits.option.normal'      => 0,
                    'char.spirits.option.high'        => 1,
                    'char.spirits.option.very_high'   => 2,
                ],
            ])
            ->add('resurrections', NumberType::class, ['empty_data' => 0, 'label' => 'char.resurrections'])
            ->add('location', TextType::class, ['label' => 'char.location'])

            ->add('specialty', SpecialtyType::class, ['label' => 'char.specialty'])

            ->add('deathpower', NumberType::class, ['empty_data' => 0, 'label' => 'char.deathpower'])
            ->add('gravefights', NumberType::class, ['empty_data' => 0, 'label' => 'char.gravefights'])
            ->add('soulpoints', NumberType::class, ['empty_data' => 0, 'label' => 'char.soulpoints'])

            ->add('gems', NumberType::class, ['empty_data' => 0, 'label' => 'char.gems'])
            ->add('gold', NumberType::class, ['empty_data' => 0, 'label' => 'char.gold'])
            ->add('goldinbank', NumberType::class, ['empty_data' => 0, 'label' => 'char.goldinbank'])
            ->add('transferredtoday', NumberType::class, ['empty_data' => 0, 'label' => 'char.transferredtoday'])
            ->add('amountouttoday', NumberType::class, ['empty_data' => 0, 'label' => 'char.amountouttoday'])
            ->add('weapon', TextType::class, ['label' => 'char.weapon'])
            ->add('weapondmg', NumberType::class, ['empty_data' => 0, 'label' => 'char.weapondmg'])
            ->add('weaponvalue', NumberType::class, ['empty_data' => 0, 'label' => 'char.weaponvalue'])
            ->add('armor', TextType::class, ['label' => 'char.armor'])
            ->add('armordef', NumberType::class, ['empty_data' => 0, 'label' => 'char.armordef'])
            ->add('armorvalue', NumberType::class, ['empty_data' => 0, 'label' => 'char.armorvalue'])

            ->add('seendragon', CheckboxType::class, ['label' => 'char.seendragon', 'required' => false])
            ->add('seenmaster', CheckboxType::class, ['label' => 'char.seenmaster', 'required' => false])

            ->add('hashorse', EntityType::class, [
                'label'        => 'char.hashorse',
                'required'     => false,
                'class'        => 'LotgdCore:Mounts',
                'group_by'     => 'mountcategory',
                'choice_value' => 'mountid',
                'choice_label' => function ($mount)
                {
                    return $mount->getMountid().') '.$mount->getMountname();
                },
                'attr' => [
                    'class' => 'clearable',
                ],
            ])
            ->add('fedmount', CheckboxType::class, ['label' => 'char.fedmount', 'required' => false])

            ->add('marriedto', NumberType::class, ['label' => 'char.marriedto', 'empty_data' => 0, 'required' => false])

            ->add('clanid', EntityType::class, [
                'label'        => 'char.clanid',
                'required'     => false,
                'class'        => 'LotgdCore:Clans',
                'choice_value' => 'clanid',
                'choice_label' => function ($clan)
                {
                    return $clan->getClanid().') '.$clan->getClanname();
                },
                'attr' => [
                    'class' => 'clearable',
                ],
            ])
            ->add('clanrank', ClanRankType::class, ['label' => 'char.clanrank', 'required' => false])

            ->add('bio', TextareaType::class, ['label' => 'char.bio', 'empty_data' => '', 'required' => false])
            ->add('save', SubmitType::class, ['label' => 'save.button'])
        ;

        $builder->get('hashorse')
            ->addModelTransformer(new CallbackTransformer(
                function ($toInt)
                {
                    return \is_object($toInt) ? $toInt->getMountid() : $toInt;
                },
                function ($toInt)
                {
                    // transform the string back to an array
                    return \is_object($toInt) ? $toInt->getMountid() : $toInt;
                }
            ))
        ;
        $builder->get('clanid')
            ->addModelTransformer(new CallbackTransformer(
                function ($toInt)
                {
                    return \is_object($toInt) ? $toInt->getClanid() : $toInt;
                },
                function ($toInt)
                {
                    // transform the string back to an array
                    return \is_object($toInt) ? $toInt->getClanid() : $toInt;
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Avatar::class,
            'translation_domain' => 'form_core_account',
        ]);
    }
}
