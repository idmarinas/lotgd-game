<?php

$blockednavs = array(
		'blockpartial'=>array(),
		'blockfull'=>array(),
		'unblockpartial'=>array(),
		'unblockfull'=>array()
	);

/**
 * Called to block the display of a nav
 * if $partial is true, it will block any nav that begins with the given $link.
 * if $partial is false, it will block only navs that have exactly the given $link.
 *
 * @param string $link The URL to block
 * @param bool $partial
 */
function blocknav($link,$partial=false){
	//prevents a script from being able to generate navs on the given $link.
	global $blockednavs;
	$p = ($partial?'partial':'full');
	$blockednavs["block$p"][$link] = true;
	//eliminate any unblocked navs that match this description.
	if (isset($blockednavs["unblock$p"][$link])) {
		unset($blockednavs["unblock$p"][$link]);
	}
	if ($partial){
		foreach ($blockednavs['unblockpartial'] as $val) {
			if (substr($link,0,strlen($val))==$val ||
					substr($val,0,strlen($link))==$link){
				unset($blockednavs['unblockpartial'][$val]);
			}
		}
	}
}

/**
 * Unlocks a nav from the blocked navs Array
 * if $partial is true, it will block any nav that begins with the given $link.
 * if $partial is false, it will block only navs that have exactly the given $link.
 *
 * @param string $link The nav to unblock
 * @param bool $partial If the passed nav is partial or not
 */
function unblocknav($link,$partial=false){
	//prevents a link that was otherwise blocked with blocknav() from
	//actually being blocked.
	global $blockednavs;
	$p = ($partial?'partial':'full');
	$blockednavs["unblock$p"][$link] = true;
	//eliminate any blocked navs that match this description.
	if (isset($blockednavs["block$p"][$link])) {
		unset($blockednavs["block$p"][$link]);
	}
	if ($partial){
		foreach ($blockednavs['blockpartial'] as $val) {
			if (substr($link,0,strlen($val))==$val ||
					substr($val,0,strlen($link))==$link){
				unset($blockednavs['blockpartial'][$val]);
			}
		}
	}
}

function appendcount($link) {
	global $session;
	return appendlink($link, "c=" . $session['counter'] . "-" . date("His"));
}

function appendlink($link, $new)
{
	if (strpos($link, "?") !== false) {
		return $link . '&' . $new;
	} else {
		return $link . '?' . $new;
	}
}

$navsection="";
$navbysection = array();
$navschema = array();
$navnocollapse = array();
$block_new_navs = false;

/**
 * Allow header/footer code to block/unblock additional navs
 *
 * @param bool $block should new navs be blocked
 */
function set_block_new_navs($block)
{
	global $block_new_navs;
	$block_new_navs = $block;
}

/**
 * Generate and/or store a nav banner for the player
 *
 * @param string $text the display string for the nav banner
 * @param collapse $collapse (default true) can the nav section collapse
 */
function addnavheader($text, $collapse=true,$translate=TRUE)
{
	global $navsection,$navbysection,$translation_namespace;
	global $navschema,$navnocollapse, $block_new_navs,$notranslate;

	if ($block_new_navs) return;

	if (is_array($text)){
		$text = "!array!".serialize($text);
	}
	$navsection=$text;
	if (!array_key_exists($text,$navschema))
		$navschema[$text] = $translation_namespace;
	//So we can place sections with out adding navs to them.
	if (!isset($navbysection[$navsection]))
		$navbysection[$navsection] = array();
	if ($collapse === false) {
		$navnocollapse[$text] = true;
	}
	if ($translate === false) {
		if (!isset($notranslate))
			$notranslate = array();
		array_push($notranslate,array($text,""));
	}
}

/**
 * Generate and/or store the allowed navs or nav banners for the player.
 * If $link is missing - then a banner will be displayed in the nav list
 * If $text is missing - the nav will be stored in the allowed navs for the player but not displayed
 * <B>ALL</B> internal site links that are displayed <B>MUST</B> also call addnav or badnav will occur.
 *
 * @param string $text (optional) The display string for the nav or nav banner
 * @param string $link (optional) The URL of the link
 * @param bool $priv Indicates if the name contains HTML
 * @param bool $pop Indicates if the URL should generate a popup
 * @param string $popsize If a popup - the size of the popup window
 *
 * @see badnav, apponencode
 */

