<?PHP
function get_cityprefs_module($lookup,$value,$player=false){
	if($player>0){
		$sql1="select location from ".db_prefix("accounts")." where acctid=$player";
		$res1=db_query($sql1);
		$row1=db_fetch_assoc($res1);
		$lookup="cityname";
		$value=$row1['location'];
	}
	
	if($lookup=='cityid'){$where="cityid=$value";}
	else{$where="cityname='".addslashes($value)."'";}

	$sql="select module from ".db_prefix("cityprefs")." where $where";
	$res=db_query($sql);
	$row=db_fetch_assoc($res);
	return $row['module'];
}

function get_cityprefs_cityid($lookup,$value,$player=false){
	if($player>0){
		$sql1="select location from ".db_prefix("accounts")." where acctid=$player";
		$res1=db_query($sql1);
		$row1=db_fetch_assoc($res1);
		$lookup="cityname";
		$value=$row1['location'];
	}
	
	if($lookup=='module'){$where="module='".addslashes($value)."'";}
	else{$where="cityname='".addslashes($value)."'";}

	$sql="select cityid from ".db_prefix("cityprefs")." where $where";
	$res=db_query($sql);
	$row=db_fetch_assoc($res);
	return $row['cityid'];
}

function get_cityprefs_cityname($lookup,$value,$player=false){
	if($player>0){
		$sql1="select location from ".db_prefix("accounts")." where acctid=$player";
		$res1=db_query($sql1);
		$row1=db_fetch_assoc($res1);
		return $row1['location'];
	}
	
	if($lookup=='module'){$where="module='".addslashes($value)."'";}
	else{$where="cityid=$value";}

	$sql="select cityname from ".db_prefix("cityprefs")." where $where";
	$res=db_query($sql);
	$row=db_fetch_assoc($res);
	return $row['cityname'];
}
?>