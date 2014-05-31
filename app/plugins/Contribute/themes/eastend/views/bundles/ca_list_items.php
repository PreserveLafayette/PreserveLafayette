<?php
/* ----------------------------------------------------------------------
 * bundles/ca_list_items.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
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
 
 	JavascriptLoadManager::register('hierBrowser');
 
	$vs_id_prefix 		= $this->getVar('placement_code').$this->getVar('id_prefix');
	$t_item 			= $this->getVar('t_item');				// list item
	$t_item_rel 		= $this->getVar('t_item_rel');
	$va_settings 		= $this->getVar('settings');
	$vs_add_label 		= $this->getVar('add_label');
	$va_rel_types		= $this->getVar('relationship_types');
	
	$va_initial_values	= $this->getVar('initialValues');
	
?>
<div id="<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>">
<?php
	//
	// Template to generate display for existing items
	//
?>
	<textarea class='caItemTemplate' style='display: none;'>
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="labelInfo roundedRel">
			<a href="<?php print urldecode(caEditorUrl($this->request, 'ca_list_items', '{item_id}')); ?>" class="caEditItemButton" id="<?php print $vs_id_prefix; ?>_edit_related_{n}">{{label}}</a>
			({{relationship_typename}})
			<input type="hidden" name="<?php print $vs_id_prefix; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_type_id{n}" value="{type_id}"/>
			<input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
			
			<a href="#" class="caDeleteItemButton"><?php print caNavIcon($this->request, __CA_NAV_BUTTON_DEL_BUNDLE__, null, null, array('graphicsPath' => $this->getVar('graphicsPath'))); ?></a>
			
			<div style="display: none;" class="itemName">{label}</div>
			<div style="display: none;" class="itemIdno">{idno_sort}</div>
		</div>
	</textarea>
<?php
	//
	// Template to generate controls for creating new relationship
	//
?>
	<textarea class='caNewItemTemplate' style='display: none;'>
		<div style="clear: both; width: 1px; height: 1px;"><!-- empty --></div>
		<div id="<?php print $vs_id_prefix; ?>Item_{n}" class="labelInfo">
<?php
	if (!(bool)$va_settings['useHierarchicalBrowser']) {
?>
			<table class="caListItem">
				<tr>
					<td><input type="text" size="60" name="<?php print $vs_id_prefix; ?>_autocomplete{n}" value="{{_display}}" id="<?php print $vs_id_prefix; ?>_autocomplete{n}" class="lookupBg"/></td>
					<td>
					<select name="<?php print $vs_id_prefix; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_type_id{n}" style="display: none;"></select>
					<input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
					</td>
					<td>
						<a href="#" class="caDeleteItemButton"><?php print caNavIcon($this->request, __CA_NAV_BUTTON_DEL_BUNDLE__, null, null, array('graphicsPath' => $this->getVar('graphicsPath'))); ?></a>
					</td>
				</tr>
			</table>
<?php
	} else {
		$vn_use_as_root_id = 'null';
		if (sizeof($va_settings['restrict_to_lists']) == 1) {
			$t_item = new ca_list_items();
			if ($t_item->load(array('list_id' => $va_settings['restrict_to_lists'][0], 'parent_id' => null))) {
				$vn_use_as_root_id = $t_item->getPrimaryKey();
			}
		} 
?>
			<div style="float: right;"><a href="#" class="caDeleteItemButton"><?php print caNavIcon($this->request, __CA_NAV_BUTTON_DEL_BUNDLE__, null, null, array('graphicsPath' => $this->getVar('graphicsPath'))); ?></a></div>
			<div style='width: 690px; height: <?php print $va_settings['hierarchicalBrowserHeight']; ?>;'>
				
				<div id='<?php print $vs_id_prefix; ?>_hierarchyBrowser{n}' style='width: 100%; height: 100%;' class='hierarchyBrowser'>
					<!-- Content for hierarchy browser is dynamically inserted here by ca.hierbrowser -->
				</div><!-- end hierarchyBrowser -->	</div>
				
			<div style="clear: both; width: 1px; height: 1px;"><!-- empty --></div>
			<div style="float: right;">
				<div class='hierarchyBrowserSearchBar'><?php print _t('Search'); ?>: <input type='text' id='<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}' class='hierarchyBrowserSearchBar' name='search' value='' size='40'/></div>
			</div>
			<div style="float: left;">
				<?php print _t('Type'); ?>: <select name="<?php print $vs_id_prefix; ?>_type_id{n}" id="<?php print $vs_id_prefix; ?>_type_id{n}" style="display: none;"></select>
				<input type="hidden" name="<?php print $vs_id_prefix; ?>_id{n}" id="<?php print $vs_id_prefix; ?>_id{n}" value="{id}"/>
			</div>	
			
			<script type='text/javascript'>
				jQuery(document).ready(function() { 
					var <?php print $vs_id_prefix; ?>oHierBrowser{n} = caUI.initHierBrowser('<?php print $vs_id_prefix; ?>_hierarchyBrowser{n}', {
						uiStyle: 'horizontal',
						levelDataUrl: '<?php print caNavUrl($this->request, 'lookup', 'ListItem', 'GetHierarchyLevel', array('noSymbols' => 1, 'voc' => 1, 'lists' => join(';', $va_settings['restrict_to_lists']))); ?>',
						initDataUrl: '<?php print caNavUrl($this->request, 'lookup', 'ListItem', 'GetHierarchyAncestorList'); ?>',
						
						selectOnLoad : true,
						browserWidth: "<?php print $va_settings['hierarchicalBrowserWidth']; ?>",
						
						dontAllowEditForFirstLevel: false,
						
						className: 'hierarchyBrowserLevel',
						classNameContainer: 'hierarchyBrowserContainer',
						
						editButtonIcon: '<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/buttons/arrow_grey_right.gif" border="0" title="Edit"/>',
						
						initItemID: <?php print (int)$this->request->session->getVar('ca_list_items_browse_last_id'); ?>,
						useAsRootID: <?php print $vn_use_as_root_id; ?>,
						indicatorUrl: '<?php print $this->request->getThemeUrlPath(); ?>/graphics/icons/indicator.gif',
						
						currentSelectionDisplayID: '<?php print $vs_id_prefix; ?>_browseCurrentSelectionText{n}',
						onSelection: function(item_id, parent_id, name, display, type_id) {
							caRelationBundle<?php print $vs_id_prefix; ?>.select('{n}', [0, item_id, type_id], display);
						}
					});
					
					jQuery('#<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}').autocomplete(
						'<?php print caNavUrl($this->request, 'lookup', 'ListItem', 'Get'); ?>', {minChars: 3, matchSubset: 1, matchContains: 1, delay: 800, extraParams: {noSymbols: 1}}
					);
					
					jQuery('#<?php print $vs_id_prefix; ?>_hierarchyBrowserSearch{n}').result(function(event, data, formatted) {
						<?php print $vs_id_prefix; ?>oHierBrowser{n}.setUpHierarchy(data[1]);	// jump browser to selected item
					});
				});
			</script>
<?php
	}
?>
		</div>
	</textarea>
	
	<div class="bundleContainer">
<?php
	if(sizeof($va_initial_values)) {
?>
		<div class="caItemListSortControlTrigger" id="<?php print $vs_id_prefix; ?>caItemListSortControlTrigger">
			<?php print _t('Sort by'); ?>
			<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/icons/bg.gif" alt='Sort'/>
		</div>
		<div class="caItemListSortControls" id="<?php print $vs_id_prefix; ?>caItemListSortControls">
			<a href='#' onclick="caRelationBundle<?php print $vs_id_prefix; ?>.sort('name'); return false;" class='caItemListSortControl'><?php print _t('name'); ?></a><br/>
			<a href='#' onclick="caRelationBundle<?php print $vs_id_prefix; ?>.sort('idno'); return false;" class='caItemListSortControl'><?php print _t('idno'); ?></a><br/>
			<a href='#' onclick="caRelationBundle<?php print $vs_id_prefix; ?>.sort('type'); return false;" class='caItemListSortControl'><?php print _t('type'); ?></a><br/>
			<a href='#' onclick="caRelationBundle<?php print $vs_id_prefix; ?>.sort('entry'); return false;" class='caItemListSortControl'><?php print _t('entry'); ?></a><br/>
		</div>
<?php
	}
?>

		<div class="caItemList">
		
		</div>
		<input type="hidden" name="<?php print $vs_id_prefix; ?>BundleList" id="<?php print $vs_id_prefix; ?>BundleList" value=""/>
		<div style="clear: both; width: 1px; height: 1px;"><!-- empty --></div>
		<div class='button labelInfo caAddItemButton'><a href='#'><?php print caNavIcon($this->request, __CA_NAV_BUTTON_ADD__, null, null, array('graphicsPath' => $this->getVar('graphicsPath'))); ?> <?php print $vs_add_label ? $vs_add_label : _t("Add relationship"); ?></a></div>
	</div>
</div>
			
<script type="text/javascript">
	var caRelationBundle<?php print $vs_id_prefix; ?>;
	jQuery(document).ready(function() {
		jQuery('#<?php print $vs_id_prefix; ?>caItemListSortControlTrigger').click(function() { jQuery('#<?php print $vs_id_prefix; ?>caItemListSortControls').slideToggle(200); });
		jQuery('#<?php print $vs_id_prefix; ?>caItemListSortControls a.caItemListSortControl').click(function() {jQuery('#<?php print $vs_id_prefix; ?>caItemListSortControls').slideUp(200); });
		
		caRelationBundle<?php print $vs_id_prefix; ?> = caUI.initRelationBundle('#<?php print $vs_id_prefix.$t_item->tableNum().'_rel'; ?>', {
			fieldNamePrefix: '<?php print $vs_id_prefix; ?>_',
			templateValues: ['_display', 'type_id', 'id'],
			initialValues: <?php print json_encode($va_initial_values); ?>,
			itemID: '<?php print $vs_id_prefix; ?>Item_',
			templateClassName: 'caNewItemTemplate',
			initialValueTemplateClassName: 'caItemTemplate',
			itemListClassName: 'caItemList',
			addButtonClassName: 'caAddItemButton',
			deleteButtonClassName: 'caDeleteItemButton',
			showEmptyFormsOnLoad: 1,
			relationshipTypes: <?php print json_encode($this->getVar('relationship_types_by_sub_type')); ?>,
			autocompleteUrl: '<?php print caNavUrl($this->request, 'lookup', 'Vocabulary', 'Get', array()); ?>',
			lists: <?php print json_encode($va_settings['restrict_to_lists']); ?>,
			types: <?php print json_encode($va_settings['restrict_to_types']); ?>,
			isSortable: true,
			listSortOrderID: '<?php print $vs_id_prefix; ?>BundleList',
			listSortItems: 'div.roundedRel'
		});
	});
</script>

<?php
	foreach($va_initial_values as $vn_id => $va_info) {
		TooltipManager::add("#{$vs_id_prefix}_edit_related_{$vn_id}", "<h2>".$va_info['_display']."</h2>");
	}
?>