function addnav_notl($text,$link=false,$priv=false,$pop=false,$popsize="500x300"){
	global $navsection,$navbysection,$navschema,$notranslate;
	global $block_new_navs;

	if ($block_new_navs) return;

	if ($link===false) {
		// Don't do anything if text is ""
		if ($text != "") {
			addnavheader($text,TRUE,FALSE);
		}
	}else{
		$args = func_get_args();
		if ($text==""){
			//if there's no text to display, may as well just stick this on
			//the nav stack now.
			call_user_func_array("private_addnav",$args);
		}else{
			if (!isset($navbysection[$navsection]))
				$navbysection[$navsection] = array();
			if (!isset($notranslate))
				$notranslate = array();
			array_push($navbysection[$navsection],$args);
			array_push($notranslate,$args);
		}
	}
}
function addnav($text,$link=false,$priv=false,$pop=false,$popsize="500x300"){
	global $navsection,$navbysection,$translation_namespace,$navschema;
	global $block_new_navs;

	if ($block_new_navs) return;

	if ($link===false) {
		// Don't do anything if text is ""
		if ($text != "") {
			addnavheader($text);
		}
	}else{
		$args = func_get_args();
		if ($text==""){
			//if there's no text to display, may as well just stick this on
			//the nav stack now.
			call_user_func_array("private_addnav",$args);
		}else{
			if (!isset($navbysection[$navsection]))
				$navbysection[$navsection] = array();
			$t = $args[0];
			if (is_array($t)) {
				$t = $t[0];
			}
			if (!array_key_exists($t,$navschema))
				$navschema[$t] = $translation_namespace;
			array_push($navbysection[$navsection],array_merge($args,array("translate"=>false)));
		}
	}
}
/**
 * Determine if a nav/URL is blocked
 *
 * @param string $link The nav to check
 * @return bool
 */
function is_blocked($link)
{
	global $blockednavs;
	if (isset($blockednavs['blockfull'][$link])) return true;
	foreach ($blockednavs['blockpartial'] as $l=>$dummy) {
		$shouldblock = false;
		if (substr($link,0,strlen($l))==$l) {
			if (isset($blockednavs['unblockfull'][$link]) &&
					$blockednavs['unblockfull'][$link]) return false;
			foreach ($blockednavs['unblockpartial'] as $l2=>$dummy) {
				if (substr($link,0,strlen($l2))==$l2){
					return false;
				}
			}
			return true;
		}
	}
	return false;
}


/**
 * Determine how many navs are available
 *
 * @param string $section The nav section to check
 * @return int
 */
function count_viable_navs($section)
{
	global $navbysection;

	$count = 0;
	$val = $navbysection[$section];
	if (count($val) > 0) {
		foreach ($val as $nav) {
			if (is_array($nav) && count($nav) > 0) {
				$link = $nav[1]; // [0] is the text, [1] is the link
				if (!is_blocked($link)) $count++;
			}
		}
	}
	return $count;
}


/**
 * Determins if there are any navs for the player
 *
 * @return bool
 */
function checknavs() {
	global $navbysection, $session;

	// If we already have navs entered (because someone stuck raw links in)
	// just return true;
	if (is_array($session['allowednavs']) &&
			count($session['allowednavs']) > 0) return true;

	// If we have any links which are going to be stuck in, return true
	foreach ($navbysection as $key=>$val) {
		if (count_viable_navs($key) > 0) {
			foreach ($val as $v) {
				if (is_array($v) && count($v) > 0) return true;
			}
		}
	}

	// We have no navs.
	return false;
}

/**
 * Builds navs for display
 *
 * @return string Output formatted navs
 */
