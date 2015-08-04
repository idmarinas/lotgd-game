<?php
/***************************************************************************/
/* Name: Creation Addon                                                    */
/* ver 3.0                                                                 */
/* Billie Kennedy => dannic06@gmail.com                                    */
/*                                                                         */
/* ToDo:                                                                   */
/*   Graphic verification for bot killing.                                 */
/***************************************************************************/

require_once("lib/http.php");
require_once("lib/nltoappon.php");
require_once("lib/showform.php");

function creationaddon_getmoduleinfo(){
	$info = array(
		"name"=>"Creation Addon",
        "version"=>"3.0.1",
        "author"=>"Billie Kennedy",
        "category"=>"Administrative",
        "download"=>"http://orpgs.com/modules.php?name=Downloads&d_op=viewdownload&cid=6",
        "vertxtloc"=>"http://www.orpgs.com/downloads/",
        "allowanonymous"=>true,
        "settings"=>array(
				"Create Addon,title",
				"creationmsg"=>"This is the message to display to new users.,textarea|Para poder jugar a La Leyenda del Dragón Verde debes haber leído y aceptado las siguientes condiciones:",
                "requireage"=> "Do you require the player to be a minimum age?,bool|1",
                "age"=>"What age to players need to be to play?,int|13",
                "requireterms"=>"Do you require the player to read the terms?,bool|1",
                "terms"=>"These are your Terms.,textarea|Some Message",
                "requireprivacy"=>"Show Privacy Policy?,bool|1",
                "privacy"=>"This is your Privacy Statement.,textarea|Some Message",
                "askbday"=>"Ask the player to input thier Birth Day?,bool|0",
                "requirebday"=>"Do you require the birthday?,bool|0",
                "bdaymsg"=>"What is the message for entering the birthday?,text|Please enter your Birthday:`n",
                "requireyear"=>"Do you require the year?,bool|0",
                "chkbday"=>"Do an Age check?,bool|0",
                "showfooter"=>"Show your terms/agreements and privacy statment in every footer?,bool|0",
                "filter_titles"=>"Filter names for titles?,bool|1",
                "filter_badnames"=>"Filter names for badnames?,bool|1",
				),
		"prefs"=>array(
                "Creation preferences,title",
                "ageverified"=>"Players age has been verified?,bool|0",
                "termsverified"=>"Player has read the terms.,bool|0",
                "privacyverified"=>"Player was shown the Privacy Statment.,bool|0",
                "month"=>"Birth Month,int|0",
                "day"=>"Birth Day,int|0",
                "year"=>"Birth Year,int|0",
                ),
	);
	return $info;
}

function creationaddon_install(){
	
	if (db_table_exists(db_prefix("badnames"))) {
		debug("Bad Names table already exists");
	}else{
		debug("Creating Bad Names table");
		$sqls = array("CREATE TABLE " . db_prefix("badnames") . " (
				bad_id TINYINT NOT NULL AUTO_INCREMENT ,
				badname VARCHAR( 50 ) NOT NULL ,
				PRIMARY KEY ( bad_id )) ENGINE=InnoDB"
				);
		while (list($key,$sql)=each($sqls)){
			db_query($sql);
		}
	}
    module_addhook("create-form");
    module_addhook("check-create");
    module_addhook("process-create");
    module_addhook("everyfooter");
    module_addhook("superuser");
        
return true;
}

function creationaddon_uninstall(){
	
	return true;
}

