<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.4.0
 */

namespace Lotgd\Core;

class Hook
{
    //-- HOOKS readapted in Core 4.5.0

    /**
     * Page Hooks.
     */
    // Every header. Old: everyheader
    const HOOK_EVERY_HEADER = 'lotgd.page.every.header';

    // Every footer. Old: everyfooter
    const HOOK_EVERY_FOOTER = 'lotgd.page.every.footer';

    // Every header authenticated. Old: everyheader-loggedin
    const HOOK_EVERY_HEADER_AUTHENTICATED = 'lotgd.page.every.header.authenticated';

    // Every footer authenticated. Old: everyfooter-loggedin
    const HOOK_EVERY_FOOTER_AUTHENTICATED = 'lotgd.page.every.footer.authenticated';


    /**
     * Hooks header/footer page
     */

    // In header for script
    const HOOK_HEADER_SCRIPT = 'lotgd.page.header.script.';

    // In footer for script
    const HOOK_FOOTER_SCRIPT = 'lotgd.page.footer.script.';

    /**
     * Hooks of character
     */
    // End of generated characters stats
    const HOOK_CHARACTER_STATS = 'lotgd.character.stats';

    // When generated characters stats, but character is offline
    const HOOK_CHARACTER_ONLINE_LIST = 'lotgd.character.online.list';

    // Modify character buff
    const HOOK_CHARACTER_MODIFY_BUFF = 'lotgd.character.modify.buff';

    // Character companions
    const HOOK_CHARACTER_COMPANIONS_ALLOWED = 'lotgd.character.companions.allowed';

    // Character cleanup
    const HOOK_CHARACTER_CLEANUP = 'lotgd.character.cleanup';

    // Character killed player
    const HOOK_CHARACTER_KILLED_PLAYER = 'lotgd.character.killed.player';

    // Character increment specialty
    const HOOK_CHARACTER_SPECIALTY_INCREMENT = 'lotgd.character.specialty.increment';

    // Character pvp adjust
    const HOOK_CHARACTER_PVP_ADJUST = 'lotgd.character.pvp.adjust';

    // Character pvp win
    const HOOK_CHARACTER_PVP_WIN = 'lotgd.character.pvp.win';

    // Character pvp loss
    const HOOK_CHARACTER_PVP_LOSS = 'lotgd.character.pvp.loss';

    // Character pvp do kill
    const HOOK_CHARACTER_PVP_DO_KILL = 'lotgd.character.pvp.do.kill';

    // Character restore backup. Old: character-restore
    const HOOK_CHARACTER_BACKUP_RESTORE = 'lotgd.character.backup.restore';

    /**
     * Hooks of companions
     */
    // Alter companion. Old: alter-companion
    const HOOK_COMPANION_ALTER = 'lotgd.companion.alter';

    /**
     * Hooks of clan
     */
    // Delete clan
    const HOOK_CLAN_DELETE = 'lotgd.clan.delete';

    // Create clan
    const HOOK_CLAN_CREATE = 'lotgd.clan.create';

    // Enter clan
    const HOOK_CLAN_ENTER = 'lotgd.clan.enter';

    // Set clan rank
    const HOOK_CLAN_RANK_SET = 'lotgd.clan.rank.set';

    // Set clan rank
    const HOOK_CLAN_RANK_LIST = 'lotgd.clan.rank.list';

    // Set clan rank
    const HOOK_CLAN_WITHDRAW = 'lotgd.clan.withdraw';

    /**
     * Hooks of forest/creatures
     */
    // Creature search
    const HOOK_CREATURE_SEARCH = 'lotgd.creature.search';

    // Creature encounter
    const HOOK_CREATURE_ENCOUNTER = 'lotgd.creature.encounter';

    // Creature buff
    const HOOK_CREATURE_BUFF = 'lotgd.creature.buff.';

    /**
     * Hooks of graveyard
     */
    // Start a battle
    const HOOK_GRAVEYARD_FIGHT_START = 'lotgd.graveyard.fight.start';

    // Question, actions
    const HOOK_GRAVEYARD_DEATH_OVERLORD_ACTIONS = 'lotgd.graveyard.death.overlord.actions';

    // Question, actions
    const HOOK_GRAVEYARD_DEATH_OVERLORD_FAVORS = 'lotgd.graveyard.death.overlord.favors';

    /**
     * Hooks of Inn
     */
    // Bribe bartender
    const HOOK_INN_BARTENDER_BRIBE = 'lotgd.inn.bartender.bribe';

    // Ale
    const HOOK_INN_ALE = 'lotgd.inn.ale';

    /**
     * Hooks Fight
     */
    // Nav PRE
    const HOOK_FIGHT_NAV_PRE = 'lotgd.fight.nav.pre';

