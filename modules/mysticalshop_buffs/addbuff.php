<?php
	require_once("modules/mysticalshop_buffs/getbuff.php");
	if (get_module_pref("ring","mysticalshop")){
		$id = get_module_pref("ringid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		   
			$buff = get_item_buff($buffid);
			$buff['schema']="ring";
			apply_buff("mystical-$buffid",$buff);
		}
	}
	if (get_module_pref("amulet","mysticalshop")){
		$id = get_module_pref("amuletid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		   
			$buff = get_item_buff($buffid);
			$buff['schema']="amulet";
			apply_buff("mystical-$buffid",$buff);
		}
	}
	if (get_module_pref("weapon","mysticalshop")){
		$id = get_module_pref("weaponid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		   
			$buff = get_item_buff($buffid);
			$buff['schema']="weapon";
			apply_buff("mystical-$buffid",$buff);
		}
	}	 
	if (get_module_pref("armor","mysticalshop")){
		$id = get_module_pref("armorid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";				 
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		   
			$buff = get_item_buff($buffid);
			$buff['schema']="armor";		
			apply_buff("mystical-$buffid",$buff);			   
		}
	}	 
	if (get_module_pref("cloak","mysticalshop")){
		$id = get_module_pref("cloakid","mysticalshop");
			$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";				 
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		   
			$buff = get_item_buff($buffid);
			$buff['schema']="cloak";		
			apply_buff("mystical-$buffid",$buff);			   
		}	 
	}			 
	if (get_module_pref("glove","mysticalshop")){
		$id = get_module_pref("gloveid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";				 
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		   
			$buff = get_item_buff($buffid);
			$buff['schema']="gloves";		 
			apply_buff("mystical-$buffid",$buff);			   
		}	 
	}	 
	if (get_module_pref("boots","mysticalshop")){
		$id = get_module_pref("bootid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";				 
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		   
			$buff = get_item_buff($buffid);
			$buff['schema']="boots";		
			apply_buff("mystical-$buffid",$buff);			   
		}	 
	}	 
	if (get_module_pref("misc","mysticalshop")){
		$id = get_module_pref("miscid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";				 
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){		   
			$buff = get_item_buff($buffid);
			$buff['schema']="misc";		   
			apply_buff("mystical-$buffid",$buff);			   
		}	 
	}	 
	if (get_module_pref("helm","mysticalshop")){
		$id = get_module_pref("helmid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";				 
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){
			$buff = get_item_buff($buffid);
			$buff['schema']="helmet";
			apply_buff("mystical-$buffid",$buff);
		}
	}	 
?>