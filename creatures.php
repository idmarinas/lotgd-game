<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/http.php");
require_once("lib/listfiles.php");

check_su_access(SU_EDIT_CREATURES);

tlschema("creatures");

//this is a setup where all the creatures are generated.
$creaturetats=array();
$creatureexp=14;
$creaturegold=36;
for ($i=1;$i<=(getsetting('maxlevel',15)+4);$i++) {
	//apply algorithmic creature generation.
	$level=$i;
	$creaturehealth=($i*10)+($i-1)-round(sqrt($i-1));
	$creatureattack=1+($i-1)*2;
	$creaturedefense+=($i%2?1:2);
	if ($i>1) {
		$creatureexp+=round(10+1.5*log($i));
		$creaturegold+=31*($i<4?2:1);
		//give lower levels more gold
	}
	$creaturestats[$i]=array(
		'creaturelevel'=>$i,
		'creaturehealth'=>$creaturehealth,
		'creatureattack'=>$creatureattack,
		'creaturedefense'=>$creaturedefense,
		'creatureexp'=>$creatureexp,
		'creaturegold'=>$creaturegold,
		);
}

page_header("Creature Editor");

require_once("lib/superusernav.php");
superusernav();

$op = httpget("op");
$subop = httpget("subop");
if (httppost('refresh')) {
	httpset('op','add');
	$op="add";
	$subop='';
	$refresh=1; //let them know this is a refresh
	//had to do this as there is no onchange in a form...
} else ($refresh=0);
if ($op == "save"){
	$forest = (int)(httppost('forest'));
	$grave = (int)(httppost('graveyard'));
	$id = httppost('creatureid');
	if (!$id) $id = httpget("creatureid");
	if ($subop == "") {
		$post = httpallpost();
		$lev = (int)httppost('creaturelevel');
		if ($id){
			$sql = "";
			foreach ($post as $key=>$val) {
				if (substr($key,0,8)=="creature") $sql.="$key = '$val', ";
			}
			foreach($creaturestats[$lev] as $key=>$val){
				if ($post[$key]!="") continue;
				if ($key!="creaturelevel" && substr($key,0,8)=="creature"){
					$sql.="$key = \"".addslashes($val)."\", ";
				}
			}
			$sql.=" forest='$forest', ";
			$sql.=" graveyard='$grave', ";
			$sql.=" createdby='".$session['user']['login']."' ";
			$sql="UPDATE " . DB::prefix("creatures") . " SET " . $sql . " WHERE creatureid='$id'";
			$result=DB::query($sql) or output("`\$".DB::error()."`0`n`#$sql`0`n");
		}else{
			$cols = array();
			$vals = array();

			foreach ($post as $key=>$val) {
				if (substr($key,0,8)=="creature") {
					array_push($cols,$key);
					array_push($vals,$val);
				}
			}
			array_push($cols, "forest");
			array_push($vals, $forest);
			array_push($cols, "graveyard");
			array_push($vals, $grave);
			reset($creaturestats[$lev]);
			foreach ($creaturestats[$lev] as $key=>$val){
				if ($post[$key]!="") continue;
				if ($key!="creaturelevel"&& substr($key,0,8)=="creature"){
					array_push($cols,$key);
					array_push($vals,$val);
				}
			}
			$sql="INSERT INTO " . DB::prefix("creatures") . " (".join(",",$cols).",createdby) VALUES (\"".join("\",\"",$vals)."\",\"".addslashes($session['user']['login'])."\")";
			$result=DB::query($sql);
			$id = DB::insert_id();
		}
		if ($result) {
			output("`^Creature saved!`0`n");
		} else {
			output("`^Creature `\$not`^ saved!`0`n");
		}
	} elseif ($subop == "module") {
		// Save module settings
		$module = httpget("module");
		$post = httpallpost();
		reset($post);
		while(list($key, $val) = each($post)) {
			set_module_objpref("creatures", $id, $key, $val, $module);
		}
		output("`^Saved!`0`n");
	}
	// Set the httpget id so that we can do the editor once we save
	httpset("creatureid", $id, true);
	// Set the httpget op so we drop back into the editor
	httpset("op", "edit");
}

