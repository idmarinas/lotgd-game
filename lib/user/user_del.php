<?php

$sql = 'SELECT name,superuser from '.DB::prefix('accounts')." WHERE acctid='$userid'";
$res = DB::query($sql);
require_once 'lib/charcleanup.php';
char_cleanup($userid, CHAR_DELETE_MANUAL);
$fail = false;

while ($row = DB::fetch_assoc($res))
{
    if ($res['superuser'] > 0 && SU_MEGAUSER != ($session['user']['superuser'] & SU_MEGAUSER))
    {
        output('`$You are trying to delete a user with superuser powers. Regardless of the type, ONLY a megauser can do so due to security reasons.');
        $fail = true;
        break;
    }
    addnews('`#%s was unmade by the gods.', $row['name'], true);
    debuglog('deleted user'.$row['name']."'0");
}

if (true !== $fail)
{
    $sql = 'DELETE FROM '.DB::prefix('accounts')." WHERE acctid='$userid'";
    DB::query($sql);
    output(DB::affected_rows().' user deleted.');
}
