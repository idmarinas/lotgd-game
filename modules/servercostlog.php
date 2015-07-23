<?php
function servercostlog_getmoduleinfo(){
	$info = array(
		"name"=>"Server Cost Log",
		"version"=>"1.0",
		"author"=>"`2Oliver Brendel",
		"category"=>"Administrative",
		"download"=>"http://lotgd-downloads.com",
	);
	return $info;
}

function servercostlog_install(){
	module_addhook_priority("header-paylog", 70);
	$table=array(
		'servercostid'=>array('name'=>'servercostid', 'type'=>'int(11) unsigned', 'extra'=>'auto_increment'),
		'date'=>array('name'=>'date', 'type'=>'datetime', 'default'=>'0000-00-00 00:00:00'),
		'type'=>array('name'=>'type', 'type'=>'tinyint(3) unsigned', 'default'=>'0'),
		'amount'=>array('name'=>'amount', 'type'=>'float(9,2)', 'default'=>'0.0'),
		'comment'=>array('name'=>'comment', 'type'=>'text'),
		'key-PRIMARY' => array('name'=>'PRIMARY', 'type'=>'primary key', 'unique'=>'1', 'columns'=>'servercostid'),
		'key-one'=> array('name'=>'datetype', 'type'=>'key', 'unique'=>'0', 'columns'=>'date,type'),
		'key-two'=> array('name'=>'amount', 'type'=>'key', 'unique'=>'0', 'columns'=>'amount'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix("servercostlog"), $table, true);
	return true;
}

function servercostlog_uninstall(){
	return true;
}

function servercostlog_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
	case "header-paylog":
		if ($session['user']['superuser'] & SU_MEGAUSER == SU_MEGAUSER) {
			addnav("Servercost");
			addnav("Servercost Log","runmodule.php?module=servercostlog&op=view");
		}
	break;
	}
	return $args;
}

