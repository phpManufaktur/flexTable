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

$module_directory     = 'flex_table';
$module_name          = 'flexTable';
$module_function      = 'tool';
$module_version       = '0.25';
$module_status        = 'Stable';
$module_platform      = '2.8';
$module_author        = 'Ralf Hertsch, Berlin (Germany)';
$module_license       = 'MIT License (MIT)';
$module_description   = 'Flexible table module for WebsiteBaker';
$module_home          = 'http://phpmanufaktur.de/flex_table';
$module_guid          = 'E47DC7FE-5511-4BF9-91D7-148416EB796E';

?>
