<?php
$subop = httpget('subop');
$type = httpget("type");
$cangift = get_module_setting("givegift");
$userdk = $session['user']['dragonkills'];
//pages
$perpage = get_module_setting("shownum");
if ($subop=="") $subop=1;
$min = ($subop-1)*$perpage;
//pages display
$limit = "LIMIT $min,$perpage";
$magic_table = db_prefix( 'magicitems' );
$sql = 'SELECT COUNT(id) AS c FROM '.$magic_table
	.' WHERE '.$userdk.'>=dk AND category='.$cat
	.' ORDER BY category';
$result = db_query_cached( $sql, 'modules-mysticalshop-viewgoods-'.$cat.'-'.$userdk, 3600 );
$row = db_fetch_assoc($result);
$total = $row['c'];
addnav("Pages");
for($i = 0; $i < $total; $i+= $perpage) {
	$pnum = ($i/$perpage+1);
	$min = ($i+1);
	$max = min($i+$perpage,$total);
	addnav( array( 'Page %s (%s-%s)', $pnum, $min, $max ), $from.'op=shop&what=viewgoods&cat='.$cat.'&subop='.$pnum );
}
//
$sql = 'SELECT * FROM '.$magic_table
	.' WHERE '.$userdk.'>=dk AND category='.$cat
	.' ORDER BY category DESC '.$limit;
$ops = translate_inline("Choices");
$name = translate_inline("Item Name");
$cost = translate_inline("Item Cost");
$gift = translate_inline("Gift");
//$itemid = $row['id'];
$sell = translate_inline("Sell First");
$buy = translate_inline("Examine");
$quantity = translate_inline("Quantity");
$viewbuy = translate_inline("Examine or Buy");
$result = db_query_cached( $sql, 'modules-mysticalshop-viewgoods-'.$cat.'-'.$userdk.'-page-'.$min.'-'.$perpage, 3600 );
$count = db_num_rows($result);
if ($count == 0){
	output("`6No Items on record yet.`0");
}else{
	rawoutput( '<table cellspacing="1" cellpadding="2" width="100%">' );
	rawoutput("<tr class=\"trhead\"><td>$ops</td><td>$name</td><td>$cost</td><td>$quantity</td></tr>");
	$i = false;
	while($row = db_fetch_assoc($result)){
		$rare = $row['rare'];
		$rarenum = $row['rarenum'];
		if ($rare == 0){ $instock = translate_inline( '`3Many' ); }
		else if ($rarenum<1) { $instock = translate_inline( '`$Sold Out' ); }
		else {$instock = $rarenum; }

		$item_categories = array( 'ring', 'amulet', 'weapon', 'armor', 'cloak', 'helm', 'glove', 'boot', 'misc' );
		$category = $item_categories[$cat];
		$sellid = get_module_pref( $category.'id' );

		$class = $i ? 'trlight' : 'trdark';
		rawoutput( '<tr class="'.$class.'">' );
		//if player owns an item, then they have to sell first
		if( $category == 'boot' )
		  $category = 'boots';
		if( get_module_pref( $category ) )
		{
			rawoutput( '<td>[<a href="'.htmlentities( $from.'op=shop&what=preview&id=' ).$row['id'].'">'.$buy.'</a>] [<a href="'.htmlentities( $from.'op=shop&what=sell&id='.$sellid.'&cat=' ).$cat.'">'.$sell.'</a>]' );
			addnav( '', $from.'op=shop&what=preview&id='.$row['id'] );
			addnav( '', $from.'op=shop&what=sell&id='.$sellid.'&cat='.$cat );
		//otherwise...	
		}else{
			rawoutput( '<td>[<a href="'.htmlentities( $from.'op=shop&what=preview&id=' ).$row['id'].'">'.$viewbuy.'</a>]' );
			addnav( '', $from.'op=shop&what=preview&id='.$row['id'] );
		}
		if ($cangift == 1){
			rawoutput( ' [<a href="'.htmlentities( $from.'op=gift&what=search&id='.$row['id'].'&cat=' ).$cat.'">'.$gift.'</a>]</td>' );
			addnav( '', $from.'op=gift&what=search&id='.$row['id'].'&cat='.$cat );
		}else{
			rawoutput("</td>");
		}
		rawoutput("<td>");
		output_notl("`&%s`0", $row['name']);
		rawoutput("</td><td>");
		$gem = translate_inline( 'Gem' );
		$gem_pl = translate_inline( 'Gems' );
		output( "`^%s Gold`0 and `%%s %s`0", $row['gold'], $row['gems'], abs( $row['gems'] ) != 1 ? $gem_pl : $gem );
		rawoutput("</td><td>");
		output("`Q%s`0",$instock);
		rawoutput("</td>");
		rawoutput("</tr>");
		if (get_module_setting("displaydesc")){
			if ($row['description']>""){
				rawoutput( '<tr class="'.$class.'"><td colspan="5">' );
				output("`i`3Description: %s`0`i", $row['description']);
				rawoutput("</td></tr>");
			}else{
				rawoutput( '<tr class="'.$class.'"><td colspan="5">');
				output("`i`3No description available`0`i");
				rawoutput("</td></tr>");
			}
		}
		$i = !$i;
	}
}
rawoutput("</table>");
addnav( 'Merchandise' );
addnav( 'Overview of Goods', $from."op=shop&what=enter");
?>