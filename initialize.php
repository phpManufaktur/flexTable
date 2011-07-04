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

// include GENERAL language file
if(!file_exists(WB_PATH .'/modules/kit_tools/languages/' .LANGUAGE .'.php')) {
	require_once(WB_PATH .'/modules/kit_tools/languages/DE.php'); // Vorgabe: DE verwenden 
}
else {
	require_once(WB_PATH .'/modules/kit_tools/languages/' .LANGUAGE .'.php');
}

// include language file for flexTable
if(!file_exists(WB_PATH .'/modules/'.basename(dirname(__FILE__)).'/languages/' .LANGUAGE .'.php')) {
	require_once(WB_PATH .'/modules/'.basename(dirname(__FILE__)).'/languages/DE.php'); // Vorgabe: DE verwenden 
	if (!defined('FLEX_TABLE_LANGUAGE')) define('FLEX_TABLE_LANGUAGE', 'DE'); // die Konstante gibt an in welcher Sprache flexTable aktuell arbeitet
}
else {
	require_once(WB_PATH .'/modules/'.basename(dirname(__FILE__)).'/languages/' .LANGUAGE .'.php');
	if (!defined('FLEX_TABLE_LANGUAGE')) define('FLEX_TABLE_LANGUAGE', LANGUAGE); // die Konstante gibt an in welcher Sprache flexTable aktuell arbeitet
}

if (!class_exists('dbconnectle')) 				require_once(WB_PATH.'/modules/dbconnect_le/include.php');
if (!class_exists('Dwoo')) 								require_once(WB_PATH.'/modules/dwoo/include.php');
if (!class_exists('kitToolsLibrary'))   	require_once(WB_PATH.'/modules/kit_tools/class.tools.php');
if (!class_exists('dbFlexTable'))					require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.table.php');

global $parser;
global $kitLibrary;
global $dbFlexTable;
global $dbFlexTableCell;
global $dbFlexTableDefinition;
global $dbFlexTableRow;
global $dbFlexTableCfg;

if (!is_object($kitLibrary)) 								$kitLibrary = new kitToolsLibrary();
if (!is_object($parser)) 										$parser = new Dwoo();
if (!is_object($dbFlexTable))								$dbFlexTable = new dbFlexTable();
if (!is_object($dbFlexTableCell))						$dbFlexTableCell = new dbFlexTableCell();
if (!is_object($dbFlexTableDefinition))			$dbFlexTableDefinition = new dbFlexTableDefinition();
if (!is_object($dbFlexTableRow))						$dbFlexTableRow = new dbFlexTableRow();
if (!is_object($dbFlexTableCfg))						$dbFlexTableCfg = new dbFlexTableCfg(true);

?>