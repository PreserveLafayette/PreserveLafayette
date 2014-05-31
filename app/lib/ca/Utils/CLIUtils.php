<?php
/** ---------------------------------------------------------------------
 * app/lib/ca/Utils/CLIUtils.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013-2014 Whirl-i-Gig
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
 * @package CollectiveAccess
 * @subpackage Utils
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */
 
 /**
  *
  */

 	require_once(__CA_LIB_DIR__.'/core/Utils/CLIProgressBar.php');
 	require_once(__CA_APP_DIR__.'/helpers/CLIHelpers.php');
 	require_once(__CA_APP_DIR__.'/helpers/mediaPluginHelpers.php');
	require_once(__CA_LIB_DIR__."/core/Zend/Console/Getopt.php");
	require_once(__CA_MODELS_DIR__."/ca_metadata_elements.php");
	require_once(__CA_LIB_DIR__."/ca/MediaContentLocationIndexer.php");
 
	class CLIUtils {
		# -------------------------------------------------------
		/**
		 * Errors
		 */
		private static $errors = array();
		
		/**
		 * Process messages
		 */
		private static $messages = array();
		 
		/**
		 * ANSI foreground colors
		 */ 
		 private static $ansiForegroundColors = array(
			'black' => '0;30',
			'dark_gray' => '1;30',
			'red' => '0;31',
			'bold_red' => '1;31',
			'green' => '0;32',
			'bold_green' => '1;32',
			'brown' => '0;33',
			'yellow' => '1;33',
			'blue' => '0;34',
			'bold_blue' => '1;34',
			'purple' => '0;35',
			'bold_purple' => '1;35',
			'cyan' => '0;36',
			'bold_cyan' => '1;36',
			'white' => '1;37',
			'bold_gray' => '0;37',
		);

		/**
		 * ANSI background colors
		 */ 
		private static $ansiBackgroundColors = array(
			'black' => '40',
			'red' => '41',
			'magenta' => '45',
			'yellow' => '43',
			'green' => '42',
			'blue' => '44',
			'cyan' => '46',
			'light_gray' => '47',
		);
		# -------------------------------------------------------
		/**
		 * Determines if CLIUtils function should be presented as a command within caUtils
		 *
		 * @param string $ps_function_name The function to check
		 * @return bool True if the function is a caUtils command
		 */
		public static function isCommand($ps_function_name) {
			return (!in_array($ps_function_name, array(
				'isCommand', 'textWithColor', 'textWithBackgroundColor', 
				'clearErrors', 'numErrors', 'getErrors', 'addError',
				'clearMessages', 'numMessages', 'getMessages', 'addMessage'
			)));
		}
		# -------------------------------------------------------
		/**
		 *  Clear list of current messages
		 *
		 * @return bool Always returns true
		 */
		public static function clearMessages() {
			CLIUtils::$messages = array();
			return true;
		}
		# -------------------------------------------------------
		/**
		 *  Get count of current messages
		 *
		 * @return int Number of messages currently posted
		 */
		public static function numMessages() {
			return sizeof(CLIUtils::$messages);
		}
		# -------------------------------------------------------
		/**
		 *  Get list of posted messages
		 *
		 * @return array List of messages 
		 */
		public static function getMessages() {
			return CLIUtils::$messages;
		}
		# -------------------------------------------------------
		/**
		 *  Add message to message list
		 *
		 * @param string $ps_message Message to post
		 * @param array $pa_options Options are:
		 *		dontOutput = if set message is not output to screen. Default is false.
		 *		color = color to render message in. Default is none.
		 * @return bool Always returns true
		 */
		public static function addMessage($ps_message, $pa_options=null) {
			$pb_dont_output = caGetOption('dontOutput', $pa_options, false);
			$ps_color = caGetOption('color', $pa_options, null);
			if (!$pb_dont_output) {
				if ($ps_color && (strtolower($ps_color) !== 'none')) {
					print CLIUtils::textWithColor($ps_message, $ps_color)."\n";
				} else {
					print "{$ps_message}\n";
				}
			}
			array_push(CLIUtils::$messages, $ps_message);
			return true;
		}
		# -------------------------------------------------------
		/**
		 *  Clear list of current errors
		 *
		 * @return bool Always returns true
		 */
		public static function clearErrors() {
			CLIUtils::$errors = array();
			return true;
		}
		# -------------------------------------------------------
		/**
		 *  Get count of current errors
		 *
		 * @return int Number of errors currently posted
		 */
		public static function numErrors() {
			return sizeof(CLIUtils::$errors);
		}
		# -------------------------------------------------------
		/**
		 *  Get list of error messages
		 *
		 * @return array List of messages for current errors
		 */
		public static function getErrors() {
			return CLIUtils::$errors;
		}
		# -------------------------------------------------------
		/**
		 *  Add error to current error list
		 *
		 * @param string $ps_error Error message to post
		 * @param array $pa_options Options are:
		 *		dontOutput = if set error is not output to screen. Default is false.
		 * @return bool Always returns true
		 */
		public static function addError($ps_error, $pa_options=null) {
			if (!is_array($pa_options) || !isset($pa_options['dontOutput']) || !$pa_options['dontOutput']) {
				print CLIUtils::textWithColor($ps_error, "red")."\n";
			}
			array_push(CLIUtils::$errors, $ps_error);
			return true;
		}
		# -------------------------------------------------------
		/**
		 * Return text in ANSI color
		 *
		 * @param string $ps_string The string to output
		 * @param string $ps_color The color to output $ps_string in. Colors are defined in CLIUtils::ansiForegroundColors
		 * @return string The string with ANSI color codes. If $ps_color is invalid the original string will be returned without ANSI codes.
		 */
		public static function textWithColor($ps_string, $ps_color) {
			if (!isset(self::$ansiForegroundColors[$ps_color])) {
				return $ps_string;
			}
			return "\033[".self::$ansiForegroundColors[$ps_color]."m".$ps_string."\033[0m";
		}
		# -------------------------------------------------------
		/**
		 * Return text in ANSI color
		 *
		 * @param string $ps_string The string to output
		 * @param string $ps_color The background color to output $ps_string with. Colors are defined in CLIUtils::ansiBackgroundColors
		 * @return string The string with ANSI color codes. If $ps_color is invalid the original string will be returned without ANSI codes.
		 */
		public static function textWithBackgroundColor($ps_string, $ps_color) {
			if (!isset(self::$background[$color])) {
				return $ps_string;
			}

			return "\033[".self::$ansiBackgroundColors[$ps_color].'m'.$ps_string."\033[0m";
		}
		# -------------------------------------------------------
		# CLI utility implementations
		# -------------------------------------------------------
		/**
		 * Rebuild search indices
		 */
		public static function rebuild_search_index($po_opts=null) {
			require_once(__CA_LIB_DIR__."/core/Search/SearchIndexer.php");
			ini_set('memory_limit', '4000m');
			set_time_limit(24 * 60 * 60 * 7); /* maximum indexing time: 7 days :-) */
			
			$o_si = new SearchIndexer();
			
			$va_tables = null;
			if ($vs_tables = (string)$po_opts->getOption('tables')) {
				$va_tables = preg_split("![;,]+!", $vs_tables);
			}
			$o_si->reindex($va_tables, array('showProgress' => true, 'interactiveProgressDisplay' => true));
			
			return true;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function rebuild_search_indexParamList() {
			return array(
				"tables|t-s" => _t('Specific tables to reindex, separated by commas or semicolons. If omitted all tables will be reindexed.')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function rebuild_search_indexUtilityClass() {
			return _t('Search');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function rebuild_search_indexHelp() {
			return _t("CollectiveAccess relies upon indices when searching your data. Indices are simply summaries of your data designed to speed query processing. The precise form and characteristics of the indices used will vary with the type of search engine you are using. They may be stored on disk, in a database or on another server, but their purpose is always the same: to make searches execute faster.

\tFor search results to be accurate the database and indices must be in sync. CollectiveAccess simultaneously updates both the database and indicies as you add, edit and delete data, keeping database and indices in agreement. Occasionally things get out of sync, however. If the basic and advanced searches are consistently returning unexpected results you can use this tool to rebuild the indices from the database and bring things back into alignment.

\tNote that depending upon the size of your database rebuilding can take from a few minutes to several hours. During the rebuilding process the system will remain usable but search functions may return incomplete results. Browse functions, which do not rely upon indices, will not be affected.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function rebuild_search_indexShortHelp() {
			return _t("Rebuilds search indices. Use this if you suspect the indices are out of sync with the database.");
		}
		# -------------------------------------------------------
		/**
		 * Rebuild search indices
		 */
		public static function rebuild_sort_values() {
			$o_db = new Db();
	
			foreach(array(
				'ca_objects', 'ca_object_lots', 'ca_places', 'ca_entities',
				'ca_occurrences', 'ca_collections', 'ca_storage_locations',
				'ca_object_representations', 'ca_representation_annotations',
				'ca_list_items'
			) as $vs_table) {
				require_once(__CA_MODELS_DIR__."/{$vs_table}.php");
				$t_table = new $vs_table;
				$vs_pk = $t_table->primaryKey();
				$qr_res = $o_db->query("SELECT {$vs_pk} FROM {$vs_table}");
		
				if ($vs_label_table_name = $t_table->getLabelTableName()) {
					require_once(__CA_MODELS_DIR__."/".$vs_label_table_name.".php");
					$t_label = new $vs_label_table_name;
					$vs_label_pk = $t_label->primaryKey();
					$qr_labels = $o_db->query("SELECT {$vs_label_pk} FROM {$vs_label_table_name}");
			
					print CLIProgressBar::start($qr_labels->numRows(), _t('Processing %1', $t_label->getProperty('NAME_PLURAL')));
					while($qr_labels->nextRow()) {
						$vn_label_pk_val = $qr_labels->get($vs_label_pk);
						print CLIProgressBar::next();
						if ($t_label->load($vn_label_pk_val)) {
							$t_table->logChanges(false);
							$t_label->setMode(ACCESS_WRITE);
							$t_label->update();
						}
					}
					print CLIProgressBar::finish();
				}
		
				print CLIProgressBar::start($qr_res->numRows(), _t('Processing %1 identifiers', $t_table->getProperty('NAME_SINGULAR')));
				while($qr_res->nextRow()) {
					$vn_pk_val = $qr_res->get($vs_pk);
					print CLIProgressBar::next();
					if ($t_table->load($vn_pk_val)) {
						$t_table->logChanges(false);
						$t_table->setMode(ACCESS_WRITE);
						$t_table->update();
					}
				}
				print CLIProgressBar::finish();
			}
			return trie;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function rebuild_sort_valuesParamList() {
			return array();
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function rebuild_sort_valuesUtilityClass() {
			return _t('Maintenance');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function rebuild_sort_valuesHelp() {
			return _t("CollectiveAccess relies upon sort values when sorting values that should not sort alphabetically, such as titles with articles (eg. The Man Who Fell to Earth should sort as Man Who Fell to Earth, The) and alphanumeric identifiers (eg. 2011.001 and 2011.2 should sort next to each other with leading zeros in the first ignored).

\tSort values are derived from corresponding values in your database. The internal format of sort values can vary between versions of CollectiveAccess causing erroneous sorting behavior after an upgrade. If you notice values such as titles and identifiers are sorting incorrectly, you may need to reload sort values from your data.

\tNote that depending upon the size of your database reloading sort values can take from a few minutes to an hour or more. During the reloading process the system will remain usable but search and browse functions may return incorrectly sorted results.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function rebuild_sort_valuesShortHelp() {
			return _t("Rebuilds values use to sort by title, name and identifier.");
		}
		
		# -------------------------------------------------------
		/**
		 * Remove media present in media directories but not referenced in database (aka. orphan media)
		 */
		public static function remove_unused_media($po_opts=null) {
			require_once(__CA_LIB_DIR__."/core/Db.php");
			require_once(__CA_MODELS_DIR__."/ca_object_representations.php");

			$vb_delete_opt = (bool)$po_opts->getOption('delete');
			$o_db = new Db();
	
			$t_rep = new ca_object_representations();
			$t_rep->setMode(ACCESS_WRITE);
	
			$qr_reps = $o_db->query("SELECT * FROM ca_object_representations");
			print CLIProgressBar::start($qr_reps->numRows(), _t('Loading valid file paths from database'))."\n";
	
			$va_paths = array();
			while($qr_reps->nextRow()) {
				print CLIProgressBar::next();
				$va_versions = $qr_reps->getMediaVersions('media');
				if (!is_array($va_versions)) { continue; }
				foreach($va_versions as $vs_version) {
					$va_paths[$qr_reps->getMediaPath('media', $vs_version)] = true;
				}
			}
			print CLIProgressBar::finish();
	
			print CLIProgressBar::start(1, _t('Reading file list'));
			$va_contents = caGetDirectoryContentsAsList(__CA_BASE_DIR__.'/media', true, false);
			print CLIProgressBar::next();
			print CLIProgressBar::finish();
			
			$vn_delete_count = 0;
			
			print CLIProgressBar::start(sizeof($va_contents), _t('Finding unused files'));
			$va_report = array();
			foreach($va_contents as $vs_path) {
				print CLIProgressBar::next();
				if (!preg_match('!_ca_object_representation!', $vs_path)) { continue; } // skip non object representation files
				if (!$va_paths[$vs_path]) { 
					$vn_delete_count++;
					if ($vb_delete_opt) {
						unlink($vs_path);
					}
					$va_report[] = $vs_path;
				}
			}
			print CLIProgressBar::finish()."\n";
			
			CLIUtils::addMessage(_t('There are %1 files total', sizeof($va_contents)));
			
			$vs_percent = sprintf("%2.1f", ($vn_delete_count/sizeof($va_contents)) * 100)."%";
			
			if ($vn_delete_count == 1) {
				CLIUtils::addMessage($vb_delete_opt ? _t("%1 file (%2) was deleted", $vn_delete_count, $vs_percent) : _t("%1 file (%2) is unused", $vn_delete_count, $vs_percent));
			} else {
				CLIUtils::addMessage($vb_delete_opt ?  _t("%1 files (%2) were deleted", $vn_delete_count, $vs_percent) : _t("%1 files (%2) are unused", $vn_delete_count, $vs_percent));
			}
			return true;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function remove_unused_mediaParamList() {
			return array(
				"delete|d" => _t('Delete unused files. Default is false.')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function remove_unused_mediaUtilityClass() {
			return _t('Maintenance');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function remove_unused_mediaShortHelp() {
			return _t("Detects and, optionally, removes media present in the media directories but not referenced in the database.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function remove_unused_mediaHelp() {
			return _t("Help text to come");
		}
		# -------------------------------------------------------
		/**
		 * Export current system configuration as an XML installation profile
		 */
		public static function export_profile($po_opts=null) {
			require_once(__CA_LIB_DIR__."/ca/ConfigurationExporter.php");
	
			if(!class_exists("DOMDocument")){
				CLIUtils::addError(_t("The PHP DOM extension is required to export profiles"));
				return false;
			}

			$vs_output = $po_opts->getOption("output");
			$va_output = explode("/", $vs_output);
			array_pop($va_output);
			if ($vs_output && (!is_dir(join("/", $va_output)))) {
				CLIUtils::addError(_t("Cannot write profile to '%1'", $vs_output));
				return false;
			}
			
			$vs_profile = ConfigurationExporter::exportConfigurationAsXML($po_opts->getOption("name"), $po_opts->getOption("description"), $po_opts->getOption("base"), $po_opts->getOption("infoURL"));
			
			if ($vs_output) {
				file_put_contents($vs_output, $vs_profile);
			} else {
				print $vs_profile;
			}
			return true;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function export_profileParamList() {
			return array(
				"base|b-s" => _t('File name of profile to use as base profile. Omit if you do not want to use a base profile. (Optional)'),
				"name|n=s" => _t('Name of the profile, used for "profileName" element.'),
				"infoURL|u-s" => _t('URL pointing to more information about the profile. (Optional)'),
				"description|d-s" => _t('Description of the profile, used for "profileDescription" element. (Optional)'),
				"output|o-s" => _t('File to output profile to. If omitted profile is printed to standard output. (Optional)')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function export_profileUtilityClass() {
			return _t('Configuration');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function export_profileShortHelp() {
			return _t("Export current system configuration as an XML installation profile.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function export_profileHelp() {
			return _t("Help text to come.");
		}
		# -------------------------------------------------------
		/**
		 * Process queued tasks
		 */
		public static function process_task_queue($po_opts=null) {
			require_once(__CA_LIB_DIR__."/core/TaskQueue.php");
	
			$vo_tq = new TaskQueue();
			
			if (!$po_opts->getOption("quiet")) { CLIUtils::addMessage(_t("Processing queued tasks...")); }
			$vo_tq->processQueue();		// Process queued tasks
			
			if (!$po_opts->getOption("quiet")) { CLIUtils::addMessage(_t("Processing recurring tasks...")); }
			$vo_tq->runPeriodicTasks();	// Process recurring tasks implemented in plugins
			if (!$po_opts->getOption("quiet")) {  CLIUtils::addMessage(_t("Processing complete.")); }
			
			return true;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function process_task_queueParamList() {
			return array(
				"quiet|q" => _t("Run without outputting progress information.")
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function process_task_queueUtilityClass() {
			return _t('Cron');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function process_task_queueShortHelp() {
			return _t("Process queued tasks.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function process_task_queueHelp() {
			return _t("Help text to come.");
		}
		# -------------------------------------------------------
		/**
		 * Reprocess media
		 */
		public static function reprocess_media($po_opts=null) {
			require_once(__CA_LIB_DIR__."/core/Db.php");
			require_once(__CA_MODELS_DIR__."/ca_object_representations.php");
	
			$o_db = new Db();
	
			$t_rep = new ca_object_representations();
			$t_rep->setMode(ACCESS_WRITE);
	
			$va_mimetypes = ($vs_mimetypes = $po_opts->getOption("mimetypes")) ? explode(",", $vs_mimetypes) : array();
			$va_versions = ($vs_versions = $po_opts->getOption("versions")) ? explode(",", $vs_versions) : array();
			$va_kinds = ($vs_kinds = $po_opts->getOption("kinds")) ? explode(",", $vs_kinds) : array();
			
			if (!is_array($va_kinds) || !sizeof($va_kinds)) {
				$va_kinds = array('all');
			}
			$va_kinds = array_map('strtolower', $va_kinds);
			
			if (in_array('all', $va_kinds) || in_array('ca_object_representations', $va_kinds)) { 
				if (!($vn_start = (int)$po_opts->getOption('start_id'))) { $vn_start = null; }
				if (!($vn_end = (int)$po_opts->getOption('end_id'))) { $vn_end = null; }
			
			
				if ($vn_id = (int)$po_opts->getOption('id')) { 
					$vn_start = $vn_id; 
					$vn_end = $vn_id; 
				}
			
				$va_ids = array();
				if ($vs_ids = (string)$po_opts->getOption('ids')) { 
					if (sizeof($va_tmp = explode(",", $vs_ids))) {
						foreach($va_tmp as $vn_id) {
							if ((int)$vn_id > 0) {
								$va_ids[] = (int)$vn_id;
							}
						}
					}
				}
			
				$vs_sql_where = null;
				$va_params = array();
			
				if (sizeof($va_ids)) {
					$vs_sql_where = "WHERE representation_id IN (?)";
					$va_params[] = $va_ids;
				} else {
					if (
						(($vn_start > 0) && ($vn_end > 0) && ($vn_start <= $vn_end)) || (($vn_start > 0) && ($vn_end == null))
					) {
						$vs_sql_where = "WHERE representation_id >= ?";
						$va_params[] = $vn_start;
						if ($vn_end) {
							$vs_sql_where .= " AND representation_id <= ?";
							$va_params[] = $vn_end;
						}
					}
				}
	
				$qr_reps = $o_db->query("
					SELECT * 
					FROM ca_object_representations 
					{$vs_sql_where}
					ORDER BY representation_id
				", $va_params);
			
				print CLIProgressBar::start($qr_reps->numRows(), _t('Re-processing representation media'));
				while($qr_reps->nextRow()) {
					$va_media_info = $qr_reps->getMediaInfo('media');
					$vs_original_filename = $va_media_info['ORIGINAL_FILENAME'];
				
					print CLIProgressBar::next(1, _t("Re-processing %1", ($vs_original_filename ? $vs_original_filename." (".$qr_reps->get('representation_id').")" : $qr_reps->get('representation_id'))));
		
					$vs_mimetype = $qr_reps->getMediaInfo('media', 'original', 'MIMETYPE');
					if(sizeof($va_mimetypes)) {
						foreach($va_mimetypes as $vs_mimetype_pattern) {
							if(!preg_match("!^{$vs_mimetype_pattern}!", $vs_mimetype)) {
								continue(2);
							}
						}
					}
				
					$t_rep->load($qr_reps->get('representation_id'));
					$t_rep->set('media', $qr_reps->getMediaPath('media', 'original'), array('original_filename' => $vs_original_filename));

					if (sizeof($va_versions)) {
						$t_rep->update(array('updateOnlyMediaVersions' =>$va_versions));
					} else {
						$t_rep->update();
					}
		
					if ($t_rep->numErrors()) {
						CLIUtils::addError(_t("Error processing representation media: %1", join('; ', $t_rep->getErrors())));
					}
				}
				print CLIProgressBar::finish();
			}
			
			if (in_array('all', $va_kinds)  || in_array('ca_attributes', $va_kinds)) { 
				// get all Media elements
				$va_elements = ca_metadata_elements::getElementsAsList(false, null, null, true, false, true, array(16)); // 16=media
				
				if (is_array($va_elements) && sizeof($va_elements)) {
					if (is_array($va_element_ids = caExtractValuesFromArrayList($va_elements, 'element_id', array('preserveKeys' => false))) && sizeof($va_element_ids)) {
						$qr_c = $o_db->query("
							SELECT count(*) c 
							FROM ca_attribute_values
							WHERE
								element_id in (?)
						", $va_element_ids);
						if ($qr_c->nextRow()) { $vn_count = $qr_c->get('c'); } else { $vn_count = 0; }
				
						print CLIProgressBar::start($vn_count, _t('Re-processing attribute media'));
						foreach($va_elements as $vs_element_code => $va_element_info) {
							$qr_vals = $o_db->query("SELECT value_id FROM ca_attribute_values WHERE element_id = ?", (int)$va_element_info['element_id']);
							$va_vals = $qr_vals->getAllFieldValues('value_id');
							foreach($va_vals as $vn_value_id) {
								$t_attr_val = new ca_attribute_values($vn_value_id);
								if ($t_attr_val->getPrimaryKey()) {
									$t_attr_val->setMode(ACCESS_WRITE);
									$t_attr_val->useBlobAsMediaField(true);
							
									$va_media_info = $t_attr_val->getMediaInfo('value_blob');
									$vs_original_filename = is_array($va_media_info) ? $va_media_info['ORIGINAL_FILENAME'] : '';
							
									print CLIProgressBar::next(1, _t("Re-processing %1", ($vs_original_filename ? $vs_original_filename." ({$vn_value_id})" : $vn_value_id)));
		
							
									$t_attr_val->set('value_blob', $t_attr_val->getMediaPath('value_blob', 'original'), array('original_filename' => $vs_original_filename));
							
									$t_attr_val->update();	
									if ($t_attr_val->numErrors()) {
										CLIUtils::addError(_t("Error processing attribute media: %1", join('; ', $t_attr_val->getErrors())));
									}
								}
							}
						}
						print CLIProgressBar::finish();
					}
				}
			}
			
			
			return true;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reprocess_mediaParamList() {
			return array(
				"mimetypes|m-s" => _t("Limit re-processing to specified mimetype(s) or mimetype stubs. Separate multiple mimetypes with commas."),
				"versions|v-s" => _t("Limit re-processing to specified versions. Separate multiple versions with commas."),
				"start_id|s-n" => _t('Representation id to start reloading at'),
				"end_id|e-n" => _t('Representation id to end reloading at'),
				"id|i-n" => _t('Representation id to reload'),
				"ids|l-s" => _t('Comma separated list of representation ids to reload'),
				"kinds|k-s" => _t('Comma separated list of kind of media to reprocess. Valid kinds are ca_object_representations (object representations), and ca_attributes (metadata elements). You may also specify "all" to reprocess both kinds of media. Default is "all"')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reprocess_mediaUtilityClass() {
			return _t('Media');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reprocess_mediaShortHelp() {
			return _t("Re-process existing media using current media processing configuration.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reprocess_mediaHelp() {
			return _t("CollectiveAccess generates derivatives for all uploaded media. More here...");
		}
		# -------------------------------------------------------
		/**
		 * Reindex PDF media by content for in-PDF search
		 */
		public static function reindex_pdfs($po_opts=null) {
			require_once(__CA_LIB_DIR__."/core/Db.php");
			require_once(__CA_MODELS_DIR__."/ca_object_representations.php");
	
			if (!caPDFMinerInstalled()) { 
				CLIUtils::addError(_t("Can't reindex PDFs: PDFMiner is not installed.")); 
				return false;
			}
			
			$o_db = new Db();
	
			$t_rep = new ca_object_representations();
			$t_rep->setMode(ACCESS_WRITE);
			
			$va_versions = array("original");
			$va_kinds = ($vs_kinds = $po_opts->getOption("kinds")) ? explode(",", $vs_kinds) : array();
			
			if (!is_array($va_kinds) || !sizeof($va_kinds)) {
				$va_kinds = array('all');
			}
			$va_kinds = array_map('strtolower', $va_kinds);
			
			if (in_array('all', $va_kinds) || in_array('ca_object_representations', $va_kinds)) { 
				if (!($vn_start = (int)$po_opts->getOption('start_id'))) { $vn_start = null; }
				if (!($vn_end = (int)$po_opts->getOption('end_id'))) { $vn_end = null; }
			
			
				if ($vn_id = (int)$po_opts->getOption('id')) { 
					$vn_start = $vn_id; 
					$vn_end = $vn_id; 
				}
			
				$va_ids = array();
				if ($vs_ids = (string)$po_opts->getOption('ids')) { 
					if (sizeof($va_tmp = explode(",", $vs_ids))) {
						foreach($va_tmp as $vn_id) {
							if ((int)$vn_id > 0) {
								$va_ids[] = (int)$vn_id;
							}
						}
					}
				}
			
				$vs_sql_where = null;
				$va_params = array();
			
				if (sizeof($va_ids)) {
					$vs_sql_where = "WHERE representation_id IN (?)";
					$va_params[] = $va_ids;
				} else {
					if (
						(($vn_start > 0) && ($vn_end > 0) && ($vn_start <= $vn_end)) || (($vn_start > 0) && ($vn_end == null))
					) {
						$vs_sql_where = "WHERE representation_id >= ?";
						$va_params[] = $vn_start;
						if ($vn_end) {
							$vs_sql_where .= " AND representation_id <= ?";
							$va_params[] = $vn_end;
						}
					}
				}
				
				if ($vs_sql_where) { $vs_sql_where .= " AND mimetype = 'application/pdf'"; } else { $vs_sql_where = " WHERE mimetype = 'application/pdf'"; }
	
				$qr_reps = $o_db->query("
					SELECT * 
					FROM ca_object_representations 
					{$vs_sql_where}
					ORDER BY representation_id
				", $va_params);
			
				print CLIProgressBar::start($qr_reps->numRows(), _t('Reindexing PDF representations'));
				
				$vn_rep_table_num = $t_rep->tableNum();
				while($qr_reps->nextRow()) {
					$va_media_info = $qr_reps->getMediaInfo('media');
					$vs_original_filename = $va_media_info['ORIGINAL_FILENAME'];
					
					print CLIProgressBar::next(1, _t("Reindexing PDF %1", ($vs_original_filename ? $vs_original_filename." (".$qr_reps->get('representation_id').")" : $qr_reps->get('representation_id'))));
		
					$t_rep->load($qr_reps->get('representation_id'));
					
					$vn_rep_id = $t_rep->getPrimaryKey();
					
					$m = new Media();
					if(($m->read($vs_path = $t_rep->getMediaPath('media', 'original'))) && is_array($va_locs = $m->getExtractedTextLocations())) {
						MediaContentLocationIndexer::clear($vn_rep_table_num, $vn_rep_id);
						foreach($va_locs as $vs_content => $va_loc_list) {
							foreach($va_loc_list as $va_loc) {
								MediaContentLocationIndexer::index($vn_rep_table_num, $vn_rep_id, $vs_content, $va_loc['p'], $va_loc['x1'], $va_loc['y1'], $va_loc['x2'], $va_loc['y2']);
							}
						}
						MediaContentLocationIndexer::write();
					} else {
						//CLIUtils::addError(_t("[Warning] No content to reindex for PDF representation: %1", $vs_path));
					}
				}
				print CLIProgressBar::finish();
			}
			
			if (in_array('all', $va_kinds)  || in_array('ca_attributes', $va_kinds)) { 
				// get all Media elements
				$va_elements = ca_metadata_elements::getElementsAsList(false, null, null, true, false, true, array(16)); // 16=media
				
				$qr_c = $o_db->query("
					SELECT count(*) c 
					FROM ca_attribute_values
					WHERE
						element_id in (?)
				", caExtractValuesFromArrayList($va_elements, 'element_id', array('preserveKeys' => false)));
				if ($qr_c->nextRow()) { $vn_count = $qr_c->get('c'); } else { $vn_count = 0; }
				
				
				$t_attr_val = new ca_attribute_values();
				$vn_attr_table_num = $t_attr_val->tableNum();
				
				print CLIProgressBar::start($vn_count, _t('Reindexing metadata attribute media'));
				foreach($va_elements as $vs_element_code => $va_element_info) {
					$qr_vals = $o_db->query("SELECT value_id FROM ca_attribute_values WHERE element_id = ?", (int)$va_element_info['element_id']);
					$va_vals = $qr_vals->getAllFieldValues('value_id');
					foreach($va_vals as $vn_value_id) {
						$t_attr_val = new ca_attribute_values($vn_value_id);
						if ($t_attr_val->getPrimaryKey()) {
							$t_attr_val->setMode(ACCESS_WRITE);
							$t_attr_val->useBlobAsMediaField(true);
							
							$va_media_info = $t_attr_val->getMediaInfo('value_blob');
							$vs_original_filename = $va_media_info['ORIGINAL_FILENAME'];
							
							if (!is_array($va_media_info) || ($va_media_info['MIMETYPE'] !== 'application/pdf')) { continue; }
					
							print CLIProgressBar::next(1, _t("Reindexing %1", ($vs_original_filename ? $vs_original_filename." ({$vn_value_id})" : $vn_value_id)));
		
							$m = new Media();
							if(($m->read($vs_path = $t_attr_val->getMediaPath('value_blob', 'original'))) && is_array($va_locs = $m->getExtractedTextLocations())) {
								MediaContentLocationIndexer::clear($vn_attr_table_num, $vn_attr_table_num);
								foreach($va_locs as $vs_content => $va_loc_list) {
									foreach($va_loc_list as $va_loc) {
										MediaContentLocationIndexer::index($vn_attr_table_num, $vn_value_id, $vs_content, $va_loc['p'], $va_loc['x1'], $va_loc['y1'], $va_loc['x2'], $va_loc['y2']);
									}
								}
								MediaContentLocationIndexer::write();
							} else {
								//CLIUtils::addError(_t("[Warning] No content to reindex for PDF in metadata attribute: %1", $vs_path));
							}
						}
					}
				}
				print CLIProgressBar::finish();
			}
			
			
			return true;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reindex_pdfsParamList() {
			return array(
				"start_id|s-n" => _t('Representation id to start reindexing at'),
				"end_id|e-n" => _t('Representation id to end reindexing at'),
				"id|i-n" => _t('Representation id to reindex'),
				"ids|l-s" => _t('Comma separated list of representation ids to reindex'),
				"kinds|k-s" => _t('Comma separated list of kind of media to reindex. Valid kinds are ca_object_representations (object representations), and ca_attributes (metadata elements). You may also specify "all" to reindex both kinds of media. Default is "all"')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reindex_pdfsUtilityClass() {
			return _t('Media');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reindex_pdfsShortHelp() {
			return _t("Reindex PDF media for in-viewer content search.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reindex_pdfsHelp() {
			return _t("The CollectiveAccess document viewer can search text within PDFs and highlight matches. To enable this feature PDF content must be analyzed and indexed. If your database predates the introduction of in-viewer PDF search in CollectiveAccess 1.4, or search is otherwise failing to work properly, you can use this command to analyze and index PDFs in the database.");
		}
		# -------------------------------------------------------
		/**
		 * Update database schema
		 */
		public static function update_database_schema($po_opts=null) {
			require_once(__CA_LIB_DIR__."/ca/ConfigurationCheck.php");
	
			$o_config_check = new ConfigurationCheck();
			if (($vn_current_revision = ConfigurationCheck::getSchemaVersion()) < __CollectiveAccess_Schema_Rev__) {
				CLIUtils::addMessage(_t("Are you sure you want to update your CollectiveAccess database from revision %1 to %2?\nNOTE: you should backup your database before applying updates!\n\nType 'y' to proceed or 'N' to cancel, then hit return ", $vn_current_revision, __CollectiveAccess_Schema_Rev__));
				flush();
				ob_flush();
				$confirmation  =  trim( fgets( STDIN ) );
				if ( $confirmation !== 'y' ) {
					// The user did not say 'y'.
					return false;
				}
				$va_messages = ConfigurationCheck::performDatabaseSchemaUpdate();

				print CLIProgressBar::start(sizeof($va_messages), _t('Updating database'));
				foreach($va_messages as $vs_message) {
					print CLIProgressBar::next(1, $vs_message);
				}
				print CLIProgressBar::finish();
			} else {
				print CLIProgressBar::finish();
				CLIUtils::addMessage(_t("Database already at revision %1. No update is required.", __CollectiveAccess_Schema_Rev__));
			}
			
			return true;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function update_database_schemaParamList() {
			return array();
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function update_database_schemaUtilityClass() {
			return _t('Maintenance');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function update_database_schemaShortHelp() {
			return _t("Update database schema to the current version.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function update_database_schemaHelp() {
			return _t("Updates database schema to current version. More here...");
		}
		# -------------------------------------------------------
		/**
		 * 
		 */
		public static function load_import_mapping($po_opts=null) {
			require_once(__CA_MODELS_DIR__."/ca_data_importers.php");
	
			if (!($vs_file_path = $po_opts->getOption('file'))) {
				CLIUtils::addError(_t("You must specify a file"));
				return false;
			}
			if (!file_exists($vs_file_path)) {
				CLIUtils::addError(_t("File '%1' does not exist", $vs_file_path));
				return false;
			}
			$vs_log_dir = $po_opts->getOption('log');
			$vn_log_level = CLIUtils::import_getLogLevel($po_opts);

			if (!($t_importer = ca_data_importers::loadImporterFromFile($vs_file_path, $va_errors, array('logDirectory' => $vs_log_dir, 'logLevel' => $vn_log_level)))) {
				CLIUtils::addError(_t("Could not import '%1': %2", $vs_file_path, join("; ", $va_errors)));
				return false;
			} else {
				
				CLIUtils::addMessage(_t("Created mapping %1 from %2", CLIUtils::textWithColor($t_importer->get('importer_code'), 'yellow'), $vs_file_path), array('color' => 'none'));
				return true;
			}
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_import_mappingParamList() {
			return array(
				"file|f=s" => _t('Excel XLSX file to load.'),
				"log|l-s" => _t('Path to directory in which to log import details. If not set no logs will be recorded.'),
				"log-level|d-s" => _t('Logging threshold. Possible values are, in ascending order of important: DEBUG, INFO, NOTICE, WARN, ERR, CRIT, ALERT. Default is INFO.'),
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_import_mappingUtilityClass() {
			return _t('Import/Export');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_import_mappingShortHelp() {
			return _t("Load import mapping from Excel XLSX format file.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_import_mappingHelp() {
			return _t("Loads import mapping from Excel XLSX format file. More here...");
		}
		# -------------------------------------------------------
		/**
		 * 
		 */
		public static function import_data($po_opts=null) {
			require_once(__CA_MODELS_DIR__."/ca_data_importers.php");
	
			if (!($vs_data_source = $po_opts->getOption('source'))) {
				CLIUtils::addError(_t('You must specify a data source for import'));
				return false;
			}
			if (!$vs_data_source) {
				CLIUtils::addError(_t('You must specify a source'));
				return false;
			}
			if (!($vs_mapping = $po_opts->getOption('mapping'))) {
				CLIUtils::addError(_t('You must specify a mapping'));
				return false;
			}
			if (!(ca_data_importers::mappingExists($vs_mapping))) {
				CLIUtils::addError(_t('Mapping %1 does not exist', $vs_mapping));
				return false;
			}
			
			$vb_no_ncurses = (bool)$po_opts->getOption('disable-ncurses');
			
			$vs_format = $po_opts->getOption('format');
			$vs_log_dir = $po_opts->getOption('log');
			$vn_log_level = CLIUtils::import_getLogLevel($po_opts);
			
			
			if (!ca_data_importers::importDataFromSource($vs_data_source, $vs_mapping, array('format' => $vs_format, 'showCLIProgressBar' => true, 'useNcurses' => !$vb_no_ncurses && caCLIUseNcurses(), 'logDirectory' => $vs_log_dir, 'logLevel' => $vn_log_level))) {
				CLIUtils::addError(_t("Could not import source %1", $vs_data_source));
				return false;
			} else {
				CLIUtils::addMessage(_t("Imported data from source %1", $vs_data_source));
				return true;
			}
		}
		/**
		* Helper function to get log levels
		*/
		private static function import_getLogLevel($po_opts){
			$vn_log_level = KLogger::INFO;
			switch($vs_log_level = $po_opts->getOption('log-level')) {
				case 'DEBUG':
					$vn_log_level = KLogger::DEBUG;
					break;
				case 'NOTICE':
					$vn_log_level = KLogger::NOTICE;
					break;
				case 'WARN':
					$vn_log_level = KLogger::WARN;
					break;
				case 'ERR':
					$vn_log_level = KLogger::ERR;
					break;
				case 'CRIT':
					$vn_log_level = KLogger::CRIT;
					break;
				case 'ALERT':
					$vn_log_level = KLogger::ALERT;
					break;
				default:
				case 'INFO':
					$vn_log_level = KLogger::INFO;
					break;
			}
			return $vn_log_level;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function import_dataParamList() {
			return array(
				"source|s=s" => _t('Data to import. For files provide the path; for database, OAI and other non-file sources provide a URL.'),
				"mapping|m=s" => _t('Mapping to import data with.'),
				"format|f-s" => _t('The format of the data to import. (Ex. XLSX, tab, CSV, mysql, OAI, Filemaker XML, ExcelXML, MARC). If omitted an attempt will be made to automatically identify the data format.'),
				"log|l-s" => _t('Path to directory in which to log import details. If not set no logs will be recorded.'),
				"log-level|d-s" => _t('Logging threshold. Possible values are, in ascending order of important: DEBUG, INFO, NOTICE, WARN, ERR, CRIT, ALERT. Default is INFO.'),
				"disable-ncurses" => _t('If set the ncurses terminal library will not be used to display import progress.'),
				"dryrun" => _t('If set import is performed without data actually being saved to the database. This is useful for previewing an import for errors.')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function import_dataUtilityClass() {
			return _t('Import/Export');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function import_dataShortHelp() {
			return _t("Import data from an Excel XLSX, tab or comma delimited text or XML file.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function import_dataHelp() {
			return _t("Import data from an Excel XLSX, tab or comma delimited text or XML file. More here...");
		}
		# -------------------------------------------------------
		/**
		 * 
		 */
		public static function load_export_mapping($po_opts=null) {
			require_once(__CA_MODELS_DIR__."/ca_data_exporters.php");
	
			if (!($vs_file_path = $po_opts->getOption('file'))) {
				print _t("You must specify a file!")."\n";
				return false;
			}
			if (!file_exists($vs_file_path)) {
				print _t("File '%1' does not exist!", $vs_file_path)."\n";
				return false;
			}
			
			if (!($t_exporter = ca_data_exporters::loadExporterFromFile($vs_file_path,$va_errors))) {
				if(is_array($va_errors) && sizeof($va_errors)){
					foreach($va_errors as $vs_error){
						CLIUtils::addError($vs_error);
					}
				} else {
					CLIUtils::addError(_t("Could not import '%1'", $vs_file_path));
				}
				
				return false;
			} else {
				if(is_array($va_errors) && sizeof($va_errors)){
					foreach($va_errors as $vs_error){
						CLIUtils::addMessage(_t("Warning").":".$vs_error);
					}
				}
				print _t("Created mapping %1 from %2", CLIUtils::textWithColor($t_exporter->get('exporter_code'), 'yellow'), $vs_file_path)."\n";
				return true;
			}
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_export_mappingParamList() {
			return array(
				"file|f=s" => _t('Excel XLSX file to load.')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_export_mappingUtilityClass() {
			return _t('Import/Export');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_export_mappingShortHelp() {
			return _t("Load export mapping from Excel XLSX format file.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_export_mappingHelp() {
			return _t("Loads export mapping from Excel XLSX format file. More here...");
		}
		# -------------------------------------------------------
		public static function export_data($po_opts=null) {
			require_once(__CA_MODELS_DIR__."/ca_data_exporters.php");
	
			$vs_search = $po_opts->getOption('search');
			$vs_id = $po_opts->getOption('id');
			$vb_rdf = (bool)$po_opts->getOption('rdf');

			if (!$vb_rdf && !$vs_search && !$vs_id) {
				print _t('You must specify either an idno or a search expression to select a record or record set for export or activate RDF mode.')."\n";
				return false;
			}
			if (!($vs_filename = $po_opts->getOption('file'))) {
				print _t('You must specify a file to write export output to.')."\n";
				return false;
			}

			if(@file_put_contents($vs_filename, "") === false){
				// probably a permission error
				print _t("Can't write to file %1. Check the permissions.",$vs_filename)."\n";
				return false;
			}

			// RDF mode
			if($vb_rdf){
				if (!($vs_config = $po_opts->getOption('config'))) {
					print _t('You must specify a configuration file that contains the export definition for the RDF mode.')."\n";
					return false;
				}

				// test config syntax
				if(!Configuration::load($vs_config)){
					print _t('Syntax error in configuration file %s.',$vs_config)."\n";
					return false;
				}

				if(ca_data_exporters::exportRDFMode($vs_config,$vs_filename,array('showCLIProgressBar' => true))){
					print _t("Exported data to %1", CLIUtils::textWithColor($vs_filename, 'yellow'));
					return true;
				} else {
					print _t("Could not run RDF mode export")."\n";
					return false;
				}
			}
			
			// Search or ID mode

			if (!($vs_mapping = $po_opts->getOption('mapping'))) {
				print _t('You must specify a mapping for export.')."\n";
				return false;
			}

			if (!(ca_data_exporters::loadExporterByCode($vs_mapping))) {
				print _t('Mapping %1 does not exist', $vs_mapping)."\n";
				return false;
			}

			if(sizeof($va_errors = ca_data_exporters::checkMapping($vs_mapping))>0){
				print _t("Mapping %1 has errors: %2",$vs_mapping,join("; ",$va_errors))."\n";
				return false;
			}
			
			if($vs_search){
				if(!ca_data_exporters::exportRecordsFromSearchExpression($vs_mapping, $vs_search, $vs_filename, array('showCLIProgressBar' => true, 'useNcurses' => true))){
					print _t("Could not export mapping %1", $vs_mapping)."\n";
					return false;
				} else {
					print _t("Exported data to %1", $vs_filename)."\n";
				}	
			} else if($vs_id){
				if($vs_export = ca_data_exporters::exportRecord($vs_mapping, $vs_id, $pa_options=array('singleRecord' => true))){
					file_put_contents($vs_filename, $vs_export);
					print _t("Exported data to %1", CLIUtils::textWithColor($vs_filename, 'yellow'));
				} else {
					print _t("Could not export mapping %1", $vs_mapping)."\n";
					return false;
				}
			}
		}
		# -------------------------------------------------------
		public static function export_dataParamList() {
			return array(
				"search|s=s" => _t('Search expression that selects records to export.'),
				"id|i=s" => _t('Primary key identifier of single item to export.'),
				"file|f=s" => _t('Required. File to save export to.'),
				"mapping|m=s" => _t('Mapping to export data with.'),
				"rdf" => _t('Switches to RDF export mode. You can use this to assemble record-level exports across authorities with multiple mappings in a single export (usually an RDF graph). -s, -i and -m are ignored and -c is required.'),
				"config|c=s" => _t('Configuration file for RDF export mode.'),
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function export_dataUtilityClass() {
			return _t('Import/Export');
		}
		# -------------------------------------------------------
		public static function export_dataShortHelp() {
			return _t("Export data to a MARC or XML file.");
		}
		# -------------------------------------------------------
		public static function export_dataHelp() {
			return _t("Export data to a MARC or XML file.");
		}
		# -------------------------------------------------------
		/**
		 * 
		 */
		public static function regenerate_annotation_previews($po_opts=null) {
			require_once(__CA_LIB_DIR__."/core/Db.php");
			require_once(__CA_MODELS_DIR__."/ca_representation_annotations.php");
	
			$o_db = new Db();
	
			$t_rep = new ca_object_representations();
			$t_rep->setMode(ACCESS_WRITE);
	
			if (!($vn_start = (int)$po_opts->getOption('start_id'))) { $vn_start = null; }
			if (!($vn_end = (int)$po_opts->getOption('end_id'))) { $vn_end = null; }
			
			$vs_sql_where = null;
			$va_params = array();
			if (
				(($vn_start > 0) && ($vn_end > 0) && ($vn_start <= $vn_end)) || (($vn_start > 0) && ($vn_end == null))
			) {
				$vs_sql_where = "WHERE annotation_id >= ?";
				$va_params[] = $vn_start;
				if ($vn_end) {
					$vs_sql_where .= " AND annotation_id <= ?";
					$va_params[] = $vn_end;
				}
			}
			$qr_reps = $o_db->query("
				SELECT annotation_id 
				FROM ca_representation_annotations 
				{$vs_sql_where}
				ORDER BY annotation_id
			", $va_params);
	
			$vn_total = $qr_reps->numRows();
			print CLIProgressBar::start($vn_total, _t('Finding annotations'));
			$vn_c = 1;
			while($qr_reps->nextRow()) {
				$t_instance = new ca_representation_annotations($vn_id = $qr_reps->get('annotation_id'));
				print CLIProgressBar::next(1, _t('Annotation %1', $vn_id));
				$t_instance->setMode(ACCESS_WRITE);
				$t_instance->update(array('forcePreviewGeneration' => true));
		
				$vn_c++;
			}
			print CLIProgressBar::finish();
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function regenerate_annotation_previewsParamList() {
			return array(
				"start_id|s-n" => _t('Annotation id to start reloading at'),
				"end_id|e-n" => _t('Annotation id to end reloading at')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function regenerate_annotation_previewsUtilityClass() {
			return _t('Media');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function regenerate_annotation_previewsShortHelp() {
			return _t("Regenerates annotation preview media for some or all object representation annotations.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function regenerate_annotation_previewsHelp() {
			return _t("Regenerates annotation preview media for some or all object representation annotations.");
		}
		# -------------------------------------------------------
		/**
		 * 
		 */
		public static function load_AAT($po_opts=null) {
			require_once(__CA_APP_DIR__.'/helpers/supportHelpers.php');
			
			if (!($vs_file_path = $po_opts->getOption('file'))) {
				CLIUtils::addError(_t("You must specify a file"));
				return false;
			}
			caLoadAAT($vs_file_path);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_AATParamList() {
			return array(
				"file|f=s" => _t('Path to AAT XML file.')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_AATUtilityClass() {
			return _t('Import/Export');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_AATShortHelp() {
			return _t("Load Getty Art & Architecture Thesaurus (AAT) into CollectiveAccess.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function load_AATHelp() {
			return _t("Loads the AAT from a Getty-provided XML file.");
		}
		# -------------------------------------------------------
		
		/**
		 * 
		 */
		public static function sync_data($po_opts=null) {
			require_once(__CA_LIB_DIR__.'/ca/Sync/DataSynchronizer.php');
			$o_sync = new DataSynchronizer();
			$o_sync->sync();
			//if (!($vs_file_path = $po_opts->getOption('file'))) {
			//	CLIUtils::addError(_t("You must specify a file"));
			//	return false;
			//}
			
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function sync_dataParamList() {
			return array(
				//"file|f=s" => _t('Path to AAT XML file.')
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function sync_dataUtilityClass() {
			return _t('Import/Export');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function sync_dataShortHelp() {
			return _t("Synchronize data between two CollectiveAccess systems.");
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function sync_dataHelp() {
			return _t("Synchronizes data in one CollectiveAccess instance based upon data in another instance, subject to configuration in synchronization.conf.");
		}
		# -------------------------------------------------------
		/**
		 * Fix file permissions
		 */
		public static function fix_permissions($po_opts=null) {
			// Guess web server user
			if (!($vs_user = $po_opts->getOption("user"))) {
				$vs_user = caDetermineWebServerUser();
				if (!$po_opts->getOption("quiet") && $vs_user) { CLIUtils::addMessage(_t("Determined web server user to be \"%1\"", $vs_user)); }
			}
			
			if (!$vs_user) {
				$vs_user = caGetProcessUserName();
				CLIUtils::addError(_t("Cannot determine web server user. Using %1 instead.", $vs_user));
			}
			
			if (!$vs_user) {
				CLIUtils::addError(_t("Cannot determine the user. Please specify one with the --user option."));
				return false;
			}
			
			if (!($vs_group = $po_opts->getOption("group"))) {
				$vs_group = caGetProcessGroupName();
				if (!$po_opts->getOption("quiet") && $vs_group) { CLIUtils::addMessage(_t("Determined web server group to be \"%1\"", $vs_group)); }
			}
			
			if (!$vs_group) {
				CLIUtils::addError(_t("Cannot determine the group. Please specify one with the --group option."));
				return false;
			}
			
			if (!$po_opts->getOption("quiet")) { CLIUtils::addMessage(_t("Fixing permissions for the temporary directory (app/tmp) for ownership by \"%1\"...", $vs_user)); }
			$va_files = caGetDirectoryContentsAsList($vs_path = __CA_APP_DIR__.'/tmp', true, false, false, true);
		
			foreach($va_files as $vs_path) {
				chown($vs_path, $vs_user);
				chgrp($vs_path, $vs_group);
				chmod($vs_path, 0770);
			}
			if (!$po_opts->getOption("quiet")) { CLIUtils::addMessage(_t("Fixing permissions for the media directory (media) for ownership by \"%1\"...", $vs_user)); }
			$va_files = caGetDirectoryContentsAsList($vs_path = __CA_BASE_DIR__.'/media', true, false, false, true);
			
			foreach($va_files as $vs_path) {
				chown($vs_path, $vs_user);
				chgrp($vs_path, $vs_group);
				chmod($vs_path, 0775);
			}
		
			if (!$po_opts->getOption("quiet")) { CLIUtils::addMessage(_t("Fixing permissions for the HTMLPurifier definition cache directory (app/lib/core/Parsers/htmlpurifier/standalone/HTMLPurifier/DefinitionCache) for ownership by \"%1\"...", $vs_user)); }
			$va_files = caGetDirectoryContentsAsList($vs_path = __CA_LIB_DIR__.'/core/Parsers/htmlpurifier/standalone/HTMLPurifier/DefinitionCache', true, false, false, true);
			
			foreach($va_files as $vs_path) {
				chown($vs_path, $vs_user);
				chgrp($vs_path, $vs_group);
				chmod($vs_path, 0770);
			}
			
			return true;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function fix_permissionsParamList() {
			return array(
				"user|u=s" => _t("Set ownership of directories to specifed user. If not set, an attempt will be made to determine the name of the web server user automatically. If the web server user cannot be determined the current user will be used."),
				"group|g=s" => _t("Set ownership of directories to specifed group. If not set, the current group will be used."),
				"quiet|q" => _t("Run without outputting progress information.")
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function fix_permissionsUtilityClass() {
			return _t('Maintenance');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function fix_permissionsShortHelp() {
			return _t("Fix folder permissions. MUST BE RUN WHILE LOGGED IN WITH ADMINSTRATIVE/ROOT PERMISSIONS. You are currently logged in as %1 (uid %2)", caGetProcessUserName(), caGetProcessUserID());
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function fix_permissionsHelp() {
			return _t("CollectiveAccess must have both read and write access to the temporary storage directory (app/tmp), media directory (media) and HTMLPurifier definition cache (app/lib/core/Parsers/htmlpurifier/standalone/HTMLPurifier/DefinitionCache). A run-time error will be displayed if any of these locations is not accessible to the application. To change these permissions to allow CollectiveAccess to run normally run this command while logged in with administrative/root privileges. You are currently logged in as %1 (uid %2). You can specify which user will be given ownership of the directories using the --user option. If you do not specify a user, the web server user for your server will be automatically determined and used.", caGetProcessUserName(), caGetProcessUserID());
		}
		# -------------------------------------------------------
		/**
		 * Generate mappings for ElasticSearch based upon currently configured search indexing
		 */
		public static function generate_elasticSearch_configuration($po_opts=null) {
			require_once(__CA_LIB_DIR__."/core/Search/SearchBase.php");
			require_once(__CA_LIB_DIR__."/core/Configuration.php");
			require_once(__CA_LIB_DIR__."/core/Datamodel.php");
			require_once(__CA_LIB_DIR__."/core/Zend/Http/Client.php");
	
			$vo_app_conf = Configuration::load();
			$vo_search_conf = Configuration::load($vo_app_conf->get("search_config"));
			$vo_search_indexing_conf = Configuration::load($vo_search_conf->get("search_indexing_config"));
			$o_db = new Db();
			$o_datamodel = Datamodel::load();
	
			// delete and create index
			$vo_http_client = new Zend_Http_Client();
			$vo_http_client->setUri(
				$vo_search_conf->get('search_elasticsearch_base_url')."/".
				$vo_search_conf->get('search_elasticsearch_index_name')
			);
			try {
				$vo_http_client->request('DELETE');
				$vo_http_client->request('PUT');
			} catch (Zend_Http_Client_Adapter_Exception $e){
				CLIUtils::addError(_t('Couldn\'t connect to ElasticSearch. Is the service running?'));
				return;
			}
	
			$va_tables = $vo_search_indexing_conf->getAssocKeys();
			$vo_search_base = new SearchBase();
	
			foreach($va_tables as $vs_table){
				// get fields to index for this table		
				if (!is_array($va_table_fields = $vo_search_base->getFieldsToIndex($vs_table))) {
					$va_table_fields = array();
				}
				
				$t_instance = $o_datamodel->getTableInstance($vs_table);
				$vn_table_num = $o_datamodel->getTableNum($vs_table);
		
				$va_attributes = null;
				$va_opts = array();

				if (is_array($va_table_fields)) {
					$va_rewritten_fields = array();
					foreach($va_table_fields as $vs_field_name => $va_field_options){ 
						if (preg_match('!^_ca_attribute_([\d]*)$!', $vs_field_name, $va_matches)) {
							$va_rewritten_fields['A'.$va_matches[1]] = $va_field_options;
							
							$qr_type_restrictions = $o_db->query('
								SELECT DISTINCT came.*
								FROM ca_metadata_type_restrictions camtr
								INNER JOIN ca_metadata_elements as came ON came.element_id = camtr.element_id
								WHERE camtr.table_num = ? AND came.element_code = ?
							',(int)$vn_table_num, (string)$va_matches[1]);

							while($qr_type_restrictions->nextRow()) {
								$vn_element_id = $qr_type_restrictions->get('element_id');

								$va_attributes[$vn_element_id] = array(
									'element_id' => $vn_element_id,
									'element_code' => $qr_type_restrictions->get('element_code'),
									'datatype' => $qr_type_restrictions->get('datatype')
								);
							}
						} else {
							$va_rewritten_fields[$vs_field_name] = $va_field_options;
						}
					}
					$va_table_fields = $va_rewritten_fields;
				}

				if (is_array($va_attributes)) {
					foreach($va_attributes as $vn_element_id => $va_element_info) {
						if (!preg_match("!^_ca_attribute_([\d]+)$!", $va_element_info['element_code'], $va_matches)) { continue; }
						$vs_element_code = $vs_table.".A".$va_matches[1];

						$va_element_opts = array();
						switch($va_element_info['datatype']) {
							case 1: // text
							case 3:	// list
							case 5:	// url
							case 6: // currency
							case 8: // length
							case 9: // weight
							case 13: // LCSH
							case 14: // geonames
							case 15: // file
							case 16: // media
							case 19: // taxonomy
							case 20: // information service
								$va_element_opts['properties']['type'] = 'string';
								break;
							case 2:	// daterange
								$va_element_opts['properties']['type'] = 'date';
								$va_element_opts['properties']["format"] = 'dateOptionalTime';
								$va_element_opts['properties']["ignore_malformed"] = false;
								$va_table_fields[$vs_element_code.'_text'] = array_merge($va_opts, array('properties' => array('type' => 'string')));
								break;
							case 4:	// geocode
								$va_element_opts['properties']['type'] = 'geo_point';
								$va_table_fields[$vs_element_code.'_text'] = array_merge($va_opts, array('properties' => array('type' => 'string')));
								break;
							case 10:	// timecode
							case 12:	// numeric/float
								$va_element_opts['properties']['type'] = 'double';
								break;
							case 11:	// integer
								$va_element_opts['properties']['type'] = 'long';
								break;
							default:
								$va_element_opts['properties']['type'] = 'string';
								break;
						}
						$va_table_fields[$vs_element_code] = array_merge($va_opts, $va_element_opts);
					}
				}
		
				if(is_array($va_table_fields)){
					foreach($va_table_fields as $vs_field_name => $va_field_options){				
						$va_field_options['properties']["store"] = in_array("STORE",$va_field_options) ? 'yes' : 'no';
				
						if($va_field_options["BOOST"]){
							$va_field_options['properties']["boost"] = floatval($va_field_options["BOOST"]);
						}
				
						if(in_array("DONT_TOKENIZE",$va_field_options)){
							// TODO: maybe do something?
						}
				
						// "intrinsic" fields
						if (!isset($va_field_options['properties']['type']) && $t_instance->hasField($vs_field_name)) {
							switch($t_instance->getFieldInfo($vs_field_name, "FIELD_TYPE")){
								case (FT_TEXT):
								case (FT_MEDIA):
								case (FT_FILE):
								case (FT_PASSWORD):
								case (FT_VARS):
									$va_field_options['properties']['type'] = 'string';
									break;
								case (FT_NUMBER):
								case (FT_TIME):
								case (FT_TIMERANGE):
								case (FT_TIMECODE):
									if ($t_instance->getFieldInfo($vs_field_name, "LIST_CODE")) {	// list-based intrinsics get indexed with both item_id and label text
										$va_field_options['properties']['type'] = 'string';
									} else {
										$va_field_options['properties']['type'] = 'double';
									}
									break;
								case (FT_TIMESTAMP):
								case (FT_DATETIME):
								case (FT_HISTORIC_DATETIME):
								case (FT_DATE):
								case (FT_HISTORIC_DATE):
								case (FT_DATERANGE):
								case (FT_HISTORIC_DATERANGE):
									$va_field_options['properties']['type'] = 'date';
									break;
								case (FT_BIT):
									$va_field_options['properties']['type'] = 'boolean';
									break;
								default:
									$va_field_options['properties']['type'] = "string";
									break;
							}
						}
				
						if(!$va_field_options['properties']['type']) {
							$va_field_options['properties']['type'] = "string";
						}
				
						$vo_http_client = new Zend_Http_Client();
						$vo_http_client->setUri(
							$vo_search_conf->get('search_elasticsearch_base_url')."/".
							$vo_search_conf->get('search_elasticsearch_index_name')."/".
							$vs_table."/". /* ElasticSearch type name (i.e. table name) */
							"_mapping"
						);
				
						$va_mapping = array();
						$va_mapping[$vs_table]["properties"][$vs_table.".".$vs_field_name] = $va_field_options["properties"];
						
						$vo_http_client->setRawData(json_encode($va_mapping))->setEncType('text/json')->request('POST');
				
						try {
							$vo_http_response = $vo_http_client->request();
							$va_response = json_decode($vo_http_response->getBody(),true);
							if(!$va_response["ok"]){
								CLIUtils::addError(_t("Something went wrong at %1 with message: %2", "{$vs_table}.{$vs_field_name}", $va_response["error"]));
								CLIUtils::addError(_t("Mapping sent to ElasticSearch was: %1", json_encode($va_mapping)));
								return;
							}
						} catch (Exception $e){
							CLIUtils::addError(_t("Something went wrong at %1", "{$vs_table}.{$vs_field_name}"));
							CLIUtils::addError(_t("Response body was: %1", $vo_http_response->getBody()));
							return;
						}
				
					}
				}
		
				/* related tables */
				$va_related_tables = $vo_search_base->getRelatedIndexingTables($vs_table);
				foreach($va_related_tables as $vs_related_table){
					$va_related_table_fields = $vo_search_base->getFieldsToIndex($vs_table, $vs_related_table);
					foreach($va_related_table_fields as $vs_related_table_field => $va_related_table_field_options){
						$va_related_table_field_options['properties']["store"] = in_array("STORE",$va_related_table_field_options) ? 'yes' : 'no';
						$va_related_table_field_options['properties']['type'] = "string";
				
				
						if(in_array("DONT_TOKENIZE",$va_related_table_field_options)){
							// TODO: do something?
						}
				
						$vo_http_client = new Zend_Http_Client();
						$vo_http_client->setUri(
							$vo_search_conf->get('search_elasticsearch_base_url')."/".
							$vo_search_conf->get('search_elasticsearch_index_name')."/".
							$vs_table."/". /* ElasticSearch type name (i.e. table name) */
							"_mapping"
						);
				
						$va_mapping = array();
						$va_mapping[$vs_table]["properties"][$vs_related_table.'.'.$vs_related_table_field] = $va_related_table_field_options["properties"];
						$vo_http_client->setRawData(json_encode($va_mapping))->setEncType('text/json')->request('POST');
				
						try {
							$vo_http_response = $vo_http_client->request();
							$va_response = json_decode($vo_http_response->getBody(),true);
							if(!$va_response["ok"]){
								CLIUtils::addError(_t("Something went wrong at %1 with message: %2", "{$vs_table}/{$vs_related_table}.{$vs_related_table_field}", $va_response["error"]));
								CLIUtils::addError(_t("Mapping sent to ElasticSearch was: %1", json_encode($va_mapping)));
								return;
							}
						} catch (Exception $e){
							CLIUtils::addError(_t("Something went wrong at %1", "{$vs_table}/{$vs_related_table}.{$vs_related_table_field}"));
							CLIUtils::addError(_t("Response body was: %1", $vo_http_response->getBody()));
							return;
						}
					}
				}
		
				/* created and modified fields */
				$va_mapping = array();
				$va_mapping[$vs_table]["properties"]["created"] = array(
					'type' => 'date',
					'format' => 'dateOptionalTime',
					'ignore_malformed' => false,
				);
				$va_mapping[$vs_table]["properties"]["modified"] = array(
					'type' => 'date',
					'format' => 'dateOptionalTime',
					'ignore_malformed' => false,
				);
				$va_mapping[$vs_table]["properties"]["created_user_id"] = array(
					'type' => 'double',
				);
				$va_mapping[$vs_table]["properties"]["modified_user_id"] = array(
					'type' => 'double',
				);
		
				$vo_http_client = new Zend_Http_Client();
				$vo_http_client->setUri(
					$vo_search_conf->get('search_elasticsearch_base_url')."/".
					$vo_search_conf->get('search_elasticsearch_index_name')."/".
					$vs_table."/". /* ElasticSearch type name (i.e. table name) */
					"_mapping"
				);
		
				$vo_http_client->setRawData(json_encode($va_mapping))->setEncType('text/json')->request('POST');
				
				try {
					$vo_http_response = $vo_http_client->request();
					$va_response = json_decode($vo_http_response->getBody(), true);
					if(!$va_response["ok"]){
						CLIUtils::addError(_t("Something went wrong at %1 with message: %2", "{$vs_table}.created/modified", $va_response["error"]));
						CLIUtils::addError(_t("Mapping sent to ElasticSearch was: %1", json_encode($va_mapping)));
						return;
					}
				} catch (Exception $e){
					CLIUtils::addError(_t("Something went wrong at %1", "{$vs_table}.created"));
					CLIUtils::addError(_t("Response body was: %1", $vo_http_response->getBody()));
					return;
				}
			}
	
			CLIUtils::addMessage(_t('ElasticSearch schema was created successfully!'), array('color' => 'bold_green'));
			CLIUtils::addMessage(_t("Note that all data has been wiped from the index so you must issue a full reindex now, either using caUtils rebuild-search-index or the web-based tool under Manage > Administration > Maintenance."), array('color' => 'red'));
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function generate_elasticSearch_configurationParamList() {
			return array();
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function generate_elasticSearch_configurationUtilityClass() {
			return _t('Search');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function generate_elasticSearch_configurationShortHelp() {
			return _t('Configures ElasticSearch installation for use with CollectiveAccess');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function generate_elasticSearch_configurationHelp() {
			return _t('Configures ElasticSearch installation for use with CollectiveAccess, setting up indices and mappings. You must run this before reindexing your database.');
		}
		# -------------------------------------------------------
		/**
		 * Generate mappings for Solr based upon currently configured search indexing
		 */
		public static function generate_solr_configuration($po_opts=null) {
				require_once(__CA_LIB_DIR__."/core/Search/Solr/SolrConfiguration.php");
				SolrConfiguration::updateSolrConfiguration(true);

				// @TODO what if something goes wrong!?
				CLIUtils::addMessage(_t('Solr schema was created successfully!'), array('color' => 'bold_green'));
				CLIUtils::addMessage(_t("Note that all data has been wiped from the index so you must issue a full reindex now, either using caUtils rebuild-search-index or the web-based tool under Manage > Administration > Maintenance."), array('color' => 'red'));
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function generate_solr_configurationParamList() {
			return array();
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function generate_solr_configurationUtilityClass() {
			return _t('Search');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function generate_solr_configurationShortHelp() {
			return _t('Configures Solr installation for use with CollectiveAccess');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function generate_solr_configurationHelp() {
			return _t('Configures Solr installation for use with CollectiveAccess, setting up indices and mappings. You must run this before reindexing your database.');
		}
		# -------------------------------------------------------
		/**
		 * Reset user password
		 */
		public static function reset_password($po_opts=null) {
			if ($vs_user_name = (string)$po_opts->getOption('user')) {	
				if (!($vs_password = (string)$po_opts->getOption('password'))) {	
					CLIUtils::addError(_t("You must specify a password"));
					return false;
				}
				$t_user = new ca_users();
				if ((!$t_user->load(array("user_name" => $vs_user_name)))) {
					CLIUtils::addError(_t("User name %1 does not exist", $vs_user_name));
					return false;
				}
				$t_user->setMode(ACCESS_WRITE);
				$t_user->set('password', $vs_password);
				$t_user->update();
				if ($t_user->numErrors()) {
					CLIUtils::addError(_t("Password change for user %1 failed: %2", $vs_user_name, join("; ", $t_user->getErrors())));
					return false;
				}
				CLIUtils::addMessage(_t('Changed password for user %1', $vs_user_name), array('color' => 'bold_green'));
				return true;
			}
			CLIUtils::addError(_t("You must specify a user"));
			return false;
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reset_passwordParamList() {
			return array(
				"user|u=s" => _t("User name to reset password for."),
				"password|p=s" => _t("New password for user")
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reset_passwordUtilityClass() {
			return _t('Maintenance');
		}
		
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reset_passwordShortHelp() {
			return _t('Reset a user\'s password');
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public static function reset_passwordHelp() {
			return _t('Reset a user\'s password.');
		}
		# -------------------------------------------------------
	}
?>