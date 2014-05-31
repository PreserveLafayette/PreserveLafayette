<?php
	$va_period_data = $this->getVar("period_data");
	$q_objects = $va_period_data["objects"];
	$pn_entity_id = $this->getVar("entity_id");
	$ps_entity_name = $this->getVar("entity_name");
	$pn_occurrence_id = $this->getVar("occurrence_id");
	$ps_occurrence_name = $this->getVar("occurrence_name");
	$pn_item_id = $this->getVar("item_id");
	$ps_style_school_name = $this->getVar("style_school_name");
	
	print "<div id='chron_thumbScroll' class='chron_thumbScroll".$pn_entity_id.$pn_occurrence_id.$pn_item_id."'><div class='cols'>";
	if($pn_entity_id){
		print "<div id='chronoRefineCriteria'>";
		print "<div id='chronoRefineCriteriaText'>".$ps_entity_name."</div><!-- end chronoRefineCriteriaText -->";
		print "<div id='chronoRefineCriteriaLink'>".caNavLink($this->request, _t("Artist Info >"), "", "Detail", "Entity", "Show", array("entity_id" => $pn_entity_id))."</div><!-- end chronoRefineCriteriaLink -->";
		print "<div style='clear:both;'><!-- empty --></div></div><!-- end chronoRefineCriteria -->";
	}

	if($pn_occurrence_id){
		print "<div id='chronoRefineCriteria'>";
		print "<div id='chronoRefineCriteriaText'>".$ps_occurrence_name."</div><!-- end chronoRefineCriteriaText -->";
		print "<div id='chronoRefineCriteriaLink'>".caNavLink($this->request, _t("Occurrence Info >"), "", "Detail", "Occurrence", "Show", array("occurrence_id" => $pn_occurrence_id))."</div><!-- end chronoRefineCriteriaLink -->";
		print "<div style='clear:both;'><!-- empty --></div></div><!-- end chronoRefineCriteria -->";
	}

	if($pn_item_id){
		print "<div id='chronoRefineCriteria'>";
		print "<div id='chronoRefineCriteriaText'>".$ps_style_school_name."</div><!-- end chronoRefineCriteriaText -->";
		print "<div style='clear:both;'><!-- empty --></div></div><!-- end chronoRefineCriteria -->";
	}

	if($q_objects->numHits()){
		$vs_col1 = "";
		$vs_col2 = "";
		$vs_date = "";
		$vs_link = "";
		$vn_i = 0;
		while($q_objects->nextHit()){
			if($vs_image = $q_objects->getMediaTag('ca_object_representations.media', 'chronoGrid', array('checkAccess' => $va_access_values))){
				$vn_i++;
				$vs_link = "";
				$vs_vaga_class = "";
				if($q_objects->get("ca_objects.object_status") == 349){
					$vs_vaga_class = "vagaDisclaimer";
					$vn_vaga_disclaimer_output = 1;
				}
				$vs_link = caNavLink($this->request, $vs_image, $vs_vaga_class, "Detail", "Object", "Show", array("object_id" => $q_objects->get("object_id")))."<br/>";
				if($vs_date != $q_objects->get("ca_objects.creation_date")){
					$vs_date = $q_objects->get("ca_objects.creation_date");
					$vs_link = $vs_date."<br/>".$vs_link;
				}
				if($vn_i == 1){
					$vs_col1 .= $vs_link;
				}else{
					$vs_col2 .= $vs_link;
					$vn_i = 0;
				}
			}
		}
		print "<div class='col'>".$vs_col1."</div>";
		print "<div class='col'>".$vs_col2."</div>";
		if($vn_vaga_disclaimer_output){
			TooltipManager::add(
				".vagaDisclaimer", "<div style='width:250px;'>Reproduction of this image, including downloading, is prohibited without written authorization from VAGA, 350 Fifth Avenue, Suite 2820, New York, NY 10118. Tel: 212-736-6666; Fax: 212-736-6767; e-mail:info@vagarights.com; web: <a href='www.vagarights.com' target='_blank'>www.vagarights.com</a></div>"
			);
		}
	}
?>
	<div style='clear:both;'><!-- empty --></div></div></div><!-- end chron_thumbScroll -->
<?php
	if (!$this->request->isAjax()) {
?>
		<script type="text/javascript">
			// Initialize the plugin
			$(document).ready(function () {
				$("#chron_thumbScroll").smoothDivScroll({
					visibleHotSpotBackgrounds: "always",
					countOnlyClass: ".cols"
				});
			});
		</script>
<?php
	}
?>