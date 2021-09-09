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

use Lotgd\Core\Event\Character;

trait Buffer
{
    private $buffReplacements = [];
    private $debuggedBuffs    = [];

    public function calculateBuffFields()
    {
        if (empty($this->userBuffs))
        {
            return;
        }

        //run temp stats
        foreach ($this->userBuffs as &$buff)
        {
            if (isset($buff['tempstats_calculated']))
            {
                continue;
            }

            foreach ($buff as $property => $value)
            {
                if ('tempstat-' == substr($property, 0, 9))
                {
                    $this->applyTempStat(substr($property, 9), $value);
                }
            }

            $buff['tempstats_calculated'] = true;
        }
        unset($buff);

        //process calculated buff fields.
        foreach ($this->userBuffs as $buffname => &$buff)
        {
            if (isset($buff['fields_calculated']))
            {
                continue;
            }

            foreach ($buff as $property => $value)
            {
                //calculate dynamic buff fields
                $origstring = $value;
                //Simple <module|variable> replacements for get_module_pref('variable','module')
                //-- Deprecated use function
                $value = preg_replace('/<([A-Za-z0-9]+)\|([A-Za-z0-9]+)>/', "get_module_pref('\\2','\\1')", $value);
                //Simple <variable> replacements for $session['user']['variable']
                //-- Deprecated use function
                $value = preg_replace('/<([A-Za-z0-9]+)>/', "character_attr('\\1')", $value);

                try
                {
                    $val = $this->expression->evaluate($value, [
                        'character' => $this->userSafe,
                    ]);
                }
                catch (\Throwable $th)
                {
                    //-- Expression evaluation failed, use original value
                    $val = $value;
                }

                if (is_numeric($val) && (is_nan($val) || is_infinite($val)))
                {
                    $val = $value;
                }

                if ((string) $val != (string) $origstring)
                {
                    $this->buffReplacements[$buffname][$property] = $origstring;
                    $this->userBuffs[$buffname][$property]        = $val;
                }
                unset($val);
            }

            $buff['fields_calculated'] = true;
        }
        unset($buff);

        $this->updateData();
    }

    public function restoreBuffFields()
    {
        if (\is_array($this->buffReplacements))
        {
            foreach ($this->buffReplacements as $buffname => $val)
            {
                foreach ($val as $property => $value)
                {
                    if (isset($this->userBuffs[$buffname]))
                    {
                        $this->userBuffs[$buffname][$property] = $value;
                        unset($this->userBuffs[$buffname]['fields_calculated']);
                    }
                }
                unset($this->buffReplacements[$buffname]);
            }
        }

        foreach ($this->userBuffs as $buffname => $buff)
        {
            if (\array_key_exists('tempstats_calculated', $buff) && $buff['tempstats_calculated'])
            {
                foreach ($buff as $property => $value)
                {
                    if ('tempstat-' == substr($property, 0, 9))
                    {
                        $this->applyTempStat(substr($property, 9), -$value);
                    }
                }
                unset($this->userBuffs[$buffname]['tempstats_calculated']);
            }
        }

        $this->updateData();
    }

    public function applyBuff($name, $buff)
    {
        unset($this->buffReplacements[$name]);

        if (isset($this->userBuffs[$name]))
        {
            //we'll need to unapply buff fields before applying this buff since
            //it's already set.
            $this->restoreBuffFields();
        }

        $buff = new Character(['name' => $name, 'buff' => $buff]);
        $this->dispatcher->dispatch($buff, Character::MODIFY_BUFF);
        $buff = modulehook('modify-buff', $buff->getData());

        $this->userBuffs[$name] = $buff['buff'];

        $this->calculateBuffFields();
    }

    public function applyCompanion($name, $companion, $ignoreLimit = false)
    {
        $companionsAllowed = $this->settings->getSetting('companionsallowed', 1);
        $args              = new Character(['maxallowed' => $companionsAllowed]);
        $this->dispatcher->dispatch($args, Character::COMPANIONS_ALLOWED);
        $args = modulehook('companionsallowed', $args->getData());

        $companionsAllowed = $args['maxallowed'];
        $current           = count(array_filter($this->companions, function ($val, $key) use ($name)
        {
            return (( ! isset($val['ignorelimit']) || ! $val['ignorelimit']) && ($key != $name));
        }, ARRAY_FILTER_USE_BOTH));

        if (($ignoreLimit && $current < $companionsAllowed) || $ignoreLimit)
        {
            unset($this->companions[$name]);

            if ( ! isset($companion['ignorelimit']) && $ignoreLimit)
            {
                $companion['ignorelimit'] = true;
            }
            $this->companions[$name] = $companion;

            $this->updateData();

            return true; // success!
        }

        $this->response->pageDebug('Failed to add companion due to restrictions regarding the maximum amount of companions allowed.');

        $this->updateData();

        return false;
    }

    public function stripBuff($name)
    {
        $this->restoreBuffFields();

        unset($this->userBuffs[$name], $this->buffReplacements[$name]);

        $this->calculateBuffFields();
    }

    public function stripAllBuffs()
    {
        array_walk($this->userBuffs, function ($elem, $key)
        {
            $this->stripBuff($key);
        });
    }

    public function hasBuff($name): bool
    {
        return (bool) (isset($this->userBuffs[$name]));
    }
}
