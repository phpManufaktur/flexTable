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

// set LEPTON_2 identifier for further checks
if (!defined('LEPTON_2'))
    define('LEPTON_2', defined('LEPTON_VERSION') ? version_compare(LEPTON_VERSION, '2', '>=') : false);

// include GENERAL language file
if (! file_exists(WB_PATH . '/modules/kit_tools/languages/' . LANGUAGE . '.php')) {
    require_once (WB_PATH . '/modules/kit_tools/languages/DE.php'); // Vorgabe: DE
                                                                  // verwenden
} else {
    require_once (WB_PATH . '/modules/kit_tools/languages/' . LANGUAGE . '.php');
}

// include language file for flexTable
if (! file_exists(WB_PATH . '/modules/' . basename(dirname(__FILE__)) . '/languages/' . LANGUAGE . '.php')) {
    require_once (WB_PATH . '/modules/' . basename(dirname(__FILE__)) . '/languages/DE.php');
    if (! defined('FLEX_TABLE_LANGUAGE')) define('FLEX_TABLE_LANGUAGE', 'DE');
} else {
    require_once (WB_PATH . '/modules/' . basename(dirname(__FILE__)) . '/languages/' . LANGUAGE . '.php');
    if (! defined('FLEX_TABLE_LANGUAGE')) define('FLEX_TABLE_LANGUAGE', LANGUAGE);
}

if (! class_exists('dbconnectle')) require_once (WB_PATH . '/modules/dbconnect_le/include.php');
if (! class_exists('kitToolsLibrary')) require_once (WB_PATH . '/modules/kit_tools/class.tools.php');
if (! class_exists('dbFlexTable')) require_once (WB_PATH . '/modules/' . basename(dirname(__FILE__)) . '/class.table.php');

global $kitLibrary;
global $dbFlexTable;
global $dbFlexTableCell;
global $dbFlexTableDefinition;
global $dbFlexTableRow;
global $dbFlexTableCfg;

// Template Parser
if (! class_exists('Dwoo')) require_once (WB_PATH . '/modules/dwoo/include.php');
$cache_path = WB_PATH . '/temp/cache';
if (! file_exists($cache_path)) @mkdir($cache_path, 0755, true);
$compiled_path = WB_PATH . '/temp/compiled';
if (! file_exists($compiled_path)) @mkdir($compiled_path, 0755, true);
global $parser;
if (! is_object($parser)) $parser = new Dwoo($compiled_path, $cache_path);
// load the plugins
$loader = $parser->getLoader();
$loader->addDirectory(WB_PATH.'/modules/flex_table/htt/plugins/');

if (! is_object($kitLibrary)) $kitLibrary = new kitToolsLibrary();
if (! is_object($dbFlexTable)) $dbFlexTable = new dbFlexTable();
if (! is_object($dbFlexTableCell)) $dbFlexTableCell = new dbFlexTableCell();
if (! is_object($dbFlexTableDefinition)) $dbFlexTableDefinition = new dbFlexTableDefinition();
if (! is_object($dbFlexTableRow)) $dbFlexTableRow = new dbFlexTableRow();
if (! is_object($dbFlexTableCfg)) $dbFlexTableCfg = new dbFlexTableCfg(true);
