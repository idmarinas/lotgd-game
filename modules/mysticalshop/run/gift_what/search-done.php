<?php
//so, let's find the player, and determine if they can actually receive the item
$dkreq = (int)httpget('dk');
$id = (int)httpget('id');
$giftee = httppost( 'whom' );
$sql = 'SELECT acctid,login,name,level,dragonkills FROM '.db_prefix('accounts')." WHERE (name LIKE '%".$giftee."%' OR login LIKE '%".$giftee."%') AND acctid<>".$session['user']['acctid'].' AND locked=0 ORDER BY dragonkills DESC';
$result = db_query($sql);
$count = db_num_rows($result);

addnav( 'Search' );
addnav( 'Search Again', 'runmodule.php?module=mysticalshop&op=gift&what=search&dk='.$dkreq.'&id='.$id.'&cat='.$cat );

//Well, I was unable to find player. Let's tell the giftOR...
if ($count == 0){
	output("`3Couldn't find a person by that name. Try again.`0");
}
else
{
	rawoutput('<table cellpadding="3" cellspacing="1" style="border:none">');
	$i = true;
	$name = translate_inline('Name');
	$level = translate_inline('Level');
	$met_dk = translate_inline('Meets DK Requirement?');
	$donator = translate_inline('Donator');
	$item_categories = array( 'ring', 'amulet', 'weapon', 'armor', 'cloak', 'helm', 'glove', 'boots', 'misc' );
	$found = 0;
	$found_a_giftee = false;

	while($row = db_fetch_assoc($result)){
		//now onto checking to see if the player owns and item of that category, or if they've already received a gift
		$givengift = get_module_pref('gifted','mysticalshop',$row['acctid']);
		if ($row['dragonkills']>=$dkreq){
			$dkmet = 'Yes';
		}else{
			$dkmet = 'No';
		}
		$playerid = $row['acctid'];
		$playername = $row['name'];
		$giveme = !get_module_pref( $item_categories[$cat], 'mysticalshop', $playerid );

		//if selected player(s) meet the requirements, let's find 'em and list 'em
		//now includes checks to see if the player carries a pass from the hunting lodge
		if( $giveme && $givengift == 0 )
		{
		  if( $found == 0 )
		  {
				if (get_module_setting('shopappear') == 1){
					rawoutput("<tr class=\"trhead\"><td>$name</td><td style=\"text-align:right\">$level</td><td style=\"text-align:center\">$met_dk</td><td style=\"text-align:center\">$donator</td></tr>");
				}else{
					rawoutput("<tr class=\"trhead\"><td>$name</td><td style=\"text-align:right\">$level</td><td style=\"text-align:center\">$met_dk</td></tr>");
				}
			}
			rawoutput( '<tr class="'.( $i ? 'trdark' : 'trlight' ).'"><td>' );
			if( $dkmet == 'Yes' )
			{
				$link = $from.'op=gift&what=done&playerid='.$playerid.'&id='.$id.'&cat='.$cat;
				rawoutput( '<a href="'.htmlentities( $link ).'">' );
				output_notl( '%s`0', $playername );
				addnav( '', $link );
				rawoutput( '</a>' );
				$found_a_giftee = true;
			}
			else
				output_notl( '%s`0', $playername );
			rawoutput('</td><td style="text-align:right">');
			output_notl("%s",$row['level']);
			rawoutput('</td><td style="text-align:center">');
			//output("<td>%s</td>",$own,true);
			output( '%s', translate_inline( $dkmet ) );
			rawoutput('</td>');
			if (get_module_setting("shopappear") == 1){
				$ownpass = get_module_pref("pass","mysticalshop",$row['acctid']);
				$havepass = translate_inline($ownpass?"Yes":"No");
				rawoutput('<td style="text-align:center">');
				output("%s",$havepass);
				rawoutput('</td>');
			}
			rawoutput('</tr>');

			if( ++$found >= 24 )
			  break;
			$i = !$i;
		}elseif( $count == 1 ){
			//now, in case they can't be gifted, let's tell them possible reasons why...
			rawoutput( '<tr class="trhilight">' );
			if ($giveme == false){
				rawoutput('<td>');
				output("`3Sorry, `2%s`3 already owns an item of this type.`0",$playername);
				rawoutput('</td>');
			}elseif ($givengift != 0){
				rawoutput('<td>');
				output("`3Sorry, `2%s`3 has already received a gift, and it is awaiting pickup.`0",$playername);
				rawoutput('</td>');
			}
			rawoutput('</tr>');
		}
	}
	rawoutput('</table>');
	if( $found >= 24 )
	  output( '`n`3%s`3 stares at you for a moment before advising you that more than two dozen names match that, and that two dozen names should be enough. If the person you\'re looking for is not in the list, you should try to be more specific about the one you wish to find.`n', $shopkeep );
	if( $found_a_giftee )
	{
		output("`n`3Click on an eligible player's name to confirm the sale of the item.");
		output('`n`n`6A sign by the counter reads: "No refunds given on purchased gifts."');
		if (get_module_setting('shopappear') == 1)
			output("`n`n`^Remember: If a player doesn't have a pass (from donating), they can't claim their item!");
		output_notl( '`0' );
	}
	elseif( $count != 1 )
	  output( '`n`3It would seem that nobody is capable of handling that item at the moment.`0' );
	elseif( $dkmet == 'No' && $found != 0 )
		output( '`n`3Sorry, `2%s`3 doesn\'t meet the dragon kill requirements for this item.`0', $playername );
}
?>