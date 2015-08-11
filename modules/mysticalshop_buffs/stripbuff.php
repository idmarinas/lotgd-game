<?php
function mysticalshop_buffs_stripbuff(){
	if (get_module_pref("ring","mysticalshop") == 0){
		$id = get_module_pref("ringid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";                
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){        
			strip_buff("mystical-$buffid");        
		}
	}
	if (get_module_pref("amulet","mysticalshop") == 0){
		$id = get_module_pref("amuletid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";                
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){        
			strip_buff("mystical-$buffid");        
		}
	}
	if (get_module_pref("weapon","mysticalshop") == 0){
		$id = get_module_pref("weaponid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";                
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){        
			strip_buff("mystical-$buffid");        
		}
	}
	if (get_module_pref("armor","mysticalshop")  == 0){
		$id = get_module_pref("armorid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";                
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){        
			strip_buff("mystical-$buffid");                
		}
	}
	if (get_module_pref("cloak","mysticalshop")  == 0){
		$id = get_module_pref("cloakid","mysticalshop");
			$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";                
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){        
			strip_buff("mystical-$buffid");                
		}    
	} 
	if (get_module_pref("glove","mysticalshop") == 0){
		$id = get_module_pref("gloveid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";                
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){        
			strip_buff("mystical-$buffid");                
		}    
	} 
	if (get_module_pref("boots","mysticalshop") == 0){
		$id = get_module_pref("bootid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";                
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){        
			strip_buff("mystical-$buffid");                
		}    
	}
	if (get_module_pref("misc","mysticalshop")  == 0){
		$id = get_module_pref("miscid","mysticalshop");
		$sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";                
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$buffid = $row['buffid'];
		if ($buffid>0){        
			strip_buff("mystical-$buffid");                
		}
	}
	if (get_module_pref("helm","mysticalshop")  == 0){
        $id = get_module_pref("helmid","mysticalshop");
        $sql = "SELECT buffid FROM " . db_prefix("magicitems") . " WHERE id=$id";                
        $result = db_query($sql);
        $row = db_fetch_assoc($result);
        $buffid = $row['buffid'];
        if ($buffid>0){            
			strip_buff("mystical-$buffid");                
        }
    }
}
mysticalshop_buffs_stripbuff();
?>