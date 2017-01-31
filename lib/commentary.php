<?php
// translator ready
// addnews ready
// mail ready
require_once 'lib/datetime.php';
require_once 'lib/sanitize.php';
require_once 'lib/http.php';

tlschema('commentary');

global $fiveminuteload;

$comsecs = [];
function commentarylocs()
{
	global $comsecs, $session;

	if (is_array($comsecs) && count($comsecs)) return $comsecs;

	$vname = getsetting('villagename', LOCATION_FIELDS);
	$iname = getsetting('innname', LOCATION_INN);
	tlschema('commentary');
	$comsecs['village'] = sprintf_translate("%s Square", $vname);
	if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO) {
		$comsecs['superuser']=translate_inline("Grotto");
	}
	$comsecs['shade']=translate_inline('Land of the Shades');
	$comsecs['grassyfield']=translate_inline('Grassy Field');
	$comsecs['inn']="$iname";
	$comsecs['motd']=translate_inline('MotD');
	$comsecs['veterans']=translate_inline('Veterans Club');
	$comsecs['hunterlodge']=translate_inline("Hunter's Lodge");
	$comsecs['gardens']=translate_inline('Gardens');
	$comsecs['waiting']=translate_inline('Clan Hall Waiting Area');
	if (getsetting("betaperplayer", 1) == 1 && @file_exists('pavilion.php')) $comsecs['beta']=translate_inline('Pavilion');

	tlschema();
	// All of the ones after this will be translated in the modules.
	$comsecs = modulehook('moderate', $comsecs);
	rawoutput(tlbutton_clear());

	return $comsecs;
}

function removecommentary($cid, $reason, $mod)
{
	global $session;

	$sql = "SELECT * FROM ".DB::prefix('commentary')." WHERE commentid=$cid";
	$result = DB::query($sql);
	$row = DB::fetch_assoc($result);
	$row['info']=@stripslashes($row['info']);
	$row['info']=@unserialize($row['info']);
	//debug($row);
	$row['info']['hidecomment']=1;
	$row['info']['hidereason']=$reason;
	$row['info']['hiddenby']=$mod;
	//debug($row);
	$info = serialize($row['info']);
	$info = addslashes($info);
	$sql = "UPDATE ".DB::prefix("commentary")." SET info='$info' WHERE commentid=$cid";
	DB::query($sql);

	invalidatedatacache("commentary/latestcommentary_{$row['section']}");
	invalidatedatacache("commentary/commentarycount_{$row['section']}");
	invalidatedatacache("commentary/whosonline_$section");
}

function restorecommentary($cid, $reason, $mod)
{
	global $session;

	$sql = "SELECT * FROM ".DB::prefix("commentary")." WHERE commentid=$cid";
	$result = DB::query($sql);
	$row = DB::fetch_assoc($result);
	$row['info']=@stripslashes($row['info']);
	$row['info']=@unserialize($row['info']);
	unset($row['info']['hidecomment']);
	$time = time();
	$row['info']['restored'][$time]['reason']=$reason;
	$row['info']['restored'][$time]['mod']=$mod;
	$info = serialize($row['info']);
	$info = addslashes($info);
	$sql = "UPDATE ".DB::prefix('commentary')." SET info='$info' WHERE commentid=$cid";
	DB::query($sql);

	invalidatedatacache("commentary/latestcommentary_{$row['section']}");
	invalidatedatacache("commentary/commentarycount_{$row['section']}");
	invalidatedatacache("commentary/whosonline_$section");
}

function addcommentary()
{
	global $session, $emptypost, $afk, $dni, $output;

	$section = httppost('section');
	$talkline = httppost('talkline');
	$schema = httppost('schema');
	$comment = trim(httppost('insertcommentary'));
	$counter = httppost('counter');
	$remove = URLDecode(httpget('removecomment'));
	$restore = URLDecode(httpget('restorecomment'));
	//debug(httpallpost());

	if (httpget("bulkdelete"))
	{
		$everything=httpallpost();
		foreach($everything AS $key=>$val)
		{
			if (substr($key,0,14)=="deletecomment_")
			{
				$del = str_replace("deletecomment_","",$key);
				removecommentary($del,"Mass deletion by ".$session['user']['name'],$session['user']['acctid']);
			}
		}
	}

	if ($remove>0) removecommentary($remove,"Moderated by ".$session['user']['name'],$session['user']['acctid']);
	if ($restore>0) restorecommentary($restore,"Restored by ".$session['user']['name'],$session['user']['acctid']);

	if (! $comment) return false;

	if ('DNI' == $session['user']['chatloc']) $dni = true;

	//-- Colors of LOTGD
	$colors = $output->get_colors();

	if (substr($comment,0,9) == "/chatcol ")
	{
		$ucol = substr($comment,9,1);
		if (!isset($colors[$ucol]))
		{
			output("`c`b`4Invalid default talk colour`b`nYou entered an invalid colour code, please try again.`0`c`n");

			return false;
		}
		else
		{
			$session['user']['prefs']['ucol'] = $ucol;
			output_notl("`c`bDefault talk colour changed`b`n`".$ucol."This is your new default commentary dialogue colour.  When you type in commentary areas, this colour will show up automatically.  If you're an Extra-Awesome Site Supporter, you can also change the colour of your character's dialogue during quests, monster fights and other in-game areas using the /talkcol switch, in the same way you just used the /chatcol switch.`0`c`n");

			return false;
		}
	}
	else if (substr($comment,0,9) == "/talkcol ")
	{
		$ucol = substr($comment,9,1);
		if (!isset($colors[$ucol]))
		{
			output("`c`b`4Invalid default talk colour`b`nYou entered an invalid colour code, please try again.`0`c`n");

			return false;
		}
		else
		{
			$session['user']['prefs']['ugcol'] = $ucol;
			if ($session['user']['donation'] >= 2000)
			{
				output_notl("`c`bDefault talk colour changed`b`n`".$ucol."This is your new default in-game dialogue colour.  This is the colour we'll use to represent your character's dialogue during quests, monster encounters, and other in-game things.  If you choose a colour commonly used by monsters or other characters, you might have problems figuring out who's talking - if that's the case, you can reset to the default with \"/talkcol #\".  You can also change your default colour for commentary areas by using the /chatcol switch, in the same way you just used /talkcol.`0`c`n");
			}
			else
			{
				output("`c`bNot enough Supporter Points`b`nSorry, but due to the extra system load that the /talkcol switch uses, this feature is restricted to players with more than 2,000 Supporter Points.`0`c`n");
			}

			return false;
		}
	}

	if ($comment == strtoupper($comment))
	{
		//this is an all-uppercase entry.  Do not add this comment to the database; instead, check it for built-in stuff like AFK and GREM, then run it through the commentarycommand hook
		if ($comment=="AFK" || $comment=="BRB")
		{
			$session['user']['chatloc']="AFK";
			$afk=true;
			output("`0`n`c`bYou are Away From the Keyboard until you load another page.`b`c`n");

			return false;
		}
		if ($comment=="DNI")
		{
			if ($session['user']['chatloc']=="DNI")
			{
				$session['user']['chatloc'] = $section;
				$dni=false;
				output("`0`n`c`bYou are no longer in Do Not Interrupt status.`b`c`n");
			}
			else
			{
				$session['user']['chatloc'] = "DNI";
				$dni=true;
				output("`0`n`c`bYou are in Do Not Interrupt status.  Type DNI again to leave.`b`nDNI status is used for whenever you're doing or saying something that means other players shouldn't try to interact with you.  For example, when two or more characters are chatting just outside of the main group of characters, and other characters shouldn't be able to hear them.`c`n");
			}
			return false;
		}
		if ($comment=="GREM")
		{
			//handle deleting the player's last comment
			$sql = "SELECT * FROM ".DB::prefix("commentary")." WHERE author='".$session['user']['acctid']."' ORDER BY commentid DESC LIMIT 1";
			$result = DB::query($sql);
			while ($row = DB::fetch_assoc($result))
			{
				$now = time();
				$then = strtotime($row['postdate']);
				$ago = $now - $then;
				if ($ago < 120)
				{
					removecommentary($row['commentid'],"Typo Gremlin",$session['user']['acctid']);
					output("`0`n`c`bA nearby Typo Gremlin notices the peculiar tastiness of your previous comment.  Within moments, a small horde of them have descended upon your words, and consumed them.`b`c`n");
				}
				else
				{
					output("`0`n`c`bThe Typo Gremlins turn up their nose at your latest comment - it's just too old.  They have no taste for stale words.`b`c`n");
				}
			}

			return false;
		}
		$hookcommand = [
			'command' => $comment,
			'section' => $section,
		];
		$returnedhook = modulehook('commentarycommand', $hookcommand);
		if (! $returnedhook['skipcommand'])
		{
			//if for some reason you're going to involve a command that can be a mix of upper and lower case, set $args['skipcommand'] and $args['ignore'] to true and handle it in postcomment instead.
			if (! $returnedhook['processed'])
			{
				output("`c`b`JCommand Not Recognized`b`0`nWhen you type in ALL CAPS, the game doesn't think you're talking to other players; it thinks you're trying to perform an action within the game.  For example, typing `#GREM`0 will remove the last comment you posted, as long as you posted it less than two minutes ago.  Typing `#AFK`0 or `#BRB`0 will turn your online status bar grey, so that people know you're `#A`0way `#F`0rom the `#K`0eyboard (or, if you prefer, that you'll `#B`0e `#R`0ight `#B`0ack).  Typing `#DNI`0 will let other players know that you're busy talking to one particular player - maybe somewhere off-camera - and that you don't want to be interrupted right now.`nSome areas have special hidden commands or other easter eggs that you can hunt for.  This time around, you didn't trigger anything special.`c`0`n");
			}

			return false;
		}
	}

	if ($section || $talkline || $comment)
	{
		$tcom = color_sanitize($comment);
		if ($tcom == "" || $tcom == ":" || $tcom == "::" || $tcom == "/me")
		{
			$emptypost = 1;
		}
		else
		{
			$comment = comment_sanitize($comment);
			injectcommentary($section, $talkline, $comment);
		}
	}
}

