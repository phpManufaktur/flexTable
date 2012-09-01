<?php

/**
 * flexTable
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link http://phpmanufaktur.de
 * @copyright 2011-2012
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

// Checking Requirements

$PRECHECK['PHP_VERSION'] = array('VERSION' => '5.2.0', 'OPERATOR' => '>=');
if (!LEPTON_2) {
    $PRECHECK['WB_ADDONS'] = array(
    	'dbconnect_le'	=> array('VERSION' => '0.70', 'OPERATOR' => '>='),
    	'dwoo' => array('VERSION' => '0.11', 'OPERATOR' => '>='),
    	'droplets_extension' => array('VERSION' => '0.22', 'OPERATOR' => '>='),
    	'kit_tools' => array('VERSION' => '0.18', 'OPRATOR' => '>='),
    	'perma_link' => array('VERSION' => '0.15', 'OPERATOR' => '>=')
    );
}
else {
    // LEPTON 2.x
    $PRECHECK['WB_ADDONS'] = array(
    	'dbconnect_le'	=> array('VERSION' => '0.65', 'OPERATOR' => '>='),
    	'kit_tools' => array('VERSION' => '0.18', 'OPRATOR' => '>='),
    	'perma_link' => array('VERSION' => '0.15', 'OPERATOR' => '>=')
    );
}

global $database;
$sql = "SELECT `value` FROM `".TABLE_PREFIX."settings` WHERE `name`='default_charset'";
$result = $database->query($sql);
if ($result) {
	$data = $result->fetchRow(MYSQL_ASSOC);
	$PRECHECK['CUSTOM_CHECKS'] = array(
		'Default Charset' => array(
			'REQUIRED' => 'utf-8',
			'ACTUAL' => $data['value'],
			'STATUS' => ($data['value'] === 'utf-8')
		)
	);
}


?>