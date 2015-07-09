<?PHP
require_once("lib/commentary.php");
require_once("lib/showform.php");
require_once("modules/cityprefs/lib.php");
        
function cityprefs_getmoduleinfo() {
	$info = array(
		"name"=>"City Preferences Addon",
		"version"=>"20070417",
		"author"=>"Sixf00t4",
		"category"=>"General",
		"description"=>"Gives the ability to use prefs based on cities",
		"vertxtloc"=>"http://www.legendofsix.com/",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1155",
    );                
	return $info;
}

function cityprefs_install(){
    global $session;
	require_once("lib/tabledescriptor.php");
	$cityprefs = array(
		'cityid'=>array('name'=>'cityid', 'type'=>'int unsigned',	'extra'=>'not null auto_increment'),
		'module'=>array('name'=>'module', 'type'=>'varchar(255)', 'extra'=>'not null'),
		'cityname'=>array('name'=>'cityname', 'type'=>'varchar(255)', 'extra'=>'not null'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'cityid'),
		'index-cityid'=>array('name'=>'cityid', 'type'=>'index', 'columns'=>'cityid'),
		'index-module'=>array('name'=>'module', 'type'=>'index', 'columns'=>'module'),
		'index-cityname'=>array('name'=>'cityname', 'type'=>'index', 'columns'=>'cityname'));
    synctable(db_prefix('cityprefs'), $cityprefs, true);
    if (!is_module_active('cityprefs')){
        if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO) output_notl("`4Installing cityprefs Module.`n");
		$sql = "INSERT INTO ".db_prefix("cityprefs")." (module,cityname) VALUES ('none','".getsetting("villagename",LOCATION_FIELDS)."')";
		db_query($sql); 
		$vloc = array();
		$vloc = modulehook("validlocation", $vloc);
		ksort($vloc);
		reset($vloc);
		foreach($vloc as $loc=>$val) {
			$sql = "select modulename from ".db_prefix("module_settings")." where value='".addslashes($loc)."' and setting='villagename'";
			$result=db_query($sql);
			$row = db_fetch_assoc($result);
			$sql = "INSERT INTO ".db_prefix("cityprefs")." (module,cityname) VALUES ('".$row['modulename']."','".addslashes($loc)."')";
			db_query($sql);  
		}
    }else{
        if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO) output("`4Updating cityprefs Module.`n");
    }

    module_addhook("superuser");
    module_addhook("changesetting");
	return true;
}

function cityprefs_uninstall(){
    output("`4Un-Installing cityprefs Module.`n");
    $sql = "DROP TABLE ".db_prefix("cityprefs");
    db_query($sql);
    $sql = "delete from ".db_prefix("module_objprefs")." where objtype='city'";
    db_query($sql);
	return true;
}

function cityprefs_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
        case "changesetting":
   			if ($args['setting'] == "villagename") {
				$sql = "UPDATE ".db_prefix("cityprefs")." SET cityname='".addslashes($args['new'])."' WHERE cityname='".addslashes($args['old'])."'";
                db_query($sql);
            }
			break;
        case "superuser":
			if ($session['user']['superuser'] & SU_EDIT_USERS) {
				addnav("Editors");
				addnav("City Prefs","runmodule.php?module=cityprefs&op=su");
			}
		break; 
	}
	return $args;
}