function creationaddon_dohook($hookname,$args){

	global $session;
        
    $age=httppost('age');
	$month=httppost('month');
	$day=httppost('day');
	$year=httppost('year');
	$terms=httppost('terms');
	$privacy=httppost('privacy');
	$msg='';
	
	switch($hookname){
        
        case "check-create":
			$blockaccount = $args['blockaccount'];
						
			// We are going to check the bad name list.
			if(get_module_setting("filter_badnames")){
				$sql = "SELECT * FROM " . db_prefix("badnames");
				$result= db_query($sql);
				for ($i=0;$i<db_num_rows($result);$i++){
					$row = db_fetch_assoc($result);
					$shortname = sanitize_name(getsetting("spaceinname", 0), httppost('name'));
					$pattern = "/".$row['badname']."/i";
					if(preg_match($pattern,$shortname)){
						$blockaccount=true;
						$msg.=translate_inline("Sorry, but your name contains a word which is not allowed.");
						$args['msg'] .=$msg;
						break;
					}
				}
			}
			
			// Lets see if they meet the age requirements.
			if(get_module_setting("requireage")==1 && $age || get_module_setting('requireage')==0){

            }else{
                $args['msg'] .= translate_inline("You must be at least years ".get_module_setting("age")." old to play.`n");
                $blockaccount=true;
			}
           
           // Did they check the box for terms?
			if(get_module_setting('requireterms')==1 && $terms || get_module_setting('requireterms')==0){

            }else{
				$args['msg'] .= translate_inline("You must read the terms.`n");
				$blockaccount=true;
			}
			
			// Did they check the box for the Privacy Policy?
			if(get_module_setting('requireprivacy')==1 && $privacy || get_module_setting('requireprivacy')==0){

            }else{
				$args['msg'] .= translate_inline("You must read the privacy policy.`n");
				$blockaccount=true;
			}
			
			if(get_module_setting("chkbday")){
			
				// Lets do a small check to see if they are actually over the age according to their birthday.
				$thisday = date("j");
				$thismonth = date("n");
				$thisyear = date("Y");
			
				// ok.. lets check to see what month they were born.  if it was after this month then subtract a year.			
				if( $thismonth-$month < 0) --$thisyear;
			
				// they were born the same month as this month.  Lets check the day to see if they have had it yet.
				if( $thisday < $day && $thismonth-$month ==0) --$thisyear;
			
				// Lets compare the math in the years.
				if(get_module_setting("requireage") && $thisyear-$year >= get_module_setting("age")){

				}else{
					$msg.=translate_inline("Sorry but you do not meet the minimum age requirements.`n");
					$args['msg'] .=$msg;
					$blockaccount=true;
				}
            }
            
			$args['blockaccount']= $blockaccount;
			
		break;	        

		case "create-form":
			
            output("`n%s`0`n`n",nltoappon(stripslashes(get_module_setting("creationmsg"))));
            
            // Make them check a box requiring a minimum age.
			if(get_module_setting("requireage")){
                rawoutput("<input type=\"checkbox\" name=\"age\" />&nbsp&nbsp");
                output("I am at or over the age of %s.`n",get_module_setting("age"));
			}
           
           // Make them check a box for terms.  Give them a link.
			if(get_module_setting("requireterms")){
				rawoutput("<input type=\"checkbox\" name=\"terms\" />&nbsp&nbsp");
				$terms = translate_inline("Terms and Agreements");
				output("I have read the ");
				rawoutput("<a href='runmodule.php?module=creationaddon&op=terms' target='_blank' onClick=\"".popup("runmodule.php?module=creationaddon&op=terms")."; return false;\" 'class='motd'>$terms</a>.<br>");		
	
			}
			
			// Make them check a box for Privacy Statement.  Give them a link.
			if(get_module_setting("requireprivacy")){
				rawoutput("<input type=\"checkbox\" name=\"privacy\" />&nbsp&nbsp");
				$privacy = translate_inline("Privacy Policy");
				output("I have read the ");
				rawoutput("<a href='runmodule.php?module=creationaddon&op=privacy' target='_blank' onClick=\"".popup("runmodule.php?module=creationaddon&op=privacy")."; return false;\" 'class='motd'>$privacy</a>.<br>");		
	
			}
			
			// Don't require birthday.  Just do it.
			if(get_module_setting("askbday")){
				output("%s`n",get_module_setting("bdaymsg"));
				output("Month");
				rawoutput("<select name=\"month\">");

				for ($i=0;$i<13;$i++){
					rawoutput("<option value='$i'>$i</option>");
				}
				rawoutput("</select>");
				output("Day");
				rawoutput("<select name=\"day\">");
				for ($i=0;$i<32;$i++){
					rawoutput("<option value='$i'>$i</option>");
				}
				rawoutput("</select>");
				if(get_module_setting("requireyear")){
					output("Year");
					rawoutput("<select name=\"year\">");
					for ($i=0;$i<75;$i++){
						$x=1935+$i;
						rawoutput("<option value='$x'>$x</option>");
					}
					rawoutput("</select>");
					rawoutput("<br>");
				}else{
					rawoutput("<br>");
				}
			}
			rawoutput("<br>");
        break;
        
        case "everyfooter":
			
			if(get_module_setting("requireprivacy") && get_module_setting('showfooter')){
				$privacy = translate_inline("Privacy Policy");
				$privacyfooter= "<br><a href='runmodule.php?module=creationaddon&op=privacy' target='_blank' onClick=\"".popup("runmodule.php?module=creationaddon&op=privacy")."; return false;\" 'class='motd'>$privacy</a>";
				addnav("","runmodule.php?module=creationaddon&op=privacy");
				if (!isset($args['source'])) {
			
					$args['source'] = array();
				
				} elseif (!is_array($args['source'])) {
						
					$args['source'] = array($args['source']);
				}
				array_push($args['source'], $privacyfooter);
			}
			
			if(get_module_setting("requireterms") && get_module_setting('showfooter')){

				$terms = translate_inline("Terms and Agreements");
				$termsfooter="<br><a href='runmodule.php?module=creationaddon&op=terms' target='_blank' onClick=\"".popup("runmodule.php?module=creationaddon&op=terms")."; return false;\" 'class='motd'>$terms</a>";
				addnav("","runmodule.php?module=creationaddon&op=terms");
				if (!isset($args['source'])) {
			
					$args['source'] = array();
				
				} elseif (!is_array($args['source'])) {
						
					$args['source'] = array($args['source']);
				}
				array_push($args['source'], $termsfooter);
			}
						
        break;
        
        case "process-create":
			global $shortname;        
			$sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE login='$shortname'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$id=$row['acctid'];
			
			if(get_module_setting("requireterms")) set_module_pref('termsverified',1,'creationaddon',$id);
			if(get_module_setting("requireprivacy")) set_module_pref('privacyverified',1,'creationaddon',$id);
			if(get_module_setting("requireage")) set_module_pref('ageverified',1,'creationaddon',$id);
			if(get_module_setting("askbday")){
				set_module_pref('month',$month,'creationaddon',$id);
				set_module_pref('day',$day,'creationaddon',$id);
				if(get_module_setting("requireyear")) set_module_pref('year',$year,'creationaddon',$id);
			}
			
		break;
		
		case "superuser":
			// lets do something here
			if (($session['user']['superuser'] & SU_EDIT_USERS)) {
				addnav("Module Configurations");
				// Stick the admin=true on so that when we call runmodule it'll
				// work to let us edit bad names even when the module is deactivated.
				addnav("Bad Names Editor","runmodule.php?module=creationaddon&op=list&admin=true");
			}
		break;
		
        }
        return $args;
}