function injectsystemcomment($section, $comment)
{

	if (strncmp($comment, "/game", 5) !== 0) $comment = "/game" . $comment;
	injectrawcomment($section, 0, $comment);
}

function injectrawcomment($section, $author, $comment, $name = false, $info = false)
{
	if ($info)
	{
		$sqlinfo = @serialize($info);
		$sqlinfo = addslashes($sqlinfo);
	}
	$name = addslashes($name);
	$sql = "INSERT INTO " . DB::prefix('commentary') . " (postdate,section,author,comment,name,info) VALUES ('".date("Y-m-d H:i:s")."','$section',$author,\"$comment\",\"$name\",\"$sqlinfo\")";
	DB::query($sql);

	//update datacache for latest commentary - no need to invalidate this and then rebuild it again, just pop one off the start and put our new one on the end
	$commentbuffer = datacache("commentary/latestcommentary_$section",60);
	if (is_array($commentbuffer))
	{
		$newcomment = [
			'postdate' => date('Y-m-d H:i:s'),
			'section' => $section,
			'author' => $author,
			'comment' => stripslashes($comment),
			'name' => $name,
			'info' => $info,
		];
		array_unshift($commentbuffer, $newcomment);
		updatedatacache("commentary/latestcommentary_".$section,$commentbuffer);
	}
	//invalidatedatacache("commentary/latestcommentary_".$section);

	//commentary count - no need to invalidate this and then rebuild it again, just increment it by one
	$count = datacache("commentary/commentarycount_".$section,60);
	if (is_array($count)){
		$count[0]['c']+=1;
		updatedatacache("commentary/commentarycount_".$section,$count);
	}
	//invalidatedatacache("commentary/commentarycount_".$section);

	invalidatedatacache("commentary/whosonline_$section");
}

function injectcommentary($section, $talkline, $comment)
{
	global $session,$doublepost;

	// Make the comment pristine so that we match on it correctly.
	$comment = stripslashes($comment);
	$doublepost=0;
	$emptypost = 0;
	$colorcount = 0;
	if ($comment !="")
	{
		$commentary = str_replace("`n","",soap($comment));

		$info = array();
		$info['rawcomment']=$comment;
		$clanid = $session['user']['clanid'];
		if ($clanid && $session['user']['clanrank'])
		{
			$clansql = "SELECT clanname,clanshort FROM ".DB::prefix("clans")." WHERE clanid='$clanid'";
			$clanresult = DB::query($clansql);
			$clanrow = DB::fetch_assoc($clanresult);
			$info['clanname'] = $clanrow['clanname'];
			$info['clanshort'] = $clanrow['clanshort'];
			$info['clanid'] = $clanid;
			$info['clanrank'] = $session['user']['clanrank'];
		}

		if (!isset($session['user']['prefs']['ucol'])) $session['user']['prefs']['ucol'] = false;
		else $info['talkcolour'] = $session['user']['prefs']['ucol'];

		$args = array('commentline'=>$commentary, 'commenttalk'=>$talkline, 'info'=>$info, 'name'=>$session['user']['name'], 'section'=>$section);
		$args = modulehook("postcomment", $args);
		//debug($args);

		//A module tells us to ignore this comment, so we will
		if (1 == $args['ignore']) return false;

		$commentary = $args['commentline'];
		$talkline = $args['commenttalk'];
		$info = $args['info'];
		$name = $args['name'];
		$talkline = $talkline;

		//Try to make it so that italics are always closed properly
		$italics = substr_count($commentary,"`i");
		if ($italics)
		{
			//odd number of italics - add one at the end
			if($odd = $italics%2) $commentary.="`i";
		}

		//Clean up the comment a bit
		$commentary = preg_replace("'([^[:space:]]{45,45})([^[:space:]])'","\\1 \\2",$commentary);
		$commentary = addslashes($commentary);

		// Sort out /game switches
		if (substr($commentary,0,5)=="/game" && ($session['user']['superuser']&SU_IS_GAMEMASTER)==SU_IS_GAMEMASTER)
		{
			//handle game master inserts now, allow double posts
			injectsystemcomment($section,$commentary);
		}
		else
		{
			//check for double posts
			$commentbuffer = datacache("commentary/latestcommentary_".$section,60);
			if (is_array($commentbuffer))
			{
				if ($commentbuffer[0]['comment'] == stripslashes($commentary)) $doublepost = true;
			}
			else
			{
				$sql = "SELECT comment FROM " . DB::prefix("commentary") . " WHERE section='$section' AND author='".$session['user']['acctid']."' ORDER BY commentid DESC LIMIT 1";
				$result = DB::query($sql);
				$row = DB::fetch_assoc($result);
				DB::free_result($result);
				if ($row['comment']==stripslashes($commentary)) $doublepost = true;
			}

			if (!$doublepost)
			{
				//Not a double post, inject the comment
				injectrawcomment($section, $session['user']['acctid'], $commentary, $session['user']['name'], $info);
				$session['user']['laston']=date("Y-m-d H:i:s");
			}
		}
	}
}

