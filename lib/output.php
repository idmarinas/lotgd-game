<?php

/**
* \class output_collector
* \brief Library Functions for page output stored in the class
* This class holds the output until it gets echo()'d to the browser in pageparts.php.
* It also has currently legacy support wrapper functions defined outside
* @author Eric Stevens+JT Traub, rewritten Oliver Brendel to OOP + adapted
*/

class output_collector {

	private $output; //!< the output to the template body
	private $block_new_output; //!< is current output blocked? boolean
	private $colors; //!< the color codes=>CSS tags
	private $color_map,$colormap_esc; //!< the letters of color codes only, escaped and not escaped
	private $nestedtags; //!<open spans, or whatever...we need to make sure that we close them on output

	/**
	* Constructor. Fill our class with the colors and set all up.
	*/
	
	public function __construct() {
		$this->output='';
		$this->nestedtags=array();
		$this->block_new_output=false;
		$this->nestedtags['font']=false;
		$this->nestedtags['div']=false;
		$this->nestedtags['i']=false;
		$this->nestedtags['b']=false;
		$this->nestedtags['<']=false;
		$this->nestedtags['>']=false;
		$this->nestedtags['B']=false;
		$this->colors = array(
			"1" => "colDkBlue",
			"2" => "colDkGreen",
			"3" => "colDkCyan",
			"4" => "colDkRed",
			"5" => "colDkMagenta",
			"6" => "colDkYellow",
			"7" => "colDkWhite",
			"!" => "colLtBlue",
			"@" => "colLtGreen",
			"#" => "colLtCyan",
			"\$" => "colLtRed",
			"%" => "colLtMagenta",
			"^" => "colLtYellow",
			"&" => "colLtWhite",
			"q" => "colDkOrange",
			"Q" => "colLtOrange",
			")" => "colLtBlack",
			"R" => "colRose",
			"V" => "colBlueViolet",
			"v" => "coliceviolet",
			"g" => "colXLtGreen",
			"G" => "colXLtGreen",
			"T" => "colDkBrown",
			"t" => "colLtBrown",
			"~" => "colBlack",
			"e" => "colDkRust",
			"E" => "colLtRust",
			"j" => "colMdGrey",
			"J" => "colMdBlue",
			"l" => "colDkLinkBlue",
			"L" => "colLtLinkBlue",
			"x" => "colburlywood",
			"X" => "colbeige",
			"y" => "colkhaki",
			"Y" => "coldarkkhaki",
			"k" => "colaquamarine",
			"K" => "coldarkseagreen",
			"p" => "collightsalmon",
			"P" => "colsalmon",
			"m" => "colwheat",
			"M" => "coltan",
 		);
		//*cough* if you choose color codes like \ or whatnot... SENSITIVE codes like special programmer chars... then escape them. Sadly we have % (breaks sprintf i.e.) AND ) in it... (breaks regular expressions)
		$cols=$this->colors;
		$escape=array(')','$',"(","[","]","{","}");
		foreach ($escape as $letter) {
			if (isset($cols[$letter])) $cols["\\".$letter]=$cols[$letter];
			unset($cols[$letter]);	
		}		
		$this->colormap_esc=array_keys($cols); //please, no empty color array.
		$this->colormap=array_keys($this->colors);
	}
	/**
	 * Raw output (unprocessed) appended to the output buffer
	 *
	 * @param $indata the raw material to be outputted
	 */
	public function rawoutput($indata) {
		if ($this->block_new_output) return;
		$this->output .= $indata . "\n";
	}

	/**
	 * Handles color and style encoding, and appends to the output buffer ($output).  It is usually called with output_notl($indata,...). If an array is passed then the format for sprintf is assumed otherwise a simple string is assumed
	 *
	 * @see appoencode
	 */
	public function output_notl(){
		if ($this->block_new_output) return;

		$args = func_get_args();
		$length=count($args);
		//get 'true' off the end if we have it
		$last = $args[$length-1];
		if ($last!==true){
			$priv = false;
		}else{
			unset($args[$length-1]);
			$priv = true;
		}
		// $out = $indata;
		// $args[0]=&$out;
		//apply variables
		$out=&$args[0];
		if (count($args)>1){
			//special case since we use `% as a color code so often.
			$out = str_replace("`%","`%%",$out);
			$out = call_user_func_array("sprintf",$args);
		}
		//holiday text
		if ($priv==false) $out = holidayize($out,'output');
		//`1`2 etc color & formatting
		$out = $this->appoencode($out,$priv);
		//apply to the page.
		$this->output.=tlbutton_pop().$out."\n";
	}

