<?php
/* ----------------------------------------------------------------------
 * themes/default/views/Results/search_secondary_results/ca_places_html.php :
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
 
	if ($this->request->config->get('do_secondary_search_for_ca_places')) {
		$qr_places = $this->getVar('secondary_search_ca_places');
		if (($vn_num_hits = $qr_places->numHits()) > 0) {
			$vn_num_hits_per_page 	= $this->getVar('secondaryItemsPerPage');
			$vn_page 				= $this->getVar('page_ca_places');
			if (!$this->request->isAjax()) {
?>
			<div class="searchSec" id="placesSecondaryResults">
				<h1><?php print _t('Places'); ?></h1>
				<div class="resultCount">
<?php
					print _t("Your search found %1 %2.", $qr_places->numHits(), ($qr_places->numHits() == 1) ? _t('result') : _t('results'));
?>
				</div><!-- end searchSecNav -->
				<div class="listItems">
<?php
			}
					$vn_c = 0;
					$vb_link_to_place_detail = (int)$this->request->config->get('allow_detail_for_ca_places') ? true : false;
					while($qr_places->nextHit()) {
						$vs_name = join('; ', $qr_places->getDisplayLabels($this->request));
						print "<div class='item'>";
						if ($vb_link_to_place_detail) {
							print caNavLink($this->request, $vs_name, '', 'Detail', 'Place', 'Show', array('place_id' => $qr_places->get("place_id")));
						} else {
							print caNavLink($this->request, $vs_name, '', '', 'Search', 'Index', array('search' => $vs_name));
						}
						print "</div>\n";
						$vn_c++;
						
						if ($vn_c >= $vn_num_hits_per_page) { break; }
					}
					if ($vn_page < (ceil($vn_num_hits/$vn_num_hits_per_page) - 1)) {
						print "<div id='morePlaceResults".$vn_page."'><div class='item more'><a href='#' onclick='jQuery(\"#morePlaceResults".$vn_page."\").load(\"".caNavUrl($this->request, '', 'Search', 'secondarySearch', array('spage' => $vn_page + 1, 'type' => 'ca_places'))."\"); return false;'>"._t("More Results")."</a></div></div>";
					}
			if (!$this->request->isAjax()) {
?>
				</div><!-- end listItems -->
			</div>
<?php
			}
		}
	}
?>