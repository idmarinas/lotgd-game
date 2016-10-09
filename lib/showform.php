<?php
// translator ready
// addnews ready
// mail ready
require_once 'lib/dump_item.php';

/**
 * Construct FORM LOTGD style
 *
 * @var array $layout
 * @var array $row
 * @var boolean $nosave
 * @var false|pattern $keypref
 * @var callable $callback This can use for personalize the form used to show all inputs or for more process.
 * 						   Can still use lotgd_show_form_field in your callable
 *						   Both functions get same parameters function($info, $row, $key, $keyout, $val, $extensions)
 */
function lotgd_showform($layout, $row, $nosave = false, $keypref = false, callable $callback = null)
{
 	static $showform_id = 0;
 	static $title_id = 0;

 	$showform_id++;
	$returnvalues = [];
	$extensions = modulehook("showformextensions", []);

	$i = false;
	$ulMenu = [];
	$ulContent = [];
	foreach ($layout as $key => $val)
	{
		$pretrans = 0;

		if ($keypref !== false) $keyout = sprintf($keypref, $key);
		else $keyout = $key;

		if (is_array($val))
		{
			$info = explode(',', $val[0]);
			$val[0] = $info[0];
			$info[0] = $val;
		}
		else $info = explode(',', $val);

		if (is_array($info[0])) $info[0] = call_user_func_array("sprintf_translate", $info[0]);
		else $info[0] = translate($info[0]);

		if (isset($info[1])) $info[1] = trim($info[1]);
		else $info[1] = "";

		if ($info[1] == "title")
		{
			$ulMenu[] = sprintf('<a href="#">%s</a>', $info[0]);
		 	$title_id++;
 		}
		elseif ($info[1]=="note")
		{
			$ulContent[$title_id][] = sprintf('<tr class="%s"><td colspan="2">%s</td></tr>', ($i?'trlight':'trdark'), $info[0]);

			$i = !$i;
		}
		else
		{
			if (! $callback) $result = lotgd_show_form_field($info, $row, $key, $keyout, $val, $extensions);
			else $result = $callback($info, $row, $key, $keyout, $val, $extensions);

			$ulContent[$title_id][] = sprintf('<tr class="%s"><td>%s</td><td>%s</td></tr>',
				($i?'trlight':'trdark'),
				$info[0],
				$result
			);

			$i = !$i;
		}
	}

	if (! empty($ulMenu))
	{
		rawoutput(sprintf('<ul class="uk-tab" data-uk-tab="{connect:\'#form-%s\'}"><li>%s</li></ul>',
				$showform_id,
				implode('</li><li>', $ulMenu)
			)
		);
	}

	$content = [];
	foreach($ulContent as $value)
	{
		$content[] = sprintf('<table>%s</table>',
			implode('', $value)
		);
	}

	rawoutput(sprintf('<ul class="uk-switcher" id="form-%s"><li>%s</li></ul>',
			$showform_id,
			implode('</li><li>', $content)
		)
	);
	unset($ulContent, $content, $ulMenu);

	tlschema("showform");
	$save = translate_inline("Save");
	tlschema();

	if (!$nosave) rawoutput("<input type='submit' class='button' value='$save'>");

	return $returnvalues;
}

