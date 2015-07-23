<?php
function get_suffix($num)
{
// Function written by Marcus L. Griswold (vujsa)
// Can be found at http://www.handyphp.com
// Do not remove this header!

	if( is_numeric($num) )
	{
		if( substr($num, -2, 2) == 11 || substr($num, -2, 2) == 12 || substr($num, -2, 2) == 13 )
		{
			$suffix = 'th';
		}
		elseif( substr($num, -1, 1) == 1 )
		{
			$suffix = 'st';
		}
		elseif( substr($num, -1, 1) == 2 )
		{
			$suffix = 'nd';
		}
		elseif( substr($num, -1, 1) == 3 )
		{
			$suffix = 'rd';
		}
		else
		{
			$suffix = 'th';
		}
		return $num.$suffix;
	}
	else
	{
		return NULL;
	}
}

function time_since($original)
{
// Function written by skyhawk133 - March 2, 2005
// http://www.dreamincode.net/code/snippet86.htm

    // array of time period chunks
    $chunks = array(
        array(60 * 60 * 24 * 365, 'year'),
        array(60 * 60 * 24 * 30, 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24, 'day'),
        array(60 * 60, 'hour'),
        array(60, 'minute'),
    );
    
    $today = time();
    $since = $today - $original;
    
    // $j saves performing the count function each time around the loop
    for( $i=0, $j=count($chunks); $i<$j; $i++ )
    {
        
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        
        // finding the biggest chunk (if the chunk fits, break)
        if( ($count = floor($since / $seconds)) != 0 )
        {
            break;
        }
    }
    
    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    
    if( $i + 1 < $j )
    {
        // now getting the second item
        $seconds2 = $chunks[$i + 1][0];
        $name2 = $chunks[$i + 1][1];
        
        // add second item if it's greater than 0
        if( ($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0 )
        {
            $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        }
    }
    return $print;
}
?>