function cityprefs_run() {
	global $session;
    page_header("City Prefs Editor");
    $op=httpget('op');
	$cityid = httpget('cityid');
	if($cityid>0){
	$cityname=get_cityprefs_cityname("cityid",$cityid);
		page_header("%s Properties",$cityname);
		$modu=get_cityprefs_module("cityid",$cityid);
		if($modu!="none"){
			addnav("Operations");
			addnav("Module settings","configuration.php?op=modulesettings&module=$modu");
		}
		addnav("Navigation");
		if(is_module_active("cities"))	addnav(array("Journey to %s",$cityname),"runmodule.php?module=cities&op=travel&city=".urlencode($cityname)."&su=1");
		else addnav(array("Journey to %s",$cityname),"village.php");
	}
	addnav("Navigation");	
   addnav("Back to the Grotto","superuser.php");   
   if(is_module_active("modloc")) addnav("Module locations","runmodule.php?module=modloc");
   if($op!="su") addnav("Back to city list","runmodule.php?module=cityprefs&op=su");
    switch ($op) {
        case "su":
			addnav("Operations");
            addnav("Auto-add new cities","runmodule.php?module=cityprefs&op=update");  
            $id=translate_inline("ID");
            $name=translate_inline("City Name");
            $module=translate_inline("Module");
            $edit=translate_inline("Edit");
            $sql = "select * from ".db_prefix("cityprefs");
            $result=db_query($sql);
            rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'><tr class='trhead'><td style=\"width:50px\">$id</td><td style='width:150px' align=center>$name</td><td align=center>$module</td><td align=center>$edit</td></tr>"); 
            for ($i = 0; $i < db_num_rows($result); $i++){
                $row = db_fetch_assoc($result);
                $vloc = array();
                $vname = getsetting("villagename", LOCATION_FIELDS);
                $vloc[$vname] = "village";
                $vloc = modulehook("validlocation", $vloc);
                ksort($vloc);
                reset($vloc);
                foreach($vloc as $loc=>$val) {
                    if ($loc == $row['cityname']) $area=$val;
                }
                rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center>".$row['cityid']."</td><td align=center>");
                output_notl("%s",$row['cityname']);
                rawoutput("</td><td align=center>");
                output_notl("%s",$row['module']);
                rawoutput("</td><td align=center>");
                rawoutput("<a href='runmodule.php?module=cityprefs&op=editmodule&area=".htmlentities($val)."&cityid=".$row['cityid']."'>$edit</a></td></tr>");
                addnav("","runmodule.php?module=cityprefs&op=editmodule&area=".htmlentities($val)."&cityid=".$row['cityid']."");  
            }
            rawoutput("</table>");
            break;
    
        case "update":
            $vloc = array();
            $vloc = modulehook("validlocation", $vloc);
            ksort($vloc);
            reset($vloc);
            $out=0;
            foreach($vloc as $loc=>$val) {
                $sql = "select cityname from ".db_prefix("cityprefs")." where cityname='".addslashes($loc)."'";
                $result=db_query($sql);
                if(db_num_rows($result)==0){
                    $sql = "select modulename from ".db_prefix("module_settings")." where value='".addslashes($loc)."' and setting='villagename'";
                    $result=db_query($sql);
                    $row = db_fetch_assoc($result);           
                    $sql = "INSERT INTO ".db_prefix("cityprefs")." (module,cityname) VALUES ('".$row['modulename']."','".addslashes($loc)."')";
                    db_query($sql);
                    $out=1;
                    output("`n`@%s`0 was added.",$loc);
                }
            }
            if($out==0) output("There were no new locations found.");
        break; 
        case "editmodule": //code from clan editor by CortalUX
        case "editmodulesave":
			addnav("Operations");
			addnav("Edit city name and module","runmodule.php?module=cityprefs&op=editcity&cityid=$cityid");
			addnav("Delete this city","runmodule.php?module=cityprefs&op=delcity&cityid=$cityid");
			$mdule = httpget("mdule");
			if($mdule==""){
				output("Select a pref to edit.`n`n");
			}else{
				if ($op=="editmodulesave") {
					// Save module prefs
					$post = httpallpost();
					reset($post);
					while(list($key, $val) = each($post)) {
						set_module_objpref("city", $cityid, $key, stripslashes($val), $mdule);
					}
					output("`^Saved!`0`n");
				}
				require_once("lib/showform.php");
				rawoutput("<form action='runmodule.php?module=cityprefs&op=editmodulesave&cityid=$cityid&mdule=$mdule' method='POST'>");
				module_objpref_edit("city", $mdule, $cityid);
				rawoutput("</form>");
				addnav("","runmodule.php?module=cityprefs&op=editmodulesave&cityid=$cityid&mdule=$mdule");
				//code from clan editor by CortalUX
			}
 			addnav("Module Prefs");
			module_editor_navs("prefs-city","runmodule.php?module=cityprefs&op=editmodule&cityid=$cityid&mdule=");
		break;
        
        case "editcity":
			output("Changing these values will not affect the city itself, just what city is associated with the preferences.  This is useful if you want to preserve prefs after removing a city.");
			addnav("Navigation");
			addnav("Back to city properties","runmodule.php?module=cityprefs&op=editmodule&cityid=$cityid");
            $sql = "select * from ".db_prefix("cityprefs")." where cityid=$cityid";
            $result=db_query($sql);
            $row = db_fetch_assoc($result);
            $module=$row['module'];
            $city=$row['cityname'];
            $submit=translate_inline("Submit");
            rawoutput("<form action='runmodule.php?module=cityprefs&op=editcity2&cityid=$cityid' method='POST'>");
            addnav("","runmodule.php?module=cityprefs&op=editcity2&cityid=$cityid");
            rawoutput("<input name='cityname' id='cityname' value='$city' size='40' maxlength='255'><br>");
            rawoutput("<input name='modulename' id='modulename' value='$module' size='40' maxlength='255'><br>");
            rawoutput("<input type='submit' class='button' value='$submit'></form>");              
        break;
        
        case "editcity2":
			addnav("Navigation");
			addnav("Back to city properties","runmodule.php?module=cityprefs&op=editmodule&cityid=$cityid");
            $cityname = httppost('cityname');
            $modulename = httppost('modulename');
            db_query("update ".db_prefix("cityprefs")." set cityname='".$cityname."',module='".$modulename."' where cityid=$cityid");
            output("The city name is now %s and the module name is %s.",$cityname,$modulename);
        break;
        
        case "delcity":
			addnav("Navigation");
            $cityid = httpget('cityid');
			addnav("Back to city properties","runmodule.php?module=cityprefs&op=editmodule&cityid=$cityid");
			addnav("Options");
			addnav("Yes, delete it","runmodule.php?module=cityprefs&op=delcity2&cityid=$cityid");
            output("Are you sure you want to delete this city?  All city prefs will be deleted.  If you would like to retain these settings for a future city, just rename it.");
        break;

        case "delcity2":
			addnav("Navigation");
			addnav("Back to city properties","runmodule.php?module=cityprefs&op=editmodule&cityid=$cityid");
            $cityid = httpget('cityid');		
            db_query("delete from ".db_prefix("cityprefs")." where cityid=$cityid");
            output("The city has been deleted.");
        break;
        }
    page_footer();
    }    
?>