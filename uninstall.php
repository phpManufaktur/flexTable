<?php

/**
 * flexTable
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link http://phpmanufaktur.de
 * @copyright 2011-2013
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {
  if (defined('LEPTON_VERSION'))
    include(WB_PATH.'/framework/class.secure.php');
}
else {
  $oneback = "../";
  $root = $oneback;
  $level = 1;
  while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
    $root .= $oneback;
    $level += 1;
  }
  if (file_exists($root.'/framework/class.secure.php')) {
    include($root.'/framework/class.secure.php');
  }
  else {
    trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
  }
}
// end include class.secure.php

// include language file
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

// set LEPTON_2 identifier for further checks
if (!defined('LEPTON_2'))
    define('LEPTON_2', defined('LEPTON_VERSION') ? version_compare(LEPTON_VERSION, '2', '>=') : false);

if (!LEPTON_2) require_once(WB_PATH.'/modules/kit_tools/class.droplets.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.table.php');

global $admin;

$tables = array('dbFlexTable', 'dbFlexTableDefinition', 'dbFlexTableRow', 'dbFlexTableCell', 'dbFlexTableCfg');
$error = '';

foreach ($tables as $table) {
	$delete = null;
	$delete = new $table();
	if ($delete->sqlTableExists()) {
		if (!$delete->sqlDeleteTable()) {
			$error .= sprintf('[UNINSTALL] %s', $delete->getError());
		}
	}
}

if (!LEPTON_2) {
    // remove Droplets
    $dbDroplets = new dbDroplets();
    $droplets = array('flex_table', 'flex_detail');
    foreach ($droplets as $droplet) {
    	$where = array(dbDroplets::field_name => $droplet);
    	if (!$dbDroplets->sqlDeleteRecord($where)) {
    		$message = sprintf('[UNINSTALL] Error uninstalling Droplet: %s', $dbDroplets->getError());
    	}
    }
}

// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}

?>