$op = httpget('op');
$id = httpget('creatureid');
if ($op=="del"){
	$sql = "DELETE FROM " . DB::prefix("creatures") . " WHERE creatureid = '$id'";
	DB::query($sql);
	if (DB::affected_rows()>0){
		output("Creature deleted`n`n");
		module_delete_objprefs('creatures',$id);
	}else{
		output("Creature not deleted: %s", DB::error());//Eliminado el LINK, ya no es necesario
	}
	$op="";
	httpset('op', "");
}
if ($op=="" || $op=="search"){
	$level = (int)httpget("level");
	if (!$level) $level = 1;
	$q = httppost("q");
	if ($q) {
		$where = "creaturename LIKE '%$q%' OR creaturecategory LIKE '%$q%' OR creatureweapon LIKE '%$q%' OR creaturelose LIKE '%$q%' OR createdby LIKE '%$q%'";
	} else {
		$where = "creaturelevel='$level'";
	}
	$sql = "SELECT * FROM " . DB::prefix("creatures") . " WHERE $where ORDER BY creaturelevel,creaturename";
	$result = DB::query($sql);
	// Search form
	$search = translate_inline("Search");
	rawoutput("<form action='creatures.php?op=search' method='POST'>");
	output("Search by field: ");
	rawoutput("<input name='q' id='q'>");
	rawoutput("<input type='submit' class='button' value='$search'>");
	rawoutput("</form>");
	rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>",true);
	addnav("","creatures.php?op=search");

	addnav("Levels");
	$sql1 = "SELECT count(creatureid) AS n,creaturelevel FROM " . DB::prefix("creatures") . " group by creaturelevel order by creaturelevel";
	$result1 = DB::query($sql1);
	while ($row = DB::fetch_assoc($result1)) {
		addnav(array("Level %s: (%s creatures)", $row['creaturelevel'], $row['n']),
				"creatures.php?level={$row['creaturelevel']}");
	}
	addnav("Edit");
	addnav("Add a creature","creatures.php?op=add&level=$level");
	$opshead = translate_inline("Ops");
	$idhead = translate_inline("ID");
	$name = translate_inline("Name");
	$lev = translate_inline("Level");
	$weapon = translate_inline("Weapon");
	$winmsg = translate_inline("Win");
	$diemsg = translate_inline("Die");
	$cat = translate_inline("Category");
	$script = translate_inline("Script?");
	$author = translate_inline("Author");
	$edit = translate_inline("Edit");
	$yes = translate_inline("Yes");
	$no = translate_inline("No");
	$confirm = translate_inline("Are you sure you wish to delete this creature?");
	$del = translate_inline("Del");

	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
	rawoutput('<tr class="trhead"><th colspan="8">Criaturas de nivel '.$level.'</th></tr>');
	rawoutput("<tr class='trhead'>");
	rawoutput("<td>$opshead</td><td>$name</td><td>$cat</td><td>$weapon</td><td>$script</td><td>$winmsg</td><td>$diemsg</td><td>$author</td></tr>");
	addnav("","creatures.php");
	$i=true;
	while ($row = DB::fetch_assoc($result)) {
		$i=!$i;
		rawoutput("<tr class='".($i?"trdark":"trlight")."'>", true);
		rawoutput("<td class='uk-text-nowrap'>[ <a href='creatures.php?op=edit&creatureid={$row['creatureid']}'>");
		output_notl("%s", $edit);
		rawoutput("</a> | <a href='creatures.php?op=del&creatureid={$row['creatureid']}&level={$row['creaturelevel']}' onClick='return confirm(\"$confirm\");'>");
		output_notl("%s", $del);
		rawoutput("</a> ]</td><td>");
		addnav("","creatures.php?op=edit&creatureid={$row['creatureid']}");
		addnav("","creatures.php?op=del&creatureid={$row['creatureid']}&level={$row['creaturelevel']}");
		output_notl("(%s) %s", $row['creatureid'], $row['creaturename']);
		rawoutput("</td><td>");
		output_notl("%s", $row['creaturecategory']);
		rawoutput("</td><td>");
		output_notl("%s", $row['creatureweapon']);
		rawoutput("</td><td>");
		if ($row['creatureaiscript']!='') output_notl($yes);
			else output_notl($no);
		rawoutput("</td><td>");
		output_notl("%s", $row['creaturewin']);
		rawoutput("</td><td>");
		output_notl("%s", $row['creaturelose']);
		rawoutput("</td><td>");
		output_notl("%s", $row['createdby']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
}else{
	$level = (int)httpget('level');
	if (!$level) $level=(int)httppost('level');
	if (!$level) $level = 1;
	if ($op=="edit" || $op=="add"){
		require_once("lib/showform.php");
		addnav("Edit");
		addnav("Creature properties", "creatures.php?op=edit&creatureid=$id");
		addnav("Add");
		addnav("Add Another Creature", "creatures.php?op=add&level=$level");
		module_editor_navs("prefs-creatures", "creatures.php?op=edit&subop=module&creatureid=$id&module=");
		if ($subop == "module") {
			$module = httpget("module");
			rawoutput("<form action='creatures.php?op=save&subop=module&creatureid=$id&module=$module' method='POST'>");
			module_objpref_edit("creatures", $module, $id);
			rawoutput("</form>");
			addnav("", "creatures.php?op=save&subop=module&creatureid=$id&module=$module");
		} else {
			if ($op=="edit" && $id!=""){
				$sql = "SELECT * FROM " . DB::prefix("creatures") . " WHERE creatureid=$id";
				$result = DB::query($sql);
				if (DB::num_rows($result)<>1){
					output("`4Error`0, that creature was not found!");
				}else{
					$row = DB::fetch_assoc($result);
				}
				$level = $row['creaturelevel'];
			} else {
				//check what was posted if this is a refresh, always fill in the base values
				if ($refresh) $level = (int)httppost('creaturelevel');
				$row = $creaturestats[$level];
				$posted=array('level','category','weapon','name','win','lose','aiscript','id');
				foreach ($posted as $field) {
					$row['creature'.$field]=stripslashes(httppost('creature'.$field));
				}
				if (!$row['creatureid']) $row['creatureid']=0;
				if ($row['creaturelevel']=="") $row['creaturelevel']=$level;
				$row['forest']=(int)httppost('forest');
				$row['graveyard']=(int)httppost('graveyard');
			}
			//get available scripts
			//(uncached, won't hit there very often
			$sort = list_files("creatureai", array());
			sort($sort);
			$scriptenum=implode("",$sort);
			$scriptenum=",,none".$scriptenum;
			$form = array(
				"Creature Properties,title",
				"creatureid"=>"Creature id,hidden",
				"creaturelevel"=>"Level,range,1,".(getsetting('maxlevel',15)+4).",1",
				"Note: After changing the level causes please refresh the form to put the new preset stats for that level in,note",
				"creaturecategory"=>"Creature Category",
				"creaturename"=>"Creature Name",
				"creaturehealth"=>"Creature Health",
				"creatureweapon"=>"Weapon",
				"creatureexp"=>"Creature Experience",
				"Note: Health and Experience of the creature are base values and get modified according to the hook buffbadguy,note",
				"creatureattack"=>"Creature Attack",
				"creaturedefense"=>"Creature Defense",
				"Note: Both are base values and will be buffed up. Try to make the creature beatable for a 0 DK person too,note",
				"creaturegold"=>"Creature Gold carried",
				"Note: Gold will be more or less when fighting suicidally or slumbering,note",
				"creaturewin"=>"Win Message",
				"creaturelose"=>"Death Message",
				"forest"=>"Creature is in forest?,bool",
				"graveyard"=>"Creature is in graveyard?,bool",
				"creatureaiscript"=>"Creature's A.I.,enum".$scriptenum,
			);
			rawoutput("<form action='creatures.php?op=save' method='POST'>");
			lotgd_showform($form, $row);
			$refresh=translate_inline("Refresh");
			rawoutput("<input type='submit' class='button' name='refresh' value='$refresh'>");
			rawoutput("</form>");
			addnav("","creatures.php?op=save");
			if ($row['creatureaiscript']!='') {
				$scriptfile="scripts/".$row['creatureaiscript'].".php";
				if (file_exists($scriptfile)) {
					output("Current Script File Content:`n`n");
					output_notl(implode("`n",str_replace(array("`n"),array("``n"),color_sanitize(file($scriptfile)))));
				}
			}
		}
	}else{
		$module = httpget("module");
		rawoutput("<form action='mounts.php?op=save&subop=module&creatureid=$id&module=$module' method='POST'>");
		module_objpref_edit("creatures", $module, $id);
		rawoutput("</form>");
		addnav("", "creatures.php?op=save&subop=module&creatureid=$id&module=$module");
	}
	addnav("Navigation");
	addnav("Return to the creature editor","creatures.php?level=$level");
}
page_footer();
?>
