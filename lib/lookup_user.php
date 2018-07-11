<?php

function lookup_user($query = false, $order = false, $fields = false, $where = false)
{
    $err = '';
    $searchresult = false;

    if (false !== $order)
    {
        $order = "ORDER BY $order";
    }

    if (false === $fields)
    {
        $fields = 'acctid,login,name,level,laston,loggedin,gentimecount,gentime,lastip,uniqueid,emailaddress';
    }
    $sql = "SELECT $fields FROM ".DB::prefix('accounts');

    if ('' != $query)
    {
        // First try for an exact match on username or login
        if (false === $where)
        {
            $sql_where = "WHERE login LIKE '$query' OR name LIKE '$query' OR acctid = '$query' OR emailaddress LIKE '$query' OR lastip LIKE '$query' OR uniqueid LIKE '$query'";
        }
        else
        {
            $sql_where = "WHERE $where";
        }
        $searchresult = DB::query($sql." $sql_where $order LIMIT 2");
    }

    if (false !== $query || $searchresult)
    {
        if (1 != DB::num_rows($searchresult))
        {
            // we didn't find an exact match
            $name_query = '%';

            for ($x = 0; $x < strlen($query); $x++)
            {
                //mind escaped stuff - and carry it along unmodified
                $char = substr($query, $x, 1);

                if ('\\' != $char)
                {
                    $name_query .= $char.'%';
                }
                else
                {
                    $name_query .= $char;
                }
            }

            if (false === $where)
            {
                $sql_where = "WHERE login LIKE '%$query%' OR acctid LIKE '%$query%' OR name LIKE '%$name_query%' OR emailaddress LIKE '%$query%' OR lastip LIKE '%$query%' OR uniqueid LIKE '%$query%' OR gentimecount LIKE '%$query%' OR level LIKE '%$query%'";
            }
            else
            {
                $sql_where = "WHERE $where";
            }

            $searchresult = DB::query($sql." $sql_where $order LIMIT 101");
        }

        if (DB::num_rows($searchresult) <= 0)
        {
            $err = '`$No results found`0';
        }
        elseif (DB::num_rows($searchresult) > 100)
        {
            $err = '`$Too many results found, narrow your search please.`0';
        }
        else
        {
            // Everything is good
        }
    }

    return [$searchresult, $err];
}
