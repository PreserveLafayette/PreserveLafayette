<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Detail/related_objects_grid.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2010 Whirl-i-Gig
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
 
	$qr_hits = $this->getVar('browse_results');
	$vn_c = 0;
	$vn_itemc = 0;
	$vn_numCols = 2;
	
	$va_access_values	= $this->getVar('access_values');
	$t_object = new ca_objects();
	$t_occ	= $this->getVar('t_item');
?>
	<table border="0" cellpadding="0px" cellspacing="0px" width="100%">
<?php
	while(($vn_itemc < 10) && ($qr_hits->nextHit())) {
		if ($vn_c == 0) { print "<tr>\n"; }
		$vn_object_id = $qr_hits->get('object_id');
		$va_object_type = $qr_hits->get('ca_objects.type_id', array('convertCodesToDisplayText' => true));
		$va_labels = $qr_hits->getDisplayLabels();
		$vs_caption = "";
		foreach($va_labels as $vs_label){
			$vs_caption .= $vs_label;
		}
		# --- get the height of the image so can calculate padding needed to center vertically
		$va_media_info = $qr_hits->getMediaTag('ca_object_representations.media', 'thumbnail', array('checkAccess' => $va_access_values));
		$va_height_info = $qr_hits->getMediaInfo('ca_object_representations.media', 'thumbnail', null, array('checkAccess' => $va_access_values));
		$vn_padding_top = ((120 - $va_height_info["HEIGHT"]) / 2);
		print "<td align='center' valign='top' class='searchResultTd'><div class='searchThumbBg searchThumbnail".$vn_object_id."' style='padding-top:".$vn_padding_top."px; padding-bottom:".$vn_padding_top."px'";
		print caNavLink($this->request, $qr_hits->getMediaTag('ca_object_representations.media', 'thumbnail', array('checkAccess' => $va_access_values)), '', 'Detail', 'Object', 'Show', array('object_id' => $qr_hits->get('ca_objects.object_id')));
		
		// Get thumbnail caption
		$this->setVar('object_id', $vn_object_id);
		$this->setVar('caption_title', $vs_caption);
		$this->setVar('caption_idno', $qr_hits->get('idno'));
		$this->setVar('object_type', $va_object_type);
		
		print "</div><div class='searchThumbCaption searchThumbnail".$vn_object_id."'>".$this->render('../Results/ca_objects_result_caption_html.php')."</div>\n</td>\n";

		
		// set view vars for tooltip
		$this->setVar('tooltip_representation', $qr_hits->getMediaTag('ca_object_representations.media', 'small', array('checkAccess' => $va_access_values)));
		$this->setVar('tooltip_title', $vs_caption);
		$this->setVar('tooltip_idno', $qr_hits->get('idno'));
		TooltipManager::add(
			".searchThumbnail{$vn_object_id}", $this->render('../Results/ca_objects_result_tooltip_html.php')
		);
		
		$vn_c++;
		$vn_itemc++;
		
		if ($vn_c == $vn_numCols) {
			print "</tr>\n";
			$vn_c = 0;
		}else{
			print "<td><!-- empty for spacing --></td>";
		}
	}
	if(($vn_c > 0) && ($vn_c < $vn_numCols)){
		while($vn_c < $vn_numCols){
			print "<td class='searchResultTd'><!-- empty --></td>\n";
			$vn_c++;
			if($vn_c < $vn_numCols){
				print "<td><!-- empty for spacing --></td>";
			}
		}
		print "</tr>\n";
	}
?>
	</table>
<?php

	print $this->render('paging_controls_html.php');
?>