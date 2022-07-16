<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Tool;

use Lotgd\Core\Repository\CreaturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Event\Creature;
use Lotgd\Core\Http\Response;
use Lotgd\Core\Lib\Settings;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreatureFunction
{
    private $dispatcher;
    private $response;
    private $cache;
    private CreaturesRepository $repository;
    private $translator;
    private $settings;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        Response $response,
        CacheInterface $cache,
        EntityManagerInterface $repository,
        TranslatorInterface $translator,
        Settings $settings
    ) {
        $this->dispatcher = $dispatcher;
        $this->response   = $response;
        $this->cache      = $cache;
        $this->repository = $repository->getRepository('LotgdCore:Creatures');
        $this->translator = $translator;
        $this->settings   = $settings;
    }

    /**
     * Buff creature for optimize to character stats.
     *
     * @param array  Information of creature
     * @param string $hook   Hook to activate when buff badguy
     * @param mixed  $badguy
     *
     * @return array
     */
    public function buffBadguy($badguy, $hook = 'buffbadguy')
    {
        global $session;

        $badguy = $this->lotgdTransformCreature($badguy, false);

        $expflux = round($badguy['creatureexp'] / 10, 0);
        $expflux = e_rand(-$expflux, $expflux);
        $badguy['creatureexp'] += $expflux;

        if ('' !== $this->settings->getSetting('disablebonuses', 1) && '0' !== $this->settings->getSetting('disablebonuses', 1))
        {
            //adapting flux as for people with many DKs they will just bathe in gold....
            $base = 30 - min(20, round(sqrt($session['user']['dragonkills']) / 2));
            $base /= 1000;
            $bonus = 1 + $base * ($badguy['creatureattackattrs'] + $badguy['creaturedefenseattrs']) + .001 * $badguy['creaturehealthattrs'];

            $badguy['creaturegold'] = round($badguy['creaturegold'] * $bonus, 0);
            $badguy['creatureexp']  = round($badguy['creatureexp'] * $bonus, 0);
        }

        //-- Activate hook when find a creature
        $badguy = new Creature($badguy);
        $this->dispatcher->dispatch($badguy, Creature::ENCOUNTER);
        $badguy->setData($badguy->getData());

        $hookNew = $hook;

        if ('buffbadguy' == $hook)
        {
            $hookNew = 'badguy';
        }
        elseif ('buffmaster' == $hook)
        {
            $hookNew = 'master';
        }
        //-- Activate hook custom or default (buffbadguy)
        $this->dispatcher->dispatch($badguy, Creature::BUFF_FOR.$hookNew);

        //-- Update max creature health
        $badguy['creaturemaxhealth'] = $badguy['creaturehealth'];

        $this->lotgdShowDebugCreature($badguy->getData());

        return $badguy;
    }

    /**
     * Generate a base creature stats
     * Can use for generated your own creatures in your modules.
     *
     * @param int|null $level Level of creature
     *
     * @return array
     */
    public function lotgdGenerateCreatureLevels($level = null)
    {
        $maxLvl = $this->settings->getSetting('maxlevel', 15) + 5;
        $stats  = $this->cache->get("lotgd-generate-creature-levels-{$maxLvl}", function () use ($maxLvl)
        {
            $stats = [];
            $creatureexp = 14;
            $creaturegold = 36;
            $creaturedefense = 0;

            for ($i = 1; $i <= $maxLvl; ++$i)
            {
                //apply algorithmic creature generation.
                $creaturehealth = ($i * 10) + ($i - 1) - round(sqrt($i - 1));
                $creatureattack = 1 + ($i - 1) * 2;
                $creaturedefense += (0 !== $i % 2 ? 1 : 2);

                if ($i > 1)
                {
                    $creatureexp += round(10 + 1.5 * log($i));
                    $creaturegold += (31 * ($i < 4 ? 2 : 1));
                    //give lower levels more gold
                }
                $stats[$i] = [
                    'creaturelevel'   => $i,
                    'creaturehealth'  => $creaturehealth,
                    'creatureattack'  => $creatureattack,
                    'creaturedefense' => $creaturedefense,
                    'creatureexp'     => $creatureexp,
                    'creaturegold'    => $creaturegold,
                ];
            }

            return $stats;
        });

        return $stats[$level] ?? $stats;
    }

    /**
     * Generate a dummy creature Doppelganger.
     */
    public function lotgdGenerateDoppelganger(int $level): array
    {
        global $session;

        //-- There is nothing in the database to challenge you, let's give you a doppelganger.
        $badguy                   = $this->lotgdGenerateCreatureLevels($level);
        $badguy['creaturename']   = $this->translator->trans('doppelganger', ['name' => $session['user']['name']], 'page_creatures');
        $badguy['creatureweapon'] = $session['user']['weapon'];
        $badguy['creaturegold']   = 0;

        return $badguy;
    }

    /**
     * Transform creature to adapt to player.
     *
     * @param array $badguy Data of creature
     * @param bool  $debug  Show or not debug of creature
     *
     * @return array
     */
    public function lotgdTransformCreature(array $badguy, bool $debug = true)
    {
        global $session;

        // This will save us a lot of trouble when going through
        static $dk = false;	// this function more than once...

        if (false === $dk)
        {
            $dk = $session['user']['dragonkills'];
        }

        $badguy = array_merge($this->lotgdGenerateCreatureLevels($badguy['creaturelevel']), $badguy);

        $badguy['playerdragonkills']  = $dk;
        $badguy['creaturespeed'] ??= 2.5;
        $badguy['creatureexp'] ??= 0;
        $badguy['physicalresistance'] ??= 0;

        //-- Apply multipliers
        $badguy['creaturegold'] = round($badguy['creaturegold'] * ($badguy['creaturegoldbonus'] ?? 1));
        $badguy['creatureattack']  *= $badguy['creatureattackbonus']   ?? 1;
        $badguy['creaturedefense'] *= $badguy['creaturedefensebonus'] ?? 1;
        $badguy['creaturehealth'] = round($badguy['creaturehealth'] * ($badguy['creaturehealthbonus'] ?? 1));

        $creatureattr = $this->getCreatureStats($dk);

        //-- Bonus to atributes
        $badguy['creaturestrbonus'] = $creatureattr['str'];
        $badguy['creaturedexbonus'] = $creatureattr['dex'];
        $badguy['creatureconbonus'] = $creatureattr['con'];
        $badguy['creatureintbonus'] = $creatureattr['int'];
        $badguy['creaturewisbonus'] = $creatureattr['wis'];

        //-- Total atributes of creature
        $badguy['creaturestr'] = $creatureattr['str'] + 10;
        $badguy['creaturedex'] = $creatureattr['dex'] + 10;
        $badguy['creaturecon'] = $creatureattr['con'] + 10;
        $badguy['creatureint'] = $creatureattr['int'] + 10;
        $badguy['creaturewis'] = $creatureattr['wis'] + 10;

        //-- Attack, defense, health from attributes
        $badguy['creatureattackattrs']  = $this->getCreatureAttack($creatureattr);
        $badguy['creaturedefenseattrs'] = $this->getCreatureDefense($creatureattr);
        $badguy['creaturehealthattrs']  = $this->getCreatureHitpoints($creatureattr);
        $badguy['creaturespeedattrs']   = $this->getCreatureSpeed($creatureattr);

        //-- Sum bonus
        $badguy['creatureattack']  += $badguy['creatureattackattrs'];
        $badguy['creaturedefense'] += $badguy['creaturedefenseattrs'];
        $badguy['creaturehealth']  += $badguy['creaturehealthattrs'];
        $badguy['creaturespeed']   += $badguy['creaturespeedattrs'];

        //-- Set max health for creature
        $badguy['creaturemaxhealth']      = $badguy['creaturehealth'];
        $badguy['creaturestartinghealth'] = $badguy['creaturehealth'];

        //-- Check if script exist
        if (isset($badguy['creatureaiscript']))
        {
            $aiscriptfile = "{$badguy['creatureaiscript']}.php";

            $badguy['creatureaiscript'] = file_exists($aiscriptfile) ? "include '{$aiscriptfile}';" : '';
        }

        //-- Not show debug
        if ( ! $debug)
        {
            return $badguy;
        }

        $this->lotgdShowDebugCreature($badguy);

        return $badguy;
    }

    /**
     * Search for creature in data base.
     *
     * @param int       $multi
     * @param int       $targetlevel
     * @param int       $mintargetlevel
     * @param bool      $packofmonsters For diferent or same creatures
     * @param bool|null $forest         TRUE for creature of forest, FALSE for graveyard and NULL for none
     */
    public function lotgdSearchCreature($multi, $targetlevel, $mintargetlevel, $packofmonsters = false, $forest = true): array
    {
        $multi = $packofmonsters ? 1 : $multi;
        $limit = ($multi > 1 ? $multi : 1);

        $query = $this->repository->createQueryBuilder('u');
        $query->orderBy('RAND()');

        if (true === $forest)
        {
            $query->where('u.forest = 1');
        }
        elseif (false === $forest)
        {
            $query->where('u.graveyard = 1');
        }

        //-- Select creatures of diferent categories
        if ($this->settings->getSetting('multicategory', 0) && $limit > 1)
        {
            $query->groupBy('u.creaturecategory')->addGroupBy('u.creatureid');
        }

        $query = $this->repository->createTranslatebleQuery($query);
        $query->setMaxResults($limit);

        $creatures = $query->getArrayResult();

        foreach ($creatures as &$creature)
        {
            $creature['creaturelevel'] = e_rand($mintargetlevel, $targetlevel);
        }

        //-- You can add more creatures. This is good, when not find nothing in data base
        $creatures = new Creature([
            'creatures'      => $creatures,
            'multi'          => $multi,
            'targetlevel'    => $targetlevel,
            'mintargetlevel' => $mintargetlevel,
            'packofmonsters' => $packofmonsters,
            'forest'         => $forest,
        ]);
        $this->dispatcher->dispatch($creatures, Creature::SEARCH);

        return $creatures['creatures'];
    }

    public function getCreatureStats($dk = 0)
    {
        global $session;

        if (0 == $dk)
        {
            $dk = $session['user']['dragonkills'];
        }

        //-- They are placed in order of importance
        $con = e_rand($dk / 6, $dk / 2);
        $dk -= $con;
        $str = e_rand(0, $dk);
        $dk -= $str;
        $dex = e_rand(0, $dk);
        $dk -= $dex;
        $int = e_rand(0, $dk);
        $wis = ($dk - $int);

        return ['str' => $str, 'dex' => $dex, 'con' => $con, 'int' => $int, 'wis' => $wis];
    }

    public function getCreatureHitpoints($attrs)
    {
        $conbonus = $attrs['con'] * .5;
        $wisbonus = $attrs['wis'] * .2;
        $strbonus = $attrs['str'] * .3;

        $hitpoints = round($conbonus + $wisbonus + $strbonus, 0);

        return max($hitpoints, 0);
    }

    public function getCreatureAttack($attrs)
    {
        $strbonus = (1 / 3) * $attrs['str'];
        // // $speedbonus = (1 / 3) * get_creature_speed($attrs);
        $wisdombonus = (1 / 6) * $attrs['wis'];
        $intbonus    = (1 / 6) * $attrs['int'];

        $attack = $strbonus + $wisdombonus + $intbonus;

        return max($attack, 0);
    }

    public function getCreatureDefense($attrs)
    {
        $wisdombonus = (1 / 4) * $attrs['wis'];
        $constbonus  = (3 / 8) * $attrs['con'];
        // // $speedbonus = (3 / 8) * get_creature_speed($attrs);

        $defense = $wisdombonus + $constbonus;

        return max($defense, 0);
    }

    public function getCreatureSpeed($attrs)
    {
        $speed = (1 / 2) * $attrs['dex'] + (1 / 4) * $attrs['int'] + (5 / 2);

        return max($speed, 0);
    }

    public function lotgdShowDebugCreature(iterable $badguy)
    {
        $this->response->pageDebug("DEBUG: Basic information: Atk: {$badguy['creatureattack']}, Def: {$badguy['creaturedefense']}, HP: {$badguy['creaturehealth']}");
        $this->response->pageDebug("DEBUG: {$badguy['playerdragonkills']} modification points total for attributes.");
        $this->response->pageDebug("DEBUG: +{$badguy['creaturestrbonus']} allocated to strength.");
        $this->response->pageDebug("DEBUG: +{$badguy['creaturedexbonus']} allocated to dexterity.");
        $this->response->pageDebug("DEBUG: +{$badguy['creatureconbonus']} allocated to constitution.");
        $this->response->pageDebug("DEBUG: +{$badguy['creatureintbonus']} allocated to intelligence.");
        $this->response->pageDebug("DEBUG: +{$badguy['creaturewisbonus']} allocated to wisdom.");
        $this->response->pageDebug("DEBUG: +{$badguy['creatureattackattrs']} modification of attack.");
        $this->response->pageDebug("DEBUG: +{$badguy['creaturedefenseattrs']} modification of defense.");
        $this->response->pageDebug("DEBUG: +{$badguy['creaturespeedattrs']} modification of speed.");
        $this->response->pageDebug("DEBUG: +{$badguy['creaturehealthattrs']} modification of hitpoints.");
    }
}
