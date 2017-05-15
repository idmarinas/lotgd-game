<?php
$sql = "DELETE FROM " . DB::prefix("bans") . " WHERE ipfilter = '".httpget("ipfilter"). "' AND uniqueid = '".httpget("uniqueid")."'";
DB::query($sql);
redirect("user.php?op=removeban");
?>
