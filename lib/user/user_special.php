<?php

if ('' != httppost('newday'))
{
    //	$offset = "-".(24 / (int)getsetting("daysperday",4))." hours";
    //	$newdate = date("Y-m-d H:i:s",strtotime($offset));
    //	$sql = "UPDATE " . DB::prefix("accounts") . " SET lasthit='$newdate' WHERE acctid='$userid'";
    $sql = 'UPDATE '.DB::prefix('accounts')." SET lasthit='0000-00-00 00:00:00' WHERE acctid='$userid'";
    DB::query($sql);
}
elseif ('' != httppost('fixnavs'))
{
    $sql = 'UPDATE '.DB::prefix('accounts')." SET allowednavs='', restorepage='', specialinc='' WHERE acctid='$userid'";
    DB::query($sql);
    $sql = 'DELETE FROM '.DB::prefix('accounts_output')." WHERE acctid='$userid';";
    DB::query($sql);
}
elseif ('' != httppost('clearvalidation'))
{
    $sql = 'UPDATE '.DB::prefix('accounts')." SET emailvalidation='' WHERE acctid='$userid'";
    DB::query($sql);
}
$op = 'edit';
httpset('op', 'edit');