    // Nav Graveyard
    const HOOK_FIGHT_NAV_GRAVEYARD = 'lotgd.fight.nav.graveyard';

    // Nav Specialties
    const HOOK_FIGHT_NAV_SPECIALTY = 'lotgd.fight.nav.specialty';

    // Nav
    const HOOK_FIGHT_NAV = 'lotgd.fight.nav';

    // Options
    const HOOK_FIGHT_OPTIONS = 'lotgd.fight.options';

    // Alter gem chance
    const HOOK_FIGHT_ALTER_GEM_CHANCE = 'lotgd.fight.alter.gem.chance';

    // Apply specialties
    const HOOK_FIGHT_APPLY_SPECIALTY = 'lotgd.fight.apply.specialty';

    /**
     * Core hooks
     */
    // Everyhit. Old: everyhit
    const HOOK_CORE_EVERYHIT = 'lotgd.core.everyhit';

    // Everyhit loggedin. Old: everyhit-loggedin
    const HOOK_CORE_EVERYHIT_LOGGEDIN = 'lotgd.core.everyhit.loggedin';

    // Check login. Old: check-login
    const HOOK_CORE_LOGIN_CHECK = 'lotgd.core.login.check';

    // Player login. Old: player-login
    const HOOK_CORE_LOGIN_PLAYER = 'lotgd.core.login.player';

    // Player logout. Old: player-logout
    const HOOK_CORE_LOGOUT_PLAYER = 'lotgd.core.logout.player';

    // Add petition. Old: addpetition
    const HOOK_CORE_PETITION_ADD = 'lotgd.core.pettion.add';

    // Petition faq toc. Old: faq-toc
    const HOOK_CORE_PETITION_FAQ_TOC = 'lotgd.core.pettion.faq.toc';

    // Change setting
    const HOOK_CORE_SETTING_CHANGE = 'lotgd.core.setting.change';

    // Specialty names: Old: specialtynames
    const HOOK_CORE_SPECIALTY_NAMES = 'lotgd.core.specialty.names';

    // DK point recalc. Old: pdkpointrecalc
    const HOOK_CORE_DK_POINT_RECALC = 'lotgd.core.pdk.point.recalc';

    // New day run once. Old: newday-runonce
    const HOOK_CORE_NEWDAY_RUNONCE = 'lotgd.core.newday.runonce';

    // Pre new day. Old: pre-newday
    const HOOK_CORE_NEWDAY_PRE = 'lotgd.core.newday.pre';

    // New day. Old: newday
    const HOOK_CORE_NEWDAY = 'lotgd.core.newday';

    // Set race. Old: setrace
    const HOOK_CORE_RACE_SET = 'lotgd.core.race.set';

    // Choose race. Old: chooserace
    const HOOK_CORE_RACE_CHOOSE = 'lotgd.core.race.choose';

    // Set specialty. Old: set-specialty
    const HOOK_CORE_SPECIALTY_SET = 'lotgd.core.specialty.set';

    // Choose specialty. Old: choose-specialty
    const HOOK_CORE_SPECIALTY_CHOOSE = 'lotgd.core.specialty.choose';

    /**
     * Hook comentary
     */
    // Post comment. Old: postcomment
    const HOOK_COMENTARY_COMMENT_POST = 'lotgd.core.comentary.comment.post';

    // Commands. Old: commentary-command
    const HOOK_COMENTARY_COMMANDS = 'lotgd.core.comentary.commands';

    // Comment. Old: commentary-comment
    const HOOK_COMENTARY_COMMENT = 'lotgd.core.comentary.comment';

    // Moderate sections. Old: moderate-comment-sections
    const HOOK_COMENTARY_MODERATE_SECTIONS = 'lotgd.core.comentary.moderate.sections';

    /**
     * Other hooks
     */
    // Special holiday
    const HOOK_SPECIAL_HOLIDAY = 'lotgd.other.holiday';

    // Dragonpoints reset
    const HOOK_SERVER_DRAGON_POINT_RESET = 'lotgd.other.server.dragon.point.reset';

    // Superuser
    const HOOK_SUPERUSER = 'lotgd.other.superuser';

    // Check su access
    const HOOK_SUPERUSER_CHECK_SU_ACCESS = 'lotgd.other.superuser.check.su.access';

    // Check su permission
    const HOOK_SUPERUSER_CHECK_SU_PERMISSION = 'lotgd.other.superuser.check.su.permission';

    // Check su permission. Old: stamina-newday
    const HOOK_OTHER_STAMINA_NEWDAY = 'lotgd.other.stamina.newday';

    // Locations. Old: camplocs
    const HOOK_OTHER_LOCATIONS = 'lotgd.other.locations';

    // End of bio page. Old: bioend
    const HOOK_OTHER_BIO_END = 'lotgd.other.bio.end';
}
