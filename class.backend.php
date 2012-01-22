<?php

/**
 * flexTable
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de
 * @copyright 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id$
 * 
 * FOR VERSION- AND RELEASE NOTES PLEASE LOOK AT INFO.TXT!
 */

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {
    if (defined('LEPTON_VERSION')) include (WB_PATH . '/framework/class.secure.php');
} else {
    $oneback = "../";
    $root = $oneback;
    $level = 1;
    while (($level < 10) && (! file_exists($root . '/framework/class.secure.php'))) {
        $root .= $oneback;
        $level += 1;
    }
    if (file_exists($root . '/framework/class.secure.php')) {
        include ($root . '/framework/class.secure.php');
    } else {
        trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
    }
}
// end include class.secure.php

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/initialize.php');
require_once(WB_PATH.'/framework/functions.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.editor.php');
require_once(WB_PATH.'/modules/perma_link/class.interface.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.frontend.php');

class tableBackend {
	
	const request_action							= 'act';
	const request_add_definition			= 'adef';
	const request_active_definition		= 'dact';
	const request_add_row							= 'rad';
	const request_delete_table				= 'del';
	const request_edit_detail					= 'edd';
	const request_items								= 'its';
	
	const action_about								= 'abt';
	const action_config								= 'cfg';
	const action_config_check					= 'cfgc';
	const action_default							= 'def';
	const action_list									= 'lst';
	const action_edit									= 'edt';
	const action_edit_check						= 'edtc';
	
	
	private $tab_navigation_array = array(
		self::action_list								=> ft_tab_list,
		self::action_edit								=> ft_tab_edit,
		self::action_config							=> ft_tab_cfg,
		self::action_about							=> ft_tab_about		
	);
	
	const add_max_rows								= 5;
	
	private $page_link 								= '';
	private $img_url									= '';
	private $template_path						= '';
	private $error										= '';
	private $message									= '';
	private $media_path								= '';
	private $media_file_types					= array();
	