function commentdisplay($intro, $section, $message = "Interject your own commentary?", $limit = 10, $talkline = "says", $schema = false)
{
	// Let's add a hook for modules to block commentary sections
	$args = modulehook('blockcommentarea', ['section' => $section] );
	if (isset($args['block']) && ($args['block'] == "yes")) return;

	if ($intro) output($intro);

	viewcommentary($section, $message, $limit, $talkline, $schema);
}

function getcommentary($section, $limit = 25, $talkline, $customsql = false, $showmodlink = false, $returnlink = false)
{
	//$gcstart = getmicrotime(true);
	global $session, $REQUEST_URI, $translation_namespace, $chatloc, $bottomcid;

	$com = max((int)httpget("comscroll"), 0);

	if (! $returnlink) $returnlink = urlencode($_SERVER['REQUEST_URI']);

	if ($showmodlink)
	{
		$link = buildcommentarylink("&bulkdelete=true&comscroll=".$com);
		addnav("",$link);
		rawoutput("<form action=".$link." id='bulkdelete' method='post'>");
		$del = "Del";
		$undel = "UNDel";
	}

	//stops people from clicking on Bio links in the MoTD
	$nobios = array("motd.php"=>true,"runmodule.php?module=global_banter"=>true);
	if (!array_key_exists(basename($_SERVER['SCRIPT_NAME']),$nobios)) $nobios[basename($_SERVER['SCRIPT_NAME'])] = false;
	if ($nobios[basename($_SERVER['SCRIPT_NAME'])]) $linkbios=false;
	else $linkbios=true;

	// Needs to be here because scrolling through the commentary pages, entering a bio, then scrolling again forward
	// then re-entering another bio will lead to $com being smaller than 0 and this will lead to an SQL error later on.
	if (httpget("comscroll") !== false && (int)$session['lastcom'] == $com+1) $cid = (int)$session['lastcommentid'];
	else $cid = 0;

	$session['lastcom'] = $com;

	if (!$cid) $cid=1;
	if ($customsql) $sql = $customsql;
	else if ($section=="all")
	{
		$sql = "SELECT * FROM ".DB::prefix("commentary")." WHERE section NOT LIKE 'dwelling%' AND section NOT LIKE 'clan%' AND section NOT LIKE 'pet-%' ORDER BY commentid DESC LIMIT ".($com*$limit).",$limit";
		$result = DB::query($sql);
		$viewingallsections=1;
	}
	else
	{
		$start = microtime(true);
		$sql = "SELECT * FROM ".DB::prefix("commentary")." WHERE section='$section' ORDER BY commentid DESC LIMIT ".($com*$limit).",$limit";
		if (!$com)
		{
			//save doing DB::fetch_assoc on commentary that's already cached; just unserialize and load it in one chunk
			$commentbuffer = datacache("commentary/latestcommentary_".$section,60);
			//debug($commentbuffer);
		}
		if (!is_array($commentbuffer) || $com)
		{
			$commentbuffer = array();
			$result = DB::query($sql);
			while ($row = DB::fetch_assoc($result))
			{
				$row['info']=@stripslashes($row['info']);
				$row['info']=@unserialize($row['info']);
				if (!is_array($row['info'])) $row['info']=array();
				$row['info']['link'] = buildcommentarylink("&commentid=".$row['commentid']);
				$commentbuffer[]=$row;
			}
			if (!$com) updatedatacache("commentary/latestcommentary_$section", $commentbuffer);
		}
	}

	$end = microtime(true);
	$tot = $end - $start;
	//debug($tot);

	//pre-formatting
	$commentbuffer = modulehook("commentbuffer-preformat",$commentbuffer);

	$rowcount = count($commentbuffer);
	if ($rowcount > 0) $session['lastcommentid'] = $commentbuffer[0]['commentid'];

	//figure out whether to handle absolute or relative time
	if (!array_key_exists('timestamp', $session['user']['prefs'])) $session['user']['prefs']['timestamp'] = 0;
	$session['user']['prefs']['timeoffset'] = round($session['user']['prefs']['timeoffset'],1);

	if (!array_key_exists('commentary_reverse', $session['user']['prefs'])) $session['user']['prefs']['commentary_reverse'] = 0;

	//this array of userids means that with a single query we can figure out who's online and nearby
	$acctidstoquery=array();

	//prepare the actual comment line part of the comment - is it hidden, is it an action, is it a game comment, should we show a moderation link, clan rank colours, posting date abs/rel
	$loop1start = getmicrotime(true);
	$bioretlink = $returnlink;
	$bioretlink = urlencode(buildcommentarylink("&frombio=true",$returnlink));
	$restorelink = buildcommentarylink("&comscroll=".$com."&restorecomment=",$returnlink);
	$removelink = buildcommentarylink("&comscroll=".$com."&removecomment=",$returnlink);
	for ($i=0; $i < $rowcount; $i++)
	{
		if ((!$commentbuffer[$i]['info']['hidecomment']) || $showmodlink)
		{
			$thiscomment="";
			if ($viewingallsections) $thiscomment.="`b".$row['section']."`0`b: ";
			$row = $commentbuffer[$i];
			$row['acctid']=$row['author'];
			$acctidstoquery[]=$row['author'];
			//$row['comment'] = comment_sanitize($row['comment']);
			if (substr($row['comment'],0,1)==":" || substr($row['comment'],0,3)=="/me")
			{
				$row['skiptalkline']=true;
				//remove beginning /me
				//$length = strlen($row['comment']);
				if (substr($row['comment'],0,3)=="/me")
				{
					//debug("Match on ".$row['comment']);
					$row['comment'] = substr($row['comment'],3);
				}
				else if (substr($row['comment'],0,2)=="::")
				{
					//debug("Match on ".$row['comment']);
					$row['comment'] = substr($row['comment'],2);
				}
				else if (substr($row['comment'],0,1)==":")
				{
					//debug("Match on ".$row['comment']);
					$row['comment'] = substr($row['comment'],1);
				}
			}
			if ($row['info']['gamecomment'] || (substr($row['comment'],0,5)=="/game" && !$row['name']))
			{
				//debug("Game Comment: ".$row['comment']);
				$row['gamecomment']=true;
				$row['skiptalkline']=true;
				$row['info']['icons']=array();
				//$length = strlen($row['comment']);
				$row['comment'] = str_replace("/game","",$row['comment']);
			}
			if ($linkbios && !isset($row['biolink']))$row['biolink']=true;
			if ($showmodlink)
			{
				if ($row['info']['hidecomment'])
				{
					//$link = buildcommentarylink("&restorecomment=".$row['commentid']."&comscroll=".$com,$returnlink);
					$thiscomment.="`0[<a href='$restorelink".$row['commentid']."'>$undel</a>]`0 <del>";
					addnav("",$restorelink.$row['commentid']);
				}
				else
				{
					//$link = buildcommentarylink("&removecomment=".$row['commentid']."&comscroll=".$com,$returnlink);
					$thiscomment.="`0[<a href='$removelink".$row['commentid']."'>$del</a>] <input type='checkbox' name='deletecomment_".$row['commentid']."'> `0 ";
					addnav("",$removelink.$row['commentid']);
				}
			}
			if (!$row['gamecomment'] && ($row['info']['clanid'] || $row['info']['clanid']===0) && $row['info']['clanrank']){
				$clanrankcolors=array(CLAN_APPLICANT=>"`!",CLAN_MEMBER=>"`3",CLAN_OFFICER=>"`^",CLAN_LEADER=>"`&", CLAN_FOUNDER=>"`\$");
				$thiscomment.="`0<a title=\"".$row['info']['clanname']."\">&lt;".$clanrankcolors[$row['info']['clanrank']].$row['info']['clanshort']."`0&gt;</a>";
			}
			if (!$row['gamecomment'])
			{
				if ($row['biolink'])
				{
					$bio = "bio.php?char=".$row['acctid']."&ret=".$bioretlink;
					if (!$row['skiptalkline']) $thiscomment.="<a href=\"$bio\" style=\"text-decoration: none\">`&".$row['name']."</a>`& ";
					else $thiscomment.="<a href=\"$bio\" style=\"text-decoration: none\">`&".$row['name']."</a>`&";

					addnav("",$bio);
				}
				else
				{
					if (!$row['skiptalkline']) $thiscomment.="`&".$row['name']."`& ";
					else $thiscomment.="`&".$row['name']."`&";
				}
			}
			// if ($row['skiptalkline']){
				// $thiscomment.="`&";
			// }
			if (!$row['skiptalkline'])
			{
				$thiscomment.=$talkline." \"";
				if (!isset($row['info']['talkcolour']) || $row['info']['talkcolour']===false) $thiscomment.="`#";
				else $thiscomment.="`".$row['info']['talkcolour'];
			}
			$thiscomment.=str_replace("&amp;","&",htmlentities($row['comment'], ENT_COMPAT, getsetting("charset", "UTF-8")));
			$thiscomment.="`0";
			if (!$row['skiptalkline']) $thiscomment.="\"";
			if ($row['info']['hidecomment']) $thiscomment.="</del>";
			$commentbuffer[$i]['comment']=$thiscomment;
			$commentbuffer[$i]['icons']=$row['info']['icons'];
			$commentbuffer[$i]['time']=strtotime($row['postdate']);
			if ($session['user']['prefs']['timestamp']==1)
			{
				if (!isset($session['user']['prefs']['timeformat'])) $session['user']['prefs']['timeformat'] = "[m/d h:ia]";
				$time = strtotime($row['postdate']) + ($session['user']['prefs']['timeoffset'] * 60 * 60);
				$s=date("`7" . $session['user']['prefs']['timeformat'] . "`0 ",$time);
				$commentbuffer[$i]['displaytime'] = $s;
			}
			elseif ($session['user']['prefs']['timestamp']==2)
			{
				$s=reltime(strtotime($row['postdate']));
				$commentbuffer[$i]['displaytime'] = "<span style='font-family: Courier New, Courier, monospace;'>[$s]`0</span> ";
			}
		}
		else unset($commentbuffer[$i]);

		$bottomcid = $commentbuffer[$i]['commentid'];
	}
	$loop1end = getmicrotime(true);
	$loop1tot = $loop1end - $loop1start;
	//debug("Loop 1: ".$loop1tot);

	//send through a modulehook for additional processing by modules
	$commentbuffer = modulehook("commentbuffer",$commentbuffer);

	//get offline/online/nearby status
	$acctids = join(',',$acctidstoquery);
	$onlinesql = "SELECT acctid, laston, loggedin, chatloc FROM ".DB::prefix("accounts"). (!empty($acctids) ? " WHERE acctid IN ($acctids)" : "");
	//cache it for 30 seconds
	if (!$com){
		$onlineresult = DB::query_cached($onlinesql,"commentary/whosonline_".$section,30);
	} else {
		$onlineresult = DB::query($onlinesql);
	}
	$onlinestatus = [];
	$offline = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"));
	while ($row = DB::fetch_assoc($onlineresult)){
		$onlinestatus[$row['acctid']]=$row;
	}
	$onlinestatus[$session['user']['acctid']]['chatloc']=$chatloc;

	$commentbuffer = array_values($commentbuffer);
	$rowcount = count($commentbuffer);

	//second loop through - add basic status icons for online/offline/nearby/afk/dnd
	$loop2start = getmicrotime(true);
	for ($i=0; $i < $rowcount; $i++)
	{
		if (isset($commentbuffer[$i]))
		{
			$row = $commentbuffer[$i];
			if ($onlinestatus[$row['author']]['chatloc']=="AFK")
			{
				$commentbuffer[$i]['info']['online']=-1;
				$icon = [
					'icon' => "images/icons/onlinestatus/afk.png",
					'mouseover' => "Away from Keyboard",
				];
				$commentbuffer[$i]['info']['icons']['online']=$icon;
				continue;
			}
			if ($onlinestatus[$row['author']]['chatloc']=="DNI"){
				$commentbuffer[$i]['info']['online']=-1;
				$icon = [
					'icon' => "images/icons/onlinestatus/dni.png",
					'mouseover' => "DNI (please don't try to talk to this player right now!)",
				];
				$commentbuffer[$i]['info']['icons']['online']=$icon;
				continue;
			}
			if ($onlinestatus[$row['author']]['laston'] < $offline || !$onlinestatus[$row['author']]['loggedin']){
				$commentbuffer[$i]['info']['online']=0;
				$icon = [
					'icon' => "images/icons/onlinestatus/offline.png",
					'mouseover' => "Offline",
				];
			}
			else if ($onlinestatus[$row['author']]['chatloc']==$chatloc)
			{
				$commentbuffer[$i]['info']['online']=2;
				$icon = [
					'icon' => "images/icons/onlinestatus/nearby.png",
					'mouseover' => "Nearby",
				];
			}
			else
			{
				$commentbuffer[$i]['info']['online']=1;
				$icon = [
					'icon' => "images/icons/onlinestatus/online.png",
					'mouseover' => "Online",
				];
			}
			$commentbuffer[$i]['info']['icons']['online']=$icon;
		}
	}
	$loop2end = getmicrotime(true);
	$loop2tot = $loop2end - $loop2start;
	//debug("Loop 2: ".$loop2tot);

	//debug($commentbuffer);
	// $gcend = getmicrotime(true);
	// $gctotal = $gcend - $gcstart;
	// debug("getcommentary execution time: ".$gctotal);

	return $commentbuffer;
}

