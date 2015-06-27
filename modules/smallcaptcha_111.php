<?php

function smallcaptcha_111_getmoduleinfo(){
$info = array(
	"name"=>"Small Petition Captcha",
	"version"=>"1.0",
	"author"=>"`2Oliver Brendel",
	"override_forced_nav"=>true,
	"category"=>"Administrative",
	"download"=>"",
	/*"settings"=>array(
		"Captcha Settings,title",
		"maxmails"=>"Maximum amount of mails you can have (read+unread),int|200",
		"After that you will not receive any more emails,note",
		"su_sent"=>"Is a superuser excluded from that limit when trying to send mail to somebody?,bool|1",
		),*/
	);
	return $info;
}

function smallcaptcha_111_install(){
	module_addhook_priority("addpetition",50);
	module_addhook_priority("petitionform",50);
	return true;
}

function smallcaptcha_111_uninstall(){
	return true;
}

function smallcaptcha_111_dohook($hookname, $args){
	global $session;
	switch ($hookname) {
		case "addpetition": 
			if (httppost('alpha')!=sha1(httppost('gamma')).date("zty") || httppost('gamma')=='' || httppost('alpha')=='') {
				$args['cancelreason']="`c`b`\$Sorry, but you entered the wrong captcha code, try again`b`c`n`n";
				$args['cancelpetition']=true;
			}
			break;							
		case "petitionform":
			output("`nPlease enter the following numbers in the Captcha Box to verify you are not a bot hopping into the server:`n");
			$n = new Number( rand(1000,9999) );
			$n->printNumber();
			output("`nCaptcha Code: ");
			rawoutput("<input name='gamma'>");
			$encoded=sha1($n->getNum()).date("zty");
			rawoutput("<input type='hidden' name='alpha' value='$encoded'>");
			output_notl("`n");
		break;
	}
	return $args;
}

function smallcaptcha_111_run(){
}

# Von Rene Schmidt (rene@reneschmidt.de) fuer DrWeb.de 18855 original 7
class Digit {

  var $bits = array(1,2,4,8,16,32,64,128,256,512,1024,2048,4096,8192,16384);
  var $matrix  = array();
  var $bitmasks = array(31599, 18740, 29607, 31143, 18921, 31183, 31695, 18727, 31727, 31215);

  function digit( $dig ) {
    $this->matrix[] = array(0, 0, 0); // 2^0, 2^1, 2^2 ... usw.
    $this->matrix[] = array(0, 0, 0);
    $this->matrix[] = array(0, 0, 0);
    $this->matrix[] = array(0, 0, 0);
    $this->matrix[] = array(0, 0, 0); // ..., ..., 2^14

    ((int)$dig >= 0 && (int)$dig <= 9) && $this->setMatrix( $this->bitmasks[(int)$dig] );
  }

  function setMatrix( $bitmask ) {
    $bitsset = array();

    for ($i=0; $i<count($this->bits); ++$i)
      (($bitmask & $this->bits[$i]) != 0) && $bitsset[] = $this->bits[$i];

    foreach($this->matrix AS $row=>$col)
      foreach($col AS $cellnr => $bit)
        in_array( pow(2,($row*3+$cellnr)), $bitsset) && $this->matrix[$row][$cellnr] = 1;
  }
}

class Number {

  var $num = 0;
  var $digits = array();

  function number( $num ) {
    $this->num = (int)$num;

    $r = "{$this->num}";
    for( $i=0; $i<strlen($r); $i++ )
      $this->digits[] = new Digit((int)$r[$i]);
  }

  function getNum() { return $this->num; }

  function printNumber() {
	output("`n");
	$char="&nbsp;";
	$char="X";
    for($row=0; $row<count($this->digits[0]->matrix); $row++) {
      foreach( $this->digits AS $digit ) {
        foreach($digit->matrix[$row] AS $cell)
          if($cell === 1) rawoutput("<span style='color: white; background-color: white;'>$char$char</span>"); else rawoutput("<span style='color: black; background-color: black;'>$char$char</span>");
        rawoutput("<span style='color: black; background-color: black;'>$char</span>");
      }
      rawoutput("<br>");
    }
  }
}
?>
