<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php print $this->request->config->get('html_page_title'); ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<?php print MetaTagManager::getHTML(); ?>
	
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/global.css" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/sets.css" rel="stylesheet" type="text/css" />
	<!--[if IE]>
    <link rel="stylesheet" type="text/css" href="<?php print $this->request->getThemeUrlPath(true); ?>/css/iestyles.css" />
	<![endif]-->
<?php
	print JavascriptLoadManager::getLoadHTML($this->request->getBaseUrlPath());
?>
	<script type="text/javascript">
		 jQuery(document).ready(function() {
			jQuery('#quickSearch').searchlight('<?php print $this->request->getBaseUrlPath(); ?>/index.php/Search/lookup', {showIcons: false, searchDelay: 100, minimumCharacters: 3, limitPerCategory: 3});
		});
	</script>
</head>
<body>
<?php
	if (!$this->request->isAjax()) {
?>
		

		<div id="pageArea"><div id="headerdivide">
				<div id="topBar">
<?php

			
			# Locale selection
			global $g_ui_locale;
			$vs_base_url = $this->request->getRequestUrl();
			$vs_base_url = ((substr($vs_base_url, 0, 1) == '/') ? $vs_base_url : '/'.$vs_base_url);
			$vs_base_url = str_replace("/lang/[A-Za-z_]+", "", $vs_base_url);
			
			if (is_array($va_ui_locales = $this->request->config->getList('ui_locales')) && (sizeof($va_ui_locales) > 1)) {
				print caFormTag($this->request, $this->request->getAction(), 'caLocaleSelectorForm', null, 'get', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true));
			
				$va_locale_options = array();
				foreach($va_ui_locales as $vs_locale) {
					$va_parts = explode('_', $vs_locale);
					$vs_lang_name = Zend_Locale::getTranslation(strtolower($va_parts[0]), 'language', strtolower($va_parts[0]));
					$va_locale_options[$vs_lang_name] = $vs_locale;
				}
				print caHTMLSelect('lang', $va_locale_options, array('id' => 'caLocaleSelectorSelect', 'onclick' => 'jQuery(document).attr("location", "'.caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(), array('lang' => '')).'" + jQuery("#caLocaleSelectorSelect").val());'), array('value' => $g_ui_locale));
				print "</form>\n";
			
			}
?>
		</div><!-- end topbar -->
			<div id="header">
<?php
				print caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/bamlogo.jpg' width='300' height='96' style='margin-left:-16px' border='0'>", "", "", "", "");
?>				
			</div><!-- end header -->
<?php
	// get last search ('basic_search' is the find type used by the SearchController)
	$o_result_context = new ResultContext($this->request, 'ca_objects', 'basic_search');
	$vs_search = $o_result_context->getSearchExpression();
?>
			<div id="nav">
				<div id="search" ><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get">
						<a href="#" name="searchButtonSubmit" onclick="document.forms.header_search.submit(); return false;"><img src="http://publicbam.whirl-i-gig.com/themes/bam/graphics/go.gif" width='35' height='16' border='0'></a>
						<input type="text" name="search" value="Search" onclick='jQuery("#quickSearch").select();' id="quickSearch"  autocomplete="off" size="100"/>
				</form></div>
<?php
				print caNavLink($this->request, _t("Browse Objects"), "", "", "Browse", "Index/target/ca_objects");
				print caNavLink($this->request, _t("Browse Productions"), "navborder", "", "Browse", "Index/target/ca_occurrences");
				print caNavLink($this->request, _t("About"), "navborder", "", "About", "index");
		if (!$this->request->config->get('dont_allow_registration_and_login')) {
			if($this->request->isLoggedIn()){
				print caNavLink($this->request, _t("View Cart"), "navborder", "", "Sets", "index");
				print caNavLink($this->request, _t("Logout"), "navborder", "", "LoginReg", "logout");
			}else{
				print caNavLink($this->request, _t("Login"), "navborder", "", "LoginReg", "form");
			}
		}
?>
			</div><!-- end nav --></div>
<?php
}
?>