function preparecommentaryline($line)
{
	$finaloutput="";

	//debug($line);
	if (!$line['info']['gamecomment'])
	{
		$icons = $line['info']['icons'];
		$mouseover = $line['info']['mouseover'];
		//debug($line);

		//make it so that online icons always show up first
		$online = $icons['online'];
		unset($icons['online']);
		if (isset($online['icon'])) $finaloutput.="<img src=\"".$online['icon']."\" title=\"".$online['mouseover']."\" alt=\"".$online['mouseover']."\"> ";

		//show mouseover icon
		if (count($mouseover))
		{
			$mouseoutput = "<a href=\"#\" class=\"commentarymouseoverlink\"><img src=\"images/icons/eye.png\"><span class=\"commentarymouseover\">";
			foreach($mouseover AS $entry)
			{
				$mouseoutput.=$entry;
			}
			$mouseoutput.="</a></span>";
			$finaloutput.=$mouseoutput;
		}

		//show other icons
		if (count($icons))
		{
			foreach($icons AS $key => $vals)
			{
				if (file_exists($vals['icon'])) $finaloutput.="<img src=\"".$vals['icon']."\" title=\"".$vals['mouseover']."\" alt=\"".$vals['mouseover']."\"> ";
				else $finaloutput.=$vals['mouseover']." ";
			}
		}
	}
	$finaloutput.=$line['displaytime'];
	$finaloutput.=$line['comment'];

	return $finaloutput;
}

