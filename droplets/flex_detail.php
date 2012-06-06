//:interface to flexTable to show one or more records by ID
//:Please visit http://phpManufaktur.de for informations about kitForm!
/**
 * flexTable
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de
 * @copyright 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id: flex_table.php 10 2011-07-27 07:37:19Z phpmanufaktur $
 */
if (file_exists(WB_PATH.'/modules/flex_table/class.frontend.php')) {
	require_once(WB_PATH.'/modules/flex_table/class.frontend.php');
	$table = new tableFrontend();
	$params = $table->getParams();
	$params[tableFrontend::param_preset] = (isset($preset)) ? (int) $preset : 1;
	$params[tableFrontend::param_css] = (isset($css) && (strtolower($css) == 'false')) ? false : true;
	$params[tableFrontend::param_search] = (isset($search) && (strtolower($search) == 'false')) ? false : true;
	$params[tableFrontend::param_page_header] = (isset($page_header) && (strtolower($page_header) == 'false')) ? false : true;
	// set mode to "detail"
	$params[tableFrontend::param_mode] = tableFrontend::mode_detail;
	$params[tableFrontend::param_rows] = (isset($rows)) ? $rows : '';
	$params[tableFrontend::param_show_last] = (isset($show_last)) ? (int) $show_last : 0;
	$params[tableFrontend::param_name] = (isset($name)) ? strtolower($name) : ''; 
	if (!$table->setParams($params)) return $table->getError();
	return $table->action();
}
else {
	return "flexTable is not installed!";
}