<?php
/* ----------------------------------------------------------------------
 * /views/Sets/exportTemplates/ca_objects_sets_pdf_html.php 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2012 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */	
	$t_set = $this->getVar("t_set");
	$va_items = $this->getVar("items");
	$vs_title = $t_set->getLabelForDisplay();
	$vn_num_items = (int)sizeof($va_items);
	
	$vn_num_items_per_page = 6;
	
?>
<HTML>
	<HEAD>
		<style type="text/css">
			<!--
			table { border: 1px solid #999999; color: #000000; text-wrap: normal; font-size: 11px; margin:15px; font-family: Helvetica, sans-serif;}
			table td { border: 1px solid #999999; color: #000000; text-wrap: normal; width: 135px; height: 120px; padding: 5px; font-size: 11px; font-family: Helvetica, sans-serif;}
			tr.odd   { background-color: #f2f2f2; }
			.displayHeader { background-color: #EEEEEE; padding: 5px; border: 1px solid #999999; font-size: 12px; font-family: Helvetica, sans-serif; }
			.pageHeader { background-color: #FFFFFF; margin: 0px 10px 20px 10px; padding: 0px 5px 20px 5px; width: 100%; height: 45px; font-family: Helvetica, sans-serif; }
			.pageHeader img{ vertical-align:middle;  }
			.headerText { color: #000; margin: 0px 0px 10px 20px; font-family: Helvetica, sans-serif; }
			.pagingText { color: #000; margin: 0px 0px 10px 20px; text-align: right; font-family: Helvetica, sans-serif;}
			-->
		</style>
	</HEAD>
	<BODY>
		<div class='pageHeader'>
<?php
			if(file_exists($this->request->getThemeDirectoryPath().'/graphics/CAlogo.gif')){
				print '<img src="'.$this->request->getThemeDirectoryPath().'/graphics/CAlogo.gif"/>';
 			}
			print "<span class='headerText'>".caGetLocalizedDate(null, array('dateFormat' => 'delimited'))."</span>";
			print "<span class='headerText'>".(($vn_num_items == 1) ? _t('%1 item', $vn_num_items) : _t('%1 items', $vn_num_items))."</span>";
			print "<span class='headerText'>".mb_substr($vs_title, 0, 30).((mb_strlen($vs_title) > 30) ? '...' : '')."</span>";
?>
		</div>

	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<th class="displayHeader"><?php print _t("Media"); ?></th>
			<th class="displayHeader"><?php print _t("ID"); ?></th>
			<th class="displayHeader"><?php print _t("Title"); ?></th>
		</tr>
<?php
		$i = 0;
		foreach($va_items as $va_item){
			$vn_object_id = $va_item['object_id'];
			
			($i == 2) ? $i = 0 : "";
			print "<tr".(($i == 1) ? " class='odd'" : "").">";
			print "<td>".((file_exists(str_replace($this->request->config->get("site_host"), $this->request->config->get("ca_base_dir"), $va_item["representation_url_thumbnail"]))) ? $va_item["representation_tag_thumbnail"] : "")."</td>";
			print "<td>".$va_item["idno"]."</td>";
			print "<td>".$va_item["name"]."</td>";
?>	
			</tr>
<?php
			$i++;
		}
?>

	</table>
	</BODY>
</HTML>