/**
 * flexTable
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link http://phpmanufaktur.de
 * @copyright 2011-2013
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

function ft_execOnChange(target_url, select_id) {
  var x;
  x = target_url + document.getElementById(select_id).value;
  document.body.style.cursor='wait';
  window.location = x;
  return false;	
}
