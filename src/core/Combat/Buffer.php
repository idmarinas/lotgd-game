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

use Lotgd\Core\Event\Character;
use Lotgd\Core\Http\Response;
use Lotgd\Core\Lib\Settings;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Buffer
{
    private $buffreplacements = [];
    private $debuggedbuffs    = [];
    private $response;
    private $dispatcher;
    private $tempStat;
    private $settings;

    public function __construct(Response $response, EventDispatcherInterface $dispatcher, TempStat $tempStat, Settings $settings)
    {
        $this->response   = $response;
        $this->dispatcher = $dispatcher;
        $this->tempStat   = $tempStat;
        $this->settings   = $settings;
    }

    public function calculateBuffFields()
    {
        global $session;

        if ( ! isset($session['bufflist']) || ! $session['bufflist'])
        {
            return;
        }

        //run temp stats
        reset($session['bufflist']);

        foreach ($session['bufflist'] as $buffname => $buff)
        {
            if ( ! isset($buff['tempstats_calculated']))
            {
                foreach ($buff as $property => $value)
                {
                    if ('tempstat-' == substr($property, 0, 9))
                    {
                        $this->tempStat->applyTempStat(substr($property, 9), $value);
                    }
                }//end foreach
                $session['bufflist'][$buffname]['tempstats_calculated'] = true;
            }//end if
        }//end foreach

        //process calculated buff fields.
        reset($session['bufflist']);

        if ( ! \is_array($this->buffreplacements))
        {
            $this->buffreplacements = [];
        }

        foreach ($session['bufflist'] as $buffname => $buff)
        {
            if ( ! isset($buff['fields_calculated']))
            {
                foreach ($buff as $property => $value)
                {
                    //calculate dynamic buff fields
                    $origstring = $value;
                    //Simple <module|variable> replacements for get_module_pref('variable','module')
                    $value = preg_replace('/<([A-Za-z0-9]+)\\|([A-Za-z0-9]+)>/', "get_module_pref('\\2','\\1')", $value);
                    //simple <variable> replacements for $session['user']['variable']
                    $value = preg_replace('/<([A-Za-z0-9]+)>/', "\$session['user']['\\1']", $value);

                    if ( ! \defined('OLDSU'))
                    {
                        \define('OLDSU', $session['user']['superuser']);
                    }

                    if ($value != $origstring)
                    {
                        if ('debug:' == strtolower(substr($value, 0, 6)))
                        {
                            $errors     = '';
                            $origstring = substr($origstring, 6);
                            $value      = substr($value, 6);

                            if ( ! isset($this->debuggedbuffs[$buffname]))
                            {
                                $this->debuggedbuffs[$buffname] = [];
                            }

                            ob_start();
                            $val    = eval("return {$value};");
                            $errors = ob_get_contents();
                            ob_end_clean();

                            if ( ! isset($this->debuggedbuffs[$buffname][$property]))
                            {
                                if ('' == $errors)
                                {
                                    $this->response->pageDebug("Buffs[{$buffname}][{$property}] evaluates successfully to {$val}");
                                }
                                else
                                {
                                    $this->response->pageDebug("Buffs[{$buffname}][{$property}] has an evaluation error<br>"
                                    .htmlentities($origstring, ENT_COMPAT, $this->settings->getSetting('charset', 'UTF-8')).' becomes <br>'
                                    .htmlentities($value, ENT_COMPAT, $this->settings->getSetting('charset', 'UTF-8')).'<br>'
                                    .$errors);
                                    $val = '';
                                }
                                $this->debuggedbuffs[$buffname][$property] = true;
                            }

                            $origstring = 'debug:'.$origstring;
                            $value      = 'debug'.$value;
                        }
                        else
                        {
                            $val = eval('return '.$value.';');
                        }
                    }
                    else
                    {
                        $val = $value;
                    }

                    $session['user']['superuser'] = OLDSU;

                    if (is_numeric($val) && (is_nan($val) || is_infinite($val)))
                    {
                        $val = $value;
                    }

                    $output = $output ?? '';

                    if ('' == $output && (string) $val != (string) $origstring)
                    {
                        $this->buffreplacements[$buffname][$property] = $origstring;
                        $session['bufflist'][$buffname][$property]    = $val;
                    }//end if
                    unset($val);
                }//end foreach
                $session['bufflist'][$buffname]['fields_calculated'] = true;
            }//end if
        }//end foreach
    }

    //end function

    public function restoreBuffFields()
    {
        global $session;

        if (\is_array($this->buffreplacements))
        {
            reset($this->buffreplacements);

            foreach ($this->buffreplacements as $buffname => $val)
            {
                reset($val);

                foreach ($val as $property => $value)
                {
                    if (isset($session['bufflist'][$buffname]))
                    {
                        $session['bufflist'][$buffname][$property] = $value;
                        unset($session['bufflist'][$buffname]['fields_calculated']);
                    }//end if
                }//end foreach
                unset($this->buffreplacements[$buffname]);
            }//end foreach
        }//end if

        //restore temp stats
        if ( ! isset($session['bufflist']) || ! \is_array($session['bufflist']))
        {
            $session['bufflist'] = [];
        }
        reset($session['bufflist']);

        foreach ($session['bufflist'] as $buffname => $buff)
        {
            if (\array_key_exists('tempstats_calculated', $buff) && $buff['tempstats_calculated'])
            {
                reset($buff);

                foreach ($buff as $property => $value)
                {
                    if ('tempstat-' == substr($property, 0, 9))
                    {
                        $this->tempStat->applyTempStat(substr($property, 9), -$value);
                    }
                }//end foreach
                unset($session['bufflist'][$buffname]['tempstats_calculated']);
            }//end if
        }//end foreach
    }

    //end function

    public function applyBuff($name, $buff)
    {
        global $session, $translation_namespace;

        if ( ! isset($buff['schema']) || '' == $buff['schema'])
        {
            $buff['schema'] = $translation_namespace;
        }

        unset($this->buffreplacements[$name]);

        if (isset($session['bufflist'][$name]))
        {
            //we'll need to unapply buff fields before applying this buff since
            //it's already set.
            $this->restoreBuffFields();
        }

        $buff = new Character(['name' => $name, 'buff' => $buff]);
        $this->dispatcher->dispatch($buff, Character::MODIFY_BUFF);
        $buff = modulehook('modify-buff', $buff->getData());

        $session['bufflist'][$name] = $buff['buff'];
        $this->calculateBuffFields();
    }

    public function applyCompanion($name, $companion, $ignorelimit = false)
    {
        global $session, $companions;

        $companionsallowed = $this->settings->getSetting('companionsallowed', 1);
        $args              = new Character(['maxallowed' => $companionsallowed]);
        $this->dispatcher->dispatch($args, Character::COMPANIONS_ALLOWED);
        $args = modulehook('companionsallowed', $args->getData());

        $companionsallowed = $args['maxallowed'];
        $current           = 0;

        if ( ! $ignorelimit)
        {
            foreach ($companions as $thisname => $thiscompanion)
            {
                if (( ! isset($thiscompanion['ignorelimit']) || ! $thiscompanion['ignorelimit'])
                    && ($thisname != $name)
                ) {
                    ++$current;
                }
            }
        }

        if ($current < $companionsallowed || $ignorelimit)
        {
            unset($companions[$name]);

            if ( ! isset($companion['ignorelimit']) && $ignorelimit)
            {
                $companion['ignorelimit'] = true;
            }
            $companions[$name]             = $companion;
            $session['user']['companions'] = $companions;

            return true; // success!
        }

        $this->response->pageDebug('Failed to add companion due to restrictions regarding the maximum amount of companions allowed.');

        return false;
    }

    public function stripBuff($name)
    {
        global $session;
        $this->restoreBuffFields();

        unset($session['bufflist'][$name]);
        unset($this->buffreplacements[$name]);

        $this->calculateBuffFields();
    }

    public function stripAllBuffs()
    {
        global $session;

        foreach ($session['bufflist'] as $buffname => $buff)
        {
            $this->stripBuff($buffname);
        }
    }

    public function hasBuff($name): bool
    {
        global $session;

        return (bool) (isset($session['bufflist'][$name]));
    }
}
