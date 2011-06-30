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

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

if (!class_exists('dbconnectle')) 				require_once(WB_PATH.'/modules/dbconnect_le/include.php');

class dbFlexTable extends dbConnectLE {
	
	const field_id						= 'ft_id';
	const field_name					= 'ft_name';
	const field_description		= 'ft_desc';
	const field_definitions		= 'ft_defs';
	const field_timestamp			= 'ft_stamp';

	public $template_names = array(
		self::field_definitions		=> 'definitions',
		self::field_description		=> 'description',
		self::field_id						=> 'id',
		self::field_name					=> 'name',
		self::field_timestamp			=> 'timestamp'
	);
	
	private $createTables 		= false;
  
  public function __construct($createTables = false) {
  	$this->createTables = $createTables;
  	parent::__construct();
  	$this->setTableName('mod_flex_table');
  	$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
  	$this->addFieldDefinition(self::field_name, "VARCHAR(50) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_description, "VARCHAR(255) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_definitions, "VARCHAR(255) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_timestamp, "TIMESTAMP");	
  	$this->checkFieldDefinitions();
  	// Tabelle erstellen
  	if ($this->createTables) {
  		if (!$this->sqlTableExists()) {
  			if (!$this->sqlCreateTable()) {
  				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
  			}
  		}
  	}
  } // __construct()
	
} // class dbFlexTable

class dbFlexTableDefinition extends dbConnectLE {
	
	const field_id					= 'ftd_id';
	const field_table_id		= 'ft_id';
	const field_name				= 'ftd_name';
	const field_head				= 'ftd_head';
	const field_title				= 'ftd_title';
	const field_description	= 'ftd_desc';
	const field_type				= 'ftd_type';
	const field_table_cell	= 'ftd_cell';		// in der Tabelle anzeigen oder nur bei den Details?
	const field_timestamp		= 'ftd_stamp';
	
	public $template_names = array(
		self::field_id						=> 'id',
		self::field_table_id			=> 'table_id',
		self::field_name					=> 'name',
		self::field_head					=> 'head',
		self::field_title					=> 'title',
		self::field_description		=> 'description',
		self::field_type					=> 'type',
		self::field_table_cell		=> 'table_cell',
		self::field_timestamp			=> 'timestamp'
	);
	
	const type_undefined		= 0;
	const type_integer			= 1;
	const type_float				= 2;
	const type_datetime			= 3;
	const type_char					= 4;
	const type_text					= 5;
	const type_media_link		= 6;
	const type_html					= 7;
	
	public $type_array = array(
		array('key' => self::type_undefined, 'value' => ft_type_undefined),
		array('key' => self::type_integer, 'value' => ft_type_integer),
		array('key' => self::type_float, 'value' => ft_type_float),
		array('key' => self::type_datetime, 'value'	=> ft_type_datetime),
		array('key' => self::type_char, 'value'	=> ft_type_char),
		array('key' => self::type_text, 'value'	=> ft_type_text),
		array('key' => self::type_media_link, 'value' => ft_type_media_link),
		array('key' => self::type_html, 'value' => ft_type_html)
	);
	
	public $template_type_array = array(
		self::type_undefined		=> 'undefined',
		self::type_char					=> 'char',
		self::type_datetime			=> 'datetime',
		self::type_float				=> 'float',
		self::type_integer			=> 'integer',
		self::type_text					=> 'text',
		self::type_media_link		=> 'media_link',
		self::type_html					=> 'html'
	);
	
	const cell_true					= 1;
	const cell_false				= 0;
	
	public $cell_array = array(
		array('key' => self::cell_false, 'value' => ft_cell_false),
		array('key' => self::cell_true, 'value'	=> ft_cell_true)
	);
	
	private $createTables 		= false;
  
