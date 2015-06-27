<?php


       	require_once("lib/gamelog.php");
	// Clean up referer entries
	$timestamp = date("Y-m-d H:i:s",strtotime("-2 month"));
	db_query("DELETE FROM ".db_prefix("referers")." WHERE last < '$timestamp'");
	gamelog("Deleted ".db_affected_rows()." records from ".db_prefix("referers")." older than $timestamp.","maintenance");

	// shift old entries from the debuglog to the archive, clean up the archive if necessary
	//Clean up debug log arhive, moved from there
	$timestamp = date("Y-m-d H:i:s",strtotime("now"));
	$sql = "INSERT IGNORE INTO ".db_prefix('debuglog_archive')." SELECT * FROM " . db_prefix("debuglog")." WHERE date <'$timestamp'";

	$ok = db_query($sql);
	if ($ok) {
		$sql = "DELETE FROM ".db_prefix('debuglog')." WHERE date <'$timestamp'";
		db_query($sql);
               	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expiredebuglog',18),0)." days"));
      	        $sql = "DELETE FROM " . db_prefix("debuglog_archive") . " WHERE date <'$timestamp'";
		if (getsetting('expiredebuglog',18)>0) db_query($sql);
       		gamelog("Moved ".db_affected_rows()." from ".db_prefix("debuglog")." to ".db_prefix('debuglog_archive')." older than $timestamp.",'maintenance');
	} else {
		gamelog("ERROR, problems with moving the debuglog to the archive",'maintenance');
	}



	//Clean up old mails
        $timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('oldmail',14),0)." days"));
	$sql = "DELETE FROM " . db_prefix('mail') . " WHERE sent<'$timestamp'";
	db_query($sql);
	gamelog("Deleted ".db_affected_rows()." records from ".db_prefix("mails")." older than $timestamp.","maintenance");
	massinvalidate("mail");
		
	//CONTENT
		
	//Clean up news
	if ((int)getsetting("expirecontent",180)>0){
        	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expirecontent',180),0)." days"));
		$sql = "DELETE FROM " . db_prefix("news") . " WHERE newsdate<'$timestamp'";
		gamelog("Deleted ".db_affected_rows()." records from ".db_prefix("news")." older than $timestamp.","comment expiration");
		db_query($sql);
	}



	//Clean up game log
	$timestamp = date("Y-m-d H:i:s",strtotime("-".round(getsetting('expiregamelog',30),0)." days"));
	$sql = "DELETE FROM ".db_prefix("gamelog")." WHERE date < '$timestamp' ";
	if (getsetting('expiregamelog',30)>0) {
		db_query($sql);
		gamelog("Cleaned up ".db_prefix("gamelog")." table removing ".db_affected_rows()." older than $timestamp.","maintenance");
	}
	
	//Clean up old comments

	$sql = "DELETE FROM " . db_prefix("commentary") . " WHERE postdate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
	if (getsetting("expirecontent",180)>0) {
        	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expirecontent',180),0)." days"));
		db_query($sql);
		gamelog("Deleted ".db_affected_rows()." records from ".db_prefix("commentary")." older than $timestamp.","comment expiration");
	}	

	//Clean up old moderated comments

	$sql = "DELETE FROM " . db_prefix("moderatedcomments") . " WHERE moddate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
	if (getsetting("expirecontent",180)>0) {
        	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expirecontent',180),0)." days"));
		db_query($sql);
		gamelog("Deleted ".db_affected_rows()." records from ".db_prefix("moderatedcomments")." older than $timestamp.","comment expiration");
	}

	//Expire the faillog entries

	$sql = "DELETE FROM " . db_prefix("faillog") . " WHERE date<'".date("Y-m-d H:i:s",strtotime("-".round(getsetting("expirefaillog",1),0)." days"))."'";
	if (getsetting("expirefaillog",1)>0) {
		db_query($sql);
        	$timestamp = date("Y-m-d H:i:s",strtotime('-'.round(getsetting('expirecontent',180),0)." days"));
		gamelog("Deleted ".db_affected_rows()." records from ".db_prefix("faillog")." older than $timestamp.","maintenance");
	}