function creationaddon_run(){
	global $session;
	require_once("lib/superusernav.php");
	$op=httpget("op");
	
	switch($op){
		case "terms":
			$terms = translate_inline("Terms and Agreements");
			popup_header($terms);
			output_notl('`n');
			output_notl(nltoappon(stripslashes(get_module_setting("terms"))),true);
			output_notl('`0`n`n');
		break;
		
		case "privacy":
			$privacy = translate_inline("Privacy Policy");
			popup_header($privacy);
			output_notl('`n');
			output_notl(nltoappon(stripslashes(get_module_setting("privacy"))),true);
			output_notl('`0`n`n');
		break;
		
		case "list":
			creationaddon_list();
		break;
		
		case "delete":
			creationaddon_delete();
		break;
		
		case "add":
			creationaddon_add();
		break;
		
	}
	
	popup_footer();
	
}

function creationaddon_list(){
	page_header("Bad Name Editor");
	global $session;
	$op = httpget('op');
	require_once("lib/superusernav.php");
	superusernav();
	addnav("Bad Names Editor");
	addnav("List Names","runmodule.php?module=creationaddon&op=list&admin=true");
	addnav("Add a Name","runmodule.php?module=creationaddon&op=add&admin=true");
	$name=translate_inline("List of Bad Names");
	
	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
	rawoutput("<tr class='trhead'>");
	rawoutput("<td colspan=3>$name</td>");
	rawoutput("</tr>");
	$sql = "SELECT bad_id, badname FROM " . db_prefix("badnames") . " ORDER BY badname";
	$result= db_query($sql);
	$x=0;
	for ($i=0;$i<db_num_rows($result);$i++){
		++$x;
		$row = db_fetch_assoc($result);
		$id = $row['bad_id'];
		if($x==1){
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
		}
		$bad_id = $row['bad_id'];
		$badname = $row['badname'];
		addnav("","runmodule.php?module=creationaddon&op=delete&bad_id=$bad_id&admin=true");
		rawoutput("<td><a href='runmodule.php?module=creationaddon&op=delete&bad_id=$bad_id&admin=true'>$badname</a></td>");
		if($x==3){
			rawoutput("</tr>");	
			$x=0;
		}
	}
	rawoutput("</table>");
	output("Just click on a name to delete it.");
	page_footer();
	return true;
}

