<?php
// translator ready
// addnews ready
// mail ready

/**
*Documentated by Catscradler
*Add to the user's log
*if $field $value and $consolidate have values, entry will be merged with existing line from today with identical $field.
*otherwise, a new line will be added to the log.
*
*@param string $message the text to be added
*@param int $target acctid of the user on the receiving end of the event eg. the user who did NOT initiate PvP, gold transfer recipient (optional)
*@param int $user acctid of the user the log entry is about (optional, defaults to current user)
*@param string $field the label for this line, appears as first word on this line in the log eg. healing, forestwin (optional)
*@param int $value how much was gained or lost.  Only useful if also using $field and $consolidate (optional)
*@param bool $consolidate add $value to previous log lines with the same $field, keeping a running total for today (optional, defaults to true)
*/
function debuglog($message,$target=false,$user=false,$field=false,$value=false,$consolidate=true){
	if ($target===false) $target=0;
	static $needsdebuglogdelete = true;
	global $session;
	$args = func_get_args();
	if ($user === false) $user = $session['user']['acctid'];
	$corevalue = $value;
	$id=0;
	if ($field !== false && $value !==false && $consolidate){
		$sql = "SELECT * FROM ".db_prefix("debuglog")." WHERE actor=$user AND field='$field' AND date>'".date("Y-m-d 00:00:00")."'";
		$result = db_query($sql);
		if (db_num_rows($result)>0){
			$row = db_fetch_assoc($result);
			$value = $row['value']+$value;
			$message = $row['message'];
			$id = $row['id'];
		}
	}
	if ($corevalue!==false) $message.=" ($corevalue)";
	if ($field===false) $field="";
	if ($value===false) $value=0;
	if ($id > 0){
		$sql = "UPDATE ".db_prefix("debuglog")."
			SET
				date='".date("Y-m-d H:i:s")."',
				actor='$user',
				target='$target',
				message='".addslashes($message)."',
				field='$field',
				value='$value'
			WHERE
				id=$id
				";
	}else{
		$sql = "INSERT INTO " . db_prefix("debuglog") . " (id,date,actor,target,message,field,value) VALUES($id,'".date("Y-m-d H:i:s")."',$user,$target,'".addslashes($message)."','$field','$value')";
	}
	db_query($sql);
}

?>