function dualcommentary($section, $message = 'Interject your own commentary?', $limit = 25, $talkline = 'says', $schema = false)
{
	global $session,$REQUEST_URI;

	rawoutput("
		<script type=\"text/javascript\">
		function commentaryHelp(id) {
			if (document.getElementById(id).style.display == \"none\") {
				document.getElementById(id).style.display = \"\";
			} else {
				document.getElementById(id).style.display = \"none\";
			}
		}
		</script>");

	if (!array_key_exists('commentary_multichat', $session['user']['prefs'])){
		$session['user']['prefs']['commentary_multichat'] = 1;
	}
	if (!array_key_exists('commentary_multichat_stack', $session['user']['prefs'])){
		$session['user']['prefs']['commentary_multichat_stack'] = 0;
	}

	if (httpget('switchmultichat')){
		$session['user']['prefs']['commentary_multichat'] = httpget('switchmultichat');
	}
	if (httpget('switchstack')){
		if ($session['user']['prefs']['commentary_multichat_stack']==0){
			$session['user']['prefs']['commentary_multichat_stack']=1;
		} else {
			$session['user']['prefs']['commentary_multichat_stack']=0;
		}
	}

	$cpref = $session['user']['prefs']['commentary_multichat'];
	$spref = $session['user']['prefs']['commentary_multichat_stack'];

	$nlink = buildcommentarylink("&refresh=1");
	//debug($nlink);

	if ($session['user']['dragonkills']==0 && $session['user']['level']==1){
		output("`JHey, it looks like you're new in town!  Why not introduce yourself in the `bBanter Channel`b?  Everybody's really friendly here!`n`nIf you haven't already done so, you can give us a short physical description of your character that'll be shown to other players when they move their mouse over the eye symbol.  Once you've figured out a good description, check your preferences menu!`n`nThis message will go away after you've hit Level 2 for the first time.  Have fun!`n`n");
	}

	if ($cpref==1)
	{
		if ($spref)
		{
			output("`0`bStory Channel`b (<a href=\"$nlink&switchmultichat=3\">hide</a> | <a href=\"$nlink&switchstack=1\">split</a> | <a href=\"javascript:commentaryHelp('roleplay')\">?</a>)",true);
			rawoutput("<div id='roleplay' style='display:none;'>");
			output("The Story channel is an in-character chat board for roleplaying.  Some people end up with very complex storylines!  You can find a roleplaying guide in the Wiki, if you're so inclined.");
			rawoutput("</div>");
			addnav("","$nlink&switchmultichat=3");
			viewcommentary($section,$message,$limit,$talkline,$schema,false,false,true);
			output("`0`bBanter Channel`b (<a href=\"$nlink&switchmultichat=2\">hide</a> | <a href=\"$nlink&switchstack=1\">split</a> | <a href=\"javascript:commentaryHelp('ooc')\">?</a>)",true);
			rawoutput("<div id='ooc' style='display:none;'>");
			output("The Banter channel is an out-of-character chat board for whatever the hell you feel like chinwagging about.  The channels are separate so that roleplaying doesn't get in the way of friendly natter and vice-versa!");
			rawoutput("</div>");
			addnav("","$nlink&switchmultichat=2");
			addnav("","$nlink&switchstack=1");
			viewcommentary($section."_aux",$message,$limit,$talkline,$schema,false,false,true);
		}
		else
		{
			//Side-by-side output
			rawoutput("<table width=100% border=0 cellpadding=5 cellspacing=0><tr><td width=50% valign=top>");
			rawoutput("<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td>");
			output("`bStory Channel`b (<a href=\"$nlink&switchmultichat=3\">hide</a> | <a href=\"$nlink&switchstack=1\">stack</a> | <a href=\"javascript:commentaryHelp('roleplay')\">?</a>)",true);
			rawoutput("<div id='roleplay' style='display:none;'>");
			output("The Story channel is an in-character chat board for roleplaying.  Some people end up with very complex storylines!  You can find a roleplaying guide in the Wiki, if you're so inclined.");
			rawoutput("</div>");
			rawoutput("</td></tr><tr><td style=\"border-right: 1px dotted #333333; padding-right: 8px;\">");
			addnav("","$nlink&switchmultichat=3");
			addnav("","$nlink&switchstack=1");
			viewcommentary($section,$message,$limit,$talkline,$schema,true,false,true);
			rawoutput("</td></tr></table>");
			rawoutput("</td><td width=50% valign=top>");
			rawoutput("<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td>");
			output("`bBanter Channel`b (<a href=\"$nlink&switchmultichat=2\">hide</a> | <a href=\"$nlink&switchstack=1\">stack</a> | <a href=\"javascript:commentaryHelp('ooc')\">?</a>)",true);
			rawoutput("<div id='ooc' style='display:none;'>");
			output("The Banter channel is an out-of-character chat board for whatever the hell you feel like chinwagging about.  The channels are separate so that roleplaying doesn't get in the way of friendly natter and vice-versa!");
			rawoutput("</div>");
			rawoutput("</td></tr><tr><td>");
			addnav("","$nlink&switchmultichat=2");
			viewcommentary($section."_aux",$message,$limit,$talkline,$schema,true,false,true);
			rawoutput("</td></tr></table>");
			rawoutput("</td></tr><tr><td valign=top>");
			commentaryfooter($section,$message,$limit,$talkline,$schema);
			rawoutput("</td><td valign=top>");
			commentaryfooter($section."_aux",$message,$limit,$talkline,$schema);
			rawoutput("</td></tr></table>");
		}
	}
	else if ($cpref==2)
	{
		output("`bStory Channel`b (<a href=\"$nlink&switchmultichat=3\">switch</a> | <a href=\"$nlink&switchmultichat=1\">dual</a>)",true);
		viewcommentary($section,$message,$limit,$talkline,$schema);
		addnav("","$nlink&switchmultichat=3");
		addnav("","$nlink&switchmultichat=1");
	}
	else if ($cpref==3)
	{
		output("`bBanter Channel`b (<a href=\"$nlink&switchmultichat=2\">switch</a> | <a href=\"$nlink&switchmultichat=1\">dual</a>)",true);
		viewcommentary($section."_aux",$message,$limit,$talkline,$schema);
		addnav("","$nlink&switchmultichat=2");
		addnav("","$nlink&switchmultichat=1");
	}
	$session['recentcomments'] = date("Y-m-d H:i:s");
}

function preparecommentaryblock($section, $message = 'Interject your own commentary?', $limit = 25, $talkline = 'says', $schema = false,$skipfooter = false, $customsql = false, $skiprecentupdate = false, $overridemod = false, $returnlink = false)
{
 	global $session,$REQUEST_URI,$doublepost, $translation_namespace;
	global $emptypost;
	global $chatloc,$afk,$dni,$moderating;

	if (($session['user']['superuser'] & SU_EDIT_COMMENTS) || $overridemod){
		$showmodlink=true;
	} else {
		$showmodlink=false;
	}

	$ret = "";

	//skip assigning chatloc if this chatloc's id ends with "_aux" - this way we can have dual chat areas
	if (!$afk && isset($session['user']['chatloc']) && $session['user']['chatloc'] != 'DNI')
	{
		if (substr($section,strlen($section)-4,strlen($section))!="_aux"){
			$chatloc = $section;
		} else {
			$chatloc = substr($section,0,-4);
		}
		$session['user']['chatloc']=$chatloc;
	} else {
		$chatloc = "AFK";
	}

	if($section) {
		$ret.="<a name='$section'></a>";
		// Let's add a hook for modules to block commentary sections
		$args = modulehook("blockcommentarea", array("section"=>$section));
		if (isset($args['block']) && ($args['block'] == "yes"))
			return;
	}

	$commentbuffer = getcommentary($section,$limit,$talkline,$customsql,$showmodlink,$returnlink);
	$rowcount = count($commentbuffer);

	if ($doublepost) $ret .= "`\$`bDouble post?`b`0`n";
	if ($emptypost) $ret .= "`\$`bWell, they say silence is a virtue.`b`0`n";

	//output the comments!

	$start = microtime(true);

	if (($moderating && $rowcount) || !$moderating)
	{
		$new = 0;
		if (!isset($session['user']['prefs']['commentary_recentline']))
		{
			$session['user']['prefs']['commentary_recentline']=1;
		}
		if (!$session['user']['prefs']['commentary_reverse'])
		{
			//debug($session['recentcomments']);
			for ($i=$rowcount-1; $i>=0; $i--)
			{
				$line = preparecommentaryline($commentbuffer[$i]);
				//highlight new commentary lines
				if ($commentbuffer[$i]['postdate'] > $session['recentcomments'] && !$new && $session['user']['prefs']['commentary_recentline']){
					$ret .= "<hr style=\"border-bottom: 1px dotted #333333; border-top: 0; border-left: 0; border-right: 0;\" />";
					$new = 1;
					$ret .= "<a name=\"commentaryjump_".$section."\"></a>";
					//debug("Jump at comment id".$commentbuffer[$i]['commentid']);
				} else if ($i == 2){
					$ret .= "<a name=\"commentaryjump_".$section."\"></a>";
					//debug("Jump at comment id".$commentbuffer[$i]['commentid']);
				}
				$ret .= "$line`n";
			}
		}
		else
		{
			for ($i=0; $i<=$rowcount-1; $i++)
			{
				$line = preparecommentaryline($commentbuffer[$i]);
				$ret .= "$line`n";
				if ($commentbuffer[$i]['postdate'] > $session['recentcomments'] && !$new && $session['user']['prefs']['commentary_recentline'])
				{
					$ret .= "<hr style=\"border-bottom: 1px dotted #333333; border-top: 0; border-left: 0; border-right: 0;\" />";
					$new = 1;
				}
			}
		}
	}

	$end = microtime(true);
	$tot = $end - $start;
	//debug("Preparecommentaryblock loop: ".$tot);

	return $ret;
}

function viewcommentary($section, $message = 'Interject your own commentary?', $limit = 25, $talkline = 'says', $schema = false,$skipfooter = false, $customsql = false, $skiprecentupdate = false, $overridemod = false)
{
	global $session, $REQUEST_URI, $doublepost, $translation_namespace, $emptypost, $chatloc, $afk, $dni, $moderating;

	if (! array_key_exists('commentary_auto_update', $session['user']['prefs'])) $session['user']['prefs']['commentary_auto_update'] = 1;

	if (httpget("disable_auto_update")) $session['user']['prefs']['commentary_auto_update'] = 0;

	if (httpget("enable_auto_update")) $session['user']['prefs']['commentary_auto_update'] = 1;

	if (! isset($returnlink) || ! $returnlink) $returnlink = urlencode($_SERVER['REQUEST_URI']);

	if (($session['user']['superuser'] & SU_EDIT_COMMENTS) || $overridemod) $showmodlink=true;
	else $showmodlink=false;

	if (! $skiprecentupdate) $session['recentcomments'] = date("Y-m-d H:i:s");

	rawoutput("<!--start of commentary area--><a name=\"commentarystart\"></a>");

	global $fiveminuteload;
	if ($session['user']['prefs']['commentary_auto_update'] && !httpget('comscroll') && $fiveminuteload < 8)
	{
		$jsec = strtolower($section);
		$jsec = str_replace("_","",$jsec);
		$jsec = str_replace("-","",$jsec);
		$jsec = str_replace(",","0",$jsec);

		//this javascript code auto-updates the chat automatically, based on server load.
		//The current five-minute average as last reported to the game (reported once per minute) is stored in the global $fiveminuteload.
		// $refreshbase = ceil($fiveminuteload);
		// if ($refreshbase < 1) $refreshbase = 1;
		// $refreshrate = $refreshbase*1000;

		//or, just go with every six seconds
		$refreshrate = 6000;

		rawoutput("<script type=\"text/javascript\">
			var limit".$jsec." = 0;
			timer".$jsec."=setTimeout(\"loadnewchat".$jsec."()\",".$refreshrate.");
			function loadnewchat".$jsec."() {
				limit".$jsec." ++;
				if (window.XMLHttpRequest){
					// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp".$jsec."=new XMLHttpRequest();
				} else {
					// code for IE6, IE5
					xmlhttp".$jsec."=new ActiveXObject(\"Microsoft.XMLHTTP\");
				}
				xmlhttp".$jsec.".onreadystatechange=function(){
					if (xmlhttp".$jsec.".readyState==4 && xmlhttp".$jsec.".status==200 && limit".$jsec." < 100){
						document.getElementById(\"ajaxcommentarydiv".$jsec."\").innerHTML = xmlhttp".$jsec.".responseText;
						timer".$jsec."=setTimeout(\"loadnewchat".$jsec."()\",".$refreshrate.");
					} else if (limit".$jsec." >= 100){
						document.getElementById(\"ajaxcommentarynoticediv".$jsec."\").innerHTML = 'Auto-update has timed out.  Click any link to restart the clock.';
					}
				}
				xmlhttp".$jsec.".open(\"GET\",\"ajaxcommentary.php?section=".$section."&message=".$message."&limit=".$limit."&talkline=".$talkline."&returnlink=".$returnlink."\",true);
				xmlhttp".$jsec.".send();
			}
		</script>");
		rawoutput("<div id=\"ajaxcommentarydiv".$jsec."\">");
	}

	output_notl("`n");

	$out = preparecommentaryblock($section,$message,$limit,$talkline,$schema,$skipfooter,$customsql,$skiprecentupdate,$overridemod,$returnlink);
	$commentary = appoencode($out,true);
	rawoutput($commentary);
	//output_notl("%s",$commentary,true);

	if ($session['user']['prefs']['commentary_auto_update']) rawoutput("</div>");

	if ($showmodlink){
		//moderation link mass delete button
		rawoutput('<input type="submit" class="ui primary button" value="Mass Delete"></form>');
	}

	if (!$skipfooter) commentaryfooter($section,$message,$limit,$talkline,$schema);

	rawoutput("<!--end of commentary area-->");
}

function commentaryfooter($section, $message = 'Interject your own commentary?', $limit = 25, $talkline = 'says', $schema = false)
{
 	global $session, $REQUEST_URI, $doublepost, $translation_namespace, $emptypost, $chatloc, $moderating, $bottomcid;
	//Output page jumpers
	$com = httpget('comscroll');
	if ($section=="all") $sql = "SELECT count(commentid) AS c FROM " . DB::prefix("commentary") . " WHERE section NOT LIKE 'dwelling%' AND section NOT LIKE 'clan%' AND section NOT LIKE 'pet-%'";
	else $sql = "SELECT count(commentid) AS c FROM " . DB::prefix("commentary") . " WHERE section='$section'";

	$r = DB::query_cached($sql,"commentary/commentarycount_$section", 60);
	//$r = DB::query($sql);
	$val = DB::fetch_assoc($r);
	$rowcount = $val['c'];
	$val = round($val['c'] / $limit + 0.5,0) - 1;

	$returnlink = urlencode($_SERVER['REQUEST_URI']);
	$returnlink = urlencode(buildcommentarylink("&frombio=true",$returnlink));

	$hook = [
		"section"=>$section,
		"message"=>$message,
		"talkline"=>$talkline,
		"returnlink"=>$returnlink,
	];

	$hook = modulehook('commentary_talkform',$hook);

	$section = $hook['section'];
	$message = $hook['message'];
	$talkline = $hook['talkline'];

	if ($session['user']['loggedin'])
	{
		if ($message!="X")
		{
			$message="`n`@$message`0`n";
			output($message,true);
			if (! isset($hook['blocktalkform']) || ! $hook['blocktalkform']) talkform($section,$talkline,$limit,$schema);
		}
	}

	$jump = false;
	if (!isset($session['user']['prefs']['nojump']) || $session['user']['prefs']['nojump'] == false) $jump = true;

	//new-style commentary display with page numbers
	$nlink = buildcommentarylink("&refresh=1");

	//reinstating back and forward links
	output_notl("`n");
	// $prev = "`0&lt;&lt;";
	// $next = "`0&gt;&gt;";
	$prev = '<i class="fa fa-fw fa-angle-double-left"><span>`0&lt;&lt;</span></i>';
	$next = '<i class="fa fa-fw fa-angle-double-right"><span>`0&gt;&gt;</span></i>';

	if ($rowcount>=$limit  && $com!=$val)
	{
		$req = buildcommentarylink("&comscroll=".($com+1));
		output_notl("<a href=\"$req\">$prev</a> | ",true);
		addnav("",$req);
	}
	$cplink = buildcommentarylink("&comscroll=".$com."&refresh=1");
	addnav("",$cplink);
	output_notl("`0<a href=\"$cplink\">".translate_inline("Refresh")."</a> | <a href=\"$nlink\">".translate_inline('Latest')."</a>",true);
	if ($com>0)
	{
		$req = buildcommentarylink("&comscroll=".($com-1));
		output_notl(" | <a href=\"$req\">$next</a>",true);
		addnav("",$req);
	}

	output_notl("`n");
	if ($session['user']['prefs']['commentary_auto_update'])
	{
		$req = buildcommentarylink("&disable_auto_update=true");
		addnav("",$req);
		output_notl(" <a href=\"$req\">".translate_inline("Disable Auto-Update")."</a>",true);
		$jsec = strtolower($section);
		$jsec = str_replace("_","",$jsec);
		$jsec = str_replace("-","",$jsec);
		$jsec = str_replace(",","0",$jsec);
		rawoutput("<div id =\"ajaxcommentarynoticediv".$jsec."\"></div>");
	}
	else
	{
		$req = buildcommentarylink("&enable_auto_update=true");
		output_notl(" <a href=\"$req\">".translate_inline("Enable Auto-Update")."</a>",true);
		addnav("",$req);
	}

	$jsec = strtolower($section);
	$jsec = str_replace("_","",$jsec);
	$jsec = str_replace("-","",$jsec);
	$jsec = str_replace(",","0",$jsec);
	rawoutput("<div id=\"typedisplay".$jsec."\"></div>");

	addnav("",$nlink);
	output("`n`n`0Jump to commentary page: ");
	$start = microtime(true);
	$nlink = buildcommentarylink("&refresh=1&comscroll=");
	for ($i=$val; $i>=0; $i--)
	{
		// $nlink = buildcommentarylink("&comscroll=".$i."&refresh=1");
		$ndisp = 1+$val-$i;
		if ($com!=$i)
		{
			output_notl("<a href=\"".$nlink.$i."\">$ndisp</a> ",true);
			addnav("",$nlink.$i);
		}
		else output_notl("`@$ndisp`0 ",true);
	}
	$end = microtime(true);
	$tot = $end - $start;
	//debug("commentary footer page numbers loop: ".$tot);
	output_notl("`n");
	if ($moderating) output("`bLast Comment ID shown on this page: %s`b`n",number_format($bottomcid));
	else modulehook("commentaryoptions");
}

function buildcommentarylink($append, $returnlink = false)
{
	//TODO: Check for removecomment and restorecomment flags, possibly via regexp
	global $session,$REQUEST_URI;
	$jump = false;
	if (isset($session['user']['prefs']['nojump']) && $session['user']['prefs']['nojump'] == true) {
		$jump = true;
	}
	if (!$returnlink){
		$nlink = comscroll_sanitize($REQUEST_URI);
	} else {
		$decoded = urldecode($returnlink);
		//debug($decoded);
		if (strpos($decoded,'/') !== false){
			//debug("Decoded!");
			$nlink = str_replace("/","",$decoded);
			//debug($nlink);
		}
		$nlink = comscroll_sanitize($nlink);
	}

	$nlink = preg_replace("'&r(emovecomment)?=([[:digit:]]|-)*'", "", $nlink);
	$nlink = preg_replace("'\\?r(emovecomment)?=([[:digit:]]|-)*'", "?", $nlink);
	$nlink = preg_replace("'&r(estorecomment)?=([[:digit:]]|-)*'", "", $nlink);
	$nlink = preg_replace("'\\?r(estorecomment)?=([[:digit:]]|-)*'", "?", $nlink);

	// $nlink = preg_replace("'&a(utomod_inc)?=([[:digit:]]|-)*'", "", $nlink);
	// $nlink = preg_replace("'\\?a(utomod_inc)?=([[:digit:]]|-)*'", "?", $nlink);
	// $nlink = preg_replace("'&a(utomod_dec)?=([[:digit:]]|-)*'", "", $nlink);
	// $nlink = preg_replace("'\\?a(utomod_dec)?=([[:digit:]]|-)*'", "?", $nlink);

	//solve world map re-fight exploit
	$nlink = str_replace("?op=fight","?op=continue",$nlink);
	$nlink = str_replace("&op=fight","&op=continue",$nlink);

	//stop multiple addnav counters getting added to the end
	$nlink = preg_replace("'&c?=([[:digit:]]|-)*'", "", $nlink);
	$nlink = preg_replace("'\\?c?=([[:digit:]]|-)*'", "?", $nlink);

	$nlink = str_replace("?enable_auto_update=true","",$nlink);
	$nlink = str_replace("?disable_auto_update=true","",$nlink);
	$nlink = str_replace("&enable_auto_update=true","",$nlink);
	$nlink = str_replace("&disable_auto_update=true","",$nlink);
	$nlink = str_replace("&bulkdelete=true","",$nlink);
	$nlink = str_replace("?bulkdelete=true","",$nlink);
	$nlink = str_replace("&frombio=true","",$nlink);
	$nlink .= $append;
	$nlink = str_replace(".php&",".php?",$nlink);
	$nlink = str_replace("&switchstack=1","",$nlink);
	$nlink = str_replace("?switchstack=1","",$nlink);
	$nlink = str_replace("&switchmultichat=1","",$nlink);
	$nlink = str_replace("&switchmultichat=2","",$nlink);
	$nlink = str_replace("&switchmultichat=3","",$nlink);
	$nlink = str_replace("?switchmultichat=1","",$nlink);
	$nlink = str_replace("?switchmultichat=2","",$nlink);
	$nlink = str_replace("?switchmultichat=3","",$nlink);

	//debug($nlink);

	if (!strpos($nlink,"?"))
	{
		$nlink = str_replace("&","?",$nlink);
		if (!strpos($nlink,"?")) $nlink.="?";
	}

	//debug($nlink);

	if ($jump && $section) $nlink .= '#commentarystart';
	addnav('',$nlink);
	return $nlink;
}

function talkform($section, $talkline, $limit = 10, $schema = false)
{
	require_once 'lib/forms.php';

	global $REQUEST_URI, $session, $translation_namespace, $chatsonpage;

	if ($schema===false) $schema=$translation_namespace;
	tlschema("commentary");

	$jump = true;
	if (isset($session['user']['prefs']['nojump']) && $session['user']['prefs']['nojump'] == true) $jump = false;

	if ($jump && httpget('comment') && httppost('focus')==$section) $focus = true;
	else $focus = false;

	if (! isset($session['user']['prefs']['ucol'])) $session['user']['prefs']['ucol'] = false;

	if (translate_inline($talkline,$schema)!="says") $tll = strlen(translate_inline($talkline,$schema))+11;
	else $tll=0;

	$req = buildcommentarylink("&comment=1");
	if ($jump) $req .= "#commentaryjump_".$section;
	addnav("",$req);

	// *** AJAX CHAT MOD START ***
	output_notl("<form action=\"$req\" method='POST' autocomplete='false'>",true);
	$args1 = array(
			"formline"	=>	"<form action=\"$req\" id='commentaryform' method='post' autocomplete='false'",
			"section"	=>	$section,
			"talkline"	=>	$talkline,
			"schema"	=>	$schema
		);
		// $args1 = modulehook("commentarytalkline",$args1);
		//rawoutput('<div id="commentaryformcontainer">');
		output_notl($args1['formline'] . ">",true);
	// *** AJAX CHAT MOD END ***

	global $fiveminuteload;
	$add = htmlentities(translate_inline("Add"), ENT_QUOTES, getsetting("charset", "UTF-8"));

	if ($session['user']['prefs']['commentary_auto_update'] && !httpget('comscroll') && $fiveminuteload < 8)
	{
		$jsec = strtolower($section);
		$jsec = str_replace("_","",$jsec);
		$jsec = str_replace("-","",$jsec);
		$jsec = str_replace(",","0",$jsec);
		//debug($jsec);
		rawoutput('<div class="ui action input">');
		previewfield("insertcommentary", $session['user']['name'], $talkline, true, array("size"=>"30", "maxlength"=>255-$tll),false,$jsec,$session['user']['prefs']['ucol'],$focus);
		output_notl("<button type='submit' class='ui primary button'>$add</button>",true);
		rawoutput('</div>');
		rawoutput("<script type=\"text/javascript\">
			var typetimelimit".$jsec." = 0;
			var timebetween".$jsec." = 1500;
			var oldchars".$jsec." = 'xxxxx';
			var newchars".$jsec." = 'xxxxx';
			var newchars".$jsec." = document.getElementById('input".$jsec."').value;
			var oldchars".$jsec." = newchars".$jsec.";
			function typedisplay".$jsec."() {
				typetimelimit".$jsec." ++;
				newchars".$jsec." = document.getElementById('input".$jsec."').value;

				if (window.XMLHttpRequest){
					// code for IE7+, Firefox, Chrome, Opera, Safari
					txmlhttp".$jsec."=new XMLHttpRequest();
				} else {
					// code for IE6, IE5
					txmlhttp".$jsec."=new ActiveXObject(\"Microsoft.XMLHTTP\");
				}
				txmlhttp".$jsec.".onreadystatechange=function(){
					if (txmlhttp".$jsec.".readyState==4 && txmlhttp".$jsec.".status==200 && typetimelimit".$jsec." < 600){
						document.getElementById(\"typedisplay".$jsec."\").innerHTML = txmlhttp".$jsec.".responseText;
						ttimer".$jsec."=setTimeout(\"typedisplay".$jsec."()\",timebetween".$jsec.");
						ttimeopen".$jsec." = typetimelimit".$jsec." * 2;
						ttimeleft".$jsec." = 600 - ttimeopen".$jsec.";
					}
				}
				if (oldchars".$jsec.".length!=newchars".$jsec.".length){
					txmlhttp".$jsec.".open(\"GET\",\"whostyping.php?section=".$section."&updateplayer=1\",true);
				} else {
					txmlhttp".$jsec.".open(\"GET\",\"whostyping.php?section=".$section."&updateplayer=0\",true);
				}
				txmlhttp".$jsec.".send();
				oldchars".$jsec." = newchars".$jsec.";
			}
			typedisplay".$jsec."();
		</script>");
	}
	else
	{
		if ($fiveminuteload >= 8)
		{
			output("Server load is currently too high for auto-update chat.  This will hopefully balance out in a few minutes.`n");
		}
		rawoutput('<div class="ui action input">');
		previewfield("insertcommentary", $session['user']['name'], $talkline, true, array("size"=>"30", "maxlength"=>255-$tll),false,false,$session['user']['prefs']['ucol']);
		output_notl("<button type='submit' class='ui primary button'>$add</button>",true);
		rawoutput('</div>');
		//debug("System load too high at ".$fiveminuteload);
	}
	rawoutput("<input type='hidden' name='talkline' value='$talkline'>");
	rawoutput("<input type='hidden' name='schema' value='$schema'>");
	rawoutput("<input type='hidden' name='focus' value='$section'>");
	rawoutput("<input type='hidden' name='counter' value='{$session['counter']}'>");
	$session['commentcounter'] = $session['counter'];
	if ($section=="X"){
		$vname = getsetting("villagename", LOCATION_FIELDS);
		$iname = getsetting("innname", LOCATION_INN);
		$sections = commentarylocs();
		reset ($sections);
		output_notl("<select name='section'>",true);
		while (list($key,$val)=each($sections)){
			output_notl("<option value='$key'>$val</option>",true);
		}
		output_notl("</select>",true);
	}
	else
	{
		output_notl("<input type='hidden' name='section' value='$section'>",true);
	}

	// *** DRAGONBG.COM CORE PATCH START***
	//Aparece junto al campo de texto
	// output_notl("<input type='submit' class='button' value='$add'>	",true);
	// *** DRAGONBG.COM CORE PATCH END***
	rawoutput("</form>");
	tlschema();
}

tlschema();