	public function __construct() {
		global $dbFlexTableCfg;
		$this->page_link = ADMIN_URL.'/admintools/tool.php?tool=flex_table';
		$this->template_path = WB_PATH . '/modules/' . basename(dirname(__FILE__)) . '/htt/' ;
		$this->img_url = WB_URL. '/modules/'.basename(dirname(__FILE__)).'/images/';
		$this->media_path = WB_PATH.MEDIA_DIRECTORY.'/'.$dbFlexTableCfg->getValue(dbFlexTableCfg::cfgMediaDirectory).'/';
		date_default_timezone_set(tool_cfg_time_zone);
		$img = $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgImageFileTypes);
		$doc = $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgDocFileTypes);
		$this->media_file_types = array_merge($img, $doc);
	} // __construct()
	
	/**
    * Set $this->error to $error
    * 
    * @param STR $error
    */
  public function setError($error) {
  	$debug = debug_backtrace();
    $caller = next($debug);
  	$this->error = sprintf('[%s::%s - %s] %s', basename($caller['file']), $caller['function'], $caller['line'], $error);
  } // setError()

  /**
    * Get Error from $this->error;
    * 
    * @return STR $this->error
    */
  public function getError() {
    return $this->error;
  } // getError()

  /**
    * Check if $this->error is empty
    * 
    * @return BOOL
    */
  public function isError() {
    return (bool) !empty($this->error);
  } // isError

  /**
   * Reset Error to empty String
   */
  public function clearError() { 
  	$this->error = ''; 
  }

  /** Set $this->message to $message
    * 
    * @param STR $message
    */
  public function setMessage($message) {
    $this->message = $message;
  } // setMessage()

  /**
    * Get Message from $this->message;
    * 
    * @return STR $this->message
    */
  public function getMessage() {
    return $this->message;
  } // getMessage()

  /**
    * Check if $this->message is empty
    * 
    * @return BOOL
    */
  public function isMessage() {
    return (bool) !empty($this->message);
  } // isMessage
  
  /**
   * Return Version of Module
   *
   * @return FLOAT
   */
  public function getVersion() {
    // read info.php into array
    $info_text = file(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/info.php');
    if ($info_text == false) {
      return -1; 
    }
    // walk through array
    foreach ($info_text as $item) {
      if (strpos($item, '$module_version') !== false) {
        // split string $module_version
        $value = explode('=', $item);
        // return floatval
        return floatval(preg_replace('([\'";,\(\)[:space:][:alpha:]])', '', $value[1]));
      } 
    }
    return -1;
  } // getVersion()
  
  public function getTemplate($template, $template_data) {
  	global $parser;
  	try {
  		$result = $parser->get($this->template_path.$template, $template_data); 
  	} catch (Exception $e) {
  		$this->setError(sprintf(tool_error_template_error, $template, $e->getMessage()));
  		return false;
  	}
  	return $result;
  } // getTemplate()
  
  
  /**
   * Verhindert XSS Cross Site Scripting
   * 
   * @param REFERENCE $_REQUEST Array
   * @return $request
   */
	public function xssPrevent(&$request) { 
  	if (is_string($request)) {
	    $request = html_entity_decode($request);
	    $request = strip_tags($request);
	    $request = trim($request);
	    $request = stripslashes($request);
  	}
	  return $request;
  } // xssPrevent()
	
  public function action() {
  	$html_allowed = array();
  	foreach ($_REQUEST as $key => $value) {
  		if (!in_array($key, $html_allowed) && (strpos($key, 'cell_') === false)) {
  			$_REQUEST[$key] = $this->xssPrevent($value);	  			
  		} 
  	}
    isset($_REQUEST[self::request_action]) ? $action = $_REQUEST[self::request_action] : $action = self::action_default;
        
  	switch ($action):
  	case self::action_about:
  		$this->show(self::action_about, $this->dlgAbout());
  		break;
  	case self::action_config:
  		$this->show(self::action_config, $this->dlgConfig());
  		break;
  	case self::action_config_check:
  		$this->show(self::action_config, $this->checkConfig());
  		break;
  	case self::action_edit:
  		$this->show(self::action_edit, $this->dlgEdit());
  		break;
  	case self::action_edit_check:
  		$this->show(self::action_edit, $this->checkEdit());
  		break;
  	case self::action_list:
  	case self::action_default:
  	default:
  		$this->show(self::action_list, $this->dlgList());
  		break;
  	endswitch;
  } // action
	
  	
  /**
   * Ausgabe des formatierten Ergebnis mit Navigationsleiste
   * 
   * @param $action - aktives Navigationselement
   * @param $content - Inhalt
   * 
   * @return ECHO RESULT
   */
  public function show($action, $content) {
  	$navigation = array();
  	foreach ($this->tab_navigation_array as $key => $value) {
  		$navigation[] = array(
  			'active' 	=> ($key == $action) ? 1 : 0,
  			'url'			=> sprintf('%s&%s=%s', $this->page_link, self::request_action, $key),
  			'text'		=> $value
  		);
  	}
  	$data = array(
  		'WB_URL'			=> WB_URL,
  		'navigation'	=> $navigation,
  		'error'				=> ($this->isError()) ? 1 : 0,
  		'content'			=> ($this->isError()) ? $this->getError() : $content
  	);
  	echo $this->getTemplate('backend.body.htt', $data);
  } // show()
	
  public function dlgAbout() {
  	$data = array(
  		'version'					=> sprintf('%01.2f', $this->getVersion()),
  		'img_url'					=> $this->img_url.'/flex_table_424x283.jpg',
  		'release_notes'		=> file_get_contents(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/info.txt'),
  	);
  	return $this->getTemplate('backend.about.htt', $data);
  } // dlgAbout()
  
  public function dlgList() {
  	global $dbFlexTable;
  	
  	$where = array();
  	$tables = array();
  	if (!$dbFlexTable->sqlSelectRecord($where, $tables)) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
  		return false;
  	}
  	$table_array = array();
  	foreach ($tables as $table) {
  		$table_array[$table[dbFlexTable::field_name]] = array(
  			'id'					=> $table[dbFlexTable::field_id],
  			'name'				=> $table[dbFlexTable::field_name],
  			'description'	=> $table[dbFlexTable::field_description],
  			'timestamp'		=> $table[dbFlexTable::field_timestamp],
  			'link'				=> sprintf(	'%s%s%s', $this->page_link, (strpos($this->page_link, '?') === false) ? '?' : '&', 
  																http_build_query(array(	self::request_action => self::action_edit,
  																												dbFlexTable::field_id => $table[dbFlexTable::field_id])))
  		);
  	}
  	
  	$header = array(
  		'id'					=> ft_th_id,
  		'name'				=> ft_th_name,
  		'description'	=> ft_th_description,
  		'timestamp'		=> ft_th_timestamp
  	);
  	
  	$data = array(
  		'header'			=> $header,
  		'tables'			=> $table_array,
  		'title'				=> ft_header_table_list,
  		'intro'				=> ft_intro_table_list
  	);
  	return $this->getTemplate('backend.table.list.htt', $data);
  } // dlgList()
  
	public function dlgEdit() {
		global $dbFlexTable;
		global $dbFlexTableDefinition;
		global $dbFlexTableRow;
		global $dbFlexTableCell;
		global $database;
		
		if (!file_exists($this->media_path)) {
			if (!mkdir($this->media_path, 0755)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(tool_error_mkdir, $this->media_path)));
				return false;
			}
		}
		
		$table_id = (isset($_REQUEST[dbFlexTable::field_id])) ? (int) $_REQUEST[dbFlexTable::field_id] : -1;
		
		if ($table_id > 0) {
			// Tabelle auslesen
			$where = array(dbFlexTable::field_id => $table_id);
			$table = array();
			if (!$dbFlexTable->sqlSelectRecord($where, $table)) {
				$this->setError(sprintf('[%s _ %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
				return false;
			}
			if (count($table) < 1) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(tool_error_id_invalid, $table_id)));
				return false;
			}
			$table = $table[0];
		}
		else {
			// Default Werte setzen
			$table = $dbFlexTable->getFields();
			$table[dbFlexTable::field_id] = -1;
		}
		
		// page_extension ermitteln
		$SQL = sprintf("SELECT value FROM %ssettings WHERE name='page_extension'", TABLE_PREFIX);
		if (false === ($page_extension = $database->get_one($SQL, MYSQL_ASSOC))) {
			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $database->get_error()));
			return false;
		}

		// Seiten ermitteln
		$SQL = sprintf("SELECT link FROM %spages ORDER BY link ASC", TABLE_PREFIX);
		if (false === ($query = $database->query($SQL))) {
			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $database->get_error()));
			return false;
		}
		$pages_array = array();
		$pages_array[] = array(
			'key'		=> '',
			'value'	=> ft_text_select_page
		);
		while (false !== ($page = $query->fetchRow())) {
			$pages_array[] = array(
				'key'		=> $page['link'].$page_extension,
				'value'	=> $page['link'].$page_extension
			); 
		}
		
		$table_fields = array();
		foreach ($table as $key => $value) {
			$options = ($key == dbFlexTable::field_homepage) ? $pages_array : ''; 
			$table_fields[$dbFlexTable->template_names[$key]] = array(
				'name'			=> $key,
				'value'			=> $value,
				'options'		=> $options,
				'hint'			=> constant(sprintf('ft_hint_%s', $key)),
				'label'			=> constant(sprintf('ft_label_%s', $key))
			);
		}
		
		// Definitionen auslesen
		if ($table[dbFlexTable::field_id] != -1) {
			$SQL = sprintf( "SELECT * FROM %s WHERE %s='%s' ORDER BY FIND_IN_SET(%s, '%s')",
											$dbFlexTableDefinition->getTableName(),
											dbFlexTableDefinition::field_table_id,
											$table_id,
											dbFlexTableDefinition::field_id,
											$table[dbFlexTable::field_definitions]);
			$definitions = array();
			if (!$dbFlexTableDefinition->sqlExec($SQL, $definitions)) {
				$this->setError(sprintf('[%s - %] %s', __METHOD__, __LINE__, $dbFlexTableDefinition->getError()));
				return false;
			}
		}
		else {
			$definitions = array();
		}
		
		// einzelne Definitionsfelder anzeigen
		$definitions_array = array();
		$def_fields = $dbFlexTableDefinition->getFields();
		$def_fields['active'] = 1;
		
		foreach ($definitions as $definition) {
			$def_id = $definition[dbFlexTableDefinition::field_id];
			$def_array = array();
			foreach ($def_fields as $field => $value) {
				switch ($field):
				case 'active':
					$def_array[$field] = array(	
						'name'	=> sprintf('%s_%s', self::request_active_definition, $def_id),
						'value'	=> $value,
						'label'	=> constant(sprintf('ft_label_ftd_%s', $field)),
						'hint'	=> constant(sprintf('ft_hint_ftd_%s', $field)));
					break;
				case dbFlexTableDefinition::field_type:
					$def_array[$dbFlexTableDefinition->template_names[$field]] = array(	
						'name'	=> sprintf('%s_%s', $field, $def_id),
						'value'	=> $dbFlexTableDefinition->type_array[$definition[$field]]['value'],
						'label'	=> constant(sprintf('ft_label_%s', $field)),
						'hint'	=> constant(sprintf('ft_hint_%s', $field)));
					break;
				case dbFlexTableDefinition::field_head:
				case dbFlexTableDefinition::field_id:
				case dbFlexTableDefinition::field_name:
				case dbFlexTableDefinition::field_table_cell:
				case dbFlexTableDefinition::field_table_id:
				case dbFlexTableDefinition::field_title:
				case dbFlexTableDefinition::field_description:
					$def_array[$dbFlexTableDefinition->template_names[$field]] = array(	
						'name'	=> sprintf('%s_%s', $field, $def_id),
						'value'	=> $definition[$field],
						'label'	=> constant(sprintf('ft_label_%s', $field)),
						'hint'	=> constant(sprintf('ft_hint_%s', $field)));
					break;
				default:
					continue;
				endswitch;
			}
			$definitions_array[] = $def_array;
		}
		
		// neue Definitionsfelder hinzufuegen
		$add_definition = array(
			'label'				=> ft_label_add_definition,
			'hint'				=> ft_hint_add_definition,
			'name'				=> self::request_add_definition,
			'values'			=> $dbFlexTableDefinition->type_array
		);

		// Tabelle anzeigen
		$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' ORDER BY %s ASC, FIND_IN_SET(%s, '%s')",
										$dbFlexTableCell->getTableName(),
										dbFlexTableCell::field_table_id,
										$table_id,
										dbFlexTableCell::field_row_id,
										dbFlexTableCell::field_definition_id,
										$table[dbFlexTable::field_definitions]
									);
		$rows = array();
		if (!$dbFlexTableCell->sqlExec($SQL, $rows)) {
			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
			return false;
		}
		
		$row_array = array();
		$row_id = -1;
		$cells = array();
			
		foreach ($rows as $row) {
			if ($row_id == -1) $row_id = $row[dbFlexTableCell::field_row_id];
			if ($row_id != $row[dbFlexTableCell::field_row_id]) {
				$row_array[$row_id] = array(
					'id'		=> $row_id,
					'name'	=> sprintf('%s_%s', dbFlexTableCell::field_row_id, $row_id),
					'value'	=> 1,
					'cells'	=> $cells,
					'link'	=> sprintf(	'%s%s%s%s', $this->page_link, (strpos($this->page_link, '?') === false) ? '?' : '&', 
  														http_build_query(array(	self::request_edit_detail => $row_id,
  																										self::request_action => self::action_edit,
  																										dbFlexTable::field_id => $table_id)), '#fte'),
					'copy'	=> sprintf('copy_row_%d', $row_id)    																										
				);
				$row_id = $row[dbFlexTableCell::field_row_id];
				$cells = array();
			}
			$value = $dbFlexTableCell->getCellValueByType($row);
			$cell = array(
				'id'							=> $row[dbFlexTableCell::field_id],
				'table_id'				=> $table_id,
				'row_id'					=> $row_id,
				'definition_id'		=> $row[dbFlexTableCell::field_definition_id],
				'class'						=> $row[dbFlexTableCell::field_definition_name],
				'type'						=> $dbFlexTableDefinition->template_type_array[$row[dbFlexTableCell::field_definition_type]],
				'table_cell'			=> $row[dbFlexTableCell::field_table_cell],
				'value'						=> $value,
				'name'						=> sprintf('%s_%s', dbFlexTableCell::field_id, $row[dbFlexTableCell::field_id]),
				'timestamp'				=> $row[dbFlexTableCell::field_timestamp]
			);
			$cells[] = $cell;
		} // foreach
		
		if ($row_id != -1) {
			$row_array[$row_id] = array(
					'id'		=> $row_id,
					'name'	=> sprintf('%s_%s', dbFlexTableCell::field_row_id, $row_id),
					'value'	=> 1,
					'cells'	=> $cells,
					'link'	=> sprintf(	'%s%s%s%s', $this->page_link, (strpos($this->page_link, '?') === false) ? '?' : '&', 
  														http_build_query(array(	self::request_edit_detail => $row_id,
  																										self::request_action => self::action_edit,
  																										dbFlexTable::field_id => $table_id)), '#fte'),
					'copy'	=> sprintf('copy_row_%d', $row_id)  																										
				);
		}
	
		// Neuer Eintrag oder bestehenden Eintrag bearbeiten
		if (isset($_REQUEST[self::request_edit_detail]) && $_REQUEST[self::request_edit_detail] > 0) {
			$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' AND %s='%s' ORDER BY FIND_IN_SET(%s, '%s')",
											$dbFlexTableCell->getTableName(),
											dbFlexTableCell::field_table_id,
											$table_id,
											dbFlexTableCell::field_row_id,
											$_REQUEST[self::request_edit_detail],
											dbFlexTableCell::field_definition_id,
											$table[dbFlexTable::field_definitions]
										);
			$edit_array = array();							
			if (!$dbFlexTableCell->sqlExec($SQL, $edit_array)) {
				$this->setError(sprintf('[%s - %] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
				return false;
			}
			$edit = true;
		}
		else {
			$edit = false;
		}
		$edit_row = array();
		$i = 0;
		foreach ($definitions as $def) {
			$value = '';
			if ($edit) {
				$value = $dbFlexTableCell->getCellValueByType($edit_array[$i]);
				$i++;
			}
			// media_link ?
			if ($def[dbFlexTableDefinition::field_type] == dbFlexTableDefinition::type_media_link) {
				$media_directory = scandir($this->media_path);
				$media_files = array();
				$media_files[] = array(
					'value'			=> '',
					'text'			=> ft_text_select_file,
					'selected'	=> ($value == '') ? 1 : 0
				);
				foreach ($media_directory as $file) {
					if (is_file($this->media_path.$file)) {
						$ext = pathinfo($this->media_path.$file, PATHINFO_EXTENSION);
						if (in_array(strtolower($ext), $this->media_file_types)) {
							$media_files[] = array(
								'value'			=> $file,
								'text'			=> $file,
								'selected'	=> ($value == $file) ? 1 : 0
							);
						}
					}
				}
				$value = $media_files;
			}
			// HTML ?
			if ($def[dbFlexTableDefinition::field_type] == dbFlexTableDefinition::type_html) {
				ob_start();
					show_wysiwyg_editor(sprintf('cell_%s', $def[dbFlexTableDefinition::field_id]), sprintf('cell_%s', $def[dbFlexTableDefinition::field_id]), $value, '99%', '200px');
					$value = ob_get_contents();
				ob_end_clean();		
			}
			
			$edit_row[] = array(
				'name'		=> sprintf('cell_%s', $def[dbFlexTableDefinition::field_id]),
				'type'		=> $dbFlexTableDefinition->template_type_array[$def[dbFlexTableDefinition::field_type]],
				'value'		=> $value,
				'head'		=> $def[dbFlexTableDefinition::field_head],
				'copy'		=> array(	'name' 		=> sprintf('copy_cell_%d', $def[dbFlexTableDefinition::field_id]),
														'value'		=> 1,
														'active'	=> ($edit) ? 1 : 0)
			);
		}
		
		$table_delete = array(
			'name'		=> self::request_delete_table,
			'value'		=> $table_id,
			'text'		=> ft_text_table_delete
		);
		
		$data = array(
			'form_action'				=> $this->page_link,
			'action_name'				=> self::request_action,
			'action_value'			=> self::action_edit_check,
			'table'							=> $table_fields,
			'language'					=> (LANGUAGE == 'EN') ? '' : strtolower(LANGUAGE),
  		'table_id'					=> $table_id,
			'header'						=> ft_header_table_edit,
			'is_intro'					=> $this->isMessage() ? 0 : 1,
			'intro'							=> $this->isMessage() ? $this->getMessage() : ft_intro_table_edit,
			'add_definition'		=> $add_definition,
			'definitions'				=> $definitions_array,
			'btn_ok'						=> tool_btn_ok,
			'btn_abort'					=> tool_btn_abort,
			'btn_edit'					=> ft_btn_edit,
			'abort_location'		=> $this->page_link,
			'sorter_intro'			=> ft_intro_definition_sort,
			'intro_rows_add'		=> $edit ? sprintf(ft_intro_row_edit, $_REQUEST[self::request_edit_detail]) : ft_intro_row_add,
			'intro_rows_list'		=> ft_intro_rows_list,
			'edit_row'					=> $edit_row,
			'text_active'				=> ft_text_active,
			'text_copy'					=> ft_text_copy,
			'rows'							=> $row_array,
			'table_delete'			=> $table_delete,
			'edit_detail'				=> array(	'name' => self::request_edit_detail,
																		'value'=> isset($_REQUEST[self::request_edit_detail]) ? $_REQUEST[self::request_edit_detail] : -1)
		);
		
		return $this->getTemplate('backend.table.edit.htt', $data);
	} // dlgEdit()
	
	private function checkPermaLink($homepage, $row_id, $table_id, $old_perma_link, &$new_perma_link, &$message) {
		
		$permaLink = new permaLink();
		if (!empty($new_perma_link) && empty($homepage)) {
			// es ist keine Homepage angegeben, permaLink wird nicht uebernommen
			$new_perma_link = '';
			$message .= ft_msg_permalink_missing_homepage;
			return false;
		}
		else {
			// es kann ein permaLink verwendet werden
			$create_permaLink = false;
			$delete_permaLink = false;
			// URL fuer die Detailseite zusammenstellen
			$url = sprintf(	'%s%s%s?%s',
											WB_URL,	PAGES_DIRECTORY, 
											$homepage,
											http_build_query(array(	tableFrontend::request_action 	=> tableFrontend::action_detail,
											 												dbFlexTableRow::field_id 				=> $row_id,
											 												dbFlexTableRow::field_table_id	=> $table_id)));
			if (empty($old_perma_link) && !empty($new_perma_link)) {
				// neuen permaLink anlegen
				$create_permaLink = true; 
			}	
			elseif(!empty($old_perma_link) && !empty($new_perma_link) && ($old_perma_link != $new_perma_link)) {
				// permaLink hat sich geaendert
				$delete_permaLink = true;
				$create_permaLink = true;
			}
			elseif (!empty($old_perma_link) && empty($new_perma_link)) {
				// permaLink soll geloescht werden
				$delete_permaLink = true;
			}
		}

		if ($delete_permaLink) {
			// permaLink loeschen
			if (!$permaLink->deletePermaLink($old_perma_link)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $permaLink->getError()));
				return false;
			}
			$message .= sprintf(ft_msg_permalink_deleted, $old_perma_link);
		}
		
		if ($create_permaLink) {
			// neuen permaLink anlegen
			$pid = -1;
			if (!$permaLink->createPermaLink($url, $new_perma_link, 'flexTable', dbPermaLink::type_addon, $pid, permaLink::use_request)) {
				if ($permaLink->isError()) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $permaLink->getError()));
					return false;
				}
				$message .= $permaLink->getMessage();
				// permaLink zuruecksetzen
				$new_perma_link = '';
			}
			else {
				$message .= sprintf(ft_msg_permalink_created, $new_perma_link);
			}
		}
	} // checkPermaLink()
	
	public function checkEdit() { 
		
		global $dbFlexTable;
		global $dbFlexTableDefinition;
		global $dbFlexTableRow;
		global $dbFlexTableCell;
		global $kitLibrary;
		
		$table_id = isset($_REQUEST[dbFlexTable::field_id]) ? (int) $_REQUEST[dbFlexTable::field_id] : -1;
		
		if ($table_id > 0) {
			if (isset($_REQUEST[self::request_delete_table])) {
				// tabelle soll geloescht werden
				if ($_REQUEST[self::request_delete_table] == $table_id) {
					$where = array(dbFlexTable::field_id => $table_id);
					if (!$dbFlexTable->sqlDeleteRecord($where)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
						return false;
					}
				}
				unset($_REQUEST[dbFlexTable::field_id]);
				$this->setMessage(sprintf(ft_msg_table_deleted, $table_id));
				return $this->dlgEdit();
			}
			$where = array(dbFlexTable::field_id => $table_id);
			$table = array();
			if (!$dbFlexTable->sqlSelectRecord($where, $table)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
				return false;
			}
			if (count($table) > 0) {
				$table = $table[0];
			}
			else {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(tool_error_id_invalid, $table_id)));
				return false;
			}
		}
		else {
			$table = $dbFlexTable->getFields();
		}
		$checked = true;
		$message = '';
		
		// ignore timestamp
		unset($table[dbFlexTable::field_timestamp]);
		
		foreach ($table as $field => $value) {
			switch($field):
			case dbFlexTable::field_id:
				$table[$field] = $table_id;
				break;
			case dbFlexTable::field_title:
			case dbFlexTable::field_keywords:
			case dbFlexTable::field_description:
			case dbFlexTable::field_homepage:
				$table[$field] = isset($_REQUEST[$field]) ? $_REQUEST[$field] : '';
				break;
			case dbFlexTable::field_name:
				$table[$field] = isset($_REQUEST[$field]) ? $_REQUEST[$field] : '';
				if (empty($table[$field])) {
  				$message .= ft_msg_table_name_empty;
  				$checked = false;
  				break;
  			}
  			$name = str_replace(' ', '_', strtolower(media_filename(trim($table[$field]))));
  			$SQL = sprintf( "SELECT %s FROM %s WHERE %s='%s'",
  											dbFlexTable::field_id,
  											$dbFlexTable->getTableName(),
  											dbFlexTable::field_name,
  											$name);
  			$result = array();
  			if (!$dbFlexTable->sqlExec($SQL, $result)) {
  				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError())); 
  				return false;
  			}
  			if (count($result) > 0) {
  				if (($table_id > 0) && ($result[0][dbFlexTable::field_id] != $table_id)) {
  					// Umfrage kann nicht umbenannt werden, der Bezeichner wird bereits verwendet
  					$message .= sprintf(ft_msg_table_name_rename_rejected, $name, $result[0][dbFlexTable::field_id]);
  					unset($_REQUEST[$field]);
  					$checked = false;
  					break;
  				}
  				elseif ($table_id < 1) {
  					// Der Bezeichner wird bereits verwendet
  					$message .= sprintf(ft_msg_table_name_rejected, $name, $result[0][dbFlexTable::field_id]);
  					unset($_REQUEST[$field]);
  					$checked = false;
  					break; 
  				}
  			}
  			$table[$field] = $name;				
				break;
			default:
				// nothing to do
				continue;
			endswitch;
		}
		
		// Tabelle einfuegen oder aktualisieren
		if ($checked) {
			if ($table[dbFlexTable::field_id] > 0) {
				// Datensatz aktualisieren
				$where = array(dbFlexTable::field_id => $table[dbFlexTable::field_id]);
				if (!$dbFlexTable->sqlUpdateRecord($table, $where)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
					return false;
				}
				$message .= sprintf(ft_msg_record_updated, $table_id);
			}
			else {
				// neuer Datensatz
				if (!$dbFlexTable->sqlInsertRecord($table, $table_id)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
					return false;
				}
				$message .= sprintf(ft_msg_record_inserted, $table_id);
				$_REQUEST[dbFlexTable::field_id] = $table_id;
			}
		}
		else {
			// Abbruch wegen Fehlern...
			$this->setMessage($message);
			return $this->dlgEdit();
		}

		// Definitionsfelder ?
		$definitions = array();
		$SQL = sprintf( "SELECT * FROM %s WHERE %s='%s' ORDER BY FIND_IN_SET(%s, '%s')",
										$dbFlexTableDefinition->getTableName(),
										dbFlexTableDefinition::field_table_id,
										$table_id,
										dbFlexTableDefinition::field_id,
										$table[dbFlexTable::field_definitions]);
		if (!$dbFlexTableDefinition->sqlExec($SQL, $definitions)) {
			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableDefinition->getError()));
			return false;
		}
		foreach ($definitions as $definition) {
			$def_id = $definition[dbFlexTableDefinition::field_id];
			if (isset($_REQUEST[sprintf('%s_%s', self::request_active_definition, $def_id)])) {
				// ok - Datensatz pruefen
				$checked = true;
				foreach ($dbFlexTableDefinition->getFields() as $field => $value) { 
					switch ($field):
					case dbFlexTableDefinition::field_name:
						if (empty($_REQUEST[sprintf('%s_%s', $field, $def_id)])) {
							$message .= ft_msg_cell_name_empty;
							$checked = false;
						}
						$name = str_replace(' ', '_', strtolower(media_filename(trim($_REQUEST[sprintf('%s_%s', $field, $def_id)]))));
  					// Duplikate sind erlaubt, keine weitere Pruefung
  					$definition[$field] = $name;
						break;
					case dbFlexTableDefinition::field_head:
						$value = (isset($_REQUEST[sprintf('%s_%s', $field, $def_id)])) ? $_REQUEST[sprintf('%s_%s', $field, $def_id)] : '';
						if (empty($value)) {
							$message .= ft_msg_cell_head_empty;
							$checked = false;
						}
						$definition[$field] = $value;
						break;
					case dbFlexTableDefinition::field_table_cell:
						$cell = (isset($_REQUEST[sprintf('%s_%s', $field, $def_id)])) ? dbFlexTableDefinition::cell_true : dbFlexTableDefinition::cell_false;
						$definition[$field] = $cell;
						break;
					case dbFlexTableDefinition::field_description:
					case dbFlexTableDefinition::field_title:
						$value = (isset($_REQUEST[sprintf('%s_%s', $field, $def_id)])) ? $_REQUEST[sprintf('%s_%s', $field, $def_id)] : '';
						$definition[$field] = $value;
						break;
					default:
						continue;
					endswitch;
				}
				if ($checked) {
					// Datensatz aktualisieren
					$where = array(dbFlexTableDefinition::field_id => $def_id);
					if (!$dbFlexTableDefinition->sqlUpdateRecord($definition, $where)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableDefinition->getError()));
						return false;
					}
					//$message .= sprintf(ft_msg_cell_definition_updated, $def_id);
					// Zellen aktualisieren
					$data = array(
						dbFlexTableCell::field_definition_name => $definition[dbFlexTableDefinition::field_name],
						dbFlexTableCell::field_table_cell => $definition[dbFlexTableDefinition::field_table_cell]
					);
					$where = array(dbFlexTableCell::field_definition_id => $definition[dbFlexTableDefinition::field_id]);
					if (!$dbFlexTableCell->sqlUpdateRecord($data, $where)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
						return false;
					}
				}
			}
			else {
				// existiert nicht, soll geloescht werden!
				$where = array(dbFlexTableDefinition::field_id => $def_id);
				if (!$dbFlexTableDefinition->sqlDeleteRecord($where)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableDefinition->getError()));
					return false;
				}
				// Zellen aktualisieren!
				$where = array(
					dbFlexTableCell::field_definition_id => $def_id
				);
				if (!$dbFlexTableCell->sqlDeleteRecord($where)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
					return false;
				}
				$message .= sprintf(ft_msg_cell_definition_removed, $def_id);
			}
		}
		
		
		// Neues Definitionsfeld hinzufuegen?
		if (isset($_REQUEST[self::request_add_definition]) && ($_REQUEST[self::request_add_definition] != dbFlexTableDefinition::type_undefined)) {
			$data = array(
				dbFlexTableDefinition::field_table_id => $table_id,
				dbFlexTableDefinition::field_table_cell => dbFlexTableDefinition::cell_true,
				dbFlexTableDefinition::field_type => (int) $_REQUEST[self::request_add_definition]
			);
			$def_id = -1;
			// Datensatz einfuegen
			if (!$dbFlexTableDefinition->sqlInsertRecord($data, $def_id)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableDefinition->getError()));
				return false;
			}
			// Datensatz aktualisieren
			$data = array(
				dbFlexTableDefinition::field_head => sprintf('head_%d', $def_id),
				dbFlexTableDefinition::field_name => sprintf('name_%d', $def_id)
			);
			$where = array(dbFlexTableDefinition::field_id => $def_id);
			if (!$dbFlexTableDefinition->sqlUpdateRecord($data, $where)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableDefinition->getError()));
				return false;
			}
			
			// Tabelle aktualisieren
			$defs = (empty($table[dbFlexTable::field_definitions])) ? $def_id : $table[dbFlexTable::field_definitions].",$def_id";
			$data = array(dbFlexTable::field_definitions => $defs);
			$where = array(dbFlexTable::field_id => $table_id);
			if (!$dbFlexTable->sqlUpdateRecord($data, $where)) {
				$this->setError(sprintf('[%s - %] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
				return false;
			}
			
			// Zeilen aktualisieren
			$rows = array();
			$where = array(dbFlexTableRow::field_table_id => $table_id);
			if (!$dbFlexTableRow->sqlSelectRecord($where, $rows)) {
				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableRow->getError()));
				return false;
			}
			foreach ($rows as $row) {
				$data = array(
					dbFlexTableCell::field_definition_id => $def_id,
					dbFlexTableCell::field_definition_name => sprintf('name_%d', $def_id),
					dbFlexTableCell::field_definition_type => (int) $_REQUEST[self::request_add_definition],
					dbFlexTableCell::field_table_id => $table_id,
					dbFlexTableCell::field_row_id => $row[dbFlexTableRow::field_id]
				);
				if (!$dbFlexTableCell->sqlInsertRecord($data)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
					return false;
				}
			}
			
			// Mitteilung
			$message .= ft_msg_cell_definition_added;			 
		}
		// Zeilen durchlaufen und auf Aenderungen pruefen
		$where = array(dbFlexTableRow::field_table_id => $table_id);
		$rows = array();
		if (!$dbFlexTableRow->sqlSelectRecord($where, $rows)) {
			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableRow->getError()));
			return false;
		}
		foreach ($rows as $row) {  
			if (!isset($_REQUEST[sprintf('%s_%s', dbFlexTableRow::field_id, $row[dbFlexTableRow::field_id])])) {
				// Zeile entfernen
				$row_id = $row[dbFlexTableRow::field_id];
				$where = array(dbFlexTableRow::field_id => $row_id);
				if (!$dbFlexTableRow->sqlDeleteRecord($where)) {
					$this->setError(sprintf('[%s - %] %s', __METHOD__, __LINE__, $dbFlexTableRow->getError()));
					return false;
				}
				$where = array(	dbFlexTableCell::field_row_id => $row_id,
												dbFlexTableCell::field_table_id => $row[dbFlexTableRow::field_table_id]);
				if (!$dbFlexTableCell->sqlDeleteRecord($where)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
					return false;
				}
				$message .= sprintf(ft_msg_row_deleted, $row_id);
			}
			if (isset($_REQUEST[sprintf('copy_row_%s', $row[dbFlexTableRow::field_id])])) {
				// Zeile kopieren
				$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' ORDER BY FIND_IN_SET(%s, '%s')", 
												$dbFlexTableCell->getTableName(),
												dbFlexTableCell::field_row_id,
												$row[dbFlexTableRow::field_id],
												dbFlexTableCell::field_definition_id,
												$table[dbFlexTable::field_definitions]);
				$cells = array();
				if (!$dbFlexTableCell->sqlExec($SQL, $cells)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
					return false;
				}
				$add = false;
				$new_row_id = -1;
				foreach ($cells as $cell) {
					$def_id = $cell[dbFlexTableCell::field_definition_id];
					if ($add == false) {
						// neue Zeile einfuegen
						$data = array(dbFlexTableRow::field_table_id => $table_id);
						if (!$dbFlexTableRow->sqlInsertRecord($data, $new_row_id)) {
							$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableRow->getError()));
							return false;
						}
						$add = true;
					}
					$data = array();
					foreach ($dbFlexTableCell->getFields() as $key => $value) {
						switch ($key):
						case dbFlexTableCell::field_id:
						case dbFlexTableCell::field_timestamp:
							// nothing to do...
							break;
						case dbFlexTableCell::field_row_id:
							// use new row ID
							$data[$key] = $new_row_id; 
							break;
						default:
							// take the old values
							$data[$key] = $cell[$key];
						endswitch;
					}
					if (!$dbFlexTableCell->sqlInsertRecord($data)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
						return false;
					}
				}
				$message .= sprintf(ft_msg_row_copied, $row[dbFlexTableRow::field_id], $new_row_id);
				// Zeile kopieren
			}
		}
		// neue Zeilen hinzufuegen oder bestehende Zeilen bearbeiten?
		if (isset($_REQUEST[self::request_edit_detail]) && $_REQUEST[self::request_edit_detail] > 0) {
			// es wird eine bestehende Zeile bearbeitet
			$row_id = $_REQUEST[self::request_edit_detail];
			unset($_REQUEST[self::request_edit_detail]);
			$edit = true;
			$copy = false;
			$copy_cells = array();
			$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' AND %s='%s' ORDER BY FIND_IN_SET(%s, '%s')",
											$dbFlexTableCell->getTableName(),
											dbFlexTableCell::field_table_id,
											$table_id,
											dbFlexTableCell::field_row_id,
											$row_id,
											dbFlexTableCell::field_definition_id,
											$table[dbFlexTable::field_definitions]
										);
			$edit_array = array();							
			if (!$dbFlexTableCell->sqlExec($SQL, $edit_array)) {
				$this->setError(sprintf('[%s - %] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
				return false;
			}
			$i = 0;
			foreach ($definitions as $definition) {
				$def_id = $definition[dbFlexTableDefinition::field_id];
				$data = $edit_array[$i];
				$i++;
				if (isset($_REQUEST[sprintf('cell_%s', $def_id)])) {
					$value = $_REQUEST[sprintf('cell_%s', $def_id)];
					if ($data[dbFlexTableCell::field_definition_name] == 'permalink') {
						// permaLink pruefen
						if (!$this->checkPermaLink($table[dbFlexTable::field_homepage], $row_id, $table_id, $data[dbFlexTableCell::field_char], $value, $message) && $this->isError()) return false;
					}
					$dbFlexTableCell->setCellValueByType($data, $value);
					$where = array(dbFlexTableCell::field_id => $data[dbFlexTableCell::field_id]);
					if (!$dbFlexTableCell->sqlUpdateRecord($data, $where)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
						return false;
					}
					if (isset($_REQUEST[sprintf('copy_cell_%d', $def_id)])) {
						// Zelle kopieren
						$copy = true;
						$copy_cells[] = $def_id;
					}
					
				}
			} // foreach
			if ($copy) {
				// einzelne Zellen in eine neue Zeile uebernehmen
				$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' ORDER BY FIND_IN_SET(%s, '%s')", 
												$dbFlexTableCell->getTableName(),
												dbFlexTableCell::field_row_id,
												$row_id,
												dbFlexTableCell::field_definition_id,
												$table[dbFlexTable::field_definitions]);
				$cells = array();
				if (!$dbFlexTableCell->sqlExec($SQL, $cells)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
					return false;
				}
				$add = false;
				$new_row_id = -1;
				$copied_cells = array();
				foreach ($cells as $cell) {
					$def_id = $cell[dbFlexTableCell::field_definition_id];
					if ($add == false) {
						// neue Zeile einfuegen
						$data = array(dbFlexTableRow::field_table_id => $table_id);
						if (!$dbFlexTableRow->sqlInsertRecord($data, $new_row_id)) {
							$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableRow->getError()));
							return false;
						}
						$add = true;
					}
					$data = array();
					foreach ($dbFlexTableCell->getFields() as $key => $value) {
						switch ($key):
						case dbFlexTableCell::field_id:
						case dbFlexTableCell::field_timestamp:
							// nothing to do...
							break;
						case dbFlexTableCell::field_row_id:
							// use new row ID
							$data[$key] = $new_row_id; 
							break;
						case dbFlexTableCell::field_definition_id:
						case dbFlexTableCell::field_definition_name:
						case dbFlexTableCell::field_definition_type:
						case dbFlexTableCell::field_row_id:
						case dbFlexTableCell::field_table_cell:
						case dbFlexTableCell::field_table_id:
							$data[$key] = $cell[$key];
							break;
						case dbFlexTableCell::field_char:
						case dbFlexTableCell::field_datetime:
						case dbFlexTableCell::field_float:
						case dbFlexTableCell::field_html:
						case dbFlexTableCell::field_integer:
						case dbFlexTableCell::field_media_link:
						case dbFlexTableCell::field_text:	
							// take the value?
							if (in_array($def_id, $copy_cells)) {
								$data[$key] = $cell[$key];
								if (!in_array($cell[dbFlexTableCell::field_definition_name], $copied_cells)) $copied_cells[] = $cell[dbFlexTableCell::field_definition_name];
							}
							else {
								$dbFlexTableCell->setCellValueByType($data, '');
							}
						endswitch;
					}
					if (!$dbFlexTableCell->sqlInsertRecord($data)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
						return false;
					}
				}
				// Benachrichtigung zusammenstellen
				$copied_names = implode(', ', $copied_cells);
				$message .= sprintf(ft_msg_cells_copied_to_row, $copied_names, $row_id, $new_row_id);
			}
		} // EDIT ROW
		else {
			// es wird eine neue Zeile eingefuegt
			$data = array();
			$add = false;
			$row_id = -1;
			$start = true;
			foreach ($definitions as $definition) {
				$def_id = $definition[dbFlexTableDefinition::field_id];
				if ($start && (isset($_REQUEST[sprintf('cell_%s', $def_id)]) && empty($_REQUEST[sprintf('cell_%s', $def_id)]))) {
					// das erste Feld darf nicht leer sein!
					break;
				}
				else {
					$start = false;
				}
				
				if (isset($_REQUEST[sprintf('cell_%s', $def_id)])) { 
					$value = $_REQUEST[sprintf('cell_%s', $def_id)]; 
					if ($add == false) {
						// neue Zeile einfuegen
						$data = array(dbFlexTableRow::field_table_id => $table_id);
						if (!$dbFlexTableRow->sqlInsertRecord($data, $row_id)) {
							$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableRow->getError()));
							return false;
						}
						$add = true;
					}
					$data = array(
						dbFlexTableCell::field_definition_id => $definition[dbFlexTableDefinition::field_id],
						dbFlexTableCell::field_definition_type => $definition[dbFlexTableDefinition::field_type],
						dbFlexTableCell::field_definition_name => $definition[dbFlexTableDefinition::field_name],
						dbFlexTableCell::field_table_cell => $definition[dbFlexTableDefinition::field_table_cell],
						dbFlexTableCell::field_row_id => $row_id,
						dbFlexTableCell::field_table_id => $table_id
					);
					if ($data[dbFlexTableCell::field_definition_name] == 'permalink') {
						// permaLink pruefen
						if (!$this->checkPermaLink($table[dbFlexTable::field_homepage], $row_id, $table_id, '', $value, $message) && $this->isError()) return false;
					}
					$dbFlexTableCell->setCellValueByType($data, $value);
					if (!$dbFlexTableCell->sqlInsertRecord($data)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
						return false;
					}
				}
			}
		}	
		
		$this->setMessage($message);
		return $this->dlgEdit();
	} // checkEdit()
	
  /**
   * Dialog zur Konfiguration und Anpassung von flexTable
   * 
   * @return STR dialog
   */
  public function dlgConfig() {
		global $dbFlexTableCfg;
		$SQL = sprintf(	"SELECT * FROM %s WHERE NOT %s='%s' ORDER BY %s",
										$dbFlexTableCfg->getTableName(),
										dbFlexTableCfg::field_status,
										dbFlexTableCfg::status_deleted,
										dbFlexTableCfg::field_name);
		$config = array();
		if (!$dbFlexTableCfg->sqlExec($SQL, $config)) {
			$this->setError($dbFlexTableCfg->getError());
			return false;
		}
		$count = array();
		$header = array(
			'identifier'	=> tool_header_cfg_identifier,
			'value'				=> tool_header_cfg_value,
			'description'	=> tool_header_cfg_description
		);
		
		$items = array();
		// bestehende Eintraege auflisten
		foreach ($config as $entry) {
			$id = $entry[dbFlexTableCfg::field_id];
			$count[] = $id;
			$value = (isset($_REQUEST[dbFlexTableCfg::field_value.'_'.$id])) ? $_REQUEST[dbFlexTableCfg::field_value.'_'.$id] : $entry[dbFlexTableCfg::field_value];
			$value = str_replace('"', '&quot;', stripslashes($value));
			$items[] = array(
				'id'					=> $id,
				'identifier'	=> constant($entry[dbFlexTableCfg::field_label]),
				'value'				=> $value,
				'name'				=> sprintf('%s_%s', dbFlexTableCfg::field_value, $id),
				'description'	=> constant($entry[dbFlexTableCfg::field_description])  
			);
		}
		$data = array(
			'form_name'						=> 'flex_table_cfg',
			'form_action'					=> $this->page_link,
			'action_name'					=> self::request_action,
			'action_value'				=> self::action_config_check,
			'items_name'					=> self::request_items,
			'items_value'					=> implode(",", $count), 
			'head'								=> tool_header_cfg,
			'intro'								=> $this->isMessage() ? $this->getMessage() : sprintf(tool_intro_cfg, 'flexTable'),
			'is_message'					=> $this->isMessage() ? 1 : 0,
			'items'								=> $items,
			'btn_ok'							=> tool_btn_ok,
			'btn_abort'						=> tool_btn_abort,
			'abort_location'			=> $this->page_link,
			'header'							=> $header
		);
		return $this->getTemplate('backend.config.htt', $data);
	} // dlgConfig()
	
	/**
	 * Ueberprueft Aenderungen die im Dialog dlgConfig() vorgenommen wurden
	 * und aktualisiert die entsprechenden Datensaetze.
	 * 
	 * @return STR DIALOG dlgConfig()
	 */
	public function checkConfig() {
		global $dbFlexTableCfg;
		$message = '';
		// ueberpruefen, ob ein Eintrag geaendert wurde
		if ((isset($_REQUEST[self::request_items])) && (!empty($_REQUEST[self::request_items]))) {
			$ids = explode(",", $_REQUEST[self::request_items]);
			foreach ($ids as $id) {
				if (isset($_REQUEST[dbFlexTableCfg::field_value.'_'.$id])) {
					$value = $_REQUEST[dbFlexTableCfg::field_value.'_'.$id];
					$where = array();
					$where[dbFlexTableCfg::field_id] = $id; 
					$config = array();
					if (!$dbFlexTableCfg->sqlSelectRecord($where, $config)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCfg->getError()));
						return false;
					}
					if (sizeof($config) < 1) {
						$this->setError(sprintf(tool_error_cfg_id, $id));
						return false;
					}
					$config = $config[0];
					if ($config[dbFlexTableCfg::field_value] != $value) {
						// Wert wurde geaendert
							if (!$dbFlexTableCfg->setValue($value, $id) && $dbFlexTableCfg->isError()) {
								$this->setError($dbFlexTableCfg->getError());
								return false;
							}
							elseif ($dbFlexTableCfg->isMessage()) {
								$message .= $dbFlexTableCfg->getMessage();
							}
							else {
								// Datensatz wurde aktualisiert
								$message .= sprintf(tool_msg_cfg_id_updated, $config[dbFlexTableCfg::field_name]);
							}
					}
				}
			}		
		}		
		$this->setMessage($message);
		return $this->dlgConfig();
	} // checkConfig()
  
	
} // class tableBackend

?>