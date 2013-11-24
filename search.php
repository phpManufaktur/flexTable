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

require_once (WB_PATH . '/modules/' . basename(dirname(__FILE__)) . '/initialize.php');

/**
 * LEPTON 2.x search function for registered DropLEPs
 * This function is called by the LEPTON Search library - please consult the
 * LEPTON documentation for further informations!
 *
 * @param array $func_vars - parameters for the search
 * @return boolean true on success
 */
function flex_table_search($func_vars) {
    global $dbFlexTable;
    global $dbFlexTableCell;
    global $dbFlexTableDefinition;
    global $dbFlexTableCfg;

    $result = array();
    $params = array();
    $page_url = '';

    $page_url = WB_URL.PAGES_DIRECTORY.$func_vars['page_link'].PAGE_EXTENSION;

    $table_names = get_flex_table_names($func_vars['page_id'], $params, $page_url);

    if (count($table_names) < 1) return $result;

    $anchor = $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgAnchorDetail);
    $type_array = $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgImageFileTypes);

    $media_url = WB_URL . MEDIA_DIRECTORY . '/' . $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgMediaDirectory) . '/';

    foreach ($table_names as $table_name) {
        // Tabellendaten einlesen
        $where = array(dbFlexTable::field_name => $table_name);
        $table = array();
        if (! $dbFlexTable->sqlSelectRecord($where, $table)) {
            trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbFlexTable->getError()), E_USER_ERROR);
            return false;
        }
        if (count($table) < 1) {
            trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, sprintf(tool_error_id_invalid, - 1)), E_USER_ERROR);
            return false;
        }
        $table = $table[0];
        $table_id = $table[dbFlexTable::field_id];

        $SQL = sprintf("SELECT * FROM %s WHERE %s='%s' ORDER BY %s ASC, FIND_IN_SET(%s, '%s')",
            $dbFlexTableCell->getTableName(),
            dbFlexTableCell::field_table_id,
            $table_id,
            dbFlexTableCell::field_row_id,
            dbFlexTableCell::field_definition_id,
            $table[dbFlexTable::field_definitions]
            );
        $rows = array();
        if (! $dbFlexTableCell->sqlExec($SQL, $rows)) {
            trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbFlexTableCell->getError()), E_USER_ERROR);
            return false;
        }

        $result = false;
        $divider = '.';
        $row_id = -1;
        $text = '';
        $image_link = '';

        foreach ($rows as $row) {
            if ($row_id == -1) $row_id = $row[dbFlexTableCell::field_row_id];
            if ($row_id != $row[dbFlexTableCell::field_row_id]) {
                $mod_vars = array(
    				'page_link' => sprintf('%s?%s#%s', $page_url, http_build_query(array(
                                    'act' => 'det',
                                    dbFlexTableRow::field_id => $row_id,
                                    dbFlexTable::field_id => $table_id)),
    				                $anchor),
    				'page_link_target' => '',
    				'page_title' => $table[dbFlexTable::field_title],
    				'page_description' => $table[dbFlexTable::field_description],
                    'page_keywords' => $table[dbFlexTable::field_keywords],
    				'page_modified_when' => strtotime($table[dbFlexTable::field_timestamp]),
    				'page_modified_by' => $func_vars['page_modified_by'],
    				'text' => $text,
    				'max_excerpt_num' => $func_vars['default_max_excerpt'],
                    'image_link' => $image_link
    			);
    			if (print_excerpt2($mod_vars, $func_vars)) {
    				$result = true;
    			}
    			$row_id = $row[dbFlexTableCell::field_row_id];
                $text = '';
                $image_link = '';
            }

            if (!empty($row[dbFlexTableCell::field_char])) $text .= $row[dbFlexTableCell::field_char].$divider;
            if (!empty($row[dbFlexTableCell::field_html])) $text .= $row[dbFlexTableCell::field_html].$divider;
            if (!empty($row[dbFlexTableCell::field_text])) $text .= $row[dbFlexTableCell::field_text].$divider;
            $text .= $row[dbFlexTableCell::field_integer].$divider;
            $text .= number_format($row[dbFlexTableCell::field_float], 2, ft_cfg_decimal_separator, ft_cfg_thousand_separator).$divider;
            $text .= date(ft_cfg_datetime_str, strtotime($row[dbFlexTableCell::field_datetime]));

            if (!empty($row[dbFlexTableCell::field_media_link])) $image_link = $media_url.$row[dbFlexTableCell::field_media_link];

        }

        if (!empty($text)) {
            $mod_vars = array(
				'page_link' => sprintf('%s?%s#%s', $page_url, http_build_query(array(
                                'act' => 'det',
                                dbFlexTableRow::field_id => $row_id,
                                dbFlexTable::field_id => $table_id)),
				                $anchor),
				'page_link_target' => '',
				'page_title' => $table[dbFlexTable::field_title],
				'page_description' => $table[dbFlexTable::field_description],
                'page_keywords' => $table[dbFlexTable::field_keywords],
				'page_modified_when' => strtotime($table[dbFlexTable::field_timestamp]),
				'page_modified_by' => $func_vars['page_modified_by'],
				'text' => $text,
				'max_excerpt_num' => $func_vars['default_max_excerpt'],
                'image_link' => $image_link
			);
			if (print_excerpt2($mod_vars, $func_vars)) {
				$result = true;
			}
        }
    }
    return $result;
} // flex_table_search()

