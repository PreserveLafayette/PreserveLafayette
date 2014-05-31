<?php
/* ----------------------------------------------------------------------
 * includes/AboutController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2010 Whirl-i-Gig
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
 
 	class AboutController extends ActionController {
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			if (($this->request->config->get('pawtucket_requires_login') && !($this->request->isLoggedIn()))||($this->request->config->get('show_bristol_only'))&&!($this->request->isLoggedIn())) {
                 $this->response->setRedirect(caNavUrl($this->request, "", "LoginReg", "form"));
            }
 		}
 		# -------------------------------------------------------
 		/**
 		 * Generic action handler - used for any action that is not implemented
 		 * in the controller
 		 */
 		public function __call($ps_name, $pa_arguments) {
 			$ps_name = preg_replace('![^A-Za-z0-9_\-]!', '', $ps_name);
 			return $this->render('About/'.$ps_name.'.php');
 		}
 		# -------------------------------------------------------
 	}
 ?>