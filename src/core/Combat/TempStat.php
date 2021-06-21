<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Combat;

use Lotgd\Core\Http\Response;

class TempStat
{
    private $temp_user_stats = ['is_suspended' => false];
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function apply_temp_stat($name, $value, $type = 'add')
    {
        global $session;

        if ('add' == $type)
        {
            if ( ! isset($this->temp_user_stats['add']))
            {
                $this->temp_user_stats['add'] = [];
            }
            $temp = &$this->temp_user_stats['add'];

            if ( ! isset($temp[$name]))
            {
                $temp[$name] = $value;
            }
            else
            {
                $temp[$name] += $value;
            }

            if ( ! $this->temp_user_stats['is_suspended'])
            {
                $session['user'][$name] += $value;
            }

            return true;
        }

        $this->response->pageDebug("Temp stat type {$type} is not supported.");

        return false;
    }

    public function check_temp_stat($name, $color = false)
    {
        global $session;

        if (isset($this->temp_user_stats['add'][$name]))
        {
            $v = $this->temp_user_stats['add'][$name];
        }
        else
        {
            $v = 0;
        }

        if (false === $color)
        {
            return 0 == $v ? '' : $v;
        }
        else
        {
            if ($v > 0)
            {
                return ' `&('.($session['user'][$name] - \round($v, 1)).'`@+'.\round($v, 1).'`&)';
            }
            else
            {
                return 0 == $v ? '' : ' `&('.($session['user'][$name] + \round($v, 1)).'`$-'.\round($v, 1).'`&)';
            }
        }
    }

    public function suspend_temp_stats()
    {
        global $session;

        if ( ! $this->temp_user_stats['is_suspended'])
        {
            \reset($this->temp_user_stats);

            foreach ($this->temp_user_stats as $type => $collection)
            {
                if ('add' == $type)
                {
                    \reset($collection);

                    foreach ($collection as $attribute => $value)
                    {
                        $session['user'][$attribute] -= $value;
                    }
                }
            }
            $this->temp_user_stats['is_suspended'] = true;

            return true;
        }

        return false;
    }

    public function restore_temp_stats()
    {
        global $session;

        if ($this->temp_user_stats['is_suspended'])
        {
            \reset($this->temp_user_stats);

            foreach ($this->temp_user_stats as $type => $collection)
            {
                if ('add' == $type)
                {
                    \reset($collection);

                    foreach ($collection as $attribute => $value)
                    {
                        $session['user'][$attribute] += $value;
                    }
                }
            }
            $this->temp_user_stats['is_suspended'] = false;

            return true;
        }

        return false;
    }
}
