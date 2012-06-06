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

// set LEPTON_2 identifier for further checks
if (!defined('LEPTON_2'))
    define('LEPTON_2', defined('LEPTON_VERSION') ? version_compare(LEPTON_VERSION, '2', '>=') : false);

if (!LEPTON_2) require_once(WB_PATH.'/modules/kit_tools/class.droplets.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.table.php');


global $admin;

$error = '';

$tables = array('dbFlexTableCfg');
foreach ($tables as $table) {
	$create = null;
	$create = new $table();
	if (!$create->sqlTableExists()) {
		if (!$create->sqlCreateTable()) {
			$error .= sprintf('[ADD TABLE %s] %s', $table, $create->getError());
		}
	}
}

// Release 0.12
$dbFlexTable = new dbFlexTable();
if (!$dbFlexTable->sqlFieldExists(dbFlexTable::field_title)) {
	$insert_fields = array(dbFlexTable::field_title, dbFlexTable::field_keywords);
	foreach ($insert_fields as $iField) { 
		if (!$dbFlexTable->sqlAlterTableAddField($iField, "VARCHAR(255) NOT NULL DEFAULT ''")) {
			$error .= sprintf('[UPGRADE] %s', $dbFlexTable->getError());
			break;
		}
	}
}

// Release 0.13
if (!$dbFlexTable->sqlFieldExists(dbFlexTable::field_homepage)) {
	if (!$dbFlexTable->sqlAlterTableAddField(dbFlexTable::field_homepage, "VARCHAR(255) NOT NULL DEFAULT ''", dbFlexTable::field_definitions)) {
		$error .= sprintf('[UPGRADE] %s', $dbFlexTable->getError());
		break;
	}
}

if (!LEPTON_2) {
    // remove Droplets
    $dbDroplets = new dbDroplets();
    $droplets = array('flex_table', 'flex_detail');
    foreach ($droplets as $droplet) {
    	$where = array(dbDroplets::field_name => $droplet);
    	if (!$dbDroplets->sqlDeleteRecord($where)) {
    		$message = sprintf('[UPGRADE] Error uninstalling Droplet: %s', $dbDroplets->getError());
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
}

// delete files
if (file_exists(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/frontend.css')) {
	@unlink(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/frontend.css'); 
}

// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}

?>