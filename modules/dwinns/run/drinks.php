<?
	$dwname = get_module_setting("dwname","dwinns");
	$dwid = httpget("dwid");
	page_header("Inside the brewery");
	
	output("`2Inside the brewery of your %s`2 you can smell the sweet aroma of brewing beer.",$dwname);
	output("`2It may take a while to brew, and quite a lot of experience to brew a good ale, but in the end, it's worth it.`n`n");
	
	$sql = "SELECT drinks, drinkqual, brewname, brewexp FROM " . db_prefix("dwinns") . " WHERE dwid='$dwid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	
	$alename = $row['brewname'];
	$bexp = floor(($row['brewexp'])/1000);

	if($alename==""){
		$alename = httppost('newname');
		$alename = str_replace("`n", "", $alename);
		if($alename!="") {
			output("`2Your ale is known by the name of %s`2.",$alename);
			
			$sql = "UPDATE " . db_prefix("dwinns") . " SET brewname='$alename' WHERE dwid='$dwid'";
			db_query($sql);
			
		}else{
			$submit = translate_inline("Submit");
			output("You still haven't picked a name for your ale. Do this now:");
			rawoutput("<script language='JavaScript'>
			function previewtext(t){
			var out = \"<span class=\'colLtWhite\'>\";
			var end = '</span>';
			var x=0;
			var y='';
			var z='';
			if(t.substr(0,2)=='::'){
				x=2;
				out += '</span><span class=\'colLtWhite\'>';
			}else if(t.substr(0,1)==':'){
				x=1;
				out += '</span><span class=\'colLtWhite\'>';
			}else if(t.substr(0,3)=='/me'){
				x=3;
				out += '</span><span class=\'colLtWhite\'>';
			}else{
				out += '</span><span class=\'colDkWhite\'>';
				end += '</span>';
			}
			for (; x < t.length; x++){
				y = t.substr(x,1);
				if(y=='<'){
					out += '&lt;';
					continue;
				}else if(y=='>'){
					out += '&gt;';
					continue;
				}else if(y=='`'){
					if(x < t.length-1){
						z = t.substr(x+1,1);
						if(z=='0'){
							out += '</span>';
						}else if(z=='1'){
							out += '</span><span class=\'colDkBlue\'>';
						}else if(z=='2'){
							out += '</span><span class=\'colDkGreen\'>';
						}else if(z=='3'){
							out += '</span><span class=\'colDkCyan\'>';
						}else if(z=='4'){
							out += '</span><span class=\'colDkRed\'>';
						}else if(z=='5'){
							out += '</span><span class=\'colDkMagenta\'>';
						}else if(z=='6'){
							out += '</span><span class=\'colDkYellow\'>';
						}else if(z=='7'){
							out += '</span><span class=\'colDkWhite\'>';
						}else if(z=='q'){
							out += '</span><span class=\'colDkOrange\'>';
						}else if(z=='!'){
							out += '</span><span class=\'colLtBlue\'>';
						}else if(z=='@'){
							out += '</span><span class=\'colLtGreen\'>';
						}else if(z=='#'){
							out += '</span><span class=\'colLtCyan\'>';
						}else if(z=='$'){
							out += '</span><span class=\'colLtRed\'>';
						}else if(z=='%'){
							out += '</span><span class=\'colLtMagenta\'>';
						}else if(z=='^'){
							out += '</span><span class=\'colLtYellow\'>';
						}else if(z=='&'){
							out += '</span><span class=\'colLtWhite\'>';
						}else if(z=='Q'){
							out += '</span><span class=\'colLtOrange\'>';
						}else if(z==')'){
							out += '</span><span class=\'colLtBlack\'>';
						}
						x++;
					}
				}else{
					out += y;
				}
			}
			document.getElementById(\"previewtext\").innerHTML=out+end+'<br/>';
			}
			</script>");
			
			rawoutput("<form action='runmodule.php?module=dwinns&op=drinks&dwid=$dwid' method='POST' autocomplete='false'>");
			addnav("","runmodule.php?module=dwinns&op=drinks&dwid=$dwid");
			rawoutput("<input name='newname' id='newname' onKeyUp='previewtext(document.getElementById(\"newname\").value);'; size='40' maxlength='255'><br>");
			rawoutput("<input type='submit' class='button' value='$submit'><br>");
			rawoutput("<div id='previewtext'></div></form>");
		}
	}else
		output("`2Your ale is known by the name of %s`2.`n`n",$alename);
	output("`2Ale can't be bought, as any drinker will tell by taste alone in which city it was brewed, what ingredients were used, which day of the week it was brewed and what cologne the brewer was using that day (if any).`n");
	output("`2So you'll have to brew the ale yourself.`n");
	output("`2You have `5%s liters of ale`2 ready to be served.`n",$row['drinks']
); 
	
	if($row['drinkqual']==0){
		output("`nYou don't have a license to sell alcoholic drinks. Do you wish to buy one?`n");
		output("A `1low quality`2 licence allows you to brew bad ale for 30 days, it costs `61.000 gold`2.`n");
		output("A `1medium quality`2 licence allows you to brew normal ale for 30 days, it costs `610.000 gold`2.`n");
		output("A `1high quality`2 licence allows you to brew good ale for 30 days, it costs `6100.000 gold`2.`n");
		output("`nIn the end, though, the quality of the ale is set by the brewer and his experience brewing ale. The more ale he brews, the best results he will get.`n");
		output("Right now, the quality of your ale is `0%s`2. It will increase for every 1.000 ales brewed to a maximum quality of `010`2.`n",$bexp);
		
		if($session['user']['gold'] < 1000)
			output("`n`nYou don't have enough gold to buy a license.");
		else{
			addnav("Yes, buy low quality license","runmodule.php?module=dwinns&op=paid-license&dwid=$dwid&drinkqual=1");
			if($session['user']['gold'] >= 10000){
				addnav("Yes, buy medium quality license","runmodule.php?module=dwinns&op=paid-license&dwid=$dwid&drinkqual=2");
				if($session['user']['gold'] >= 100000)
					addnav("Yes, buy high quality license","runmodule.php?module=dwinns&op=paid-license&dwid=$dwid&drinkqual=3");
			}
		}
		addnav(array("No, back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}else{
		addnav("Customize your Ale","runmodule.php?module=dwinns&op=change-ale&dwid=$dwid");
		if($session['user']['turns']>0){
			output("`2`nDo you wish to brew some ale?");
			addnav("Yes, brew one liter","runmodule.php?module=dwinns&op=brew-ale&dwid=$dwid&quant=1"); 
			if($session['user']['turns']>4){
				addnav("Yes, brew five liters","runmodule.php?module=dwinns&op=brew-ale&dwid=$dwid&quant=5"); 
				if($session['user']['turns']>9)
					addnav("Yes, brew ten liters","runmodule.php?module=dwinns&op=brew-ale&dwid=$dwid&quant=10"); 
			}
			addnav(array("No, back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
		}else{
			output("`n`n`2You don't have enough turns left to brew ale");
			addnav(array("Back to the %s",$dwname),"runmodule.php?module=dwellings&op=enter&dwid=$dwid");
		}
	}
?>
