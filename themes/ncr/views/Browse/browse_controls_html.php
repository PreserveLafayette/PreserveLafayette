<?php
/* ----------------------------------------------------------------------
 * themes/default/views/ca_objects_browse_html.php : 
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
 
	$va_facets 				= $this->getVar('available_facets');
	$va_facets_with_content	= $this->getVar('facets_with_content');
	$va_facet_info 			= $this->getVar('facet_info');
	$va_criteria 			= is_array($this->getVar('criteria')) ? $this->getVar('criteria') : array();
	$va_results 			= $this->getVar('result');
	
	$o_result_context		= $this->getVar('result_context');
	
	$vs_browse_target		= $this->getVar('target');
	$vs_browse_type_code = $o_result_context->getTypeRestriction($vn_has_changed);

	if (!$this->request->isAjax()) {
		$o_result_context = new ResultContext($this->request, $vs_browse_target, 'basic_browse');
		$vs_search = (ResultContext::getLastFind($this->request, $vs_browse_target) == 'basic_browse') ? '' : $o_result_context->getSearchExpression();
?>
		<div id="resultsSearchForm"><form name="browseSearch" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">
				<input type="text" name="search" value="<?php print ($vs_search) ? $vs_search : ''; ?>" autocomplete="off"/> <a href="#" name="searchButtonSubmit" onclick="document.forms.browseSearch.submit(); return false;">Search</a>
				<input type="hidden" name="target"  value="<?php print $vs_browse_target; ?>" />
				<input type="hidden" name="view"  value="full" />
		</form></div><!-- end resultsSearchForm -->
<?php

		switch($vs_browse_target){
			case "ca_objects":
				$vs_title_img = "<img src='".$this->request->getThemeUrlPath()."/graphics/ncr/t_artworks.gif' width='111' height='23' border='0'>";
			break;
			# -------------
			case "ca_occurrences":
				$va_type_list = $this->getVar('type_list');
				foreach($va_type_list as $vn_type_id => $va_type_info) {
					if ((int)$vn_type_id == (int)$vs_browse_type_code) {
						switch($va_type_info['idno']){
							case "exhibition":
								$vs_title_img = "<img src='".$this->request->getThemeUrlPath()."/graphics/ncr/t_exhibitions.gif' width='129' height='23' border='0'>";
							break;
							# -----------------
							case "bibliography":
								$vs_title_img = "<img src='".$this->request->getThemeUrlPath()."/graphics/ncr/t_bibliography.gif' width='146' height='23' border='0'>";
							break;
							# -----------------
						}					
						break;
					}
				}
			break;
			# -------------
		}
?>
		<div id="pageHeadingResultsPage"><?php print $vs_title_img; ?></div><!-- end pageHeading -->
<?php
	}
?>
	<div id="browse"><div id="resultBox"> 
<?php
	# --- we need to calculate the height of the box using the number of facets.
	$vn_boxHeight = 25 + ( sizeof($va_facets_with_content) * 18);
	
	if ($this->getVar('browse_selector')) {
?>
		<div class="browseTargetSelect"><?php print _t('Browse for').' '.$this->getVar('browse_selector'); ?></div>
		<div style="clear: both;"><!-- empty --></div>
<?php
	}
?>
		<div style="position: relative;"><div id='browseControls'>
<?php
			if (sizeof($va_facets)) { 
?>
				<div id="browseOptions"><span class='browseOptionsHeading'><?php print (sizeof($va_criteria) == 0) ? _t('Start browsing by') : _t('Continue browsing by'); ?>:</span>
<?php
					$vn_i = 1;
					$va_available_facets = $this->getVar('available_facets');
					foreach($va_available_facets as $vs_facet_code => $va_facet_info) {
						print "<a href='#' onclick='caUIBrowsePanel.showBrowsePanel(\"{$vs_facet_code}\");' class='facetLink'>".$va_facet_info['label_plural']."</a>";
						if($vn_i < sizeof($va_available_facets)){
							print ", ";
						}
						$vn_i++;
					}
?>
				</div><!-- end browseOptions -->
<?php
			}
			if (sizeof($va_criteria)) {
				$vn_x = 0;
				print "<div id='browseCriteria'><span class='criteriaHeading'>"._t("You browsed for: ")."</span>";
				foreach($va_criteria as $vs_facet_name => $va_row_ids) {
					$vn_x++;
					$vn_row_c = 0;
					foreach($va_row_ids as $vn_row_id => $vs_label) {
						$vs_facet_label = (isset($va_facet_info[$vs_facet_name]['label_singular'])) ? unicode_ucfirst($va_facet_info[$vs_facet_name]['label_singular']) : '???';

						print "{$vs_label}".caNavLink($this->request, 'x', 'close', '', 'Browse', 'removeCriteria', array('facet' => $vs_facet_name, 'id' => $vn_row_id))."\n";
						$vn_row_c++;
						if(($vn_x < sizeof($va_criteria)) || ($vn_row_c < sizeof($va_row_ids))){
							" "._t("&")." ";
						}
					}
					
				}
				print caNavLink($this->request, _t('start over'), 'startOver', '', 'Browse', 'clearCriteria', array());
				print "</div><!-- end browseCriteria -->\n";
				
			}
?>
</div><!-- end browseControls --></div><!-- end position:relative -->

<?php
	if (sizeof($va_criteria) == 0) {
		print $this->render('Browse/browse_start_html.php');
	} else {
		print $this->render('Results/paging_controls_html.php');
?>
		<a href='#' id='showOptions' onclick='$("#searchOptionsBox").slideDown(250); $("#showOptions").hide(); return false;'><?php print _t("Options"); ?> <img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/arrow_right_gray.gif" width="6" height="7" border="0"></a>
<?php		
		print $this->render('Search/search_controls_html.php');
		print "<div class='sectionBox'>";
		$vs_view = $this->getVar('current_view');
		if(in_array($vs_view, array_keys($this->getVar('result_views')))){
			print $this->render('Results/'.$vs_browse_target.'_results_'.$vs_view.'_html.php');
		}
		print "</div>";
	}
?>
	</div><!-- end resultbox --></div><!-- end browse -->
<?php
	if (!$this->request->isAjax()) {
?>
<div id="splashBrowsePanel" class="browseSelectPanel" style="z-index:1000;">
	<a href="#" onclick="caUIBrowsePanel.hideBrowsePanel()" class="browseSelectPanelButton">&nbsp;</a>
	<div id="splashBrowsePanelContent">
	
	</div>
</div>
<script type="text/javascript">
	var caUIBrowsePanel = caUI.initBrowsePanel({ 
		facetUrl: '<?php print caNavUrl($this->request, '', 'Browse', 'getFacet', array('target' => $vs_browse_target)); ?>',
		addCriteriaUrl: '<?php print caNavUrl($this->request, '', 'Browse', 'modifyCriteria', array('target' => $vs_browse_target)); ?>',
		singleFacetValues: <?php print json_encode($this->getVar('single_facet_values')); ?>
	});

	//
	// Handle browse header scrolling
	//
	jQuery(document).ready(function() {
		jQuery("div.scrollableBrowseController").scrollable(); 
	});
</script>
<?php
}
?>