function lotgd_show_form_field($info, $row, $key, $keyout, $val, $extensions)
{
	switch ($info[1])
	{
		case "title":
		case "note":
			break;
		case "theme":
			// A generic way of allowing a theme to be selected.
			$skins = array();
			$handle = @opendir("themes");
			// Template directory open failed
			if (!$handle) {
				return 'None available';
				break;
			}
			while (false != ($file = @readdir($handle))) {
				if (strpos($file,".htm") > 0) {
					array_push($skins, $file);
				}
			}
			// No templates installed!
			if (count($skins) == 0) {
				return "None available";
				break;
			}
			natcasesort($skins); //sort them in natural order
			$select = "<select name='$keyout'>";
			foreach($skins as $skin)
			{
				if ($skin == $row[$key])
				{
					$select .= "<option value='$skin' selected>".htmlentities(substr($skin, 0, strpos($skin, ".htm")), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
				}
				else
				{
					$select .= "<option value='$skin'>".htmlentities(substr($skin, 0, strpos($skin, ".htm")), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
				}
			}
			$select .= '</select>';

			return $select;
			break;
		case "location":
			// A generic way of allowing the location to be specified for
			// things which only want to be in one place.  There are other
			// things which would be good to do as well of course, such
			// as making sure to handle village name changes in the module
			// that cares about this or what not, but this at least gives
			// some support.
			$vloc = array();
			$vname = getsetting("villagename", LOCATION_FIELDS);
			$vloc[$vname]="village";
			$vloc['all'] = 1;
			$vloc = modulehook("validlocation", $vloc);
			unset($vloc['all']);
			reset($vloc);
			$select .= "<select name='$keyout'>";
			foreach($vloc as $loc=>$val) {
				if ($loc == $row[$key]) {
					$select .= "<option value='$loc' selected>".htmlentities($loc, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
				} else {
					$select .="<option value='$loc'>".htmlentities($loc, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
				}

			}
			$select .= '</select>';

			return $select;
			break;
		case "checkpretrans":
			$pretrans = 1;
			// FALLTHROUGH
		case "checklist":
			reset($info);
			list($k,$v)=each($info);
			list($k,$v)=each($info);
			$select="";
			while (list($k,$v)=each($info)){
				$optval = $v;
				list($k,$v)=each($info);
				$optdis = $v;
				if (!$pretrans) $optdis = translate_inline($optdis);
				if (is_array($row[$key])){
					if ($row[$key][$optval]) {
						$checked=true;
					}else{
						$checked=false;
					}
				}else{
					//any other ways to represent this?
					debug("You must pass an array as the value when using a checklist.");
					$checked=false;
				}
				$select.="<input type='checkbox' name='{$keyout}[{$optval}]' value='1'".($checked==$optval?" checked":"").">&nbsp;".("$optdis")."<br>";
			}
			return $select;
			break;
		case "radiopretrans":
			$pretrans = 1;
			// FALLTHROUGH
		case "radio":
			reset($info);
			list($k,$v)=each($info);
			list($k,$v)=each($info);
			$select="";
			while (list($k,$v)=each($info)){
				$optval = $v;
				list($k,$v)=each($info);
				$optdis = $v;
				if (!$pretrans) $optdis = translate_inline($optdis);
				$select.=("<input type='radio' name='$keyout' value='$optval'".($row[$key]==$optval?" checked":"").">&nbsp;".("$optdis")."<br>");
			}
			return $select;
			break;
		case "dayrange":
			$start = strtotime(date("Y-m-d", strtotime("now")));
			$end = strtotime($info[2]);
			$step = $info[3];
			// we should really try to avoid an infinite loop here if
			// they define a time string which equates to 0 :/
			$cur = $row[$key];
			$select = "<select name='$keyout'>";
			if ($cur && $cur < date("Y-m-d H:i:s", $start))
				$select .= "<option value='$cur' selected>".htmlentities($cur, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
			for($j = $start; $j < $end; $j = strtotime($step, $j)) {
				$d = date("Y-m-d H:i:s", $j);
				$select .= "<option value='$d'".($cur==$d?" selected":"").">".HTMLEntities("$d", ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
			}
			if ($cur && $cur > date("Y-m-d H:i:s", $end))
				$select .= "<option value='$cur' selected>".htmlentities($cur, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";

			$select .="</select>";

			return $select;
			break;

		case "range":
			$min = (int)$info[2];
			$max = (int)$info[3];
			$step = (int)$info[4];
			if ($step == 0) $step = 1;
			$select = "<select name='$keyout'>";
			if ($min<$max && ($max-$min)/$step>300)
				$step=max(1,(int)(($max-$min)/300));
			for($j = $min; $j <= $max; $j += $step) {
				$select .= "<option value='$j'".($row[$key]==$j?" selected":"").">".HTMLEntities("$j", ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
			}
			$select .= "</select>";

			return $select;
			break;
		case "floatrange":
			$min = round((float)$info[2],2);
			$max = round((float)$info[3],2);
			$step = round((float)$info[4],2);
			if ($step==0) $step=1;
			$select = "<select name='$keyout'>";
			$val = round((float)$row[$key], 2);
			for($j = $min; $j <= $max; $j = round($j+$step,2)) {
				$select .= "<option value='$j'".($val==$j?" selected":"").">".HTMLEntities("$j", ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
			}
			$select .= "</select>";

			return $select;
			break;
		case "bitfieldpretrans":
			$pretrans = 1;
			// FALLTHROUGH
		case "bitfield":
			//format:
			//DisplayName,bitfield,disablemask,(highbit,display)+
			//1-26-03 added disablemask so this field type can be used
			// on bitfields other than superuser.
			reset($info);
			list($k,$v)=each($info);
			list($k,$v)=each($info);
			list($k,$disablemask)=each($info);
			$input = "<input type='hidden' name='$keyout"."[0]' value='1'>";
			while (list($k,$v)=each($info)){
				$input .= "<input type='checkbox' name='$keyout"."[$v]'"
					.((int)$row[$key] & (int)$v?" checked":"")
					.($disablemask & (int)$v?"":" disabled")
					." value='1'> ";
				list($k,$v)=each($info);
				if (!$pretrans) $v = translate_inline($v);
				$input .= sprintf("%s`n", $v);
			}

			return $input;
			break;
		case "datelength":
			// However, there was a bug with your translation code wiping
			// the key name for the actual form.  It's now fixed.
			// ok, I see that, but 24 hours and 1 day are the same
			// aren't they?
			$vals = array(
				"1 hour", "2 hours", "3 hours", "4 hours",
				"5 hours", "6 hours", "8 hours", "10 hours",
				"12 hours", "16 hours", "18 hours", "24 hours",
				"1 day", "2 days", "3 days", "4 days", "5 days",
				"6 days", "7 days",
				"1 week", "2 weeks", "3 weeks", "4 weeks",
				"1 month", "2 months", "3 months", "4 months",
				"6 months", "9 months", "12 months",
				"1 year"
			);
			tlschema("showform");
			foreach ($vals as $k=>$v) {
				$vals[$k]=translate($v);
				rawoutput(tlbutton_pop());
			}
			tlschema();
			$select = "<select name='$keyout'>";
			foreach ($vals as $k=>$v) {
				$select .= "<option value=\"".htmlentities($v, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"".($row[$key]==$v?" selected":"").">".htmlentities($v, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
			}
			$select .= "</select>";

			return $select;
			break;
		case "enumpretrans":
			$pretrans = 1;
			// FALLTHROUGH
		case "enum":
			reset($info);
			list($k,$v)=each($info);
			list($k,$v)=each($info);

			$select = "<select name='$keyout'>";
			while (list($k,$v)=each($info)){
				$optval = $v;
				list($k,$v)=each($info);
				$optdis = $v;
				if (!$pretrans) {
					$optdis = translate_inline($optdis);
				}
				$selected = 0;
				if (isset($row[$key]) && $row[$key] == $optval)
					$selected = 1;

				$select .= "<option value='$optval'".($selected?" selected":"").">".HTMLEntities("$optdis", ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</option>";
			}
			$select .= "</select>";
			return $select;
			break;
		case "password":
			if (array_key_exists($key, $row)) $out = $row[$key];
			else $out = "";
			return "<input type='password' name='$keyout' value='".HTMLEntities($out, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>";
			break;
		case "bool":
			tlschema("showform");
			$yes = translate_inline("Yes");
			$no = translate_inline("No");
			tlschema();
			$select = "<select name='$keyout'>";
			$select .= "<option value='0'".($row[$key]==0?" selected":"").">$no</option>";
			$select .= "<option value='1'".($row[$key]==1?" selected":"").">$yes</option>";
			$select .= "</select>";

			return $select;
			break;
		case "hidden":
			return "<input type='hidden' name='$keyout' value=\"".HTMLEntities($row[$key], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">".HTMLEntities($row[$key], ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
			break;
		case "viewonly":
			//don't unset it. it does not change, so nothing lost
			unset($returnvalues[$key]);
			if (isset($row[$key])) {
				return dump_item($row[$key]);
			}
			break;
		case "viewhiddenonly":
			//don't unset it, transfer it, hide it. This is now used for legacy support of playernames that are empty and showform won't carry the name over to extract the real one
			if (isset($row[$key])) {
				$text = dump_item($row[$key]);
				$text .= "<input type='hidden' name='".addslashes($key)."' value='".addslashes($row[$key])."'>";

				return $text;
			}
			break;
		case "rawtextarearesizeable":
			$raw=true;
			//BOING
		case "textarearesizeable":
			$resize=true;
			//FALLTHROUGH
		case "textarea":
			$cols = 0;
			if (isset($info[2])) $cols = $info[2];
			if (!$cols) $cols = 70;
			$text = "";
			if (isset($row[$key])) {
				$text = $row[$key];
			}
			if (isset($raw) && $raw) {
				//nothing
			} else {
				$text=str_replace("`n","\n",$text);
			}
			if (isset($resize) && $resize) {
				$text = "<script type=\"text/javascript\">function increase(target, value){  if (target.rows + value > 3 && target.rows + value < 50) target.rows = target.rows + value;}</script>";
				$text .= "<script type=\"text/javascript\">function cincrease(target, value){  if (target.cols + value > 3 && target.cols + value < 150) target.cols = target.cols + value;}</script>";
				$text .= "<input type='button' onClick=\"increase(textarea$key,1);\" value='+' accesskey='+'><input type='button' onClick=\"increase(textarea$key,-1);\" value='-' accesskey='-'>";
				$text .= "<input type='button' onClick=\"cincrease(textarea$key,-1);\" value='<-'><input type='button' onClick=\"cincrease(textarea$key,1);\" value='->' accesskey='-'><br>";
				$text .= "<textarea id='textarea$key' class='input' name='$keyout' cols='$cols' rows='5'>".htmlentities($text, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</textarea>";

				return $text;
			} else {
				return "<textarea class='input' name='$keyout' cols='$cols' rows='5'>".htmlentities($text, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</textarea>";
			}
			break;
		case "int":
			if (array_key_exists($key, $row)) (int) $out = $row[$key];
			else $out = 0;
			return "<input type='number' name='$keyout' value=\"".HTMLEntities($out, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='5'>";
			break;
		case "float":
			return "<input type='number' name='$keyout' value=\"".htmlentities($row[$key], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='8' step='any'>";
			break;
		case "string":
			$len = 50;
			$minlen = 50;
			if (isset($info[2])) $len = (int)$info[2];
			if ($len < $minlen) $minlen = $len;
			if ($len > $minlen) $minlen = $len/2;
			if ($minlen > 70) $minlen = 70;
			if (array_key_exists($key, $row)) $val = $row[$key];
			else $val = "";
			return "<input size='$minlen' maxlength='$len' name='$keyout' value=\"".HTMLEntities($val, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">";
			break;
		default:
			if (array_key_exists($info[1],$extensions)){
				$func=$extensions[$info[1]];
				if (array_key_exists($key, $row)) $val = $row[$key];
				else $val = "";
				call_user_func($func, $keyout, $val, $info);
			}else{
				if (array_key_exists($key, $row)) $val = $row[$key];
				else $val = "";
				return "<input type='text' size='50' name='$keyout' value=\"".HTMLEntities($val, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">";
			}
		break;
	}
}

//-- LEGACY
function showform($layout, $row, $nosave = false, $keypref = false)
{
	trigger_error(sprintf(
            'Usage of %s is deprecated since v1.0.0; and delete in version 2.0.0 please use %s instead',
            __METHOD__,
			'lotgd_showform'
        ), E_USER_DEPRECATED);

	return lotgd_showform($layout, $row, $nosave, $keypref);
}