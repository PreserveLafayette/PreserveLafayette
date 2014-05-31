<?php
/* ----------------------------------------------------------------------
 * plugins/vimeo/controllers/AuthController.php :
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

 	require_once(__CA_LIB_DIR__.'/core/Configuration.php');
 	include_once(__CA_APP_DIR__."/plugins/twilio/lib/twilio.php");

 	class AuthController extends ActionController {
 		# -------------------------------------------------------
 		protected $opo_config;		// plugin configuration file
 		# -------------------------------------------------------
 		#
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			$this->opo_config = Configuration::load(__CA_APP_DIR__.'/plugins/twilio/conf/twilio.conf');
 		}
 		# -------------------------------------------------------
 		public function Index() {
 			$to_twilio = new phpTwilio($this->opo_config->get('user_sid'), $this->opo_config->get('user_token'));

			// TODO: Find out if this token stuff is relevant, generally modify to match twilio auth!
 			// get stored request or access token if we have one
 			if(file_exists(__CA_APP_DIR__.'/tmp/twilio.token')){
 				$ta_token = unserialize(file_get_contents(__CA_APP_DIR__.'/tmp/twilio.token'));
 				$vb_had_stored_token = true;
 			} else { // if we don't, we need a fresh access token
 				$ta_token = $to_twilio->getRequestToken();
 				$ta_token['type'] = 'request';
 			}

			$this->view->setVar('authorize_link', $ts_authorize_link = $to_twilio->getAuthorizeUrl($ta_token['oauth_token'], 'delete'));
 			$this->view->setVar('token',$ta_token);
 			$this->view->setVar('had_stored_token', $tb_had_stored_token);

 			file_put_contents(__CA_APP_DIR__.'/tmp/twilio.token', serialize($ta_token));
 			
 			$this->render('auth_html.php');
 		}
 		# -------------------------------------------------------
 		public function verify() {
 			$to_twilio = new phpTwilio($this->opo_config->get('consumer_key'), $this->opo_config->get('consumer_secret'));

 			if(file_exists(__CA_APP_DIR__.'/tmp/twilio.token')){
 				$ta_token = unserialize(file_get_contents(__CA_APP_DIR__.'/tmp/twilio.token'));

 				// exchange request token for access token by verifying with the code from twilio
 				if($ta_token['type'] == 'request'){
 					$to_vimeo->setToken($ta_token['oauth_token'], $ta_token['oauth_token_secret']);
	 				$ts_verify_code = $this->request->getParameter('verify_code',pString);

	 				$ta_token = $to_vimeo->getAccessToken($ts_verify_code);
	 				$ta_token['type'] = 'access';

	 				file_put_contents(__CA_APP_DIR__.'/tmp/twilio.token', serialize($ta_token));	
 				}
 			}

 			$this->render('verify.php');
 		}
 		# -------------------------------------------------------
 	}
 ?>
