<?php

// translator ready
// addnews ready
// mail ready
// phpDocumentor ready

/**
 * Returns the experience needed to advance to the next level.
 *
 * @param int $curlevel the current level of the player
 * @param int $curdk    the current number of dragonkills
 *
 * @return int the amount of experience needed to advance to the next level
 */
function exp_for_next_level($curlevel, $curdk)
{
    require_once 'lib/datacache.php';

    $maxlevel = getsetting('maxlevel', 15);
    //the exp is first 3 times the starting one, then later goes down to <25% from the previous one. It is harder to obtain enough exp though.
    $expstring = getsetting('exp-array', '100,400,1002,1912,3140,4707,6641,8985,11795,15143,19121,23840,29437,36071,43930');

    $dataCacheKey = 'exp-for-next-level-array-'.md5($expstring)."-lvl-{$maxlevel}-dk-{$curdk}-";

    $exparray = datacache($dataCacheKey, 86400, true); //fetch all for that DK if already calculated!
    //check if datacache is here
    if (! is_array($exparray))
    {
        //error!
        if ('' == $expstring)
        {
            debug('`$Setting "exp-array" is empty. Configure this setting. Return 0 as exp need for next level.`0');

            return 0;
        }

        $exparray = explode(',', $expstring);
        $count = count($exparray);

        if ($curlevel < 1)
        {
            return 0;
        }

        foreach ($exparray as $key => $val)
        {
            $exparray[$key] = round($val + ($curdk / 4) * ($key + 1) * 100, 0);
        }

        //-- Always +1 level max too avoid error of cant get exp need for next level if player are in máx level
        $maxlevel++;
        //fill it up, we have too few entries to have a valid exp array
        if ($maxlevel > $count)
        {
            for ($i = $count; $i < $maxlevel; $i++)
            {
                $exparray[$i] = round($exparray[$i - 1] * 1.2);
            }
        }

        updatedatacache($dataCacheKey, $exparray, true);
    }
    //-- Avoid level less than 0 and more than max lvl
    $curlevel = min(max($curlevel - 1, 0), $maxlevel);

    //-- If not find level invalidate cache and redo it
    if (! isset($exparray[$curlevel]))
    {
        invalidatedatacache($dataCacheKey, true);

        return exp_for_next_level($curlevel, $curdk);
    }

    return $exparray[$curlevel];
}
