<?php
				$id=httpget("id");
				$yes = translate_inline("Yes");
				$no = translate_inline("No");
				rawoutput("<form action='runmodule.php?module=mysticalshop_buffs&op=editor&what=newitem2' method='post'>");
				addnav("", "runmodule.php?module=mysticalshop_buffs&op=editor&what=newitem2");
				rawoutput("<table border='0' cellpadding='1' cellspacing='5' cols='2' width='100%'>");
				rawoutput("<tr><td width='25%'>");
				output("List of Equipment:");
				rawoutput("</td><td>");
				$sql = "SELECT id, name	 FROM ".db_prefix("magicitems");
				$result = db_query($sql);
				rawoutput("<select name='id'>");
				rawoutput("<option value='0'");
				rawoutput($item['buff']==0?" selected":"");
				rawoutput(">- none -</option>");
				for($i=0;$i<db_num_rows($result);$i++){
					$row = db_fetch_assoc($result);
					rawoutput("<option value='{$row['id']}'");
					rawoutput($row['id']==$row['id']?" selected":"");
					rawoutput(">{$row['name']}</option>");
				}
				rawoutput("</select>");
				rawoutput("</td></tr><tr><td>");
				output("List of Buffs:");
				rawoutput("</td><td>");
				$sql1 = "SELECT buffid, buffname FROM ".db_prefix("magicitembuffs");
				$result1 = db_query($sql1);
				rawoutput("<select name='buffid'>");
				rawoutput("<option value='0'");
				rawoutput($item['buff']==0?" selected":"");
				rawoutput(">- none -</option>");
				for($i=0;$i<db_num_rows($result1);$i++){
					$buff1 = db_fetch_assoc($result1);
					rawoutput("<option value='{$buff1['buffid']}'");
					rawoutput($item['buffid']==$buff1['buffid']?" selected":"");
					rawoutput(">{$buff1['buffname']}</option>");
				}
				rawoutput("</select>");
				rawoutput("</td></tr><tr><td>");
				$create = translate_inline("Attach Buff");
				rawoutput("<input type='submit' value='$create'>");
				rawoutput("</td><td>");
				rawoutput("<input type='reset'>");
				rawoutput("</td></tr><tr><td colspan='5'>");
				output("`2To remove a buff from an item, choose `3-none- `2from the buff list.");
				rawoutput("</td></tr></table>");
				rawoutput("</form>");
?>