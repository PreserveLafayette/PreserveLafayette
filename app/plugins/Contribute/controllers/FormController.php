<?php
/* ----------------------------------------------------------------------
 * app/plugins/Contribute/controllers/FormController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011 Whirl-i-Gig
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
 
 	require_once(__CA_APP_DIR__.'/helpers/accessHelpers.php');
 	require_once(__CA_APP_DIR__."/plugins/Contribute/controllers/BaseEditorController.php");
 	require_once(__CA_MODELS_DIR__.'/ca_lists.php');
 
 	class FormController extends BaseEditorController {
 		# -------------------------------------------------------
 		
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			$this->ops_theme = __CA_THEME__;																	// get current theme
 			if(!is_dir(__CA_APP_DIR__.'/plugins/Contribute/themes/'.$this->ops_theme.'/views')) {		// if theme is not defined for this plugin, try to use "default" theme
 				$this->ops_theme = 'default';
 			}
 			
 			parent::__construct($po_request, $po_response, array(__CA_APP_DIR__.'/plugins/Contribute/themes/'.$this->ops_theme.'/views'));
 			
 			#$this->opo_plugin_config = Configuration::load($po_request->getAppConfig()->get('application_plugins').'/Contribute/conf/contribute.conf');
 			if(file_exists($po_request->getAppConfig()->get('application_plugins').'/Contribute/themes/'.$this->ops_theme.'/conf/contribute.conf')) {		// check if there is a config file in the theme first
 				$this->opo_plugin_config = Configuration::load($po_request->getAppConfig()->get('application_plugins').'/Contribute/themes/'.$this->ops_theme.'/conf/contribute.conf');
 			}else{
 				$this->opo_plugin_config = Configuration::load($po_request->getAppConfig()->get('application_plugins').'/Contribute/conf/contribute.conf');
 			}
 			if (!(bool)$this->opo_plugin_config->get('enabled')) { die(_t('Contribute plugin is not enabled')); }
 			
 			$vs_default_ui = $this->opo_plugin_config->get('default_ui');
 			$vs_requested_ui = $this->request->getParameter('ui', pString);
 			$va_ui_list = $this->opo_plugin_config->getAssoc('uis');
 			
 			$o_dm = Datamodel::load();
 			if (isset($va_ui_list[$vs_requested_ui]) && is_array($va_ui_list[$vs_requested_ui])) {
 				$this->opa_ui_info = $va_ui_list[$vs_requested_ui];
 				$this->ops_ui_code = $vs_requested_ui;
 			} else {
				if (isset($va_ui_list[$vs_default_ui]) && is_array($va_ui_list[$vs_default_ui])) {
					$this->opa_ui_info = $va_ui_list[$vs_default_ui];
				} else {
					$vs_default_ui = array_shift(array_keys($va_ui_list));
					$this->opa_ui_info = $va_ui_list[$vs_default_ui];
				}	
 				$this->ops_ui_code = $vs_default_ui;
			}
 			$this->ops_table_name = $this->opa_ui_info['table'];
 			
 			
 			if (!($this->opo_instance = $o_dm->getInstanceByTableName($this->ops_table_name, true))) { die(_t('Invalid table "%1" specified in Contribute plugin for form "%2"', $this->ops_table_name, $vs_default_ui)); }
 			
 			
 			$t_list = new ca_lists();
 			if(isset($this->opa_ui_info['type']) && $this->opa_ui_info['type']) {
 				$this->opa_ui_info['type_id'] = $t_list->getItemIDFromList($this->opo_instance->getTypeListCode(), $this->opa_ui_info['type']);
 			}
 			if(isset($this->opa_ui_info['representation_type']) && $this->opa_ui_info['representation_type']) {
 				$this->opa_ui_info['representation_type_id'] = $t_list->getItemIDFromList('object_representation_types', $this->opa_ui_info['representation_type']);
 			}
 			
 			ContributePlugin::setUIInfo($this->ops_ui_code, $this->opa_ui_info);
 			
 			JavascriptLoadManager::register('panel');
 			MetaTagManager::addLink('stylesheet', $po_request->getBaseUrlPath()."/app/plugins/Contribute/themes/".$this->ops_theme."/css/contribute.css",'text/css');
 			
 			$this->request->setParameter('dont_set_pawtucket2_last_page', '1');	// Setting this parameter ensures that the "last page" we (may) redirect to after submission isn't the Contribute form itself
 			
 			if ($this->opa_ui_info['require_login'] && !$po_request->isLoggedIn()) {
 				$this->notification->addNotification(_t("You must be logged in to use user contribution features."), __NOTIFICATION_TYPE_ERROR__);	
 				$this->response->setRedirect(caNavUrl($this->request, '', 'LoginReg', 'form'));
 				return;
 			}
 		}
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		public function Edit($pa_values=null, $pa_options=null) {
 			$this->view->setVar('plugin_config', $this->opo_plugin_config);
 			$this->view->setVar('spam_protection', (isset($this->opa_ui_info['spam_protection']) && $this->opa_ui_info['spam_protection']) ? 1 : 0);
 			$this->view->setVar('terms_and_conditions', (isset($this->opa_ui_info['terms_and_conditions']) && $this->opa_ui_info['terms_and_conditions']) ? 1 : 0);
 			
 			$this->request->setParameter('type_id', $pa_values['type_id'] = $this->_getTypeIDForUI($this->opo_instance));
 			
 			return parent::Edit($pa_values, array('ui' => $this->ops_ui_code));
 		}
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		public function Save($pa_options=null) {
 			$this->view->setVar('plugin_config', $this->opo_plugin_config);
 			
 			if ((isset($this->opa_ui_info['spam_protection']) && $this->opa_ui_info['spam_protection']) ? 1 : 0) {
 				// Check SPAM-preventing security question
 				if ($this->request->getParameter('security', pInteger) != $this->request->getParameter('sum', pInteger)) {
 					$this->notification->addNotification(_t("Please correctly answer the security question."), __NOTIFICATION_TYPE_ERROR__);
 					$this->Edit($_REQUEST);
 					return;
 				}
 			}
 			if ((isset($this->opa_ui_info['terms_and_conditions']) && $this->opa_ui_info['terms_and_conditions']) ? 1 : 0) {
 				// Check terms and conditions checkbox
 				if ($this->request->getParameter('iAgreeToTerms', pInteger) != 1) {
 					$this->notification->addNotification(_t("You must agree to the terms and conditions before proceeding."), __NOTIFICATION_TYPE_ERROR__);
 					$this->Edit($_REQUEST);
 					return;
 				}
 			}
 			
 			$this->request->setParameter('type_id', $this->_getTypeIDForUI($this->opo_instance));
 			return parent::Save(array('ui' => $this->ops_ui_code));
 		}
 		# -------------------------------------------------------
 		/**
 		 *
 		 */
 		protected function _initView($pa_options=null) {
 			$this->view->setVar('graphicsPath', $this->request->getBaseUrlPath()."/app/plugins/Contribute/themes/".$this->ops_theme."/graphics");
 			$this->view->setVar('viewPath', array_shift($this->view->getViewPaths()));
 			
 			return parent::_initView($pa_options);
 		}
 		# -------------------------------------------------------
 	}
 ?>