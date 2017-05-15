<?php

function char_cleanup($id, $type)
{
    require_once 'lib/gamelog.php';

	// this function handles the grunt work of character cleanup.

	// Run any modules hooks who want to deal with character deletion, or stop it
	$return = modulehook("delete_character", array("acctid"=>$id, "deltype"=>$type, "dodel"=>true));

	if(!$return['dodel']) return false;

	// delete the output field from the accounts_output table introduced in 1.1.1

	DB::query("DELETE FROM " . DB::prefix("accounts_output") . " WHERE acctid=$id;");

	// delete the comments the user posted, necessary to have the systemcomments with acctid 0 working

	DB::query("DELETE FROM " . DB::prefix("commentary") . " WHERE author=$id;");

	// Clean up any clan positions held by this character
	$sql = "SELECT clanrank,clanid FROM " . DB::prefix("accounts") .
		" WHERE acctid=$id";
	$res = DB::query($sql);
	$row = DB::fetch_assoc($res);
	if ($row['clanid'] != 0 && ($row['clanrank'] == CLAN_LEADER || $row['clanrank'] ==CLAN_FOUNDER)) {
		$cid = $row['clanid'];
		// We need to auto promote or disband the clan.
		$sql = "SELECT name,acctid,clanrank FROM " . DB::prefix("accounts") .
			" WHERE clanid=$cid AND clanrank > " . CLAN_APPLICANT . " AND acctid<>$id ORDER BY clanrank DESC, clanjoindate";
		$res = DB::query($sql);
		if (DB::num_rows($res)) {
			// Okay, we can promote if needed
			$row = DB::fetch_assoc($res);
			if ($row['clanrank'] != CLAN_LEADER) {
				// No other leaders, promote this one
				$id1 = $row['acctid'];
				$sql = "UPDATE " . DB::prefix("accounts") .
					" SET clanrank=" . CLAN_LEADER . " WHERE acctid=$id1";
				DB::query($sql);
                gamelog("Clan $cid has a new leader ".$row['name']." as there were no others left","clan");
			}
		} else {
			// this clan needs to be disbanded.
			$sql = "DELETE FROM " . DB::prefix("clans") . " WHERE clanid=$cid";
			DB::query($sql);
			// And just in case we goofed, no players associated with a
			// deleted clan  This shouldn't be important, but.
			$sql = "UPDATE " . DB::prefix("accounts") . " SET clanid=0,clanrank=0,clanjoindate='0000-00-00 00:00;00' WHERE clanid=$cid";
			DB::query($sql);
			gamelog("Clan $cid has been disbanded as the last member left","clan");
		}
	}

	// Delete any module user prefs
	module_delete_userprefs($id);

	// Delete any mail to or from the user
	DB::query('DELETE FROM ' . DB::prefix('mail') . ' WHERE msgto=' . $id . ' OR msgfrom=' . $id);

	// Delete any news from the user
	DB::query('DELETE FROM ' . DB::prefix('news') . ' WHERE accountid=' . $id);

    // delete the output field from the accounts_output table introduced in 1.1.1
    DB::query("DELETE FROM " . DB::prefix("accounts_output") . " WHERE acctid=$id;");

    return true;
}

?>
