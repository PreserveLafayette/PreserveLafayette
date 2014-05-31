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
			table { border: 1px solid #999999; color: #000000; text-wrap: normal; font-size: 11px; margin:15px; font-family:"Avenir LT W01 35 Light", helvetica, arial; }
			table td { border: 1px solid #999999; color: #000000; text-wrap: normal; width: 125px; height: 120px; padding: 5px; font-size: 11px; font-family:"Avenir LT W01 35 Light", helvetica, arial;}
			tr.odd   { background-color: #f2f2f2; }
			.displayHeader { background-color: #EEEEEE; padding: 5px; border: 1px solid #999999; font-size: 12px; font-family:"Avenir LT W01 35 Light", helvetica, arial;}
			.pageHeader { background-color: #FFFFFF; margin: 0px 10px 20px 10px; padding: 0px 5px 20px 5px; width: 100%; height: 45px; font-family:"Avenir LT W01 35 Light", helvetica, arial;}
			.pageHeader img{ vertical-align:middle; }
			.headerText { color: #000; margin: 0px 0px 10px 20px; font-family:"Avenir LT W01 35 Light", helvetica, arial; }
			.pagingText { color: #000; margin: 0px 0px 10px 20px; text-align: right; font-family:"Avenir LT W01 35 Light", helvetica, arial; }
			.pageTitle {font-family:"Avenir LT W01 95 Black", helvetica, arial; font-size:41px; color:#71cde4; text-transform:uppercase; line-height:41px; font-weight:bold;}
		</style>
	</HEAD>
	<BODY>
		<div class='pageHeader'>
<?php
			print "<div class='pageTitle'>Parrish East End Stories</div>";
			print "<span class='headerText'>".caGetLocalizedDate(null, array('dateFormat' => 'delimited'))."</span>";
			print "<span class='headerText'>".(($vn_num_items == 1) ? _t('%1 item', $vn_num_items) : _t('%1 items', $vn_num_items))."</span>";
			print "<span class='headerText'>".mb_substr($vs_title, 0, 30).((mb_strlen($vs_title) > 30) ? '...' : '')."</span>";
			#print "<span class='pagingText'>"._t("page [%1]/[%2]", "[page_cu]", "[page_nb]")."</span>";
?>
		</div>

	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<th class="displayHeader"><?php print _t("Media"); ?></th>
			<th class="displayHeader"><?php print _t("ID"); ?></th>
			<th class="displayHeader"><?php print _t("Title"); ?></th>
			<th class="displayHeader"><?php print _t("Date"); ?></th>
			<th class="displayHeader"><?php print _t("Creator"); ?></th>
		</tr>
<?php
		$i = 0;
		$t_object = new ca_objects();
		foreach($va_items as $va_item){
			$vn_object_id = $va_item['object_id'];
			$t_object->load($vn_object_id);
			
			($i == 2) ? $i = 0 : "";
			print "<tr".(($i == 1) ? " class='odd'" : "").">";
			print "<td>".((file_exists(str_replace($this->request->config->get("site_host"), $this->request->config->get("ca_base_dir"), $va_item["representation_url_thumbnail"]))) ? $va_item["representation_tag_thumbnail"] : "")."</td>";
			print "<td>".$va_item["idno"]."</td>";
			print "<td>".$va_item["name"]."</td>";
			print "<td>";
			if($t_object->get("creation_date")){
				print $t_object->get("creation_date");
			}
			print "</td><td>";
			$va_creator = $t_object->get("ca_entities", array("restrictToRelationshipTypes" => array("maker", "artist"), "returnAsArray" => 1, 'checkAccess' => $va_access_values, 'sort' => 'surname'));
			if(sizeof($va_creator) > 0){	
				foreach($va_creator as $va_entity) {
					print $va_entity["label"]."<br/>";
				}
			}
			print "</td>";
?>	
			</tr>
<?php
			$i++;
		}
?>

	</table>
	</BODY>
</HTML>