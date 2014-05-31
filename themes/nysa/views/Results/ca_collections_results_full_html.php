<?php
/* ----------------------------------------------------------------------
 * themes/default/views/ca_collections_full_html.php :
 * 		full search results
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010 Whirl-i-Gig
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
 
 	
$vo_result 				= $this->getVar('result');
$vn_items_per_page		= $this->getVar('current_items_per_page');

if($vo_result) {
	print '<div id="collectionResults">';
	
	$vn_item_count = 0;
	$va_tooltips = array();
	$t_list = new ca_lists();
	$vn_i = 0;
	while(($vn_i < $vn_items_per_page) && ($vo_result->nextHit())) {
		$vs_idno = $vo_result->get('ca_collections.idno');
		$vs_class = "";
		$vn_item_count++;
		if($vn_item_count == 2){
			$vs_class = "resultBg";
			$vn_item_count = 0;
		}
		
		$vn_collection_id = $vo_result->get('ca_collections.collection_id');
		
		
		$va_labels = $vo_result->getDisplayLabels($this->request);
		print "<div".(($vs_class) ? " class='$vs_class'" : "").">";
		print caNavLink($this->request, join($va_labels, "; "), '', 'Detail', 'Collection', 'Show', array('collection_id' => $vn_collection_id));
		if($vs_idno){
			print ", ".$vs_idno;
		}
		print "</div>\n";
		$vn_i++;
		
	}
	print "</div>\n";
}
?>