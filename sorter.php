<?php

/**
 * flexTable
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link http://phpmanufaktur.de
 * @copyright 2011-2012
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

// Mindestparameter gesetzt?
if (!isset($_POST['rowID']) || !isset($_POST['table_id'])) exit();

require_once('../../config.php');
require_once(WB_PATH.'/framework/initialize.php');

global $database;

$rowIDs = implode(',', $_POST['rowID']);
$SQL = sprintf(	"UPDATE %smod_flex_table SET ft_defs='%s' WHERE ft_id='%s'",
								TABLE_PREFIX,
								$rowIDs,
								$_POST['table_id']);
$database->query($SQL);
if ($database->is_error()) {
	echo $database->get_error();
}
else {
	echo "Sorted: $rowIDs";
}

?>