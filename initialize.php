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

// try to include LEPTON class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {	
	if (defined('LEPTON_VERSION')) include(WB_PATH.'/framework/class.secure.php');
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
	include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php'); 
} else {
	$subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));	$dir = $_SERVER['DOCUMENT_ROOT'];
	$inc = false;
	foreach ($subs as $sub) {
		if (empty($sub)) continue; $dir .= '/'.$sub;
		if (file_exists($dir.'/framework/class.secure.php')) { 
			include($dir.'/framework/class.secure.php'); $inc = true;	break; 
		} 
	}
	if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include LEPTON class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}
// end include LEPTON class.secure.php

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

if (!is_object($parser)) 										$parser = new Dwoo();
$loader = $parser->getLoader();
$loader->addDirectory(WB_PATH.'/modules/flex_table/htt/plugins/');

if (!is_object($kitLibrary)) 								$kitLibrary = new kitToolsLibrary();
if (!is_object($dbFlexTable))								$dbFlexTable = new dbFlexTable();
if (!is_object($dbFlexTableCell))						$dbFlexTableCell = new dbFlexTableCell();
if (!is_object($dbFlexTableDefinition))			$dbFlexTableDefinition = new dbFlexTableDefinition();
if (!is_object($dbFlexTableRow))						$dbFlexTableRow = new dbFlexTableRow();
if (!is_object($dbFlexTableCfg))						$dbFlexTableCfg = new dbFlexTableCfg(true);

?>