  public function __construct($createTables = false) {
  	$this->createTables = $createTables;
  	parent::__construct();
  	$this->setTableName('mod_flex_table_definition');
  	$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
  	$this->addFieldDefinition(self::field_table_id, "INT(11) NOT NULL DEFAULT '-1'");
  	$this->addFieldDefinition(self::field_name, "VARCHAR(50) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_head, "VARCHAR(50) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_title, "VARCHAR(255) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_description, "TEXT NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_type, "TINYINT NOT NULL DEFAULT '".self::type_undefined."'");
  	$this->addFieldDefinition(self::field_table_cell, "TINYINT NOT NULL DEFAULT '".self::cell_true."'");
  	$this->addFieldDefinition(self::field_timestamp, "TIMESTAMP");
  	$this->setIndexFields(array(self::field_table_id, self::field_type));	
  	$this->checkFieldDefinitions();
  	// Tabelle erstellen
  	if ($this->createTables) {
  		if (!$this->sqlTableExists()) {
  			if (!$this->sqlCreateTable()) {
  				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
  			}
  		}
  	}
  } // __construct()
	
} // class dbFlexTableDefinition

class dbFlexTableRow extends dbConnectLE {
	
	const field_id						= 'ftr_id';
	const field_table_id			= 'ft_id';
	const field_timestamp			= 'ftr_stamp';
	
	private $createTables 		= false;
  
  public function __construct($createTables = false) {
  	$this->createTables = $createTables;
  	parent::__construct();
  	$this->setTableName('mod_flex_table_row');
  	$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
  	$this->addFieldDefinition(self::field_table_id, "INT(11) NOT NULL DEFAULT '-1'");
  	$this->addFieldDefinition(self::field_timestamp, "TIMESTAMP");
  	$this->setIndexFields(array(self::field_table_id));	
  	$this->checkFieldDefinitions();
  	// Tabelle erstellen
  	if ($this->createTables) {
  		if (!$this->sqlTableExists()) {
  			if (!$this->sqlCreateTable()) {
  				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
  			}
  		}
  	}
  } // __construct()
	
} // class dbFlexTableRow

class dbFlexTableCell extends dbConnectLE {
	
	const field_id							= 'ftc_id';
	const field_table_id				= 'ft_id';
	const field_row_id					= 'ftr_id';
	const field_definition_id		= 'ftd_id';
	const field_definition_type	= 'ftd_type';
	const field_definition_name	= 'ftd_name';
	const field_table_cell			= 'ftd_cell';
	const field_char						= 'ftc_char';
	const field_datetime				= 'ftc_dt';
	const field_float						= 'ftc_float';
	const field_integer					= 'ftc_integer';
	const field_text						= 'ftc_text';
	const field_media_link			= 'ftc_media_link';
	const field_html						= 'ftc_html';
	const field_timestamp				= 'ftc_stamp';
	
	public $template_names = array(
		self::field_id							=> 'id',
		self::field_table_id				=> 'table_id',
		self::field_row_id					=> 'row_id',
		self::field_definition_id		=> 'definition_id',
		self::field_definition_type	=> 'definition_type',
		self::field_definition_name	=> 'definition_name',
		self::field_table_cell			=> 'table_cell',
		self::field_char						=> 'value_char',
		self::field_datetime				=> 'value_datetime',
		self::field_float						=> 'value_float',
		self::field_integer					=> 'value_integer',
		self::field_text						=> 'value_text',
		self::field_media_link			=> 'value_media_link',
		self::field_html						=> 'value_html',
		self::field_timestamp				=> 'timestamp'
	);
	
	private $createTables 			= false;
  
