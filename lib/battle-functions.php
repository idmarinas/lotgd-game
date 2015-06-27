<?php

//functions for the fight - until we get a full class OOP system

class fightbar {

private $bg,$red,$green,$yellow,$orange,$grey,$full,$med,$critical,$length,$height;

function __construct() {
	//set the bar colours
	$this->bg="#000099"; //blue
	$this->red="#FF0000"; //red
	$this->green="#00DD00"; //green
	$this->yellow="#FDF700"; //yellowish
	$this->orange="#FF8000"; //orange
	$this->grey="#827B84"; //greyish

	//set the levels for the foreground colours
	$this->full=0.67;
	$this->med=0.47;
	$this->critical=0.3;

	//set the bar sizes
	$this->length=50; //pixel
	$this->height=10; //pixel
}

function getBar ($current,$max) {
	$totalwidth=$this->length;
	if ($max==0) return ""; //silly
	$scale=$current/$max;
	$length=round($scale*($totalwidth));
	if ($scale > $this->full) {
		$fg=$this->green;
	} elseif ($scale> $this->med) {
		$fg=$this->orange;
	} elseif ($scale > $this->critical) { 
		$fg=$this->yellow;
	} elseif ($current<=0) {
		$fg=$this->grey;
	} else $fg=$this->red;
	$bar="<div style='display: block;background-color:".$this->grey."; width: ".$this->length."px;height: ".$this->height."px;'>";
	$bar.="<div style='background-color:".$fg."; width: ".$length."px;height: ".$this->height."px;'>";
	$bar.="</div></div>";
	return $bar;
}

}