function creationaddon_delete(){
	page_header("Bad Name Editor");
	global $session;
	$op = httpget('op');
	$bad_id = httpget('bad_id');
	require_once("lib/superusernav.php");
	superusernav();
	addnav("Bad Names Editor");
	addnav("List Names","runmodule.php?module=creationaddon&op=list&admin=true");
	addnav("Add a Name","runmodule.php?module=creationaddon&op=add&admin=true");
	$sql = "SELECT badname FROM " . db_prefix("badnames") . " WHERE bad_id='$bad_id' LIMIT 1";
	$result= db_query($sql);
	$row = db_fetch_assoc($result);
	$name = $row['badname'];
	
	$sql = "DELETE FROM " . db_prefix("badnames") . " WHERE bad_id='$bad_id'";
	$result = db_query($sql);
	if($result){
		output("`$%s`0 has been deleted.", $name);
	}else{
		output("Failed to delete from database.");
	}
	
	page_footer();
	return true;
}

function creationaddon_add(){
	page_header("Bad Name Editor");
	global $session;

	require_once("lib/superusernav.php");
	superusernav();
	$badnamesarray=array(
		"Bad Name,title",
		"bname"=>"Bad Name to Add",
	);
	addnav("Bad Names Editor");
	addnav("List Names","runmodule.php?module=creationaddon&op=list&admin=true");
	addnav("Add a Name","runmodule.php?module=creationaddon&op=add&admin=true");

	$banname = httppost("bname");

	if($banname){
		// Check to see if it is already in the list.
		$sql = "SELECT badname FROM " . db_prefix("badnames") . " WHERE badname='$banname' LIMIT 1";
		$result= db_query($sql);
		$row = db_fetch_assoc($result);
		if($banname == $row['badname']){
			output("Name already in the list.");
		}else{
			// It isn't in the list so lets add it.
			$sql = "INSERT INTO " . db_prefix("badnames") . " (badname) VALUES ('$banname')";
			$resul = db_query($sql);
			
			output("Added `$%s`0 to the Bad Name List.",$banname);
		}
	}else{
		$row = array();
		rawoutput("<form action='runmodule.php?module=creationaddon&op=add&admin=true' method='POST'>");
		addnav("","runmodule.php?module=creationaddon&op=add&admin=true");
		showform($badnamesarray,$row);
		rawoutput("</form>");
	}
	page_footer();
	return true;
}

?>
