<?php

function dwellingstats_getmoduleinfo(){
	$info = array(
		"name"=>"Dwelling Stats Display",
		"version"=>"1.0",
		"author"=>"SexyCook",
		"category"=>"Stat Display",
		"download"=>"http://www.carloscm.de/prog/lotgd/dwellingstats.zip",
    "requires"=>array(
	     "dwellings"=>"20060105|By Sixf00t4, available on DragonPrime",
	     "dwellings_pvp"=>"1.022|By Chris Vorndran, available on DragonPrime",
    ), 
 	);
	return $info;
}

function dwellingstats_install(){
	module_addhook("charstats");
    return true;
}

function dwellingstats_uninstall(){
	return true;
}

function dwellingstats_dohook($hookname,$args){
	global $session;
	
	switch($hookname){
	case "charstats":
	
	    //Number of Dwellings, Gold and Gems in Coffers, Guards
      	     
 			$sql = "SELECT * FROM ".db_prefix("dwellings")." WHERE ownerid=".$session['user']['acctid']." and status=1";
      $result = db_query($sql);
      if (db_num_rows($result)>0){
          for ($i = 0; $i < db_num_rows($result); $i++){
              $row = db_fetch_assoc($result); 
              $gold = $row['gold'];
              $gems = $row['gems'];
              $numdwel=1;
         			$dwid = $row['dwid'];
              $guards = get_module_objpref("dwellings", $dwid, "bought", "dwellings_pvp");
              $runout = get_module_objpref("dwellings", $dwid, "run-out", "dwellings_pvp");
              if (db_num_rows($result)>1){
                  for ($i = 1; $i < db_num_rows($result); $i++){
                      $row = db_fetch_assoc($result); 
                 			$dwid = $row['dwid'];
                      $gold += $row['gold'];
                      $gems += $row['gems'];
                      $numdwel++;
                      $guards += get_module_objpref("dwellings", $dwid, "bought", "dwellings_pvp");
                      $newrun= get_module_objpref("dwellings", $dwid, "run-out", "dwellings_pvp");
                      if($runout>$newrun && $newrun>0 ) 
                          { $runout = get_module_objpref("dwellings", $dwid, "run-out", "dwellings_pvp"); }
                  } 
              }             
          }
    
          $guard="$guards ($runout turns more)";
    	 	  addcharstat("Dwellings");
    	 	  addcharstat("Number of Dwellings",$numdwel);
    	 	  addcharstat("Gold in Coffers",$gold);
    	 	  addcharstat("Gems in Coffers",$gems);
      		addcharstat("Number of Guards",$guard);
      }
		
		break;
	default:
	}
	return $args;
}

function dwellingstats_run(){

}
?>
