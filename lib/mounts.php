<?php

// translator ready
// addnews ready
// mail ready
function getmount($horse = 0)
{
    $sql = 'SELECT * FROM '.DB::prefix('mounts')." WHERE mountid='$horse'";
    $result = DB::query($sql);

    if (DB::num_rows($result) > 0)
    {
        return DB::fetch_assoc($result);
    }
    else
    {
        return [];
    }
}
