<?php
/* ----------------------------------------------------------------------
 * bundles/ca_object_representations.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2013 Whirl-i-Gig
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

	JavascriptLoadManager::register('sortableUI');

	$vs_id_prefix 		= $this->getVar('placement_code').$this->getVar('id_prefix');
	$t_instance 		= $this->getVar('t_instance');
	$t_item 			= $this->getVar('t_item');			// object representation
	$t_item_rel 		= $this->getVar('t_item_rel');
	$t_subject 			= $this->getVar('t_subject');		// object
	$vs_add_label 		= $this->getVar('add_label');
	$va_settings 		= $this->getVar('settings');

	$vb_read_only		=	(isset($va_settings['readonly']) && $va_settings['readonly']);
	$vb_batch			=	$this->getVar('batch');
	
	$vb_allow_fetching_from_urls = $this->request->getAppConfig()->get('allow_fetching_of_media_from_remote_urls');
	
	// generate list of inital form values; the bundle Javascript call will
	// use the template to generate the initial form
	$va_inital_values = array();
	$va_reps = $t_subject->getRepresentations(array('thumbnail', 'original'));
	$va_rep_type_list = $t_item->getTypeList();
	$va_errors = array();
	
	if (sizeof($va_reps)) {
		$o_type_config = Configuration::load($t_item->getAppConfig()->get('annotation_type_config'));
 		$va_annotation_type_mappings = $o_type_config->getAssoc('mappings');
 		
		foreach ($va_reps as $va_rep) {
			$vn_num_multifiles = $va_rep['num_multifiles'];
			$vs_extracted_metadata = caFormatMediaMetadata(caUnserializeForDatabase($va_rep['media_metadata']));
			$vs_md5 = isset($va_rep['info']['original']['MD5']) ? _t('MD5 signature').': '.$va_rep['info']['original']['MD5'] : '';

			$va_inital_values[$va_rep['representation_id']] = array(
				'status' => $va_rep['status'], 
				'access' => $va_rep['access'], 
				'is_primary' => ($va_rep['is_primary'] == 1) ? true : false, 
				'locale_id' => $va_rep['locale_id'], 
				'icon' => $va_rep['tags']['thumbnail'], 
				'mimetype' => $va_rep['info']['original']['PROPERTIES']['mimetype'], 
				'annotation_type' => isset($va_annotation_type_mappings[$va_rep['info']['original']['PROPERTIES']['mimetype']]) ? $va_annotation_type_mappings[$va_rep['info']['original']['PROPERTIES']['mimetype']] : null,
				'type' => $va_rep['info']['original']['PROPERTIES']['typename'], 
				'dimensions' => $va_rep['dimensions']['original'], 
				'filename' => $va_rep['info']['original_filename'],
				'num_multifiles' => ($vn_num_multifiles ? (($vn_num_multifiles == 1) ? _t('+ 1 additional preview') : _t('+ %1 additional previews', $vn_num_multifiles)) : ''),
				'metadata' => $vs_extracted_metadata,
				'md5' => $vs_md5,
				'type_id' => $va_rep['type_id'],
				'typename' => $va_rep_type_list[$va_rep['type_id']]['name_singular'],
				'fetched_from' => $va_rep['fetched_from'],
				'fetched_on' => date('c', $va_rep['fetched_on'])
			);
			
			if(is_array($va_action_errors = $this->request->getActionErrors('ca_object_representations', $va_rep['representation_id']))) {
				foreach($va_action_errors as $o_error) {
					$va_errors[$va_rep['representation_id']][] = array('errorDescription' => $o_error->getErrorDescription(), 'errorCode' => $o_error->getErrorNumber());
				}
			}
		}
	}
	
	$va_failed_inserts = array();
	foreach($this->request->getActionErrorSubSources('ca_object_representations') as $vs_error_subsource) {
		if (substr($vs_error_subsource, 0, 4) === 'new_') {
			$va_action_errors = $this->request->getActionErrors('ca_object_representations', $vs_error_subsource);
			foreach($va_action_errors as $o_error) {
				$va_failed_inserts[] = array('icon' => '', '_errors' => array(array('errorDescription' => $o_error->getErrorDescription(), 'errorCode' => $o_error->getErrorNumber())));
			}
		}
	}
	
	if ($vb_batch) {
		print "<div class='editorBatchModeControl'>"._t("In batch")." ".
			caHTMLSelect($vs_id_prefix.$t_item->tableNum()."_rel_batch_mode", array(
				_t("do not use") => "_disabled_", 
				_t('add to each item') => '_add_', 
				_t('replace values') => '_replace_',
				_t('remove all values') => '_delete_'
			), array('id' => $vs_id_prefix.$t_item->tableNum()."_rel_batch_mode_select"))."</div>\n";
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#<?php print $vs_id_prefix.$t_item->tableNum(); ?>_rel_batch_mode_select').change(function() {
				if ((jQuery(this).val() == '_disabled_') || (jQuery(this).val() == '_delete_')) {
					jQuery('#<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>').slideUp(250);
				} else {
					jQuery('#<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>').slideDown(250);
				}
			});
			jQuery('#<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>').hide();
		});
	</script>
<?php
	} else {
		print caEditorBundleShowHideControl($this->request, $vs_id_prefix.$t_item->tableNum().'_rel');
	}
?>
<div id="<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>" <?php print $vb_batch ? "class='editorBatchBundleContent'" : ''; ?>>
<?php
	//
	// The bundle template - used to generate each bundle in the form
	//
?>
	<textarea class='caItemTemplate' style='display: none;'>
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="labelInfo">
			<span class="formLabelError">{error}</span>
			<table class="objectRepresentationListItem">
				<tr >
					<td width="90px"><?php print $t_item->htmlFormElement('type_id', null, array('classname' => '{fieldNamePrefix}type_id_select objectRepresentationType', 'id' => "{fieldNamePrefix}type_id_{n}", 'name' => "{fieldNamePrefix}type_id_{n}", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations',  'hide_select_if_only_one_option' => true)); ?>
					<?php print $t_item_rel->htmlFormElement('is_primary', null, array('classname' => 'objectRepresentationType', 'id' => "{fieldNamePrefix}is_primary_{n}", 'name' => "{fieldNamePrefix}is_primary_{n}", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations')); ?></td>
					<td><?php print $t_item->htmlFormElement('status', null, array('classname' => 'objectRepresentationStatus', 'id' => "{fieldNamePrefix}status_{n}", 'name' => "{fieldNamePrefix}status_{n}", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations')); ?>
					<?php print $t_item->htmlFormElement('access', null, array('classname' => 'objectRepresentationStatus', 'id' => "{fieldNamePrefix}access_{n}", 'name' => "{fieldNamePrefix}access_{n}", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations')); ?></td>
					<td colspan="1" align="right" valign="bottom" width="300px" style="padding: 5px 20px 0px 0px;">
						<div style="width:300px; overflow:hidden; padding:0px; margin:0px;"><a title="{filename}" style="font-weight:bold; color:#000000;"><em>{filename}</em></a></div>{type} {dimensions} {num_multifiles}
<?php 
	if (!$vb_read_only) {
?>
						<div style="width:110px; ">
								<a href="#" onclick="jQuery('#{fieldNamePrefix}media_{n}').toggle(200); jQuery('#{fieldNamePrefix}media_show_update_{n}').hide(); return false;" id="{fieldNamePrefix}media_show_update_{n}"><div style="margin-top:2px; width:16px; float:left;"><?php print "<img src='".$this->request->getThemeUrlPath()."/graphics/icons/updatemedia.png' border='0' height='16px' width='16px'/>";?></div><?php print _t('Update media'); ?></a>
						</div>
						<div id="{fieldNamePrefix}media_{n}" style="display: none; width:210px; margin-top:5px; float:right; clear:right; text-align:left;">
							<div id="{fieldNamePrefix}media_upload_control_{n}">
								<?php print $t_item->htmlFormElement('media', null, array('name' => "{fieldNamePrefix}media_{n}", 'id' => "{fieldNamePrefix}media_{n}", "value" => "", 'no_tooltips' => false, 'tooltip_namespace' => 'bundle_ca_object_representations')); ?>
<?php
		if ($vb_allow_fetching_from_urls) {
?>
							
								<a href='#' onclick='jQuery("#{fieldNamePrefix}media_url_control_{n}").slideDown(200); jQuery("#{fieldNamePrefix}media_upload_control_{n}").slideUp(200); return false'><?php print _t('or fetch from a URL'); ?></a>
<?php
		}
?>
							</div>
<?php
		if ($vb_allow_fetching_from_urls) {
?>
							<div id="{fieldNamePrefix}media_url_control_{n}" style="display: none;">
								<?php print _t('Fetch from URL'); ?>: <textentry cols="25" rows="3" name="{fieldNamePrefix}media_url_{n}" id="{fieldNamePrefix}media_url_{n}"></textentry>
								<br/>
								<a href='#' onclick='jQuery("#{fieldNamePrefix}media_url_control_{n}").slideUp(200); jQuery("#{fieldNamePrefix}media_upload_control_{n}").slideDown(200); return false'><?php print _t('or upload a file'); ?></a>
							</div>
<?php
		}
?>
						</div>
<?php
	}
?>
					</td>
					<td rowspan="1" class="objectRepresentationListItemImage" >
						<div class="objectRepresentationListItemImageThumb"><a href="#" onclick="caMediaPanel.showPanel('<?php print urldecode(caNavUrl($this->request, 'editor/objects', 'ObjectEditor', 'GetRepresentationEditor', array('object_id' => $t_subject->getPrimaryKey(), 'representation_id' => '{n}'))); ?>'); return false;">{icon}</a></div>
						<div style="float:right; margin:5px 0px 0px 125px; position:absolute;">
<?php 
	if (!$vb_read_only) {
?>
							<div style="margin: 0 0 10px 0;"><a href="#" class="caDeleteItemButton"><?php print caNavIcon($this->request, __CA_NAV_BUTTON_DEL_BUNDLE__); ?></a></div>
<?php
	}
?>
							<div style="margin: 10px 0 0 0;"><?php print urldecode(caNavButton($this->request, __CA_NAV_BUTTON_DOWNLOAD__, 'Download', 'editor/objects', 'ObjectEditor', 'DownloadRepresentation', array('version' => 'original', 'representation_id' => "{n}", 'object_id' => $t_subject->getPrimaryKey(), 'download' => 1), array('id' => "{fieldNamePrefix}download_{n}"), array('no_background' => true, 'dont_show_content' => true))); ?></div>
						
							<div style="margin: 10px 0 0 0;"><?php print urldecode(caNavLink($this->request, caNavIcon($this->request, __CA_NAV_BUTTON_EDIT__), '', 'editor/object_representations', 'ObjectRepresentationEditor', 'Edit', array('representation_id' => "{n}"), array('id' => "{fieldNamePrefix}edit_{n}"))); ?></div>
							
							<div style="margin: 10px 0 0 0;" class="caAnnotationEditorLaunchButton annotationType{annotation_type}"><a href="#" onclick="caAnnotationEditor<?php print $vs_id_prefix; ?>.showPanel('<?php print urldecode(caNavUrl($this->request, 'editor/object_representations', 'ObjectRepresentationEditor', 'GetAnnotationEditor', array('representation_id' => '{n}'))); ?>'); return false;"><img src='<?php print $this->request->getThemeUrlPath()."/graphics/buttons/clock.png"; ?>' border='0' height='16px' width='16px'/></a></div>
						</div>
					</td>

				</tr>

				<tr>
					<td colspan="6">
						<div id="{fieldNamePrefix}media_metadata_container_{n}" >
							<a href="#" onclick="jQuery('#{fieldNamePrefix}media_metadata_{n}').slideToggle(300); return false;"><?php print "<div style='margin-top:5px; width:11px; float:left;'><img src='".$this->request->getThemeUrlPath()."/graphics/icons/downarrow.jpg' border='0' height='11px' width='11px'/></div>"?><?php print _t('Additional Information'); ?></a>
							<div style="text-align:left; display: none;" id="{fieldNamePrefix}media_metadata_{n}" class="editorObjectRepresentationMetadata">
<?php
	if ($va_rep['fetched_from']) {
?>
								<div><?php print _t("Fetched from URL %1 on %2", '<a href="{fetched_from}" target="_ext" title="{fetched_from}">{fetched_from}</a>', '{fetched_on}') ?></div>
<?php
	}
?>
								{md5}
								{metadata}
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
<?php
	print TooltipManager::getLoadHTML('bundle_ca_object_representations');
?>
	</textarea>
	
	<div class="bundleContainer">
		<div class="caItemList">
		
		</div>
<?php 
	if (!$vb_read_only) {
?>
		<div class='button labelInfo caAddItemButton'><a href='#'><?php print caNavIcon($this->request, __CA_NAV_BUTTON_ADD__); ?> <?php print $vs_add_label ? $vs_add_label : _t("Add representation")." &rsaquo;"; ?></a></div>
<?php
	}
?>
	</div>
</div>

<input type="hidden" id="<?php print $vs_id_prefix; ?>_ObjectRepresentationBundleList" name="<?php print $vs_id_prefix; ?>_ObjectRepresentationBundleList" value=""/>
<?php
	// order element
?>
			
<script type="text/javascript">
	var caAnnotationEditor<?php print $vs_id_prefix; ?>;
	jQuery(document).ready(function() {
		caUI.initBundle('#<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>', {
			fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
			templateValues: ['status', 'access', 'is_primary', 'media', 'locale_id', 'icon', 'type', 'dimensions', 'filename', 'num_multifiles', 'metadata', 'type_id', 'typename', 'fetched_from'],
			initialValues: <?php print json_encode($va_inital_values); ?>,
			errors: <?php print json_encode($va_errors); ?>,
			forceNewValues: <?php print json_encode($va_failed_inserts); ?>,
			itemID: '<?php print $vs_id_prefix; ?>Item_',
			templateClassName: 'caItemTemplate',
			itemListClassName: 'caItemList',
			itemClassName: 'labelInfo',
			addButtonClassName: 'caAddItemButton',
			deleteButtonClassName: 'caDeleteItemButton',
			showOnNewIDList: ['<?php print $vs_id_prefix; ?>_media_', '<?php print $vs_id_prefix; ?>_type_id_div_'],
			hideOnNewIDList: ['<?php print $vs_id_prefix; ?>_media_show_update_', '<?php print $vs_id_prefix; ?>_edit_','<?php print $vs_id_prefix; ?>_download_', '<?php print $vs_id_prefix; ?>_media_metadata_container_'],
			enableOnNewIDList: ['<?php print $vs_id_prefix; ?>_type_id_'],
			disableOnExistingIDList: ['<?php print $vs_id_prefix; ?>_type_id_'],
			showEmptyFormsOnLoad: 1,
			readonly: <?php print $vb_read_only ? "true" : "false"; ?>,
			isSortable: <?php print !$vb_read_only ? "true" : "false"; ?>,
			listSortOrderID: '<?php print $vs_id_prefix; ?>_ObjectRepresentationBundleList',
			defaultLocaleID: <?php print ca_locales::getDefaultCataloguingLocaleID(); ?>
		
		});
		if (caUI.initPanel) {
			caAnnotationEditor<?php print $vs_id_prefix; ?> = caUI.initPanel({ 
				panelID: "caAnnotationEditor<?php print $vs_id_prefix; ?>",						/* DOM ID of the <div> enclosing the panel */
				panelContentID: "caAnnotationEditor<?php print $vs_id_prefix; ?>ContentArea",		/* DOM ID of the content area <div> in the panel */
				exposeBackgroundColor: "#000000",				
				exposeBackgroundOpacity: 0.7,					
				panelTransitionSpeed: 400,						
				closeButtonSelector: ".close",
				centerHorizontal: true,
				onOpenCallback: function() {
					jQuery("#topNavContainer").hide(250);
				},
				onCloseCallback: function() {
					jQuery("#topNavContainer").show(250);
				}
			});
		}
		
		jQuery("body").append('<div id="caAnnotationEditor<?php print $vs_id_prefix; ?>" class="caAnnotationEditorPanel"><div id="caAnnotationEditor<?php print $vs_id_prefix; ?>ContentArea" class="caAnnotationEditorPanelContentArea"></div></div>');
	
		// Hide annotation editor links for non-timebased media
		jQuery(".caAnnotationEditorLaunchButton").hide();
		jQuery(".annotationTypeTimeBasedAudio, .annotationTypeTimeBasedAudio").show();
	});
</script>
