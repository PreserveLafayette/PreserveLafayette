﻿			<div class="clearfix"></div>
		<!--end #main-->
		</div>
	<!-- end #pageArea -->
	</div>
	<div id="footer">
		<a href="http://roundabouttheatre.org" class="footer-logo "><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/sprite-logo.png" border="0" width="175"></a>
		<ul>
			<li class="first">
				<br/>
				Roundabout Theatre Company Archives are made possible <br/>
				by generous funding from the Leon Levy Foundation.
			</li>
			<li class="second">
				Roundabout Theatre Company Archivist <br/>
				(212) 719-9393 <br/>
				<a href="mailto:archives@roundabouttheatre.org">archives@roundabouttheatre.org</a>
			</li> 
			<li class="third">
				<br/>
				<br/>
				© 1996 - <?php echo date('Y'); ?> Roundabout Theatre Company
			</li>
		</ul>
		<?php /* ?>[<?php print $this->request->session->elapsedTime(4).'s'; ?>/<?php print caGetMemoryUsage(); ?>]<?php */ ?>
		<div class="clearfix"></div>
	<!-- end #footer -->
	</div>
<?php
print TooltipManager::getLoadHTML();
?>
	<div id="caMediaPanel"> 
		<div id="close"><a href="#" onclick="caMediaPanel.hidePanel(); return false;">&nbsp;&nbsp;&nbsp;</a></div>
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
				exposeBackgroundOpacity: 0.5,							/* opacity of background color masking out page content; 1.0 is opaque */
				panelTransitionSpeed: 200, 									/* time it takes the panel to fade in/out in milliseconds */
				allowMobileSafariZooming: true,
				mobileSafariViewportTagID: '_msafari_viewport'
				
			});
		}
	});
	</script>
	<!--Make IE behave with Cufon-->
	<!--[if IE]><script type="text/javascript">//Cufon.now();</script><![endif]-->
	<!--<div id="exposeMask"></div>-->
	</body>
</html>
