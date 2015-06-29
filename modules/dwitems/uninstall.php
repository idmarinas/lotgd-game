<?php
output("`4Un-Installing Itemssystem for Dwellings.`n");
$sql = "DROP TABLE ".db_prefix("dwitems");
db_query($sql);
$sql = "DROP TABLE ".db_prefix("dwellingsitems");
db_query($sql);
?>