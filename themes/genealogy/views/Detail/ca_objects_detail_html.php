<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Detail/ca_objects_detail_html.php : 
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
	$t_object = 						$this->getVar('t_item');
	$vn_object_id = 					$t_object->get('object_id');
	$vs_title = 						$this->getVar('label');
	
	$va_access_values = 				$this->getVar('access_values');
	$t_rep = 							$this->getVar('t_primary_rep');
	$vn_num_reps = 						$t_object->getRepresentationCount(array("return_with_access" => $va_access_values));
	$vs_display_version =				$this->getVar('primary_rep_display_version');
	$va_display_options =				$this->getVar('primary_rep_display_options');

	JavascriptLoadManager::register('cycle');
?>	
	<div id="detailBody">
		<div id="pageNav">
<?php
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_objects', _t("Back"), ''))) {
				if ($this->getVar('previous_id')) {
					print caNavLink($this->request, "&lsaquo; "._t("Previous"), '', 'Detail', 'Object', 'Show', array('object_id' => $this->getVar('previous_id')), array('id' => 'previous'));
				}else{
					print "&lsaquo; "._t("Previous");
				}
				print "&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;";
				if ($this->getVar('next_id') > 0) {
					print caNavLink($this->request, _t("Next")." &rsaquo;", '', 'Detail', 'Object', 'Show', array('object_id' => $this->getVar('next_id')), array('id' => 'next'));
				}else{
					print _t("Next")." &rsaquo;";
				}
			}
?>
		</div><!-- end nav -->
		<h1><?php print unicode_ucfirst($this->getVar('typename')).': '.$vs_title; ?></h1>
		<div id="leftCol">