function buildnavs(){
	global $navbysection, $navschema, $session, $navnocollapse;
	$builtnavs="";
	foreach ($navbysection as $key=>$val) {
		$tkey = $key;
		$navbanner="";
		if (count_viable_navs($key)>0){
			if ($key>"") {
				if ($session['loggedin']) tlschema($navschema[$key]);
				if (substr($key,0,7)=="!array!"){
					$key = unserialize(substr($key,7));
				}
				$navbanner = private_addnav($key);
				if ($session['loggedin']) tlschema();
			}

			$style = "default";
			$collapseheader = "";
			$collapsefooter = "";

			if ($tkey > "" && (!array_key_exists($tkey,$navnocollapse) || !$navnocollapse[$tkey])) {
				// Generate the collapsable section header
				$args = array("name"=>"nh-{$key}",
						"title"=>($key ? $key : "Unnamed Navs"));
				$args = modulehook("collapse-nav{", $args);
				if (isset($args['content']))
					$collapseheader = $args['content'];
				if (isset($args['style']))
					$style = $args['style'];
				if (!($key > "") && $style == "classic") {
					$navbanner = "<tr><td>";
				}
			}

			$sublinks = "";
			foreach ($val as $v) {
				if (is_array($v) && count($v)>0){
					$sublinks .=   call_user_func_array("private_addnav",$v);
				}//end if
			}//end foreach

			// Generate the enclosing collapsable section footer
			if ($tkey > "" && (!array_key_exists($tkey,$navnocollapse) || !$navnocollapse[$tkey])) {
				$args = modulehook("}collapse-nav");
				if (isset($args['content']))
					$collapsefooter = $args['content'];
			}

			switch ($style) {
			case "classic":
				$navbanner = str_replace("</tr>","",$navbanner);
				$navbanner = str_replace("</td>","",$navbanner);
				// Build the nav section
				$builtnavs .= "{$navbanner}{$collapseheader}<table align='left'>{$sublinks}</table>{$collapsefooter}</tr></td>\n";
				break;
			case "default":
			default:
				// Is style isn't set (should the module not be active)
				// - this catches it
				// Build the nav section
				$builtnavs .= "{$navbanner}{$collapseheader}{$sublinks}{$collapsefooter}\n";
				break;
			}
		}//end if
	}//end foreach
	$navbysection = array();
	return $builtnavs;
}//end function

$accesskeys=array();
$quickkeys=array();
/**
 * Private functions (Undocumented)
 *
 * @param string $text
 * @param string $link
 * @param bool $priv
 * @param bool $pop
 * @param bool $popsize
 * @return mixed
 */
