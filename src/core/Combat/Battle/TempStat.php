<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Combat\Battle;

trait TempStat
{
    private $temp_user_stats = [];

    public function applyTempStat($name, $value, $type = 'add')
    {
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

            if ( ! isset($this->temp_user_stats['is_suspended']) || ! $this->temp_user_stats['is_suspended'])
            {
                $this->user[$name] += $value;
            }

            $this->updateData();

            return true;
        }

        $this->response->pageDebug("Temp stat type {$type} is not supported.");

        return false;
    }

    public function checkTempStat($name, $color = false)
    {
        $v = $this->temp_user_stats['add'][$name] ?? 0;

        if (false === $color)
        {
            return 0 === $v ? '' : $v;
        }
        elseif ($v > 0)
        {
            return ' `&('.($this->user[$name] - round($v, 1)).'`@+'.round($v, 1).'`&)';
        }
        else
        {
            return 0 === $v ? '' : ' `&('.($this->user[$name] + round($v, 1)).'`$-'.round($v, 1).'`&)';
        }
    }

    public function suspendTempStats()
    {
        if ( ! isset($this->temp_user_stats['is_suspended']) || ! $this->temp_user_stats['is_suspended'])
        {
            foreach ($this->temp_user_stats as $type => $collection)
            {
                if ('add' == $type)
                {
                    foreach ($collection as $attribute => $value)
                    {
                        $this->user[$attribute] -= $value;
                    }
                }
            }

            $this->temp_user_stats['is_suspended'] = true;

            $this->updateData();

            return true;
        }

        return false;
    }

    public function restoreTempStats()
    {
        if ($this->temp_user_stats['is_suspended'])
        {
            foreach ($this->temp_user_stats as $type => $collection)
            {
                if ('add' == $type)
                {
                    foreach ($collection as $attribute => $value)
                    {
                        $this->user[$attribute] += $value;
                    }
                }
            }
            $this->temp_user_stats['is_suspended'] = false;

            $this->updateData();

            return true;
        }

        return false;
    }
}
