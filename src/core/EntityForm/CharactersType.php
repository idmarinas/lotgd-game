<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.3.0
 */

namespace Lotgd\Core\EntityForm;

use Lotgd\Core\Entity\Characters;
use Lotgd\Core\Form\Type\ClanRankType;
use Lotgd\Core\Form\Type\RaceType;
use Lotgd\Core\Form\Type\SpecialtyType;
use Symfony\Component\Form\AbstractType;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('acct', ChoiceType::class, [
                'label'        => 'char.acct',
                'choices'      => \Doctrine::getRepository('LotgdCore:Accounts')->findAll(),
                'choice_value' => 'acctid',
                'choice_label' => function ($account)
                {
                    return $account->getAcctid().') '.$account->getLogin();
                },
            ])
            ->add('lasthit', DateType::class, ['label' => 'char.lasthit', 'required' => false, 'disabled' => true])
            ->add('name', TextType::class, [
                'label'    => 'char.name',
                'required' => false,
                'disabled' => true,
            ])
            ->add('title', TextType::class, [
                'label'    => 'char.title',
                'required' => false,
                'disabled' => ! ((bool) getsetting('edittitles', 1)),
            ])
            ->add('ctitle', TextType::class, [
                'label'    => 'char.ctitle',
                'required' => false,
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
                'label'    => 'char.dragonkills',
                'required' => false,
            ])
            ->add('age', NumberType::class, ['label' => 'char.age', 'required' => false])
            ->add('dragonage', NumberType::class, ['label' => 'char.dragonage', 'required' => false])
            ->add('bestdragonage', NumberType::class, ['label' => 'char.bestdragonage', 'required' => false])
            ->add('pk', CheckboxType::class, ['label' => 'char.pk', 'required' => false])

            ->add('level', NumberType::class, ['label' => 'char.level'])
            ->add('race', RaceType::class, ['label' => 'char.race'])
            ->add('experience', NumberType::class, ['label' => 'char.experience'])
            ->add('hitpoints', NumberType::class, ['label' => 'char.hitpoints'])
            ->add('maxhitpoints', NumberType::class, ['label' => 'char.maxhitpoints'])
            ->add('permahitpoints', NumberType::class, ['label' => 'char.permahitpoints'])
            ->add('strength', NumberType::class, ['label' => 'char.strength'])
            ->add('dexterity', NumberType::class, ['label' => 'char.dexterity'])
            ->add('intelligence', NumberType::class, ['label' => 'char.intelligence'])
            ->add('constitution', NumberType::class, ['label' => 'char.constitution'])
            ->add('wisdom', NumberType::class, ['label' => 'char.wisdom'])
            ->add('charm', NumberType::class, ['label' => 'char.charm'])
            ->add('attack', NumberType::class, ['label' => 'char.attack'])
            ->add('defense', NumberType::class, ['label' => 'char.defense'])

            ->add('turns', NumberType::class, ['label' => 'char.turns'])
            ->add('playerfights', NumberType::class, ['label' => 'char.playerfights'])
            ->add('spirits', ChoiceType::class, [
                'label'   => 'char.spirits.label',
                'choices' => [
                    'char.spirits.option.resurrected' => '-6',
                    'char.spirits.option.very_low'    => '-2',
                    'char.spirits.option.low'         => '-1',
                    'char.spirits.option.normal'      => '0',
                    'char.spirits.option.high'        => '1',
                    'char.spirits.option.very_high'   => '2',
                ],
            ])
            ->add('resurrections', NumberType::class, ['label' => 'char.resurrections'])
            ->add('location', TextType::class, ['label' => 'char.location'])

            ->add('specialty', SpecialtyType::class, ['label' => 'char.specialty'])

            ->add('deathpower', NumberType::class, ['label' => 'char.deathpower'])
            ->add('gravefights', NumberType::class, ['label' => 'char.gravefights'])
            ->add('soulpoints', NumberType::class, ['label' => 'char.soulpoints'])

            ->add('gems', NumberType::class, ['label' => 'char.gems'])
            ->add('gold', NumberType::class, ['label' => 'char.gold'])
            ->add('goldinbank', NumberType::class, ['label' => 'char.goldinbank'])
            ->add('transferredtoday', NumberType::class, ['label' => 'char.transferredtoday'])
            ->add('amountouttoday', NumberType::class, ['label' => 'char.amountouttoday'])
            ->add('weapon', TextType::class, ['label' => 'char.weapon'])
            ->add('weapondmg', NumberType::class, ['label' => 'char.weapondmg'])
            ->add('weaponvalue', NumberType::class, ['label' => 'char.weaponvalue'])
            ->add('armor', TextType::class, ['label' => 'char.armor'])
            ->add('armordef', NumberType::class, ['label' => 'char.armordef'])
            ->add('armorvalue', NumberType::class, ['label' => 'char.armorvalue'])

            ->add('seendragon', CheckboxType::class, ['label' => 'char.seendragon', 'required' => false])
            ->add('seenmaster', CheckboxType::class, ['label' => 'char.seenmaster', 'required' => false])

            ->add('hashorse', ChoiceType::class, [
                'label'        => 'char.hashorse',
                'required'     => false,
                'empty_data'   => '0',
                'choices'      => \Doctrine::getRepository('LotgdCore:Mounts')->findAll([], ['mountcategory' => 'ASC']),
                'choice_value' => 'mountid',
                'choice_label' => function ($clan)
                {
                    return $clan->getMountid().') '.$clan->getMountname();
                },
            ])
            ->add('fedmount', CheckboxType::class, ['label' => 'char.fedmount', 'required' => false])

            ->add('marriedto', NumberType::class, ['label' => 'char.marriedto', 'required' => false])

            ->add('clanid', ChoiceType::class, [
                'label'        => 'char.clanid',
                'required'     => false,
                'empty_data'   => '0',
                'choices'      => \Doctrine::getRepository('LotgdCore:Clans')->findAll(),
                'choice_value' => 'clanid',
                'choice_label' => function ($clan)
                {
                    return $clan->getClanid().') '.$clan->getClanname();
                },
            ])
            ->add('clanrank', ClanRankType::class, ['label' => 'char.clanrank', 'required' => false])

            ->add('bio', TextareaType::class, ['label' => 'char.bio', 'required' => false])
            ->add('save', SubmitType::class, ['label' => 'save.button'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Characters::class,
            'translation_domain' => 'form-core-grotto-account',
        ]);
    }
}
