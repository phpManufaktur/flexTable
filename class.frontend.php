<?php
/**
 * flexTable
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de
 * @copyright 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id$
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

if (!LEPTON_2) require_once(WB_PATH.'/modules/droplets_extension/interface.php');

class tableFrontend {
    
    const request_action			= 'act';
	const request_filter			= 'flt';

	const action_default			= 'def';
	const action_table				= 'tbl';
	const action_detail				= 'det';
	const action_view_id			= 'id';
	
	private $page_link 								= '';
	private $img_url									= '';
	private $template_path						= '';
	private $error										= '';
	private $message									= '';
	private $media_url								= '';
	private $media_path								= '';
	
	private $media_file_types					= array();
	private $media_image_types				= array();
	private $media_doc_types					= array();
	
	const mode_table										= 'table';
	const mode_detail										= 'detail';
	
	const param_preset									= 'preset';
	const param_name										= 'name';
	const param_css											= 'css';
	const param_js = 'js';
	const param_search									= 'search';
	const param_page_header							= 'page_header';
	const param_table_header						= 'table_header';
	const param_table_filter						= 'table_filter';
	const param_mode										= 'mode';
	const param_rows										= 'rows';	
	const param_show_last								= 'show_last';		
	
	private $params = array(
		self::param_preset	=> 1, 
		self::param_name => '',
		self::param_css	=> true,
	    self::param_js => true,
		self::param_search										=> true,
		self::param_page_header								=> true,
		self::param_table_header							=> true,
		self::param_table_filter							=> true,
		self::param_mode											=> self::mode_table,
		self::param_rows											=> '',
		self::param_show_last									=> 0
	);
	
	const filter_none				= 'NONE';
	const filter_asc				= 'ASC';
	const filter_desc				= 'DESC';
	
	public function __construct() {
		global $kitLibrary;
		global $dbFlexTableCfg;
		$url = '';
		$_SESSION['FRONTEND'] = true;	
		$kitLibrary->getPageLinkByPageID(PAGE_ID, $url);
		$this->page_link = $url; 
		$this->template_path = WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/htt/'.$this->params[self::param_preset].'/'.FLEX_TABLE_LANGUAGE.'/' ;
		$this->img_url = WB_URL. '/modules/'.basename(dirname(__FILE__)).'/images/';
		$this->media_url = WB_URL.MEDIA_DIRECTORY.'/'.$dbFlexTableCfg->getValue(dbFlexTableCfg::cfgMediaDirectory).'/';
		$this->media_path = WB_PATH.MEDIA_DIRECTORY.'/'.$dbFlexTableCfg->getValue(dbFlexTableCfg::cfgMediaDirectory).'/';
		date_default_timezone_set(tool_cfg_time_zone);
		$this->media_doc_types = $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgDocFileTypes);
		$this->media_image_types = $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgImageFileTypes);
		$this->media_file_types = array_merge($this->media_image_types, $this->media_doc_types);
	} // __construct()
	
	public function getParams() {
		return $this->params;
	} // getParams()
	
	public function setParams($params = array()) {
		$this->params = $params;
		$this->template_path = WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/htt/'.$this->params[self::param_preset].'/'.FLEX_TABLE_LANGUAGE.'/';
		if (!file_exists($this->template_path)) {
			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(ft_error_preset_not_exists, '/modules/'.basename(dirname(__FILE__)).'/htt/'.$this->params[self::param_preset].'/'.FLEX_TABLE_LANGUAGE.'/')));
			return false;
		}
		// if the mode is not set, switch to "table" mode
		if (!isset($this->params[self::param_mode])) $this->params[self::param_mode] = self::mode_table;
		return true;
	} // setParams()
	
	/**
     * Set $this->error to $error
     * 
     * @param STR $error
     */
    public function setError($error) {
        $this->error = $error;
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
       * Gibt das gewuenschte Template zurueck
       * 
       * @param STR $template
       * @param ARRAY $template_data
       */
      public function getTemplate($template, $template_data) {
      	global $parser;
      	try {
      		$result = $parser->get($this->template_path.$template, $template_data); 
      	} catch (Exception $e) { 
      		$this->setError(sprintf(ft_error_template_error, $template, $e->getMessage()));
      		return false;
      	}
      	return $result;
      } // getTemplate()
      
      private function setTempVars($vars=array()) {
    		$_SESSION[self::session_temp_vars] = http_build_query($vars);
    	} // setTempVars()
    	
    	private function getTempVars() {
    		if (isset($_SESSION[self::session_temp_vars])) {
    			parse_str($_SESSION[self::session_temp_vars], $vars);
    			foreach ($vars as $key => $value) {
    				if (!isset($_REQUEST[$key])) $_REQUEST[$key] = $value;
    			}
    			unset($_SESSION[self::session_temp_vars]);
    		}
    	} // getTempVars()
    	
    	/**
       * Verhindert XSS Cross Site Scripting
       * 
       * @param REFERENCE ARRAY $request
       * @return ARRAY $request
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
	
  /**
   * ACTION HANDLER
   * @return STR result
   */
  public function action() { 
      global $wb;
  	
  	$html_allowed = array();
	  foreach ($_REQUEST as $key => $value) {
	  	if (!in_array($key, $html_allowed)) {
	  		$_REQUEST[$key] = $this->xssPrevent($value);	  			
	  	} 
	  }
	  
	  // check the mode
	  if ($this->params[self::param_mode] == self::mode_table) {
	  	$action = isset($_REQUEST[self::request_action]) ? $_REQUEST[self::request_action] : self::action_default;
	  }
	  else {
	  	// detail mode
	  	$action = isset($_REQUEST[self::request_action]) ? $_REQUEST[self::request_action] : self::action_view_id;
	  }
	  
  	// CSS laden?
  	if (!LEPTON_2) { 
        if ($this->params[self::param_css]) {
            if (!is_registered_droplet_css('flex_table', PAGE_ID)) {
                register_droplet_css('flex_table', PAGE_ID, 'flex_table', 'flex_table.css');
            }
            if (!is_registered_droplet_js('flex_table', PAGE_ID)) {
                register_droplet_js('flex_table', PAGE_ID, 'flex_table', 'flex_table.js');
            }
        }
        elseif (is_registered_droplet_css('flex_table', PAGE_ID)) {
		    unregister_droplet_css('flex_table', PAGE_ID);
		    unregister_droplet_js('flex_table', PAGE_ID);
        }
                
        // Register Droplet for the WebsiteBaker Search Function
  	    if ($this->params[self::param_search]) {
  		    if (!is_registered_droplet_search('flex_table', PAGE_ID)) {  
	 			register_droplet_search('flex_table', PAGE_ID, 'flex_table');
  		    }
 		}
 		elseif (is_registered_droplet_search('flex_table', PAGE_ID)) {
 			unregister_droplet_search('flex_table', PAGE_ID);
 		}
 		
 		// Seiteninformationen bereitstellen?
	    if ($this->params[self::param_page_header]) {
	  	    if (!is_registered_droplet_header('flex_table', PAGE_ID)) {
 				register_droplet_header('flex_table', PAGE_ID, 'flex_table');
	  	    }
	    }
	    else {
	  	    if (is_registered_droplet_header('flex_table', PAGE_ID)) {
  			    unregister_droplet_header('flex_table', PAGE_ID);
			}
	    }
  	}
  	else {
  	    // LEPTON 2.x
  	    if ($this->params[self::param_css]) {
  	        // register for loading CSS file
  	        $wb->get_helper('DropLEP')->register_css(PAGE_ID, 'flex_table', 'flex_table', 'flex_table.css');
  	        $wb->get_helper('DropLEP')->register_js(PAGE_ID, 'flex_table', 'flex_table', 'flex_table.js');
  	    } 
  	    else {
  	        $wb->get_helper('DropLEP')->unregister_css(PAGE_ID, 'flex_table', 'flex_table', 'flex_table.css');
  	        $wb->get_helper('DropLEP')->unregister_js(PAGE_ID, 'flex_table', 'flex_table', 'flex_table.js');  	         
  	    }
  	    if ($this->params[self::param_search]) {    
  	        // register for LEPTON search
  	        $wb->get_helper('DropLEP')->register_for_search(PAGE_ID, 'flex_table', 'flex_table');
  	    }
  	    else {
  	        $wb->get_helper('DropLEP')->unregister_for_search(PAGE_ID, 'flex_table');
  	    }
  	    if ($this->params[self::param_page_header]) {
  	        // register for sending page title, description and keywords
  	        $wb->get_helper('Addons')->register_page_title(PAGE_ID, 'flexTable', 'flex_table');
  	        $wb->get_helper('Addons')->register_page_description(PAGE_ID, 'flexTable', 'flex_table');
  	        $wb->get_helper('Addons')->register_page_keywords(PAGE_ID, 'flexTable', 'flex_table');
  	    }
  	    else {
  	        $wb->get_helper('Addons')->unregister_page_title(PAGE_ID, 'flex_table');
  	        $wb->get_helper('Addons')->unregister_page_description(PAGE_ID, 'flex_table');
  	        $wb->get_helper('Addons')->unregister_page_keywords(PAGE_ID, 'flex_table');
  	    }
  	}
    
    switch ($action):
  	case self::action_view_id:
  		$result = $this->showID();
  		break;
  	case self::action_detail:
  		$result = $this->showDetail();
  		break;
  	case self::action_table:
	  case self::action_default:
		default:
		 	$result = $this->showTable();
		endswitch;
  	
		if ($this->isError()) {
  		$data = array('error' => $this->getError());
  		$result = $this->getTemplate('error.htt', $data);
  	}
		return $result;
  } // action
  
  /**
   * Stellt die Daten der Tabelle zusammen und uebergibt sie an das Template table.htt
   * 
   * @return STR flexTable
   */
  public function showTable() {
  	global $dbFlexTable;
  	global $dbFlexTableCell;
  	global $dbFlexTableDefinition;
  	global $dbFlexTableCfg;
  	
  	if (empty($this->params[self::param_name])) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, ft_error_table_name_missing));
  		return false;
  	}
  	
  	// Tabellendaten einlesen
  	$where = array(dbFlexTable::field_name => $this->params[self::param_name]);
  	$table = array();
  	if (!$dbFlexTable->sqlSelectRecord($where, $table)) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
  		return false;
  	}
  	if (count($table) < 1) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(tool_error_id_invalid, -1)));
  		return false;
  	}
  	$table = $table[0];
  	$table_id = $table[dbFlexTable::field_id];
  	
  	// Definitionen einlesen
  	$SQL = sprintf( "SELECT * FROM %s WHERE %s='%s' ORDER BY FIND_IN_SET(%s, '%s')",
  			$dbFlexTableDefinition->getTableName(),
  			dbFlexTableDefinition::field_table_id,
  			$table_id,
  	        dbFlexTableDefinition::field_id,
  	        $table[dbFlexTable::field_definitions]
  	        );
	$definitions = array();
	if (!$dbFlexTableDefinition->sqlExec($SQL, $definitions)) {
			$this->setError(sprintf('[%s - %] %s', __METHOD__, __LINE__, $dbFlexTableDefinition->getError()));
			return false;
	}
	$def_array = array();
	$active_filter = '';
	$active_filter_id = -1;
  	foreach ($definitions as $def) {
  		if ($def[dbFlexTableDefinition::field_table_cell] == dbFlexTableDefinition::cell_true) {
  			// filter anlegen
  			if (isset($_REQUEST[sprintf('%s_%s', self::request_filter, $def[dbFlexTableDefinition::field_id])]) &&
  					$_REQUEST[sprintf('%s_%s', self::request_filter, $def[dbFlexTableDefinition::field_id])] != self::filter_none) {
  				$active_filter = $_REQUEST[sprintf('%s_%s', self::request_filter, $def[dbFlexTableDefinition::field_id])];
  				$active_filter_id = $def[dbFlexTableDefinition::field_id];	
  			}
  			$act_filter = isset($_REQUEST[sprintf('%s_%s', self::request_filter, $def[dbFlexTableDefinition::field_id])]) ? $_REQUEST[sprintf('%s_%s', self::request_filter, $def[dbFlexTableDefinition::field_id])] : '';
  			$field = $dbFlexTableCell->getFieldNameByType($def[dbFlexTableDefinition::field_type]);
  			if (!empty($field) && (($field != dbFlexTableCell::field_text) && ($field != dbFlexTableCell::field_html))) {
		  		$SQL = sprintf( "SELECT %s AS filter FROM %s WHERE %s='%s' AND %s='%s' GROUP BY %s ORDER BY %s ASC",
		  		        $field,
						$dbFlexTableCell->getTableName(),
						dbFlexTableCell::field_table_id,
						$table_id,
						dbFlexTableCell::field_definition_id,
						$def[dbFlexTableDefinition::field_id],
						$field,
						$field
		  		        );
				$result = array();
					if (!$dbFlexTableCell->sqlExec($SQL, $result)) {
						$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
						return false;
					}
					
					$filter_array = array(
						self::filter_none => array('value' => self::filter_none, 	'text' => ft_filter_none, 'selected' => ($act_filter == self::filter_none) ? 1 : 0),
						self::filter_asc	=> array('value' => self::filter_asc, 	'text' => ft_filter_asc, 'selected' => ($act_filter == self::filter_asc) ? 1 : 0),
						self::filter_desc	=> array('value' => self::filter_desc, 	'text' => ft_filter_desc, 'selected' => ($act_filter == self::filter_desc) ? 1 : 0)
					);
		  		foreach ($result as $item) {
		  			if ((($field == dbFlexTableCell::field_float) || ($field == dbFlexTableCell::field_integer)) && empty($item['filter'])) continue;
		  			if (empty($item['filter'])) continue;
		  			$txt = str_replace('||', '', $item['filter']);
		  			$value = urlencode($item['filter']);
		  			$filter_array[$item['filter']] = array(
		  			        'value' => $value, 
		  			        'text' => $txt, 
		  			        'selected' => ($act_filter == $item['filter']) ? 1 : 0);
		  		}
	  		}
	  		else {
	  			// keinen Filter setzen!
	  			$filter_array = array();
	  			$filter_array[self::filter_none] = array('value' => self::filter_none, 'text' => ft_filter_none, 'selected' => 0);
	  		}
  			// definitions array fuer das template
	  		$def_array[$def[dbFlexTableDefinition::field_name]] = array(
	  			'id' => $def[dbFlexTableDefinition::field_id],
	  			'name' => $def[dbFlexTableDefinition::field_name],
	  			'class'	=> $def[dbFlexTableDefinition::field_name],
	  			'head' => $def[dbFlexTableDefinition::field_head],
	  			'description' => $def[dbFlexTableDefinition::field_description],
	  			'table_cell' => $def[dbFlexTableDefinition::field_table_cell],
	  			'title'	=> $def[dbFlexTableDefinition::field_title],
	  			'type' => $dbFlexTableDefinition->template_type_array[$def[dbFlexTableDefinition::field_type]],
	  			'filter' => array(
	  			        'name' => sprintf('%s_%s', self::request_filter, $def[dbFlexTableDefinition::field_id]),
	  			        'values'	=> $filter_array),
	  		    'link' => sprintf('%s%s%s=',
	  		            $this->page_link,
	  		            (defined('LEPTON_VERSION') && isset($_GET['leptoken'])) ? sprintf('?leptoken=%s&amp;', $_GET['leptoken']) : '?',
	  		            sprintf('%s_%s', self::request_filter, $def[dbFlexTableDefinition::field_id]))
	  		);
  		}
  	}
  	
  	$table_array = array(
  		'id'						=> $table[dbFlexTable::field_id],
  		'name'					=> $table[dbFlexTable::field_name],
  		'class'					=> $table[dbFlexTable::field_name],
  		'description'		=> $table[dbFlexTable::field_description],
  		'definition'		=> $def_array,
  		'title'					=> $table[dbFlexTable::field_title],
  		'keywords'			=> $table[dbFlexTable::field_keywords],
  		'table_header'	=> ($this->params[self::param_table_header]) ? 1 : 0,
  		'table_filter'	=> ($this->params[self::param_table_filter]) ? 1 : 0  	
  	);
  	if ($active_filter_id == -1) {
	  	$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' AND (%s='%s' OR %s='%s') ORDER BY %s ASC, FIND_IN_SET(%s, '%s')",
											$dbFlexTableCell->getTableName(),
											dbFlexTableCell::field_table_id,
											$table_id,
											dbFlexTableCell::field_table_cell,
											dbFlexTableDefinition::cell_true,
											dbFlexTableCell::field_definition_name,
											'permalink',
											dbFlexTableCell::field_row_id,
											dbFlexTableCell::field_definition_id,
											$table[dbFlexTable::field_definitions]
										);
  	}									
  	else {
  		// Filter verwenden
  		$where = array(dbFlexTableDefinition::field_id => $active_filter_id);
  		$def = array();
  		if (!$dbFlexTableDefinition->sqlSelectRecord($where, $def)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableDefinition->getError()));
  			return false;
  		}
  		$def = $def[0];
  		$field = $dbFlexTableCell->getFieldNameByType($def[dbFlexTableDefinition::field_type]);
	  	if ($active_filter == self::filter_asc || $active_filter == self::filter_desc) {
	  		$SQL = sprintf( "SELECT %s FROM %s WHERE %s='%s' ORDER BY %s %s",
	  										dbFlexTableCell::field_row_id,
	  										$dbFlexTableCell->getTableName(),
	  										dbFlexTableCell::field_definition_id,
	  										$def[dbFlexTableDefinition::field_id],
	  										$field,
	  										$active_filter);
		  	$order = array();
		  	if (!$dbFlexTableCell->sqlExec($SQL, $order)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
					return false;
				}
				$row_order = '';
				foreach ($order as $ord) {
					if (!empty($row_order)) $row_order .= ',';
					$row_order .= $ord[dbFlexTableCell::field_row_id];
				}
				$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' AND (%s='%s' OR %s='%s') ORDER BY FIND_IN_SET(%s, '%s'), FIND_IN_SET(%s, '%s')",
												$dbFlexTableCell->getTableName(),
												dbFlexTableCell::field_table_id,
												$table_id,
												dbFlexTableCell::field_table_cell,
												dbFlexTableDefinition::cell_true,
												dbFlexTableCell::field_definition_name,
												'permalink',
												dbFlexTableCell::field_row_id,
												$row_order,
												dbFlexTableCell::field_definition_id,
												$table[dbFlexTable::field_definitions]
											);
	  	}
	  	else {
	  		// nach Gruppen filtern
	  		$SQL = sprintf( "SELECT %s FROM %s WHERE %s='%s' AND %s='%s' AND %s LIKE '%s' ORDER BY %s ASC",
	  										dbFlexTableCell::field_row_id,
	  										$dbFlexTableCell->getTableName(),
	  										dbFlexTableCell::field_table_id,
	  										$table_id,
	  										dbFlexTableCell::field_definition_name,
	  										$def[dbFlexTableDefinition::field_name],
	  										$field,
	  										$_REQUEST[sprintf('%s_%s', self::request_filter, $active_filter_id)],
	  										dbFlexTableCell::field_row_id);
	  		$order = array();
		  	if (!$dbFlexTableCell->sqlExec($SQL, $order)) {
					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
					return false;
			}
			$row_order = '';
			foreach ($order as $ord) {
				if (!empty($row_order)) $row_order .= ',';
				$row_order .= "'".$ord[dbFlexTableCell::field_row_id]."'";
			}
			$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' AND (%s='%s' OR %s='%s') AND %s IN (%s) ORDER BY %s ASC, FIND_IN_SET(%s, '%s')",
												$dbFlexTableCell->getTableName(),
												dbFlexTableCell::field_table_id,
												$table_id,
												dbFlexTableCell::field_table_cell,
												dbFlexTableDefinition::cell_true,
												dbFlexTableCell::field_definition_name,
												'permalink',
												dbFlexTableCell::field_row_id,
												$row_order,
												dbFlexTableCell::field_row_id,
												dbFlexTableCell::field_definition_id,
												$table[dbFlexTable::field_definitions]
											);				
	  	}	
  	} // Filter
	$rows = array();
	if (!$dbFlexTableCell->sqlExec($SQL, $rows)) {
		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
		return false;
	}
	
	$row_array = array();
	$cells = array();
	$row_id = -1;
	$permalink = '';

	foreach ($rows as $row) {
		if ($row_id == -1) $row_id = $row[dbFlexTableCell::field_row_id];
		if ($row_id != $row[dbFlexTableCell::field_row_id]) {
		    $row_array[$row_id] = array(		        
		            'id' => $row_id,
				    'cells'	=> $cells,
				    'link' => sprintf('%s%s%s', 
				            $this->page_link, 
				            (strpos($this->page_link, '?') === false) ? '?' : '&', 
							http_build_query(array(	
							        self::request_action => self::action_detail,
									dbFlexTableRow::field_id => $row_id, 
									dbFlexTable::field_id => $row[dbFlexTableCell::field_table_id]
							        ))
				            ),
			        'permalink'	=> $permalink
			    );
			$row_id = $row[dbFlexTableCell::field_row_id];
			$cells = array();
			$permalink = '';
		}
		$value = $dbFlexTableCell->getCellValueByType($row);
		if (($row[dbFlexTableCell::field_definition_name] == 'permalink') && !empty($value)) {
			// permalink
			$permalink = WB_URL.PAGES_DIRECTORY.$value;
			continue;
		}
		if (($row[dbFlexTableCell::field_definition_type] == dbFlexTableDefinition::type_media_link) && 
				(!empty($value))) {
			$ext = strtolower(pathinfo($this->media_path.$value, PATHINFO_EXTENSION));
			$basename = pathinfo($this->media_path.$value, PATHINFO_FILENAME);
			$media_type = $ext;
			$width = 0;
			$height = 0;
			if (in_array($ext, $this->media_image_types)) { 
				list($width, $height) = getimagesize($this->media_path.$value);
			}
			$media_data = array(
				'ext'			=> $ext,
				'url'			=> $this->media_url.$value,
				'text'		=> $basename,
				'width'		=> $width,
				'height'	=> $height
			);		
		}
		else {
			$media_type = 'txt';
			$media_data = array();
		}
		$cells[$row[dbFlexTableCell::field_id]] = array(
			'id'		=> $row[dbFlexTableCell::field_id],
			'value'	=> $value,
			'class'	=> $row[dbFlexTableCell::field_definition_name],
		'media_type'	=> $media_type,
		'media_data'	=> $media_data
		);
	} // foreach $rows

	if ($row_id != -1) {
		$row_array[$row_id] = array(
			'id'		=> $row_id,
			'cells'	=> $cells,
			'link'	=> sprintf(	'%s%s%s', $this->page_link, (strpos($this->page_link, '?') === false) ? '?' : '&', 
												http_build_query(array(	self::request_action => self::action_detail,
																								dbFlexTableRow::field_id => $row_id, 
																								dbFlexTable::field_id => $row[dbFlexTableCell::field_table_id]))),
		'permalink'	=> $permalink
		);
	}
	
  	$data = array(
  		'table'					=> $table_array,
  	  'rows'					=> $row_array,
  		'page_link'			=> $this->page_link,
  		'anchor'				=> array(	'detail'	=> $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgAnchorDetail),
  															'table'		=> $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgAnchorTable))
  	);
  	return $this->getTemplate('table.htt', $data);
  } // showTable()
  
  /**
   * Show the details of a row record - also this items which are not shown in the table
   * showDetail() can be called from the table in "table" mode or from showID() in "detail" mode
   * 
   * @param INT $row_id
   * @return MIXED STR detail dialog on success or BOOL FALSE on error
   */
  public function showDetail($row_id = -1) {
  	global $dbFlexTableCell;
  	global $dbFlexTable;
  	global $dbFlexTableDefinition;
  	global $dbFlexTableCfg;
  	global $dbFlexTableRow;
  	
  	if ($this->params[self::param_mode] == self::mode_detail) {
  		// flexTable works in "detail" mode!
  		if ($row_id < 1) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, ft_error_row_id_missing));
  			return false;
  		}
  	 	$where = array(dbFlexTableRow::field_id => $row_id);
  		$row = array();
  		if (!$dbFlexTableRow->sqlSelectRecord($where, $row)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableRow->getError()));
  			return false;
  		}
  		if (count($row) < 1) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(ft_error_row_id_invalid, $row_id)));
  			return false;
  		}
  		$table_id = $row[0][dbFlexTableRow::field_table_id];
  	}
  	else {
  		// flexTable is using standard "table" mode
  		$row_id = (isset($_REQUEST[dbFlexTableRow::field_id])) ? $_REQUEST[dbFlexTableRow::field_id] : -1;
  		if ($row_id < 1) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, ft_error_row_id_missing));
  			return false;
  		}
  	 	$table_id = (isset($_REQUEST[dbFlexTable::field_id])) ? $_REQUEST[dbFlexTable::field_id] : -1; 	
  	}
  	
  	if ($table_id < 1) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, dt_error_table_id_missing));
  		return false;
  	}
  	$SQL = sprintf( "SELECT * FROM %s WHERE %s='%s'",
  									$dbFlexTable->getTableName(),
  									dbFlexTable::field_id,
  									$table_id);
  	$table = array();
  	if (!$dbFlexTable->sqlExec($SQL, $table)) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
  		return false;
  	}
  	if (count($table) < 1) {
  		$this->setError(sprintf('[%s - %] %s', __METHOD__, __LINE__, sprintf(tool_error_id_invalid, $table_id)));
  		return false;
  	}
  	$table = $table[0];
  	$table_array = array(
  		'id'					=> $table[dbFlexTable::field_id],
  		'name'				=> $table[dbFlexTable::field_name],
  		'class'				=> $table[dbFlexTable::field_name],
  		'description'	=> $table[dbFlexTable::field_description],
  		'title'				=> $table[dbFlexTable::field_title],
  		'keywords'		=> $table[dbFlexTable::field_keywords]
  	);
  	
  	
  	$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s'ORDER BY FIND_IN_SET(%s, '%s')",
										$dbFlexTableCell->getTableName(),
										dbFlexTableCell::field_row_id,
										$row_id,
										dbFlexTableCell::field_definition_id,
										$table[dbFlexTable::field_definitions]
									);
		$items = array();
  	if (!$dbFlexTableCell->sqlExec($SQL, $items)) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableCell->getError()));
  		return false;
  	}
  	$items_array = array();
  	$permalink = '';
  	
  	foreach ($items as $item) {
  		$where = array(dbFlexTableDefinition::field_id => $item[dbFlexTableCell::field_definition_id]);
  		$definition = array();
  		if (!$dbFlexTableDefinition->sqlSelectRecord($where, $definition)) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableDefinition->getError()));
  			return false;
  		}
  		if (count($definition) < 1) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(tool_error_id_invalid, $item[dbFlexTableCell::field_definition_id])));
  			return false;
  		}
  		$definition = $definition[0];
  		$value = $dbFlexTableCell->getCellValueByType($item);
  		
  		if (($item[dbFlexTableCell::field_definition_name] == 'permalink') && !empty($value)) {
				// permalink
				$permalink = WB_URL.PAGES_DIRECTORY.$value;
				continue;
			}
			
  		if (($item[dbFlexTableCell::field_definition_type] == dbFlexTableDefinition::type_media_link) && 
					(!empty($value))) {
				$ext = strtolower(pathinfo($this->media_path.$value, PATHINFO_EXTENSION));
				$basename = pathinfo($this->media_path.$value, PATHINFO_FILENAME);
				$media_type = $ext;
				$width = 0;
				$height = 0;
				if (in_array($ext, $this->media_image_types)) {
					list($width, $height) = getimagesize($this->media_path.$value);
				}
				$media_data = array(
					'ext'			=> $ext,
					'url'			=> $this->media_url.$value,
					'text'		=> $basename,
					'width'		=> $width,
					'height'	=> $height
				);		
			}
			else {
				$media_type = 'txt';
				$media_data = array();
			}
  		
			$items_array[$item[dbFlexTableCell::field_definition_name]] = array(
				$dbFlexTableDefinition->template_names[dbFlexTableDefinition::field_description] => $definition[dbFlexTableDefinition::field_description],
				$dbFlexTableDefinition->template_names[dbFlexTableDefinition::field_head] => $definition[dbFlexTableDefinition::field_head],
				$dbFlexTableDefinition->template_names[dbFlexTableDefinition::field_title] => $definition[dbFlexTableDefinition::field_title],				
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_char] => $item[dbFlexTableCell::field_char],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_datetime] => $item[dbFlexTableCell::field_datetime],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_definition_id] => $item[dbFlexTableCell::field_definition_id],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_definition_name] => $item[dbFlexTableCell::field_definition_name],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_definition_type] => $dbFlexTableDefinition->template_type_array[$item[dbFlexTableCell::field_definition_type]],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_float] => $item[dbFlexTableCell::field_float],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_id] => $item[dbFlexTableCell::field_id],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_integer] => $item[dbFlexTableCell::field_integer],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_row_id] => $item[dbFlexTableCell::field_row_id],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_table_cell] => $item[dbFlexTableCell::field_table_cell],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_table_id] => $item[dbFlexTableCell::field_table_id],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_text] => $item[dbFlexTableCell::field_text],
  			$dbFlexTableCell->template_names[dbFlexTableCell::field_timestamp] => $item[dbFlexTableCell::field_timestamp],
  			'value' => $value,
  			'media_type' => $media_type,
  			'media_data'	=> $media_data,
  		);
  	}
		
  	$data = array(
  		'mode'					=> $this->params[self::param_mode],
  		'table'					=> $table_array,
  		'items'					=> $items_array,
  		'link_back'			=> sprintf('%s', $this->page_link),
  		'link'					=> sprintf(	'%s%s%s', $this->page_link, (strpos($this->page_link, '?') === false) ? '?' : '&', 
  																http_build_query(array(	self::request_action => self::action_detail,
  																												dbFlexTableRow::field_id => $row_id, 
  																												dbFlexTable::field_id => $table[dbFlexTable::field_id]))),
  		'permalink'			=> $permalink,
  		'anchor'				=> array(	'detail'	=> $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgAnchorDetail),
  															'table'		=> $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgAnchorTable))
  	 );
  	 return $this->getTemplate('detail.htt', $data);
  } // showDetail()
	
  /**
   * 
   */
  public function showID() {
  	global $dbFlexTable;
  	global $dbFlexTableRow;
  	
  	if (empty($this->params[self::param_rows]) && ($this->params[self::param_show_last] < 1)) {
  		$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, ft_error_detail_params_missing));
  		return false;
  	}
  	if ($this->params[self::param_show_last] > 0) {
  		// show last row items ...
  		$limit = intval($this->params[self::param_show_last]);
  		$tables = array();
  		if (!empty($this->params[self::param_name])) { 
  			// select from desired tables
  			$tabs = explode(',', $this->params[self::param_name]);
  			foreach ($tabs as $tab) {
  				$tab = trim($tab);
  				$SQL = sprintf( "SELECT %s FROM %s WHERE %s='%s'", 
  												dbFlexTable::field_id,
  												$dbFlexTable->getTableName(),
  												dbFlexTable::field_name,
  												$tab);
  				$table = array();
  				if (!$dbFlexTable->sqlExec($SQL, $table)) {
  					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTable->getError()));
  					return false;
  				}
  				if (count($table) < 1) {
  					$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(ft_error_table_name_invalid, $tab)));
  					return false;
  				}
  				$tables[] = $table[0][dbFlexTable::field_id];
  			}
  		}
  		$tab_str = '';
  		if (count($tables) > 0) {
  			$tab_str = 'WHERE (';
  			$start = true;
  			foreach ($tables as $table) {
  				if (!$start) $tab_str .= ' OR ';
  				$tab_str .= sprintf("%s='%s'", dbFlexTableRow::field_table_id, $table);
  				if ($start) $start = false;
  			}
  			$tab_str .= ')';
  		}
  		$SQL = sprintf( "SELECT * FROM %s %s ORDER BY %s DESC LIMIT %s",
  										$dbFlexTableRow->getTableName(),
  										$tab_str,
  										dbFlexTableRow::field_timestamp,
  										$limit);
  	  $result = array();
  
  	  if (!$dbFlexTableRow->sqlExec($SQL, $result)) {
  	  	$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbFlexTableRow->getError()));
  	  	return false;
  	  }
  	  $ids = array();
  	  foreach ($result as $tab) $ids[] = $tab[dbFlexTableRow::field_id];
  	}
  	else {
  		$ids = explode(',', $this->params[self::param_rows]);
  	}
  	$result = '';
  	foreach ($ids as $id) {
  		if (false === ($result .= $this->showDetail(intval($id)))) {
  			$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
  			return false;
  		}
  	}
  	return $result;
  } // showID
  
} // class tableFrontend

?>