  public function __construct($createTables = false) {
  	$this->createTables = $createTables;
  	parent::__construct();
  	$this->setTableName('mod_flex_table_cell');
  	$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
  	$this->addFieldDefinition(self::field_table_id, "INT(11) NOT NULL DEFAULT '-1'");
  	$this->addFieldDefinition(self::field_row_id, "INT(11) NOT NULL DEFAULT '-1'");
  	$this->addFieldDefinition(self::field_definition_id, "INT(11) NOT NULL DEFAULT '-1'");
  	$this->addFieldDefinition(self::field_definition_type, "TINYINT NOT NULL DEFAULT '".dbFlexTableDefinition::type_undefined."'");
  	$this->addFieldDefinition(self::field_definition_name, "VARCHAR(50) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_table_cell, "TINYINT NOT NULL DEFAULT '".dbFlexTableDefinition::cell_true."'");
  	$this->addFieldDefinition(self::field_char, "VARCHAR(255) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_datetime, "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
  	$this->addFieldDefinition(self::field_float, "FLOAT(11) NOT NULL DEFAULT '0'");
  	$this->addFieldDefinition(self::field_integer, "INT(11) NOT NULL DEFAULT '0'");
  	$this->addFieldDefinition(self::field_text, "TEXT NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_media_link, "VARCHAR(255) NOT NULL DEFAULT ''"); 
  	$this->addFieldDefinition(self::field_html, "TEXT NOT NULL DEFAULT ''", false, false, true);
  	$this->addFieldDefinition(self::field_timestamp, "TIMESTAMP");
  	$this->setIndexFields(array(self::field_table_id, self::field_row_id, self::field_definition_id));	
  	$this->checkFieldDefinitions();
  	// Tabelle erstellen
  	if ($this->createTables) {
  		if (!$this->sqlTableExists()) {
  			if (!$this->sqlCreateTable()) {
  				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
  			}
  		}
  	}
  } // __construct()
		
    public function getCellValueByType($data) {
  	switch ($data[dbFlexTableCell::field_definition_type]):
		case dbFlexTableDefinition::type_char:
			$value = $data[dbFlexTableCell::field_char]; break;
		case dbFlexTableDefinition::type_datetime:
			$value = date(ft_cfg_date_str, $data[dbFlexTableCell::field_datetime]); 
			break;
		case dbFlexTableDefinition::type_float:
			$value = number_format($data[dbFlexTableCell::field_float], 2, ft_cfg_decimal_separator, ft_cfg_thousand_separator); 
			break;
		case dbFlexTableDefinition::type_integer:
			$value = $data[dbFlexTableCell::field_integer]; break;
		case dbFlexTableDefinition::type_html:
			$value = $data[dbFlexTableCell::field_html]; break;
		case dbFlexTableDefinition::type_text:
			$value = $data[dbFlexTableCell::field_text]; break;
		case dbFlexTableDefinition::type_media_link:
			$value = $data[dbFlexTableCell::field_media_link]; break;
		default:
			$value = '';
		endswitch;
		return $value;
  } // getCellValueByType
  
  public function setCellValueByType(&$data, $value) {
  	global $kitLibrary;
  	switch ($data[dbFlexTableDefinition::field_type]):
		case dbFlexTableDefinition::type_char:
			$data[dbFlexTableCell::field_char] = $value; break;
		case dbFlexTableDefinition::type_datetime:
			$data[dbFlexTableCell::field_datetime] = date(ft_cfg_date_str, strtotime($value)); break;
		case dbFlexTableDefinition::type_float:
			$data[dbFlexTableCell::field_float] = $kitLibrary->str2float($value, ft_cfg_thousand_separator, ft_cfg_decimal_separator); 
			break;
		case dbFlexTableDefinition::type_integer:
			$data[dbFlexTableCell::field_integer] = (int) $value; break;
		case dbFlexTableDefinition::type_text:
			$data[dbFlexTableCell::field_text] = $value; break;
		case dbFlexTableDefinition::type_html:
			$data[dbFlexTableCell::field_html] = $value; break;
		case dbFlexTableDefinition::type_media_link:
			$data[dbFlexTableCell::field_media_link] = $value; break;
		endswitch;
		return true;
  } // setValueTypeByType
  
  public function getFieldNameByType($type) {
  	switch ($type):
  	case dbFlexTableDefinition::type_char:
  		$field = dbFlexTableCell::field_char; break;
  	case dbFlexTableDefinition::type_datetime:
  		$field = dbFlexTableCell::field_datetime; break;
  	case dbFlexTableDefinition::type_float:
  		$field = dbFlexTableCell::field_float; break;
  	case dbFlexTableDefinition::type_integer:
  		$field = dbFlexTableCell::field_integer; break;
  	case dbFlexTableDefinition::type_media_link:
  		$field = dbFlexTableCell::field_media_link; break;
  	case dbFlexTableDefinition::type_html:
  		$field = dbFlexTableCell::field_html; break;
  	default:
  		$field = '';
	  endswitch;
	  return $field;	
  } // getFieldNameByType()
  
} // class dbFlexTableCell_Integer


?>