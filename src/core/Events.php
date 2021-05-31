<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.3.0
 */

namespace Lotgd\Core;

/**
 * Events available in LoTGD Core. See also events in 'src/core/Event/'.
 *
 * This events recibe a instance of Symfony\Component\EventDispatcher\GenericEvent.
 */
class Events
{
    /**
     * Payment events.
     */
    public const PAYMENT_DONATION_SUCCESS    = 'lotgd.payment.donation.success';
    public const PAYMENT_DONATION_ERROR      = 'lotgd.payment.donation.error';
    public const PAYMENT_DONATION_ADJUSTMENT = 'lotgd.payment.donation.adjustment';

    /**
     * Special events.
     */
    public const EVENTS_COLLECT = 'lotgd.events.collect';

    /**
     * Pages.
     */
    public const PAGE_ABOUT      = 'lotgd.page.about';
    public const PAGE_ABOUT_POST = 'lotgd.page.about.post';

    public const PAGE_ACCOUNTS_STATS = 'lotgd.page.accounts.stats';
    public const PAGE_ACCOUNTS_POST  = 'lotgd.page.accounts.post';

    public const PAGE_ARMOR_PRE  = 'lotgd.page.armor.pre';
    public const PAGE_ARMOR_POST = 'lotgd.page.armor.post';

    public const PAGE_BANK_PRE  = 'lotgd.page.bank.pre';
    public const PAGE_BANK_POST = 'lotgd.page.bank.post';

    public const PAGE_BIO_POST = 'lotgd.page.bio.post';

    public const PAGE_BATTLE_TURN_START           = 'lotgd.page.battle.turn.start';
    public const PAGE_BATTLE_TURN_END             = 'lotgd.page.battle.turn.end';
    public const PAGE_BATTLE_ROUND_START_BUFF_PRE = 'lotgd.page.battle.round_start_buff_pre';
    public const PAGE_BATTLE_ROUND_END            = 'lotgd.page.battle.round_end';
    public const PAGE_BATTLE_PAGE_END             = 'lotgd.page.battle.page_end';
    public const PAGE_BATTLE_END_VICTORY          = 'lotgd.page.battle.end_victory';
    public const PAGE_BATTLE_END_DEFEAT           = 'lotgd.page.battle.end_defeat';

    public const PAGE_CLAN_PRE  = 'lotgd.page.clan.pre';
    public const PAGE_CLAN_POST = 'lotgd.page.clan.post';

    public const PAGE_CREATE_PRE              = 'lotgd.page.create.pre';
    public const PAGE_CREATE_POST             = 'lotgd.page.create.post';
    public const PAGE_CREATE_CHECK_CREATION   = 'lotgd.page.create.check.creation';
    public const PAGE_CREATE_FORGOT           = 'lotgd.page.create.forgot';
    public const PAGE_CREATE_VAL              = 'lotgd.page.create.val';
    public const PAGE_CREATE_FORGOTVAL        = 'lotgd.page.create.forgotval';
    public const PAGE_CREATE_FORM             = 'lotgd.page.create.form';
    public const PAGE_CREATE_PROCESS_CREATION = 'lotgd.page.create.process.creation';

    public const PAGE_DRAGON_PRE         = 'lotgd.page.dragon.pre';
    public const PAGE_DRAGON_POST        = 'lotgd.page.dragon.post';
    public const PAGE_DRAGON_DEATH       = 'lotgd.page.dragon.death';
    public const PAGE_DRAGON_BUFF        = 'lotgd.page.dragon.buff';
    public const PAGE_DRAGON_KILL        = 'lotgd.page.dragon.kill';
    public const PAGE_DRAGON_DK_PRESERVE = 'lotgd.page.dragon.dk.preserve';
    public const PAGE_DRAGON_HP_RECALC   = 'lotgd.page.dragon.hp.recalc';

    public const PAGE_FOREST                  = 'lotgd.page.forest';
    public const PAGE_FOREST_PRE              = 'lotgd.page.forest.pre';
    public const PAGE_FOREST_POST             = 'lotgd.page.forest.post';
    public const PAGE_FOREST_SEARCH           = 'lotgd.page.forest.search';
    public const PAGE_FOREST_SOBERUP          = 'lotgd.page.forest.soberup';
    public const PAGE_FOREST_FIGHT_START      = 'lotgd.page.forest.fight.start';
    public const PAGE_FOREST_HEADER           = 'lotgd.page.forest.header';
    public const PAGE_FOREST_VALID_FOREST_LOC = 'lotgd.page.forest.valid.forest.loc';

    public const PAGE_GARDEN      = 'lotgd.page.garden';
    public const PAGE_GARDEN_PRE  = 'lotgd.page.garden.pre';
    public const PAGE_GARDEN_POST = 'lotgd.page.garden.post';

    public const PAGE_GRAVEYARD      = 'lotgd.page.graveyard';
    public const PAGE_GRAVEYARD_PRE  = 'lotgd.page.graveyard.pre';
    public const PAGE_GRAVEYARD_POST = 'lotgd.page.graveyard.post';
    public const PAGE_GRAVEYARD_HEAL = 'lotgd.page.graveyard.heal';

    public const PAGE_GYPSY      = 'lotgd.page.gypsy';
    public const PAGE_GYPSY_PRE  = 'lotgd.page.gypsy.pre';
    public const PAGE_GYPSY_POST = 'lotgd.page.gypsy.post';

