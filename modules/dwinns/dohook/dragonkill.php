<?php
$dwinns = db_prefix("dwinns");
$dwellings = db_prefix("dwellings");
$sql = "UPDATE $dwinns, $dwellings SET logmeals=0, logrooms=0, logdrinks=0 WHERE ownerid = " . $session['user']['acctid']. " AND $dwellings.dwid = $dwinns.dwid";
db_query($sql);
?>