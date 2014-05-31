<?php
 	require_once(__CA_MODELS_DIR__."/ca_objects.php");
	require_once(__CA_MODELS_DIR__."/ca_sets.php");
 	require_once(__CA_APP_DIR__.'/helpers/accessHelpers.php');
 	
 	$t_object = new ca_objects();
 	$t_featured_obj = new ca_sets();
 			
				
	# --- load the featured items set - set name assigned in app.conf
	$t_featured_obj->load(array('set_code' => 'featured_object'));
	# --- Enforce access control on set
	if((sizeof($va_access_values) == 0) || (sizeof($va_access_values) && in_array($t_featured_obj->get("access"), $va_access_values))){
		$vn_featured_set_id = $t_featured_obj->get("set_id");
		$va_featured_ids = array_keys(is_array($va_tmp = $t_featured_obj->getItemRowIDs(array('checkAccess' => $va_access_values, 'limit' => 1))) ? $va_tmp : array());	// These are the object ids in the set
	}
	if(is_array($va_featured_ids) && (sizeof($va_featured_ids) > 0)){
		$t_object = new ca_objects($va_featured_ids[0]);
		$va_rep = $t_object->getPrimaryRepresentation(array('widepreview'), null, array('return_with_access' => $va_access_values));
		$feat_obj_content_id = $va_featured_ids[0];
		$feat_obj_content_widepreview = $va_rep["tags"]["widepreview"];
		$vs_featured_content_label = $t_object->getLabelForDisplay();
	}
	
# --- Recently Added

# --- get the 12 most recently added objects to display
		$va_recently_added_items = $t_object->getRecentlyAddedItems(12, array('checkAccess' => $va_access_values, 'hasRepresentations' => 1));
		$va_labels = $t_object->getPreferredDisplayLabelsForIDs(array_keys($va_recently_added_items));
		$va_media = $t_object->getPrimaryMediaForIDs(array_keys($va_recently_added_items), array('small', 'thumbnail', 'preview', 'widepreview'), array("checkAccess" => $va_access_values));
		foreach($va_recently_added_items as $vn_object_id => $va_object_info){
			$va_object_info['title'] = $va_labels[$vn_object_id];
			$va_object_info['media'] = $va_media[$vn_object_id];
			$va_recently_added_objects[$vn_object_id] = $va_object_info;
		}
		#$this->view->setVar('recently_added_objects', $va_recently_added_objects);
		
		if(is_array($va_recently_added_objects) && (sizeof($va_recently_added_objects) > 0)){
			$va_object_info = array_shift($va_recently_added_objects);
			$recently_added_id = $va_object_info['object_id'];
			
			$recently_added_widepreview = $va_media[$va_object_info['object_id']]["tags"]["widepreview"];
		} 		
	
?>
		<div id="splashBrowsePanel" class="browseSelectPanel" style="z-index:1000;">
			<a href="#" onclick="caUIBrowsePanel.hideBrowsePanel()" class="browseSelectPanelButton"></a>
			<div id="splashBrowsePanelContent">
			
			</div>
		</div>
		<script type="text/javascript">
			var caUIBrowsePanel = caUI.initBrowsePanel({ facetUrl: '<?php print caNavUrl($this->request, '', 'Browse', 'getFacet'); ?>'});
		</script><pre>
<?php
		$t_featured = new ca_objects($this->getVar("featured_content_id"));
		$t_info = $t_featured->getPrimaryRepresentation(array('mediumlarge'), null, array('return_with_access' => $va_access_values));
		$vn_image_height = $t_info['info']['mediumlarge']['HEIGHT'];
		$vn_padding_top_bottom = (450 - $vn_image_height)/2;
?></pre>
		<div id="hpFeatured">
			<table cellpadding="0" cellspacing="0"><tr><td valign="middle" align="center"><div>
				<img src='<?php print $this->request->getThemeUrlPath() ?>/graphics/ladyliberty.png' border='0'>
				<?php # print caNavLink($this->request, $this->getVar("featured_content_mediumlarge"), '', 'Detail', 'Object', 'Show', array('object_id' =>  $this->getVar("featured_content_id"))); ?>
			</div></td></tr></table>
			<div id="featuredLabel">
				<?php # print $this->getVar("featured_content_label"); ?>
			</div><!-- end featuredLabel -->
		<div id="hpText">
<?php
		print $this->render('Splash/splash_intro_text_html.php');
?> 

		</div>
		</div><!-- end hpfeatured -->			
			<div id="hpBrowse">
				<div class="quickBrowseTitle"><b><?php print _t("Quickly browse by"); ?>:</b></div>
				<div class="quickBrowseLinks" style="margin-top:10px;">
<?php
					$va_facets = $this->getVar('available_facets');
					foreach($va_facets as $vs_facet_name => $va_facet_info) {
?>
						<a href="#" style="white-space:nowrap;" onclick='caUIBrowsePanel.showBrowsePanel("<?php print $vs_facet_name; ?>")'><?php print ucwords($va_facet_info['label_plural']); ?></a>
<?php
					}
?>
				</div>

				<div class="hpRss"><?php print caNavLink($this->request, '<img src="'.$this->request->getThemeUrlPath(true).'/graphics/feed.gif" border="0" title="'._t('Get alerted to newly added items by RSS').'" width="14" height="14"/> '._t('Get alerted to newly added items by RSS'), 'caption', '', 'Feed', 'recentlyAdded'); ?></div>
			</div><!-- end hpBrowse-->

		<div id="quickLinkItems">
			<div class="quickLinkItem" >
				<div class="title"><?php print _t("Recently Added"); ?></div>
				<?php print caNavLink($this->request, $recently_added_widepreview, '', 'Detail', 'Object', 'Show', array('object_id' =>  $recently_added_id)); ?>
			</div>
			<div class="quickLinkItem">
				<div class="title"><?php print _t("Most Viewed"); ?></div>
				<?php print caNavLink($this->request, $this->getVar("most_viewed_widepreview"), '', 'Detail', 'Object', 'Show', array('object_id' =>  $this->getVar("most_viewed_id"))); ?>
			</div>
			<div class="quickLinkItem" style="margin-right:0px;">
				<div class="title"><?php print _t("Featured Object"); ?></div>
				<?php print caNavLink($this->request, $feat_obj_content_widepreview, '', 'Detail', 'Object', 'Show', array('object_id' =>  $feat_obj_content_id)); ?>
			</div>
		</div><!-- end quickLinkItems -->
			
		