    public const PAGE_HEALER          = 'lotgd.page.healer';
    public const PAGE_HEALER_PRE      = 'lotgd.page.healer.pre';
    public const PAGE_HEALER_POST     = 'lotgd.page.healer.post';
    public const PAGE_HEALER_MULTIPLY = 'lotgd.page.healer.multiply';
    public const PAGE_HEALER_POTION   = 'lotgd.page.healer.potion';

    public const PAGE_HOF      = 'lotgd.page.hof';
    public const PAGE_HOF_PRE  = 'lotgd.page.hof.pre';
    public const PAGE_HOF_POST = 'lotgd.page.hof.post';
    public const PAGE_HOF_ADD  = 'lotgd.page.hof.add';

    public const PAGE_HOME        = 'lotgd.page.home';
    public const PAGE_HOME_MIDDLE = 'lotgd.page.home.middle';
    public const PAGE_HOME_TEXT   = 'lotgd.page.home.text';
    public const PAGE_HOME_POST   = 'lotgd.page.home.post';

    public const PAGE_INN                    = 'lotgd.page.inn';
    public const PAGE_INN_PRE                = 'lotgd.page.inn.pre';
    public const PAGE_INN_POST               = 'lotgd.page.inn.post';
    public const PAGE_INN_ROOMS              = 'lotgd.page.inn.rooms';
    public const PAGE_INN_CHATTER            = 'lotgd.page.inn.chatter';
    public const PAGE_INN_BLOCK_COMMENT_AREA = 'lotgd.page.inn.block.comment.area';

    public const PAGE_LIST_POST = 'lotgd.page.list.post';

    public const PAGE_LODGE      = 'lotgd.page.lodge';
    public const PAGE_LODGE_PRE  = 'lotgd.page.lodge.pre';
    public const PAGE_LODGE_POST = 'lotgd.page.lodge.post';

    public const PAGE_MERCENARY_CAMP_PRE  = 'lotgd.page.mercenary.camp.pre';
    public const PAGE_MERCENARY_CAMP_POST = 'lotgd.page.mercenary.camp.post';

    public const PAGE_NEWDAY                 = 'lotgd.page.newday';
    public const PAGE_NEWDAY_PRE             = 'lotgd.page.newday.pre';
    public const PAGE_NEWDAY_POST            = 'lotgd.page.newday.post';
    public const PAGE_NEWDAY_INTERCEPT       = 'lotgd.page.newday.intercept';
    public const PAGE_NEWDAY_DK_POINT_LABELS = 'lotgd.page.newday.dk.point.labels';

    public const PAGE_NAVIGATION_VILLAGE = 'lotgd.page.navigation.village';

    public const PAGE_NEWS_INTERCEPT = 'lotgd.page.news.intercept';
    public const PAGE_NEWS_POST      = 'lotgd.page.news.post';

    public const PAGE_PAYLOG = 'lotgd.page.paylog';

    public const PAGE_PREFS_CHECK  = 'lotgd.page.prefs.check';
    public const PAGE_PREFS_CHANGE = 'lotgd.page.prefs.change';
    public const PAGE_PREFS_POST   = 'lotgd.page.prefs.post';

    public const PAGE_PVP_POST = 'lotgd.page.pvp.post';

    public const PAGE_REFERRAL_POST = 'lotgd.page.referral.post';

    public const PAGE_ROCK_PRE  = 'lotgd.page.rock.pre';
    public const PAGE_ROCK_POST = 'lotgd.page.rock.post';

    public const PAGE_SHADES_PRE  = 'lotgd.page.shades.pre';
    public const PAGE_SHADES_POST = 'lotgd.page.shades.post';

    public const PAGE_STABLES_PRE    = 'lotgd.page.stables.pre';
    public const PAGE_STABLES_POST   = 'lotgd.page.stables.post';
    public const PAGE_STABLES_MOUNT  = 'lotgd.page.stables.mount';
    public const PAGE_STABLES_BOUGHT = 'lotgd.page.stables.bought';
    public const PAGE_STABLES_SOLD   = 'lotgd.page.stables.sold';

    public const PAGE_TRAIN_PRE             = 'lotgd.page.train.pre';
    public const PAGE_TRAIN_POST            = 'lotgd.page.train.post';
    public const PAGE_TRAIN_AUTOCHALLENGE   = 'lotgd.page.train.autochallenge';
    public const PAGE_TRAIN_TRANING_VICTORY = 'lotgd.page.train.traning.victory';
    public const PAGE_TRAIN_TRANING_DEFEAT  = 'lotgd.page.train.traning.defeat';

    public const PAGE_USER_VALIDATE_PREFS = 'lotgd.page.user.validate.prefs';

    public const PAGE_PETITION_STATUS = 'lotgd.page.petition.status';

    public const PAGE_VILLAGE          = 'lotgd.page.village';
    public const PAGE_VILLAGE_PRE      = 'lotgd.page.village.pre';
    public const PAGE_VILLAGE_POST     = 'lotgd.page.village.post';
    public const PAGE_VILLAGE_LOCATION = 'lotgd.page.village.location';

    public const PAGE_WEAPONS_PRE  = 'lotgd.page.weapons.pre';
    public const PAGE_WEAPONS_POST = 'lotgd.page.weapons.post';
}
