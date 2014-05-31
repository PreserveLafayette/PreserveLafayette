<?php
/* ----------------------------------------------------------------------
 * app/plugins/simpleGallery/ajax_item_info_html.php : 
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
	$t_rep 						= $this->getVar('t_object_representation');
	$vs_display_version 		= $this->getVar('rep_display_version');
	$va_display_options	 		= $this->getVar('rep_display_options');
	
	$va_item 					= $this->getVar("item_info");
	$vn_set_id 					= $this->getVar("set_id");
	$vn_text_height 			= 100 + (450 - $va_item["image_height"]);
	
	$vs_media_tag 				= $t_rep->getMediaTag('media', $vs_display_version, $va_display_options);
?>
	<div id="galleryOverlayNextPrevious">
<?php
	if($va_item['previous_id']){
		print "<a href='#' onclick=\"jQuery('#setItem').load('".caNavUrl($this->request, 'simpleGallery', 'Show', 'setItemInfo', array('set_item_id' => $va_item['previous_id'], 'set_id' => $vn_set_id))."'); return false;\">"._t("&lsaquo; Previous")."</a>";
	}else{
		print _t("&lsaquo; Previous");
	}
	print "&nbsp;&nbsp;|&nbsp;&nbsp;";
	if($va_item['next_id']){
		print "<a href='#' onclick=\"jQuery('#setItem').load('".caNavUrl($this->request, 'simpleGallery', 'Show', 'setItemInfo', array('set_item_id' => $va_item['next_id'], 'set_id' => $vn_set_id))."'); return false;\">"._t("Next &rsaquo;")."</a>";
	}else{
		print _t("Next &rsaquo;");
	}
	print "</div>";	
	if($vs_media_tag){
		if ($va_display_options['no_overlay']) {
			print "<div id='galleryOverlayImage' style='width:".((int)$va_display_options['viewer_width'])."px;'>".$vs_media_tag."</div>";
		} else {
			print "<div id='galleryOverlayImage' style='width:".((int)$va_display_options['viewer_width'])."px;'>".caNavLink($this->request, $vs_media_tag, '', 'Detail', 'Object', 'Show', array('object_id' => $va_item['row_id']))."</div>";
		}
	}
	if($va_item['object_label']){
		print "<div id='galleryOverlayImageCaption'>".caNavLink($this->request, $va_item['object_label'], '', 'Detail', 'Object', 'Show', array('object_id' => $va_item['row_id']))."</div>";
	}
	if($va_item['label'] || $va_item['description']){
?> 	
		<div class="galleryOverlayContent">
<?php
			if($va_item['label']){
				print "<div><b>".$va_item['label']."</b></div>";
			}
			if($va_item['description']){
				print "<div id='itemDescription'>";
				print $va_item['description'];
				print "</div>";
				print "<div>".caNavLink($this->request, _t('See this object in the archive'), '', 'Detail', 'Object', 'Show', array('object_id' => $va_item['row_id']))." &rsaquo</div>";
?>
			
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#itemDescription').expander({
				slicePoint: 250,
				expandText: '<?php print _t('more &rsaquo;'); ?>',
				userCollapse: false
			});
		});
	</script>
<?php		
			}
?>
		</div>
<?php
	}
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#galleryOverlayImage').swipe({
		 		swipeLeft: function() { <?php print ($va_item['next_id']) ? "jQuery('#setItem').load('".caNavUrl($this->request, 'simpleGallery', 'Show', 'setItemInfo', array('set_item_id' => $va_item['next_id'], 'set_id' => $vn_set_id))."')" : ""; ?> },
		 		swipeRight: function() { <?php print ($va_item['previous_id']) ? "jQuery('#setItem').load('".caNavUrl($this->request, 'simpleGallery', 'Show', 'setItemInfo', array('set_item_id' => $va_item['previous_id'], 'set_id' => $vn_set_id))."')" : ""; ?> },
			});
		});
	</script>