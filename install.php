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
echo LEPTON_VERSION."LEP2: ".LEPTON_2;
if (!LEPTON_2) require_once(WB_PATH.'/modules/kit_tools/class.droplets.php');
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

if (!LEPTON_2) {
    // WB & LEPTON 1.x - install Droplets
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
else {
    // LEPTON_2 - import DropLEPs
    if (!function_exists('dropleps_import')) 
        require_once WB_PATH.'/modules/dropleps/include.php';
    
    $inst_dir = sanitize_path(dirname(__FILE__).'/install');
    $temp_unzip = sanitize_path(WB_PATH.'/temp/unzip/');
    $files = $admin->get_helper('Directory')->getFiles($inst_dir);
    
    if (is_array($files) && count($files)) {
        foreach ($files as $file) {
            dropleps_import($file, $temp_unzip);
        }
    }    
}

// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}

?>