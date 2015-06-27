<?php

function char_cleanup($id, $type)
{
	// this function handles the grunt work of character cleanup.

	// Run any modules hooks who want to deal with character deletion
	modulehook("delete_character",
			array("acctid"=>$id, "deltype"=>$type));

	// delete the output field from the accounts_output table introduced in 1.1.1

	db_query("DELETE FROM " . db_prefix("accounts_output") . " WHERE acctid=$id;");

	// delete the comments the user posted, necessary to have the systemcomments with acctid 0 working

	db_query("DELETE FROM " . db_prefix("commentary") . " WHERE author=$id;");

	// Clean up any clan positions held by this character
	$sql = "SELECT clanrank,clanid FROM " . db_prefix("accounts") .
		" WHERE acctid=$id";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	if ($row['clanid'] != 0 && ($row['clanrank'] == CLAN_LEADER || $row['clanrank'] ==CLAN_FOUNDER)) {
		$cid = $row['clanid'];
		//check if there are any leaders or founders left
		$sql = "SELECT count(acctid) FROM " . db_prefix("accounts") .
			" WHERE clanid=$cid AND clanrank >= " . CLAN_LEADER . " AND acctid<>$id ORDER BY clanrank DESC, clanjoindate";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		if ($row['counter']==0) {
			// We need to auto promote or disband the clan.
			$sql = "SELECT name,acctid,clanrank FROM " . db_prefix("accounts") .
				" WHERE clanid=$cid AND clanrank > " . CLAN_APPLICANT . " AND acctid<>$id ORDER BY clanrank DESC, clanjoindate";
			$res = db_query($sql);
			if (db_num_rows($res)) {
				// Okay, we can promote if needed
				$row = db_fetch_assoc($res);
				if ($row['clanrank'] != CLAN_LEADER && $row['clanrank'] != CLAN_FOUNDER) {
					// No other leaders, promote this one
					$id1 = $row['acctid'];
					$sql = "UPDATE " . db_prefix("accounts") .
						" SET clanrank=" . CLAN_LEADER . " WHERE acctid=$id1";
					db_query($sql);
				}
				require_once("lib/gamelog.php");
				gamelog("Clan $cid has a new leader ".$row['name']." as there were no others left","clan");
			} else {
				// this clan needs to be disbanded.
				$sql = "DELETE FROM " . db_prefix("clans") . " WHERE clanid=$cid";
				db_query($sql);
				// And just in case we goofed, no players associated with a
				// deleted clan  This shouldn't be important, but.
				require_once("lib/gamelog.php");
				gamelog("Clan $cid has been disbanded as the last member left","clan");
				$sql = "UPDATE " . db_prefix("accounts") . " SET clanid=0,clanrank=0,clanjoindate='0000-00-00 00:00;00' WHERE clanid=$cid";
				db_query($sql);
			}
		}
	}

	// Delete any module user prefs
	module_delete_userprefs($id);
}

?>
