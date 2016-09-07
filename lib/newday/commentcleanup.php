<?php


       	require_once("lib/gamelog.php");
	// Clean up referer entries
	$timestamp = date("Y-m-d H:i:s",strtotime("-2 month"));
	DB::query("DELETE FROM ".DB::prefix("referers")." WHERE last < '$timestamp'");
	gamelog("Deleted ".DB::affected_rows()." records from ".DB::prefix("referers")." older than $timestamp.","maintenance");

	// shift old entries from the debuglog to the archive, clean up the archive if necessary
	//Clean up debug log arhive, moved from there
	$timestamp = date("Y-m-d H:i:s",strtotime("now"));
	$sql = "INSERT IGNORE INTO ".DB::prefix('debuglog_archive')." SELECT * FROM " . DB::prefix("debuglog")." WHERE date <'$timestamp'";

	$ok = DB::query($sql);
	if ($ok) {
		$sql = "DELETE FROM ".DB::prefix('debuglog')." WHERE date <'$timestamp'";
		DB::query($sql);
               	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expiredebuglog',18),0)." days"));
      	        $sql = "DELETE FROM " . DB::prefix("debuglog_archive") . " WHERE date <'$timestamp'";
		if (getsetting('expiredebuglog',18)>0) DB::query($sql);
       		gamelog("Moved ".DB::affected_rows()." from ".DB::prefix("debuglog")." to ".DB::prefix('debuglog_archive')." older than $timestamp.",'maintenance');
	} else {
		gamelog("ERROR, problems with moving the debuglog to the archive",'maintenance');
	}



	//Clean up old mails
        $timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('oldmail',14),0)." days"));
	$sql = "DELETE FROM " . DB::prefix('mail') . " WHERE sent<'$timestamp'";
	DB::query($sql);
	gamelog("Deleted ".DB::affected_rows()." records from ".DB::prefix("mails")." older than $timestamp.","maintenance");
	massinvalidate("mail");

	//CONTENT

	//Clean up news
	if ((int)getsetting("expirecontent",180)>0){
        	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expirecontent',180),0)." days"));
		$sql = "DELETE FROM " . DB::prefix("news") . " WHERE newsdate<'$timestamp'";
		gamelog("Deleted ".DB::affected_rows()." records from ".DB::prefix("news")." older than $timestamp.","comment expiration");
		DB::query($sql);
	}



	//Clean up game log
	$timestamp = date("Y-m-d H:i:s",strtotime("-".round(getsetting('expiregamelog',30),0)." days"));
	$sql = "DELETE FROM ".DB::prefix("gamelog")." WHERE date < '$timestamp' ";
	if (getsetting('expiregamelog',30)>0) {
		DB::query($sql);
		gamelog("Cleaned up ".DB::prefix("gamelog")." table removing ".DB::affected_rows()." older than $timestamp.","maintenance");
	}

	//Clean up old comments

	$sql = "DELETE FROM " . DB::prefix("commentary") . " WHERE postdate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
	if (getsetting("expirecontent",180)>0) {
        	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expirecontent',180),0)." days"));
		DB::query($sql);
		gamelog("Deleted ".DB::affected_rows()." records from ".DB::prefix("commentary")." older than $timestamp.","comment expiration");
	}

	//Clean up old moderated comments

	$sql = "DELETE FROM " . DB::prefix("moderatedcomments") . " WHERE moddate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
	if (getsetting("expirecontent",180)>0) {
        	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expirecontent',180),0)." days"));
		DB::query($sql);
		gamelog("Deleted ".DB::affected_rows()." records from ".DB::prefix("moderatedcomments")." older than $timestamp.","comment expiration");
	}

	//Expire the faillog entries

	$sql = "DELETE FROM " . DB::prefix("faillog") . " WHERE date<'".date("Y-m-d H:i:s",strtotime("-".round(getsetting("expirefaillog",1),0)." days"))."'";
	if (getsetting("expirefaillog",1)>0) {
		DB::query($sql);
        	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expirecontent',180),0)." days"));
		gamelog("Deleted ".DB::affected_rows()." records from ".DB::prefix("faillog")." older than $timestamp.","maintenance");
	}
