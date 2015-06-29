<?php
	$storedinfo = array();
		$dwell = httppost('dwell');
		$dwid = httpget('dwid');
        list($sql, $keys, $vals) = postparse(false, $dwell);
        if ($dwid>""){
            $sql="UPDATE " . db_prefix("dwellings") .
                " SET $sql WHERE dwid=$dwid";
        }else{
            $sql="INSERT INTO " . db_prefix("dwellings") .
                " ($keys) VALUES ($vals)";
        }
        db_query($sql);
        if (db_affected_rows()>0){
            output("`^Dwelling saved!`0`n");
        }else{
            output("`^Dwelling `\$not`^ saved: `\$%s`0`n", $sql);
        }
?>