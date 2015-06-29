<?php
//May 30, 2006... psst that's my birthday
function furniture_getmoduleinfo(){
	$info = array(
		"name"=>"Furniture Store",
		"version"=>"5.06",
		"author"=>"DaveS",
		"category"=>"Dwellings",
		"download"=>"",
		"settings"=>array(
			"Furniture Store,title",
			"storename"=>"What is the name of the store?,text|`QF`qurniture `QS`qtore",
			"owner"=>"Who is the store owner?,text|Douglas",
			"resetnd"=>"Reset uses on newday or system newday?,enum,0,Newday,1,System Newday|0",
			"perdk"=>"Allow players to buy each piece of furniture only once per dk?,bool|0",
			"If there are more than 7 dwellings the rest will have the same settings,note",
			"Chair,title",
			"dkenter"=>"Dks needed to enter the store and buy a chair?,int|10",
			"chairstress"=>"How many times to stress the chair before it breaks?,int|2",
			"Note: Custom chairs are 1 or 2 stress points higher before breaking,note",
			"bchairgo"=>"How much gold does the basic chair cost?,int|500",
			"bchairge"=>"How many gems does the basic chair cost?,int|1",
			"custchairgo1"=>"What is the base gold cost for the General Chair?,int|650",
			"custchairge1"=>"What is the base gem cost for the General Chair?,int|2",
			"custchairgo2"=>"What is the base gold cost for the Heirloom Chair?,int|850",
			"custchairge2"=>"What is the base gem cost for the Heirloom Chair?,int|3",
			"Table,title",
			"dktable"=>"Dks needed to buy a table?,int|20",
			"tablestress"=>"How many times to stress the basic table before it breaks?,int|2",
			"Note: Custom tables are 1 or 2 stress points higher before breaking,note",
			"btablego"=>"How much gold does the basic table cost?,int|1000",
			"btablege"=>"How many gems does the basic table cost?,int|3",
			"custtablego1"=>"What is the base gold cost for the General Table?,int|1250",
			"custtablege1"=>"What is the base gem cost for the General Table?,int|5",
			"custtablego2"=>"What is the base gold cost for Heirloom Table?,int|1500",
			"custtablege2"=>"What is the base gem cost for the Heirloom Table?,int|6",
			"Bed,title",
			"dkbed"=>"Dks needed to buy a bed?,int|30",
			"bedstress"=>"How many times to stress the bed before it breaks?,int|2",
			"Note: Custom bedss are 1 or 2 stress points higher before breaking,note",
			"bbedgo"=>"How much gold does the basic bed cost?,int|2500",
			"bbedge"=>"How many gems does the basic bed cost?,int|7",
			"custbedgo1"=>"What is the base gold cost for the General Bed?,int|3000",
			"custbedge1"=>"What is the base gem cost for the General Bed?,int|8",
			"custbedgo2"=>"What is the base gold cost for Heirloom Bed?,int|3500",
			"custbedge2"=>"What is the base gem cost for the Heirloom Bed?,int|10",
			"Dwelling Furnishings,title",
			"The modulename will automatically be filled in for up to 7 dwelling types that are already installed.,note",
			"If you have more than 7 dwelling types installed all others will default to the 8th dwelling type.,note",
			"If you install a dwelling type AFTER installing the furniture store you will need to manually put in the module name in the next available spot.,note",
			"modulename1"=>"What is the modulename for the 1st dwelling type?,text|",
			"chair1"=>"Allow 1st dwelling type to have a chair?,bool|1",
			"table1"=>"Allow 1st dwelling type to have a table?,bool|1",
			"bed1"=>"Allow 1st dwelling type to have a bed?,bool|1",
			"modulename2"=>"What is the modulename for the 2nd dwelling type?,text|",
			"chair2"=>"Allow 2nd dwelling type to have a chair?,bool|1",
			"table2"=>"Allow 2nd dwelling type to have a table?,bool|1",
			"bed2"=>"Allow 2nd dwelling type to have a bed?,bool|0",
			"modulename3"=>"What is the modulename for the 3rd dwelling type?,text|",
			"chair3"=>"Allow 3rd dwelling type to have a chair?,bool|1",
			"table3"=>"Allow 3rd dwelling type to have a table?,bool|0",
			"bed3"=>"Allow 3rd dwelling type to have a bed?,bool|0",
			"modulename4"=>"What is the modulename for the 4th dwelling type?,text|",
			"chair4"=>"Allow 4th dwelling type to have a chair?,bool|0",
			"table4"=>"Allow 4th dwelling type to have a table?,bool|0",
			"bed4"=>"Allow 4th dwelling type to have a bed?,bool|0",
			"modulename5"=>"What is the modulename for the 5th dwelling type?,text|",
			"chair5"=>"Allow 5th dwelling type to have a chair?,bool|0",
			"table5"=>"Allow 5th dwelling type to have a table?,bool|0",
			"bed5"=>"Allow 5th dwelling type to have a bed?,bool|0",
			"modulename6"=>"What is the modulename for the 6th dwelling type?,text|",
			"chair6"=>"Allow 6th dwelling type to have a chair?,bool|0",
			"table6"=>"Allow 6th dwelling type to have a table?,bool|0",
			"bed6"=>"Allow 6th dwelling type to have a bed?,bool|0",
			"modulename7"=>"What is the modulename for the 7th dwelling type?,text|",
			"chair7"=>"Allow 7th dwelling type to have a chair?,bool|0",
			"bed7"=>"Allow 7th dwelling type to have a bed?,bool|0",
			"table7"=>"Allow 7th dwelling type to have a table?,bool|0",
			"modulename8"=>"Do you have more than 7 dwelling types installed?,bool|0",
			"chair8"=>"Allow additional dwellings to have a chair?,bool|0",
			"bed8"=>"Allow additional dwellings to have a bed?,bool|0",
			"table8"=>"Allow additional dwellings to have a table?,bool|0",
		),
		"prefs"=>array(
			"Furniture Store,title",
			"first"=>"Has the player been to the shop before?,bool|0",
			"Chair,title",
			"usedchair"=>"Number of times player used the chair today?,int|0",
			"buychair"=>"Has the player bought a chair this dk?,bool|0",
			"Table,title",
			"usedtable"=>"Number of times player used the table today?,int|0",
			"buytable"=>"Has the player bought a table this dk?,bool|0",
			"Bed,title",
			"usedbed"=>"Number of times player used the bed today?,int|0",
			"buybed"=>"Has the player bought a bed this dk?,bool|0",
		),
		"prefs-dwellings"=>array(
			"Furniture,title",
			"chair"=>"Which chair has the player purchased?,enum,0,None,1,Basic,2,General,3,Heirloom|0",
			"custchair"=>"If custom chair purchased, what is it called?,text|",
			"chairstress"=>"How many times has the chair been stressed?,int|0",
			"table"=>"Which table has the player purchased?,enum,0,None,1,Basic,2,General,3,Heirloom|0",
			"custtable"=>"If custom table purchased, what is it called?,text|",
			"tablestress"=>"How many times has the table been stressed?,int|0",
			"bed"=>"Which bed has the player purchased?,enum,0,None,1,Basic,2,General,3,Heirloom|0",
			"custbed"=>"If custom bed purchased, what is it called?,text|",
			"bedstress"=>"How many times has the bed been stressed?,int|0",
		),
		"requires"=>array(
			"dwellings" => "Dwellings by Sixf00t4",
		),
	);
	return $info;
}
function furniture_install(){
	require_once("modules/furniture/furniture_install.php");
	return true;
}
function furniture_uninstall(){
	return true;
}
function furniture_dohook($hookname,$args){
	global $session;
	require("modules/furniture/dohook/$hookname.php");
	return $args;
}
function furniture_run(){
	include("modules/furniture/furniture.php");
}
?>