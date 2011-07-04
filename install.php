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

require_once(WB_PATH.'/modules/kit_tools/class.droplets.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.table.php');

global $admin;

$tables = array('dbFlexTable', 'dbFlexTableDefinition', 'dbFlexTableRow', 'dbFlexTableCell', 'dbFlexTableCfg');
$error = '';

foreach ($tables as $table) {
	$create = null;
	$create = new $table();
	if (!$create->sqlTableExists()) {
		if (!$create->sqlCreateTable()) {
			$error .= sprintf('[INSTALLATION %s] %s', $table, $create->getError());
		}
	}
}

// Install Droplets
$droplets = new checkDroplets();
$droplets->droplet_path = WB_PATH.'/modules/flex_table/droplets/';

if ($droplets->insertDropletsIntoTable()) {
  $message = sprintf(tool_msg_install_droplets_success, 'flexTables');
}
else {
  $message = sprintf(tool_msg_install_droplets_failed, 'flexTables', $droplets->getError());
}
if ($message != "") {
  echo '<script language="javascript">alert ("'.$message.'");</script>';
}


// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}

?>