	/**
	 * Outputs a translated, color/style encoded string to the browser.
	 *
	 * Argument in: What to output. If an array is passed then the format used by sprintf is assumed
	 *
	 * @see output_notl
	 *
	 */
	public function output(){
		if ($this->block_new_output) return;
		$args = func_get_args();
		if (is_array($args[0])) $args = $args[0];
		if (is_bool($args[0]) && array_shift($args)) {
			$schema= array_shift($args);
			$args[0] = translate($args[0],$schema);
		} else {
			$args[0] = translate($args[0]);
		}
		//in an object, call the function with an array pointing to object and then function, make sure we add this  to *our* object
		call_user_func_array(array(&$this,"output_notl"),$args);
	}

	/**
	* Returns the formatted output
	* @return the complete output for the {content} tag
	*/
	
	public function get_output(){
		$output=$this->output;
		//clean up unclosed output tags.
		foreach (array_keys($this->nestedtags) as $key=>$val) {
			if ($key=='font') $key="span";
			if ($val === true) $output.="</".$key.">";
		}
		return $output;
	
	}
	
	/**
	* Returns the formatted output
	* @return the complete output WITHOUT closing open tags 
	*/
	public function get_rawoutput(){
		$output=$this->output;
		return $output;
	
	}	
	/**
	* If you want to block new output, this is your function.
	* @param $block boolean or 0,1 or similar
	* @return void
	*/
	
	function set_block_new_output($block) {
		$this->block_new_output = ($block?true:false);
	}
	
	/**
	* Returns if new output is blocked or not
	* @return boolean
	*/

	function get_block_new_output() {
		return $this->block_new_output;
	}

	/**
	* Lets you display debug output (specially formatted, optionally only visible to SU_DEBUG users).
	* @param $text The input text or variable to debug, string
	* @param $force Default is false, if true it will always be outputted to ANY user. If false, only SU_DEBUG will see it.
	* @return void
	*/
	
	function debug($text, $force=false){
		global $session;
		$temp = $this->get_block_new_output();
		$this->set_block_new_output(false);
		if ($force || $session['user']['superuser'] & SU_DEBUG_OUTPUT){
			if (is_array($text)){
				require_once("lib/dump_item.php");
				$text = appoencode(dump_item($text),true);
			}
			$this->rawoutput("<div class='debug'>$text</div>");
		}
		$this->set_block_new_output($temp);
	}

	/**
	* This function puts the lotgd formatting `whatever into HTML tags. It will automatically close previous tags before opening new ones for the same class.
	* @param $data The logd formatted string. 
	* @param $priv If true, it uses no htmlentites before outputting to the browser, means it will parse HTML code through. Default is false
	* @return void
	*/