function servercostlog_run(){
	global $session;
	$op=httpget('op');
	page_header("Servercost Log");
	addnav("Navigation");
	addnav("Back to the Paylog","paylog.php");
	addnav("Back to the Servercostlog","runmodule.php?module=servercostlog&op=view");
	addnav("Actions");
	addnav("Enter Payment","runmodule.php?module=servercostlog&op=enter");
	addnav("Check Monthly Balance","runmodule.php?module=servercostlog&op=balance");
	addnav("Months");
	$ic=db_prefix('servercostlog');		
	$sql = "SELECT substring(date,1,7) AS month, sum(amount) AS servercost FROM $ic GROUP BY month DESC";
	$result=db_query($sql);
	//deep look at paylog.php
	while ($row = db_fetch_assoc($result)){
		addnav(array("%s %s %s", date("M Y",strtotime($row['month']."-01")), getsetting("paypalcurrency", "USD"), $row['servercost']),"runmodule.php?module=servercostlog&op=view&month={$row['month']}");
	}
	switch ($op) {
		case "balance":
			$ic=db_prefix('servercostlog');
			$pl=db_prefix('paylog');
			$sql = "SELECT substring(date,1,7) AS month, sum(amount) AS servercost FROM $ic GROUP BY month DESC";
			$result = db_query($sql);
			$payments=array();
			while ($row=db_fetch_assoc($result)) {
				$payments[date("M Y",strtotime($row['month']."-01"))]['costs']=$row['servercost'];
			}
			$sql = "SELECT substring(processdate,1,7) AS month, sum(amount)-sum(txfee) AS profit FROM $pl GROUP BY month DESC";
			$result=db_query($sql);
			while ($row = db_fetch_assoc($result)){
				$payments[date("M Y",strtotime($row['month']."-01"))]['income']=$row['profit'];
			}
			//deep look at paylog.php
			$total=0;
			$i=0;
			rawoutput("<table border='0' cellpadding='2' cellspacing='0' width='100%'>");
			rawoutput("<tr class='trhead'><td>". translate_inline("Month") ."</td><td>". translate_inline("Profit")."</td></tr>");
			while (list ($key,$row)=each($payments)){
				$i++;
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
				$date=$key;
				output_notl($date);
				rawoutput("</td><td>");
				$month=$row['income']-$row['costs'];
				$total+=$month;
				if ($month>0) $col="`2";
					elseif ($month==0) $col="`)";
					else $col='`$';
				output_notl($col.getsetting('paypalcurrency','USD')." ".$month."`0");
				rawoutput("</td></tr>");
			}
			$i++;
			rawoutput("<tr><td colspan='2'><hr></td></tr><tr class='".($i%2?"trlight":"trdark")."'><td>");
			output("Sum in total:");
			rawoutput("</td><td>");
			if ($total>0) $col="`2";
				elseif ($total==0) $col="`)";
				else $col='`$';
			output_notl($col.getsetting('paypalcurrency','USD')." ".$total."`0");
			rawoutput("</td></tr>");
			rawoutput("</table>");
			break;
		case "view":
			$month=httpget('month');
			if ($month=="") $month = date("Y-m");
			$startdate = $month."-01 00:00:00";
			$enddate = date("Y-m-d H:i:s",strtotime("+1 month",strtotime($startdate)));
			$sql = "SELECT $ic.* FROM $ic WHERE date>='$startdate' AND date < '$enddate' ORDER BY servercostid DESC";
			$result = db_query($sql);
			rawoutput("<table border='0' cellpadding='2' cellspacing='0' width='100%'>");
			rawoutput("<tr class='trhead'><td>". translate_inline("Date") ."</td><td>". translate_inline("Type") ."</td><td>".translate_inline("Amount")."</td><td>".translate_inline("Details")."</td></tr>");
			$i=0;
			while ($row=db_fetch_assoc($result)) {
				$i++;
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
				output_notl($row['date']);
				rawoutput("</td><td>");
				if ($row['type']==1) $type='Monthly Regular Payment';
					elseif ($row['type']==2) $type='One-Time Payment';
				output($type);
				rawoutput("</td><td>");
				output_notl($row['amount']);
				rawoutput("</td><td>");
				output_notl($row['comment']);
				rawoutput("</td></tr>");
			}
			rawoutput("</table>");
			break;
		case "save":
			$amount=httppost('amount');
			$type=httppost('type');
			$date=httppost('date');
			$comment=str_replace(chr(13),'`n',httppost('comment'));
			$sql="INSERT INTO ".db_prefix('servercostlog')." VALUES ";
			$sql.="(0,'$date','$type','$amount','$comment');";
			$result=db_query($sql);
			if ($result==1)
				output("`7The Entry has been generated.");
				else
				output("`\$There has been an error while processing the entry!");
				
			break;
		case "enter":
			$paycurrency=getsetting('paypalcurrency','USD');
			output("`c`b`^Servercost Payment entry`c`b");
			output_notl("`n`n");
			output("`7Enter here the payment you have done to pay the server.`n`n");
			rawoutput("<form action='runmodule.php?module=servercostlog&op=save' method='post'>");
			addnav("","runmodule.php?module=servercostlog&op=save");
			output("Date:");
			rawoutput("<input class='input' name='date' value='".date("Y-m-d H:i:s")."'><br><br>");
			output("Type:`n");
			$monthly=translate_inline("Monthly Regular Payment");
			$onetime=translate_inline("One-Time Payment");
			rawoutput("<select class='input' name='type'><br><br>");
			rawoutput("<option value='1'>$monthly</option>");
			rawoutput("<option value='2'>$onetime</option>");
			rawoutput("</select><br><br>");
			output("Currency: ");
			output_notl("$paycurrency`n`n");
			output("Amount:`n");
			rawoutput("<input type='input' class='input' maxlength=12 length=20 name='amount'><br><br>");
			output("Details:`n");
			rawoutput("<textarea name='comment' cols='50' rows='10' wrap='virtual' ></textarea><br><br><br>");
			$submit=translate_inline("Submit");
			rawoutput("<input type='submit' class='button' value='$submit'><br>");
			rawoutput("</form>");
						
			break;	
	
	}
	page_footer();
}


?>