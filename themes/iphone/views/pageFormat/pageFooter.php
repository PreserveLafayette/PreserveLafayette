		<div style="clear:both;"><!-- empty --></div></div><!-- end pageArea -->
		<div id="footer">
<?php
			print $this->request->config->get('page_footer_text');
			print caNavLink($this->request, ' <img src="'.$this->request->getThemeUrlPath(true).'/graphics/feed.gif" border="0" title="'._t('Get alerted to newly added items by RSS').'" width="14" height="14" style="vertical-align:middle;"/>', '', '', 'Feed', 'recentlyAdded');
			#print "<br/>[".$this->request->session->elapsedTime(4)."s".caGetMemoryUsage()."]";
?>
		</div><!-- end footer --></div><!-- end pageWidth -->
<?php
print TooltipManager::getLoadHTML();
?>
	<div id="caMediaPanel"> 
		<div id="caMediaPanelContentArea">
		
		</div>
	</div>
	<script type="text/javascript">
	/*
		Set up the "caMediaPanel" panel that will be triggered by links in object detail
		Note that the actual <div>'s implementing the panel are located here in views/pageFormat/pageFooter.php
	*/
	var caMediaPanel;
	jQuery(document).ready(function() {
		if (caUI.initPanel) {
			caMediaPanel = caUI.initPanel({ 
				panelID: 'caMediaPanel',										/* DOM ID of the <div> enclosing the panel */
				panelContentID: 'caMediaPanelContentArea',		/* DOM ID of the content area <div> in the panel */
				exposeBackgroundColor: '#000000',						/* color (in hex notation) of background masking out page content; include the leading '#' in the color spec */
				exposeBackgroundOpacity: 0.7,							/* opacity of background color masking out page content; 1.0 is opaque */
				panelTransitionSpeed: 200, 									/* time it takes the panel to fade in/out in milliseconds */
				allowMobileSafariZooming: true,
				mobileSafariViewportTagID: '_msafari_viewport',
				mobileSafariInitialZoom: .43,
				mobileSafariMinZoom: .43,
				mobileSafariMaxZoom: 1.0,
				mobileSafariDeviceWidth: 740,
				mobileSafariDeviceHeight: 640,
				mobileSafariUserScaleable: true
			});
		}
	});
	</script>
	</body>
</html>
