<?php
/* ----------------------------------------------------------------------
 * themes/default/views/ajax_search_lookup_json.php :
 * 		JSON-formatted data for "searchlight" search suggestion drop-down display
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
 
	$va_data = array();
	$va_match_list = $this->getVar('matches');
	foreach($va_match_list as $vs_table => $va_type_groups) {
		foreach($va_type_groups as $vs_type => $va_matches) {
			$va_cur_data = array(
				'title' => $vs_type,
				'results' => array()
			);
			foreach($va_matches as $vn_id => $va_match) {
					if (!is_numeric($vn_id)) { continue; }
					$vs_match = $va_match['label'];
					$vs_match_stripped = preg_replace("![^A-Za-z\-\.0-9]+!", ' ', $va_match['label']);
					
					switch($vs_table) {
						# --------------------------------------------------------
						case 'ca_objects':
							if ($this->request->config->get('allow_detail_for_ca_objects')) {
								$vs_url = caNavUrl($this->request, 'Detail', 'Object', 'Show', array('object_id' => $vn_id));
							} else {
								$vs_url = caNavUrl($this->request, '', 'Search', 'Index', array('search' => $vs_match_stripped));
							}
							$va_cur_data['results'][] = array(
								$vs_url, $vs_match, ''
							);
							break;
						# --------------------------------------------------------
						case 'ca_entities':
							if ($this->request->config->get('allow_detail_for_ca_entities')) {
								$vs_url = caNavUrl($this->request, 'Detail', 'Entity', 'Show', array('entity_id' => $vn_id));
							} else {
								$vs_url = caNavUrl($this->request, '', 'Search', 'Index', array('search' => $vs_match_stripped));
							}
							$va_cur_data['results'][] = array(
								$vs_url, $vs_match, ''
							);
							break;
						# --------------------------------------------------------
						case 'ca_places':
							if ($this->request->config->get('allow_detail_for_ca_places')) {
								$vs_url = caNavUrl($this->request, 'Detail', 'Place', 'Show', array('place_id' => $vn_id));
							} else {
								$vs_url = caNavUrl($this->request, '', 'Search', 'Index', array('search' => $vs_match_stripped));
							}
							$va_cur_data['results'][] = array(
								$vs_url, $vs_match, ''
							);
							break;
						# --------------------------------------------------------
						case 'ca_occurrences':
							if ($this->request->config->get('allow_detail_for_ca_occurrences')) {
								$vs_url = caNavUrl($this->request, 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_id));
							} else {
								$vs_url = caNavUrl($this->request, '', 'Search', 'Index', array('search' => $vs_match_stripped));
							}
							$va_cur_data['results'][] = array(
								$vs_url, $vs_match, ''
							);
							break;
						# --------------------------------------------------------
						case 'ca_collections':
							if ($this->request->config->get('allow_detail_for_ca_collections')) {
								$vs_url = caNavUrl($this->request, 'Detail', 'Collection', 'Show', array('collection_id' => $vn_id));
							} else {
								$vs_url = caNavUrl($this->request, '', 'Search', 'Index', array('search' => $vs_match_stripped));
							}
							$va_cur_data['results'][] = array(
								$vs_url, $vs_match, ''
							);
							break;
						# --------------------------------------------------------
					}
			}
			
			$va_data[] = $va_cur_data;
		}
	}
	
	print json_encode($va_data);
?>