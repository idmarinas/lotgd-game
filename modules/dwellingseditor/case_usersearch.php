<?php
        $query = httppost('q');
        output("Whose dwellings would you like to list?`n");
        rawoutput("<form action='runmodule.php?module=dwellingseditor&op=usersearch' method='POST'>");
        rawoutput("<input name='q' id='q'>");
        $se = translate_inline("Search");
        rawoutput("<input type='submit' class='button' value='$se'>");
        rawoutput("</form>");
        rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>");
        addnav("","runmodule.php?module=dwellingseditor&op=usersearch");
        
        $searchresult = false;
        $where = "";
        $sql = "SELECT acctid,login,name FROM " . db_prefix("accounts");
        if ($query != "") {
            $where = "WHERE login='$query' OR name='$query'";
            $searchresult = db_query($sql . " $where  ORDER BY '$order' LIMIT 2");
        }
    
        if ($query !== false || $searchresult) {
            if (db_num_rows($searchresult) != 1) {
                $where="WHERE login LIKE '%$query%' OR acctid LIKE '%$query%' OR name LIKE '%$query%' OR emailaddress LIKE '%$query%' OR lastip LIKE '%$query%' OR uniqueid LIKE '%$query%' OR gentimecount LIKE '%$query%' OR level LIKE '%$query%'";
                $searchresult = db_query($sql . " $where  ORDER BY '$order' LIMIT 101");
            }
            if (db_num_rows($searchresult)<=0){
                output("`\$No results found`0");
                $where="";
            }elseif (db_num_rows($searchresult)>100){
                output("`\$Too many results found, narrow your search please.`0");
                $op="";
                $where="";
            }else{
                $display=1;
            }
        }
        
        if ($display == 1){
            $acid =translate_inline("AcctID");
            $login =translate_inline("Login");
            $nm =translate_inline("Name");
            rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'><tr class='trhead'><td style=\"width:75px\">$acid</td><td style='width:100px' align=center>$login</td><td style='width:150px' align=center>$nm</td></tr>"); 
			$i = 0;
            while($row = db_fetch_assoc($searchresult)){
				$i++;
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
                output_notl("`&%s`0", $row['acctid'],true);
				rawoutput("</td><td>");
                output_notl("`&%s`0", $row['login'],true);
				rawoutput("</td><td align=left>");
                output_notl("<a href='runmodule.php?module=dwellingseditor&&ownerid={$row['acctid']}'>",true);
                addnav("","runmodule.php?module=dwellingseditor&&ownerid={$row['acctid']}");
                output_notl("`&%s`7", $row['name'],true);
				rawoutput("</a></td></tr>");
			}
            rawoutput("</table><Br>");    
        }
?>