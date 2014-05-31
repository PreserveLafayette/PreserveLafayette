<?php
/* ----------------------------------------------------------------------
 * views/editor/objects/ajax_object_representation_info_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009 Whirl-i-Gig
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
	$pn_object_id 				= $this->getVar('object_id');
	$t_rep 						= $this->getVar('t_object_representation');
	$vs_display_version 		= $this->getVar('rep_display_version');
	$va_display_options	 		= $this->getVar('rep_display_options');
	
	$va_versions 				= $this->getVar('versions');	
	$vn_representation_id 		= $t_rep->getPrimaryKey();
	$va_reps 					= $this->getVar('reps');
	# --- if there are more than one reps, make the viewer height shorter to accommodate the thumbnails at the bottom
	if(sizeof($va_reps) > 1){
		$va_display_options['viewer_height'] = $va_display_options['viewer_height'] - 84;
	}
?>
	<div id="caMediaOverlayContent">
<?php
		$va_display_options['id'] = '_caMediaOverlayMediaDisplay';
		print $t_rep->getMediaTag('media', $vs_display_version, $va_display_options);

		if($t_rep->get("image_credit_line")){
			# --- get width of image so caption matches
			print "<div class='objDetailImageCaption'>";
			if($t_rep->get("image_credit_line")){
				print "<i>".$t_rep->get("image_credit_line")."</i>";
			}
			print " &ndash; &copy; INFGM</div>";
		}
?>
	</div><!-- end caMediaOverlayContent -->
<?php
	# --- get all reps and if there are more than one to display thumbnail links
	if(sizeof($va_reps) > 1){
		print "<div id='caMediaOverlayThumbnails'>";
		# --- calculate with of div - we set the width so we can force side to side scrolling if there are a lot of reps
		print "<div style='width:".(74*(sizeof($va_reps)))."px;'>";
		foreach($va_reps as $va_rep_info){
			print "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetObjectMediaOverlay', array('object_id' => $pn_object_id, 'representation_id' => $va_rep_info['representation_id']))."\"); return false;' >".$va_rep_info["tags"]["icon"]."</a>";
		}
		print "</div></div><!-- caMediaOverlayThumbnails -->";
	}
?>