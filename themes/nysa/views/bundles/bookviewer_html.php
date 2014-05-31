<?php
/* ----------------------------------------------------------------------
 * views/bundles/bookviewer_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011-2012 Whirl-i-Gig
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
 	
 	$vn_object_id 				= $this->getVar('object_id');
 	$vn_representation_id 		= $this->getVar('representation_id');
 	$vs_content_mode 			= $this->getVar('content_mode');
 	$va_sections	 			= $this->getVar('sections');
 	$vs_display_type 			= $this->getVar('display_type');
 	$va_display_options 		= $this->getVar('display_options');
 	if (($vn_initial_page = $this->getVar('initial_page')) <= 0) {
 		$vn_initial_page = 1;
 	}
?>
<?php print ($vs_display_type != 'media_overlay') ? '<div id="BookReaderContainer">' : ''; ?><div id="BookReader_<?php print $vn_object_id.'_'.$vn_representation_id.'_'.$vs_display_type; ?>">
    <noscript>
    	<p><?php print _t('The BookReader requires JavaScript to be enabled. Please check that your browser supports JavaScript and that it is enabled in the browser settings.'); ?></p>
    </noscript>
</div><?php print ($vs_display_type != 'media_overlay') ? '</div>' : ''; ?>
<script type="text/javascript">
	var caBookReader = caUI.initBookReader({
		containerID: 'BookReader_<?php print $vn_object_id.'_'.$vn_representation_id.'_'.$vs_display_type; ?>',
		docURL: '<?php print caNavUrl($this->request, 'Detail', 'Object', 'GetPageListAsJSON', array('object_id' => $vn_object_id, 'representation_id' => $vn_representation_id, 'content_mode' => $vs_content_mode, 'download' => 1)); ?>/data/documentData.json',
		page: <?php print $vn_initial_page; ?>,
		sidebar: <?php print ((sizeof($va_sections) > 0) && !isset($va_display_options['no_overlay'])) ? "true" : "false"; ?>,
		closeButton: '<?php print (!isset($va_display_options['no_overlay'])) ? '<img src="'.$this->request->getThemeUrlPath().'/graphics/buttons/x.png" alt="'._t('Close').'"/>' : ''; ?>',
		editButton: '<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/buttons/arrow_grey_right.gif" alt="<?php print _t('View'); ?>"/>',
<?php
	if (caObjectsDisplayDownloadLink($this->request)) {
?>
		downloadButton: '<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/buttons/download.png" alt="<?php print _t('Download'); ?>"/>'
<?php
	} else {
?>
		downloadButton: null
<?php
	}
?>
	});
</script>