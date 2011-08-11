<?php
/**
 * flexTable
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de
 * @copyright 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id$
 */

function Dwoo_Plugin_add_days_to_date(Dwoo $dwoo, $str_date, $int_add_days) {
	$date = strtotime($str_date);
	$result = date('d.m.Y', mktime(0, 0, 0, date('n', $date), date('j', $date)+$int_add_days, date('Y', $date)));
	return $result;
} // Dwoo_Plugin_add_days_to_date()

?>