/**
 * Return the flexTable names, which are used by the DropLEP [[flex_table]] at
 * the page with $page_id
 *
 * @param integer $page_id
 * @param array reference $params
 * @param array referenc $page_url
 * @return mixed - array with table names or boolean false on error
 */
function get_flex_table_names($page_id, &$params = array(), &$page_url = '') {
    global $database;

    $SQL = sprintf("SELECT * FROM %smod_wysiwyg WHERE page_id='%s'", TABLE_PREFIX, $page_id);
    if (false ===($query = $database->query($SQL))) {
        trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
        return false;
    }
    $table_names = array();
    while (false !== ($section = $query->fetchRow(MYSQL_ASSOC))) {
        if (false !== ($start = strpos($section['text'], '[[flex_table?'))) {
            $start = $start + strlen('[[flex_table?');
            $end = strpos($section['text'], ']]', $start);
            $param_str = substr($section['text'], $start, $end - $start);
            $param_str = str_ireplace('&amp;', '&', $param_str);
            parse_str($param_str, $params);
            if (isset($params['name'])) {
                $table_names[] = $params['name'];
            }
        }
    }

    if ((count($table_names) < 1)) {
        // keine Tabelle gefunden, moeglicherweise TOPICS!
        $SQL = sprintf("SHOW TABLE STATUS LIKE '%smod_topics'", TABLE_PREFIX);
        $query = $database->query($SQL);
        if ($query->numRows() > 0) {
            // TOPICS ist installiert
            $SQL = sprintf("SELECT topic_id, content_long, link FROM %smod_topics WHERE page_id='%s' AND (content_long LIKE '%%[[flex_table?%%')", TABLE_PREFIX, $page_id);
            $query = $database->query($SQL);
            while (false !== ($section = $query->fetchRow(MYSQL_ASSOC))) {
                if (false !== ($start = strpos($section['content_long'], '[[flex_table?'))) {
                    // Droplet gefunden
                    $start = $start + strlen('[[flex_table?');
                    $end = strpos($section['content_long'], ']]', $start);
                    $param_str = substr($section['content_long'], $start, $end - $start);
                    $param_str = str_ireplace('&amp;', '&', $param_str);
                    parse_str($param_str, $params);
                    if (isset($params['name'])) {
                        $table_names[] = $params['name'];
                        $page_url = WB_URL.PAGES_DIRECTORY.'/topics/'.$section['link'].PAGE_EXTENSION;
                    }
                }
            }
        }
    }
    return $table_names;
} // get_flex_table_names()
