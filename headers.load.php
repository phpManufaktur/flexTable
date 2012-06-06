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

/**
 * Return a page title if flexTable is showing a detail page for a row and the 
 * row contains a character field with the name 'title', containing the title.
 * 
 * @param integer $page_id
 * @return string - page title on success or an empty string if fails
 */
function flex_table_get_page_title($page_id) {
    global $database;

    if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'det') && 
        isset($_REQUEST['ftr_id']) && isset($_REQUEST['ft_id'])) {
        // flexTable is actual showing a detail page
        $row_id = (int) $_REQUEST['ftr_id'];
        $table_id = (int) $_REQUEST['ft_id'];
        $table = TABLE_PREFIX.'mod_flex_table_cell';
        $SQL = "SELECT `ftc_char`, `ftd_name` FROM `$table` WHERE `ftr_id`='$row_id' ".
            "AND `ft_id`='$table_id' AND `ftd_name`='title'";
        if (false === ($query = $database->query($SQL))) {
            trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
            return false;
        }
        if ($query->numRows() > 0) {            
            $flex_table = $query->fetchRow(MYSQL_ASSOC);
            return $flex_table['ftc_char'];
        }
    }
    return '';
} // flex_table_get_page_title()

/**
 * Return a page description if flexTable is showing a detail page for a row and 
 * the row contains a character field with the name 'description', containing 
 * the description.
 * 
 * @param integer $page_id
 * @return string - page description on success or an empty string if fails
 */
function flex_table_get_page_description($page_id) {
    global $database;

    if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'det') && 
        isset($_REQUEST['ftr_id']) && isset($_REQUEST['ft_id'])) {
        // flexTable is actual showing a detail page
        $row_id = (int) $_REQUEST['ftr_id'];
        $table_id = (int) $_REQUEST['ft_id'];
        $table = TABLE_PREFIX.'mod_flex_table_cell';
        $SQL = "SELECT `ftc_char`, `ftd_name` FROM `$table` WHERE `ftr_id`='$row_id' ".
            "AND `ft_id`='$table_id' AND `ftd_name`='description'";
        if (false === ($query = $database->query($SQL))) {
            trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
            return false;
        }
        if ($query->numRows() > 0) {            
            $flex_table = $query->fetchRow(MYSQL_ASSOC);
            return $flex_table['ftc_char'];
        }
    }
    return '';
} // flex_table_get_page_description()

/**
 * Return page keywords if flexTable is showing a detail page for a row and 
 * the row contains a character field with the name 'keywords', containing 
 * the keywords.
 * 
 * @param integer $page_id
 * @return string - page keywords on success or an empty string if fails
 */
function flex_table_get_page_keywords($page_id) {
    global $database;

    if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'det') && 
        isset($_REQUEST['ftr_id']) && isset($_REQUEST['ft_id'])) {
        // flexTable is actual showing a detail page
        $row_id = (int) $_REQUEST['ftr_id'];
        $table_id = (int) $_REQUEST['ft_id'];
        $table = TABLE_PREFIX.'mod_flex_table_cell';
        $SQL = "SELECT `ftc_char`, `ftd_name` FROM `$table` WHERE `ftr_id`='$row_id' ".
            "AND `ft_id`='$table_id' AND `ftd_name`='keywords'";
        if (false === ($query = $database->query($SQL))) {
            trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
            return false;
        }
        if ($query->numRows() > 0) {            
            $flex_table = $query->fetchRow(MYSQL_ASSOC);
            return $flex_table['ftc_char'];
        }
    }
    return '';
    } // flex_table_get_page_keywords()