function private_addnav($text,$link=false,$priv=false,$pop=false,$popsize="500x300"){
	//don't call this directly please.  I'll break your thumbs if you do.
	global $nav,$session,$accesskeys,$REQUEST_URI,$quickkeys,$navschema,$notranslate;

	if (is_blocked($link)) return false;

	$thisnav = "";
	$unschema = 0;
	$translate=true;
	if (isset($notranslate))
		if (in_array(array($text,$link),$notranslate)) $translate=false;

	if (is_array($text)){
		if ($text[0] && $session['loggedin']) {
			if ($link === false) $schema = "!array!" . serialize($text);
			else $schema = $text[0];
			if ($translate) {
				tlschema($navschema[$schema]);
				$unschema = 1;
			}
		}
		if ($link != "!!!addraw!!!") {
			if ($translate) $text[0] = translate($text[0]);
			$text = call_user_func_array("sprintf",$text);
		} else {
			$text = call_user_func_array("sprintf",$text);
		}
	}else{
		if ($text && $session['loggedin'] && $translate) {
			tlschema($navschema[$text]);
			$unschema = 1;
		}
		if ($link != "!!!addraw!!!" && $text>"" && $translate) $text = translate($text); //leave the hack in here for now, use addnav_notl please
	}

	$extra="";
	$ignoreuntil="";
	if ($link===false){
		$text = holidayize($text,'nav');
		$thisnav.=tlbutton_pop().templatereplace("navhead",array("title"=>appoencode($text,$priv)));
	}elseif ($link === "") {
		$text = holidayize($text,'nav');
		$thisnav.=tlbutton_pop().templatereplace("navhelp",array("text"=>appoencode($text,$priv)));
	} elseif ($link == "!!!addraw!!!") {
		$thisnav .= $text;
	}else{
		if ($text!=""){
			$extra="";
			if (strpos($link,"?")){
				$extra="&c={$session['counter']}";
			}else{
				$extra="?c={$session['counter']}";
			}

			$extra.="-".date("His");
			//hotkey for the link.
			$key="";
			if ($text[1]=="?") {
				// check to see if a key was specified up front.
				$hchar = strtolower($text[0]);
				if ($hchar==' ' || array_key_exists($hchar,$accesskeys) && $accesskeys[$hchar]==1){
					$text = substr($text,2);
					$text = holidayize($text,'nav');
					if ($hchar == ' ') $key = " ";
				}else{
					$key = $text[0];
					$text = substr($text,2);
					$text = holidayize($text,'nav');
					$found=false;
					$text_len = strlen($text);
					for ($i=0;$i<$text_len; ++$i){
						$char = $text[$i];
						if ($ignoreuntil == $char){
							$ignoreuntil="";
						}else{
							if ($ignoreuntil<>""){
								if ($char=="<") $ignoreuntil=">";
								if ($char=="&") $ignoreuntil=";";
								if ($char=="`") $ignoreuntil=$text[$i+1];
							}else{
								if ($char==$key) {
									$found=true;
									break;
								}
							}
						}
					}
					if ($found==false) {
						//the hotkey for this link wasn't actually in the
						//text, prepend it in parens.
						if (strpos($text, "__") !== false) {
							$text=str_replace("__", "(".$key.") ", $text);
						}else{
							$text="(".strtoupper($key).") ".$text;
						}
						$i=strpos($text, $key);
					}
				}
			} else {
				$text = holidayize($text,'nav');
			}

			if ($key==""){
				//we have no previously defined key.  Look for a new one.
				for ($i=0;$i<strlen($text); $i++){
					$char = substr($text,$i,1);
					if ($ignoreuntil == $char) {
						$ignoreuntil="";
					}else{
						if ((isset($accesskeys[strtolower($char)]) && $accesskeys[strtolower($char)]==1) || (strpos("abcdefghijklmnopqrstuvwxyz0123456789", strtolower($char)) === false) || $ignoreuntil<>"") {
							if ($char=="<") $ignoreuntil=">";
							if ($char=="&") $ignoreuntil=";";
							if ($char=="`") $ignoreuntil=substr($text,$i+1,1);
						}else{
							break;
						}
					}
				}
			}
			if (!isset($i)) $i=0;
			if ($i<strlen($text) && $key != ' '){
				$key=substr($text,$i,1);
				$accesskeys[strtolower($key)]=1;
				$keyrep=" accesskey=\"$key\" ";
			}else{
				$key="";
				$keyrep="";
			}

			if ($key=="" || $key==" "){
			}else{
				$pattern1 = "/^" . preg_quote($key, "/") . "/";
				$pattern2 = "/([^`])" . preg_quote($key, "/") . "/";
				$rep1 = "`H$key`H";
				$rep2 = "\$1`H$key`H";
				$text = preg_replace($pattern1, $rep1, $text, 1);
				if (strpos($text, "`H") === false) {
					$text = preg_replace($pattern2, $rep2, $text, 1);
				}
				if ($pop){
					if ($popsize==""){
						$quickkeys[$key]="window.open('$link')";
					}else{
						$quickkeys[$key]=popup($link,$popsize);
					}
				}else{
					$quickkeys[$key]="window.location='$link$extra'";
				}
			}
			$n= templatereplace("navitem",array(
				"text"=>appoencode($text,$priv),
				"link"=>HTMLEntities($link.($pop!=true?$extra:""), ENT_COMPAT, getsetting("charset", "ISO-8859-1")),
				"accesskey"=>$keyrep,
				"popup"=>($pop==true ? "target='_blank'".($popsize>""?" onClick=\"".popup($link,$popsize)."; return false;\"":"") : "")
				));
				

			$n = str_replace("<a ",tlbutton_pop()."<a ",$n);
			$thisnav.=$n;
		}
		$session['allowednavs'][$link.$extra]=true;
		$session['allowednavs'][str_replace(" ", "%20", $link).$extra]=true;
		$session['allowednavs'][str_replace(" ", "+", $link).$extra]=true;
		if (($pos = strpos($link, "#")) !== false) {
			$sublink = substr($link, 0, $pos);
			$session['allowednavs'][$sublink.$extra]=true;
		}

	}
	if ($unschema) tlschema();
	$nav .= $thisnav;
	return $thisnav;
}

/**
 * Determine how many navs are available
 *
 * @return int The number of legal navs
 */
function navcount(){
	//returns count of total navs added, be it they are pending addition or
	//actually added.
	global $session,$navbysection;
	$c=count($session['allowednavs']);
	if (!is_array($navbysection)) return $c;
	foreach ($navbysection as $val) {
		if (is_array($val)) $c+=count($val);
	}
	return $c;
}

/**
 * Reset and wipe the navs
 *
 */
function clearnav(){
	$session['allowednavs']=array();
}

/**
 * Reset the output and wipe the navs
 *
 */
function clearoutput(){
	global $output,$nestedtags,$header,$nav,$session;

	clearnav();
	$output=new output_collector();
	$header="";
	$nav="";
}

?>