	function appoencode($data,$priv=false){
		$start = 0;
		$out="";
		if( ($pos = strpos($data, "`")) !== false) {
			do {
				++$pos;
				if ($priv === false){
					$out .= HTMLEntities(substr($data, $start, $pos - $start - 1), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
				} else {
					$out .= substr($data, $start, $pos - $start - 1);
				}
				$start = $pos + 1;
				if(isset($this->colors[$data[$pos]])) {
					if ($this->nestedtags['font']) $out.="</span>";
					else $this->nestedtags['font']=true;
					$out.="<span class='".$this->colors[$data[$pos]]."'>";
				} else {
					switch($data[$pos]){
					case "n":
						$out.="<br>\n";
						break;
					case "0":
						if ($this->nestedtags['font']) $out.="</span>";
						$this->nestedtags['font'] = false;
						break;
					case "b":
						if ($this->nestedtags['b']){
							$out.="</b>";
							$this->nestedtags['b']=false;
						}else{
							$this->nestedtags['b']=true;
							$out.="<b>";
						}
						break;
					case "i":
						if ($this->nestedtags['i']) {
							$out.="</i>";
							$this->nestedtags['i']=false;
						}else{
							$this->nestedtags['i']=true;
							$out.="<i>";
						}
						break;
					case "c":
						if ($this->nestedtags['div']) {
							$out.="</div>";
							$this->nestedtags['div']=false;
						}else{
							$this->nestedtags['div']=true;
							$out.="<div align='center'>";
						}
						break;
					case "B":
						if ($this->nestedtags['B']) {
							$out.="</em>";
							$this->nestedtags['B']=false;
						}else{
							$this->nestedtags['B']=true;
							$out.="<em>";
						}
						break;
					case ">":
						if ($this->nestedtags['>']){
							$this->nestedtags['>']=false;
							$out.="</div>";
						}else{
							$this->nestedtags['>']=true;
							$out.="<div style='float: right; clear: right;'>";
						}
						break;
					case "<":
						if ($this->nestedtags['<']){
							$this->nestedtags['<']=false;
							$out.="</div>";
						}else{
							$this->nestedtags['<']=true;
							$out.="<div style='float: left; clear: left;'>";
						}
						break;
					case "H":
						if ($this->nestedtags['span']) {
							$out.="</span>";
							$this->nestedtags['span']=false;
						}else{
							$this->nestedtags['span']=true;
							$out.="<span class='navhi'>";
						}
						break;
					case "w":
						global $session;
						if(!isset($session['user']['weapon']))
							$session['user']['weapon']="";
						$out.=sanitize($session['user']['weapon']);
						break;
					case "`":
						$out.="`";
						++$pos;
						break;
					default:
						$out.="`".$data[$pos];
					}
				}
			} while( ($pos = strpos($data, "`", $pos)) !== false);
		}
		if ($priv === false){
			$out .= HTMLEntities(substr($data, $start), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		} else {
			$out .= substr($data, $start);
		}
		return $out;
	}
		
	/**
	* Returns the complete color array
	* @return an array with $colorcode=>$csstag format
	*/

	public function get_colors() {
		return $this->colors;
	}

	/**
	* Colormap for use with sanitize commands
	* @return Returns only the codes with no spaces: $colorcode$colorcode...
	*/
	
	public function get_colormap() {
		return implode("",$this->colormap);
	}

	/**
	* Returns the Colormap like get_colormap() but escapes the dollar letter or the slash
	* @return Returns only the codes with no spaces: $colorcode$colorcode...
	*/

	public function get_colormap_escaped() {
		return implode("",$this->colormap_esc);
	}

	/**
	* Returns the Colormap like get_colormap() but escapes the dollar letter or the slash
	* @return Returns only the codes as an array
	*/

	public function get_colormap_escaped_array() {
		return $this->colormap_esc;
	}
	
}



/* END of OOP section */



/*function support without the object call */

/**
 * Block any output statements temporarily
 *
 * @param $block should output be blocked
 */
function set_block_new_output($block)
{
	global $output;
	$output->set_block_new_output($block);
}

/**
 * Raw output (unprocessed) appended to the output buffer
 *
 * @param $indata
 */
function rawoutput($indata) {
	global $output;

	$output->rawoutput($indata);
}

/**
 * Handles color and style encoding, and appends to the output buffer ($output)
 *
 * @param $indata If an array is passed then the format for sprintf is assumed otherwise a simple string is assumed
 *
 * @see sprintf, apponencode
 */
function output_notl($indata){
	global $output;
	$args = func_get_args();
	call_user_func_array(array($output,"output_notl"),$args);
}

/**
 * Outputs a translated, color/style encoded string to the browser.
 *
 * @param What to output. If an array is passed then the format used by sprintf is assumed
 *
 * @see output_notl
 *
 */
function output(){
	global $output;

	$args = func_get_args();
	call_user_func_array(array($output,"output"),$args);
}

/**
 * Generate debug output for players who have the SU_DEBUG_OUTPUT flag set in the superuser mask
 *
 * @param $text The string to output
 * @param  $force If true, force debug output even for non SU/non flagged
 */
function debug($text, $force=false){
	global $output;
	$output->debug($text,$force);
}

/**
 * Generates the appropriate output based on the LOGD coding system (ie: `b: Bold, `i: Italic)
 *
 * @param string $data The string to be output
 * @param bool $priv Indicates if the passed string ($data) contains HTML
 * @return string An output (HTML) formatted string
 */

function appoencode($data,$priv=false) {
	global $output;
	return $output->appoencode($data,$priv);
}



?>