<?php


			# --- parent hierarchy info
			if($t_object->get('parent_id')){
				print "<div class='unit'><b>"._t("Part Of")."</b>: ".caNavLink($this->request, $t_object->get("ca_objects.parent.preferred_labels.name"), '', 'Detail', 'Object', 'Show', array('object_id' => $t_object->get('parent_id')))."</div>";
			}
			# --- attributes
			$va_attributes = $this->request->config->get('ca_objects_detail_display_attributes');
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($vs_value = $t_object->get("ca_objects.{$vs_attribute_code}")){
						print "<div class='unit'><b>".$t_object->getDisplayLabel("ca_objects.{$vs_attribute_code}").":</b> {$vs_value}</div><!-- end unit -->";
					}
				}
			}
			if($va_date = $t_object->get('ca_objects.dates', array('template' => "^dates_value (^dates_type)", 'delimiter' => "<br/>", 'convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>Date: </b>".$va_date."</div>";
			}
			if($va_type = $t_object->get('ca_objects.subtype_3', array('delimiter' => "<br/>", 'convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>Format: </b>".$va_type."</div>";
			}	
			if($va_subtype = $t_object->get('ca_objects.subtype_2', array('delimiter' => "<br/>", 'convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>Type: </b>".$va_subtype."</div>";
			}
			if($va_subtypes = $t_object->get('ca_objects.subtype_1', array('delimiter' => "<br/>", 'convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>Type: </b>".$va_subtypes."</div>";
			}
			if($va_language = $t_object->get('ca_objects.language', array('delimiter' => "<br/>", 'convertCodesToDisplayText' => true))){
				if ($va_language != '-') {
					print "<div class='unit'><b>Language: </b>".$va_language."</div>";
				}
			}			
			if($va_materials = $t_object->get('ca_objects.materials', array('delimiter' => "<br/>"))){
				print "<div class='unit'><b>Materials: </b>".$va_materials."</div>";
			}
			if($va_markings = $t_object->get('ca_objects.markings', array('delimiter' => "<br/>"))){
				print "<div class='unit'><b>Inscriptions/Markings: </b>".$va_markings."</div>";
			}	
			if($va_duration = $t_object->get('ca_objects.duration', array('delimiter' => "<br/>"))){
				print "<div class='unit'><b>Duration: </b>".$va_duration."</div>";
			}			
			# --- description
			if($this->request->config->get('ca_objects_description_attribute')){
				if($vs_description_text = $t_object->get("ca_objects.".$this->request->config->get('ca_objects_description_attribute'))){
					print "<div class='unit'><div id='description'><b>".$t_object->getDisplayLabel("ca_objects.".$this->request->config->get('ca_objects_description_attribute')).":</b> {$vs_description_text}</div></div><!-- end unit -->";				
?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#description').expander({
								slicePoint: 300,
								expandText: '<?php print _t('[more]'); ?>',
								userCollapse: false
							});
						});
					</script>
<?php
				}
			}
			if($va_transcription = $t_object->get('ca_objects.transcription', array('delimiter' => "<br/>"))){
				print "<div class='unit'><b>Transcription: </b>".$va_transcription."</div>";
			}
			if($va_verify = $t_object->get('ca_objects.verify', array('delimiter' => "<br/>", 'convertCodesToDisplayText' => true))){
				print "<div class='unit'><b>Needs Verification: </b>".$va_verify."</div>";
			}			
			# --- child hierarchy info
			$va_children = $t_object->get("ca_objects.children.preferred_labels", array('returnAsArray' => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_children) > 0){
				print "<div class='unit'><h2>"._t("Part%1", ((sizeof($va_children) > 1) ? "s" : ""))."</h2> ";
				$i = 0;
				foreach($va_children as $va_child){
					# only show the first 5 and have a more link
					if($i == 5){
						print "<div id='moreChildrenLink'><a href='#' onclick='$(\"#moreChildren\").slideDown(250); $(\"#moreChildrenLink\").hide(1); return false;'>["._t("More")."]</a></div><!-- end moreChildrenLink -->";
						print "<div id='moreChildren' style='display:none;'>";
					}
					print "<div>".caNavLink($this->request, $va_child['name'], '', 'Detail', 'Object', 'Show', array('object_id' => $va_child['object_id']))."</div>";
					$i++;
					if(($i >= 5) && ($i == sizeof($va_children))){
						print "</div><!-- end moreChildren -->";
					}
				}
				print "</div><!-- end unit -->";
			}
			# --- entities
			$va_entities = $t_object->get("ca_entities", array("returnAsArray" => 1, 'checkAccess' => $va_access_values, 'sort' => 'surname'));
			if(sizeof($va_entities) > 0){	
?>
				<div class="unit"><h2><?php print _t("Related")." ".((sizeof($va_entities) > 1) ? _t("Entities") : _t("Entity")); ?></h2>
<?php
				foreach($va_entities as $va_entity) {
					print "<div>".(($this->request->config->get('allow_detail_for_ca_entities')) ? caNavLink($this->request, $va_entity["label"], '', 'Detail', 'Entity', 'Show', array('entity_id' => $va_entity["entity_id"])) : $va_entity["label"])." (".$va_entity['relationship_typename'].")</div>";
				}
?>
				</div><!-- end unit -->
<?php
			}
			
			# --- occurrences
			$va_occurrences = $t_object->get("ca_occurrences", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			$va_sorted_occurrences = array();
			if(sizeof($va_occurrences) > 0){
				$t_occ = new ca_occurrences();
				$va_item_types = $t_occ->getTypeList();
				foreach($va_occurrences as $va_occurrence) {
					$t_occ->load($va_occurrence['occurrence_id']);
					$va_sorted_occurrences[$va_occurrence['item_type_id']][$va_occurrence['occurrence_id']] = $va_occurrence;
				}
				
				foreach($va_sorted_occurrences as $vn_occurrence_type_id => $va_occurrence_list) {
?>
						<div class="unit"><h2><?php print _t("Related")." ".$va_item_types[$vn_occurrence_type_id]['name_singular'].((sizeof($va_occurrence_list) > 1) ? "s" : ""); ?></h2>
<?php
					foreach($va_occurrence_list as $vn_rel_occurrence_id => $va_info) {
						print "<div>".(($this->request->config->get('allow_detail_for_ca_occurrences')) ? caNavLink($this->request, $va_info["label"], '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_rel_occurrence_id)) : $va_info["label"])." (".$va_info['relationship_typename'].")</div>";
					}
					print "</div><!-- end unit -->";
				}
			}
			# --- places
			$va_places = $t_object->get("ca_places", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			
			if(sizeof($va_places) > 0){
				print "<div class='unit'><h2>"._t("Related Place").((sizeof($va_places) > 1) ? "s" : "")."</h2>";
				foreach($va_places as $va_place_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_places')) ? caNavLink($this->request, $va_place_info['label'], '', 'Detail', 'Place', 'Show', array('place_id' => $va_place_info['place_id'])) : $va_place_info['label'])." (".$va_place_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- collections
			$va_collections = $t_object->get("ca_collections", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_collections) > 0){
				print "<div class='unit'><h2>"._t("Related Collection").((sizeof($va_collections) > 1) ? "s" : "")."</h2>";
				foreach($va_collections as $va_collection_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_collections')) ? caNavLink($this->request, $va_collection_info['label'], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection_info['collection_id'])) : $va_collection_info['label'])." (".$va_collection_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- lots
			$va_object_lots = $t_object->get("ca_object_lots", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_object_lots) > 0){
				print "<div class='unit'><h2>"._t("Related Lot").((sizeof($va_object_lots) > 1) ? "s" : "")."</h2>";
				foreach($va_object_lots as $va_object_lot_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_object_lots')) ? caNavLink($this->request, $va_object_lot_info['label'], '', 'Detail', 'ObjectLots', 'Show', array('lot_id' => $va_object_lot_info['lot_id'])) : $va_object_lot_info['label'])." (".$va_object_lot_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- vocabulary terms
			$va_terms = $t_object->get("ca_list_items", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_terms) > 0){
				print "<div class='unit'><h2>"._t("Subject").((sizeof($va_terms) > 1) ? "s" : "")."</h2>";
				foreach($va_terms as $va_term_info){
					print "<div>".caNavLink($this->request, $va_term_info['label'], '', '', 'Search', 'Index', array('search' => $va_term_info['label']))."</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- map
			if($this->request->config->get('ca_objects_map_attribute') && $t_object->get($this->request->config->get('ca_objects_map_attribute'))){
				$o_map = new GeographicMap(230, 200, 'map');
				$o_map->mapFrom($t_object, $this->request->config->get('ca_objects_map_attribute'));
				print "<div class='unit' style='margin-top:15px; margin-bottom:20px;'>".$o_map->render('HTML')."</div>";
			}			
			# --- output related object images as links
			$va_related_objects = $t_object->get("ca_objects", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if (sizeof($va_related_objects)) {
				print "<div class='unit'><h2>"._t("Related Objects")."</h2>";
				print "<div id='objDetailRelObjects'>";

				foreach($va_related_objects as $vn_rel_id => $va_info){
					$t_rel_object = new ca_objects($va_info["object_id"]);
					$va_reps = $t_rel_object->getPrimaryRepresentation(array('icon', 'small'), null, array('return_with_access' => $va_access_values));

					print "<div class='imageIcon icon".$va_info["object_id"]."' >";
					print caNavLink($this->request, $va_reps['tags']['icon'], '', 'Detail', 'Object', 'Show', array('object_id' => $va_info["object_id"]));
					
					// set view vars for tooltip
					$this->setVar('tooltip_representation', $va_reps['tags']['small']);
					$this->setVar('tooltip_title', $va_info['label']);
					$this->setVar('tooltip_idno', $va_info["idno"]);
					TooltipManager::add(
						".icon".$va_info["object_id"], $this->render('../Results/ca_objects_result_tooltip_html.php')
					);
					
					print "</div>";
					
				}
				print "</div></div><!-- end unit -->";
			}
?>
		</div><!-- end leftCol-->
		<div id="rightCol">
<?php
		if ($t_rep && $t_rep->getPrimaryKey()) {
?>
			<div id="objDetailImage">
			
<?php
			if ($vn_num_reps > 1) {
?>			
			<a href="#"><span id='prevImage'>&larr;</span></a>
<?php
			}
?>			
				<div id="slideshow" class="pics" style="margin-bottom:0px; width: 580px; float:left;">
<?php
				$item_rep = $t_object->getRepresentations(array('mediumlarge', 'tiny'), null, array('return_with_access' => $va_access_values));
				foreach ($item_rep as $i => $one_item) {
					$slideWidth = $one_item['info']['mediumlarge']['WIDTH'];
					$slideHeight = $one_item['info']['mediumlarge']['HEIGHT'];
					
					if ($vn_num_reps > 1) {
						$slidePadding = (580 - $slideWidth)/2;
					} else {
						$slidePadding = (630 - $slideWidth)/2;
					}
					print "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetRepresentationInfo', array('object_id' => $t_object->get("object_id"), 'representation_id' => $one_item['representation_id']))."\"); return false;' ><img src='".$one_item['urls']['mediumlarge']."' style='margin-left:".$slidePadding."px; margin-right:".$slidePadding."px;' rel='".$one_item['urls']['tiny']."' width='".$slideWidth."' height='".$slideHeight."'/></a>";
				}
?>
				</div>
<?php
			if ($vn_num_reps > 1) {
?>				
				<a href="#"><span id='nextImage'>&rarr;</span></a>
<?php
			}
?>				
			
				<div style="height:1px; width:100%; clear:both;"></div>

				
			<!--<div id='detailImageNav'></div>	-->	
				
<?php

			//if($va_display_options['no_overlay']){
			//	print $t_rep->getMediaTag('media', $vs_display_version, $this->getVar('primary_rep_display_options'));
			//}else{
			//	print "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetRepresentationInfo', array('object_id' => $t_object->get("object_id"), 'representation_id' => $t_rep->getPrimaryKey()))."\"); return false;' >".$t_rep->getMediaTag('media', $vs_display_version, $this->getVar('primary_rep_display_options'))."</a>";
				
 			//	$va_opts = array('display' => 'detail', 'object_id' => $vn_object_id, 'containerID' => 'cont');
			//	print "<div id='cont'><a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetRepresentationInfo', array('object_id' => $t_object->get("object_id"), 'representation_id' => $t_rep->getPrimaryKey()))."\"); return false;' >".$t_rep->getRepresentationViewerHTMLBundle($this->request, $va_opts)."</a></div>";
			//}
?>
			<div style='height:1px; width:100%; clear:both;'></div>
			</div><!-- end objDetailImage -->
			<div id="objDetailImageNav">
				<div style="float:right;">
					<!-- bookmark link BEGIN -->
<?php
					if((!$this->request->config->get('dont_allow_registration_and_login')) && $this->request->config->get('enable_bookmarks')){

						if($this->request->isLoggedIn()){
							print caNavLink($this->request, _t("+ Bookmark item"), '', '', 'Bookmarks', 'addBookmark', array('row_id' => $vn_object_id, 'tablename' => 'ca_objects'));
						}else{
							print caNavLink($this->request, _t("+ Bookmark item"), '', '', 'LoginReg', 'form', array('site_last_page' => 'Bookmarks', 'row_id' => $vn_object_id, 'tablename' => 'ca_objects'));
						}
					}
?>					
					<!-- bookmark link END -->
<?php
					if ((!$this->request->config->get('dont_allow_registration_and_login')) && (!$this->request->config->get('disable_my_collections'))) {
						if($this->request->isLoggedIn()){
							print caNavLink($this->request, _t("+ Add to Lightbox"), '', '', 'Sets', 'addItem', array('object_id' => $vn_object_id));
						}else{
							print caNavLink($this->request, _t("+ Add to Lightbox"), '', '', 'LoginReg', 'form', array('site_last_page' => 'Sets', 'object_id' => $vn_object_id));
						}
					}

					# --- output download link? 
					#if(caObjectsDisplayDownloadLink($this->request)){
					#	print caNavLink($this->request, _t("+ Download Media"), '', 'Detail', 'Object', 'DownloadRepresentation', array('representation_id' => $t_rep->getPrimaryKey(), "object_id" => $vn_object_id, "download" => 1, "version" => 'original'));
					#} 

					#print "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetRepresentationInfo', array('object_id' => $t_object->get("object_id"), 'representation_id' => $t_rep->getPrimaryKey()))."\"); return false;' >+ ".(($vn_num_reps > 1) ? _t("Zoom/more media") : _t("Zoom"))."</a>";
?>
				</div>			
			</div><!-- end objDetailImageNav -->
			<div id='detailThumbNav' style='clear:both; max-height:150px; overflow-y: scroll'></div>
<?php
		}
if (!$this->request->config->get('dont_allow_registration_and_login')) {
		# --- user data --- comments - ranking - tagging
?>			
		<div id="objUserData">
<?php
			if($this->getVar("ranking")){
?>
				<h2 id="ranking"><?php print _t("Average User Ranking"); ?> <img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/user_ranking_<?php print $this->getVar("ranking"); ?>.gif" width="104" height="15" border="0" style="margin-left: 20px;"></h2>
<?php
			}
			$va_tags = $this->getVar("tags_array");
			if(is_array($va_tags) && sizeof($va_tags) > 0){
				$va_tag_links = array();
				foreach($va_tags as $vs_tag){
					$va_tag_links[] = caNavLink($this->request, $vs_tag, '', '', 'Search', 'Index', array('search' => $vs_tag));
				}
?>
				<h2><?php print _t("Tags"); ?></h2>
				<div id="tags">
					<?php print implode($va_tag_links, ", "); ?>
				</div>
<?php
			}
			$va_comments = $this->getVar("comments");
			if(is_array($va_comments) && (sizeof($va_comments) > 0)){
?>
				<h2><div id="numComments">(<?php print sizeof($va_comments)." ".((sizeof($va_comments) > 1) ? _t("comments") : _t("comment")); ?>)</div><?php print _t("User Comments"); ?></h2>
<?php
				foreach($va_comments as $va_comment){
					if($va_comment["media1"]){
?>
						<div class="commentImage" id="commentMedia<?php print $va_comment["comment_id"]; ?>">
							<?php print $va_comment["media1"]["tiny"]["TAG"]; ?>							
						</div><!-- end commentImage -->
<?php
						TooltipManager::add(
							"#commentMedia".$va_comment["comment_id"], $va_comment["media1"]["large_preview"]["TAG"]
						);
					}
					if($va_comment["comment"]){
?>					
					<div class="comment">
						<?php print $va_comment["comment"]; ?>
					</div>
<?php
					}
?>					
					<div class="byLine">
						<?php print $va_comment["author"].", ".$va_comment["date"]; ?>
					</div>
<?php
				}
			}else{
				if(!$vs_tags && !$this->getVar("ranking")){
					$vs_login_message = _t("Login/register to be the first to rank, tag and comment on this object!");
				}
			}
			if($this->getVar("ranking") || (is_array($va_tags) && (sizeof($va_tags) > 0)) || (is_array($va_comments) && (sizeof($va_comments) > 0))){
?>
				<div class="divide" style="margin:12px 0px 10px 0px;"><!-- empty --></div>
<?php			
			}
		if($this->request->isLoggedIn()){
?>
			<h2><?php print _t("Add your comments"); ?></h2>
			<form method="post" action="<?php print caNavUrl($this->request, 'Detail', 'Object', 'saveCommentRanking', array('object_id' => $vn_object_id)); ?>" name="comment" enctype='multipart/form-data'>

				<div class="formLabel"><?php print _t("Media"); ?></div>
				<input type="file" name="media1">
				<div class="formLabel"><?php print _t("Comment"); ?></div>
				<textarea name="comment" rows="5"></textarea>
				<br><a href="#" name="commentSubmit" onclick="document.forms.comment.submit(); return false;"><?php print _t("Save"); ?></a>
			</form>
<?php
		}else{
			if (!$this->request->config->get('dont_allow_registration_and_login')) {
				print "<p>".caNavLink($this->request, (($vs_login_message) ? $vs_login_message : _t("Please login/register to rank, tag and comment on this item.")), "", "", "LoginReg", "form", array('site_last_page' => 'ObjectDetail', 'object_id' => $vn_object_id))."</p>";
			}
		}
?>		
		</div><!-- end objUserData-->
<?php
	}
?>
		</div><!-- end rightCol -->
	</div><!-- end detailBody -->
<?php
	require_once(__CA_LIB_DIR__.'/core/Parsers/COinS.php');
	
	print COinS::getTags($t_object);
	
	
?>
	<script type="text/javascript">
$(function() {

    $('#slideshow').cycle({
        fx:      'scrollHorz',
        timeout:  0,
        prev:    '#prevImage',
        next:    '#nextImage',
        pager:   '#detailThumbNav',
        
        pagerAnchorBuilder: function(i, slide) { 
        return '<a href="#"><img src="'
        + jQuery(slide).find('img').attr('rel')
        + '" /></a>'; 
    }
    });


    
});
</script>
