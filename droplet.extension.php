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

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/initialize.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.frontend.php');

if (!function_exists('get_flex_table_ids_by_page_id')) {
	function get_flex_table_ids_by_page_id($page_id, &$params=array(), &$page_url='') {
		global $database;
		$db_wysiwyg = new db_wb_mod_wysiwyg();
		$SQL = sprintf(	"SELECT %s FROM %s WHERE %s='%s'",
										db_wb_mod_wysiwyg::field_text,
										$db_wysiwyg->getTableName(),
										db_wb_mod_wysiwyg::field_page_id,
										$page_id);
		$sections = array();
		if (!$db_wysiwyg->sqlExec($SQL, $sections)) {
			trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $db_wysiwyg->getError()), E_USER_ERROR);
			return false;
		}
		$table_names = array();
		foreach ($sections as $section) { 
			if (false !== ($start = strpos($section[db_wb_mod_wysiwyg::field_text], '[[flex_table?'))) {
				$start = $start+strlen('[[flex_table?');
				$end = strpos($section[db_wb_mod_wysiwyg::field_text], ']]', $start);
				$param_str = substr($section[db_wb_mod_wysiwyg::field_text], $start, $end-$start);
				$param_str = str_ireplace('&amp;', '&', $param_str);
				parse_str($param_str, $params);
				if (isset($params['name'])) {
					$table_names[] = $params['name'];
					//break;
				}
			}
		}
		
		if ((count($table_names) < 1)) {  
			// keine Tabelle gefunden, moeglicherweise TOPICS!
			$SQL = sprintf("SHOW TABLE STATUS LIKE '%smod_topics'", TABLE_PREFIX);
			$query = $database->query($SQL);
			if ($query->numRows() > 0) {
				
				// TOPICS ist installiert
				$SQL = sprintf(	"SELECT topic_id, content_long, link FROM %smod_topics WHERE page_id='%s' AND (content_long LIKE '%%[[flex_table?%%')",
												TABLE_PREFIX,
												$page_id);
				$query = $database->query($SQL);
				while (false !== ($section = $query->fetchRow(MYSQL_ASSOC))) {
					if (false !== ($start = strpos($section['content_long'], '[[flex_table?'))) {
						// Droplet gefunden
						$start = $start+strlen('[[flex_table?');
						$end = strpos($section['content_long'], ']]', $start);
						$param_str = substr($section['content_long'], $start, $end-$start);
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
	} // get_flex_table_ids_by_page_id()
}

if (!function_exists('flex_table_create_image')) {
	function flex_table_create_image($extension, $file_path, $temp_path, $new_width, $new_height, $origin_width, $origin_height, $origin_filemtime) {
		
		switch ($extension):
	  	case 'gif':
	  		$origin_image = imagecreatefromgif($file_path);
	      break;
	    case 'jpeg':
	    case 'jpg':
      	$origin_image = imagecreatefromjpeg($file_path);
	      break;
	    case 'png':
	      $origin_image = imagecreatefrompng($file_path);
	      break;
	    default: 
	      // unsupported image type
	      return false;
	  	endswitch;
	  	
	  // create new image of $new_width and $new_height
    $new_image = imagecreatetruecolor($new_width, $new_height);
    // Check if this image is PNG or GIF, then set if Transparent  
    if (($extension == 'gif') OR ($extension == 'png')) {
      imagealphablending($new_image, false);
      imagesavealpha($new_image,true);
      $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
      imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
    }
    
    // resample image
    imagecopyresampled($new_image, $origin_image, 0, 0, 0, 0, $new_width, $new_height, $origin_width, $origin_height);

   // $new_file = $this->createFileName($filename, $extension, $new_width, $new_height);
   // $new_file = $this->tweak_path.$new_file;

    $new_file = $temp_path;
    
    //Generate the file, and rename it to $newfilename
    switch ($extension): 
      case 'gif': 
      	imagegif($new_image, $new_file); 
       	break;
      case 'jpg':
      case 'jpeg': 
       	imagejpeg($new_image, $new_file); 
       	break;
      case 'png': 
       	imagepng($new_image, $new_file); 
       	break;
      default:  
       	// unsupported image type
       	return false;
    endswitch;
    if (!chmod($new_file, 0755)) {
    	trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, sprintf(tool_error_chmod, basename($new_file))), E_USER_ERROR);
    	return false;
    }
    if (($origin_filemtime !== false) && (touch($new_file, $origin_filemtime) === false)) {
    	trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, sprintf(ft_error_touch, basename($new_file))));
    	return false;
    }
    return $new_file;	  
	}
}

if (!function_exists('get_flex_table_search_image')) {
	function get_flex_table_search_image($media_path, $media_image, $type_array, &$search_image='', &$width=0, &$height=0) {
		$temp_path = $media_path.'search/';
		if (!file_exists($temp_path)) {
			if (!mkdir($temp_path, 0755)) {
				trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, sprintf(tool_error_mkdir, $temp_path)), E_USER_ERROR);
				return false;
			}
		}
		$temp_url = str_replace(WB_PATH, WB_URL, $temp_path);
		if (file_exists($temp_path.$media_image)) {
			$origin_filemtime = filemtime($media_path.$media_image);
			if ($origin_filemtime == filemtime($temp_path.$media_image)) {
				// Bild existiert und die Zeiten stimmen ueberein
				$search_image = $temp_url.$media_image;
				list($width, $height) = getimagesize($temp_path.$media_image);
				return true;
			}
		}
		$origin_filemtime = filemtime($media_path.$media_image);
		$ext = strtolower(pathinfo($media_path.$media_image, PATHINFO_EXTENSION));
		if (!in_array($ext, $type_array)) return false; // falscher Dateityp

		$width = 75;
		
		list($origin_width, $origin_height) = getimagesize($media_path.$media_image);
		if ($origin_width <= $width) {
			// Bild braucht nicht geaendert werden
			if (!copy($media_path.$media_image, $temp_path.$media_image)) {
				trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, sprintf(ft_error_copy_file, $media_path.$media_image, $temp_path.$media_image)), E_USER_ERROR);
				return false;
			}
			$search_image = $temp_url.$media_image;
			$width = $origin_width;
			$height = $origin_height;
			return true;
		}
		
		$percent = (int) ($width/($origin_width/100));
  	$height = (int) (($origin_height/100)*$percent);
  	
  	$search_image = flex_table_create_image($ext, $media_path.$media_image, $temp_path.$media_image, $width, $height, $origin_width, $origin_height, $origin_filemtime);
  		
		return true;
	} // get_flex_table_search_image
}

if (!function_exists('flex_table_droplet_search')) {
	function flex_table_droplet_search($page_id, $page_url) {
		global $dbFlexTable;
		global $dbFlexTableCell;
		global $dbFlexTableDefinition;
  	global $dbFlexTableCfg;
  	
		$result = array();
		$table_names = get_flex_table_ids_by_page_id($page_id, $params, $page_url);
		if (count($table_names) < 1) return $result;
		
		$anchor = $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgAnchorDetail);
		$type_array = $dbFlexTableCfg->getValue(dbFlexTableCfg::cfgImageFileTypes);
		
		$parser = new Dwoo();
		$htt_path = WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/htt/';
	  $tpl_title = new Dwoo_Template_File($htt_path.'search.result.title.htt');
	  $tpl_description = new Dwoo_Template_File($htt_path.'search.result.description.htt');
	  $tpl_description_image = new Dwoo_Template_File($htt_path.'search.result.description.image.htt');	  
	  $media_path = WB_PATH.MEDIA_DIRECTORY.'/'.$dbFlexTableCfg->getValue(dbFlexTableCfg::cfgMediaDirectory).'/';
		
		foreach ($table_names as $table_name) {
			// Tabellendaten einlesen
	  	$where = array(dbFlexTable::field_name => $table_name);
	  	$table = array();
	  	if (!$dbFlexTable->sqlSelectRecord($where, $table)) {
	  		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbFlexTable->getError()), E_USER_ERROR);
	  		return false;
	  	}
	  	if (count($table) < 1) {
	  		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, sprintf(tool_error_id_invalid, -1)), E_USER_ERROR);
	  		return false;
	  	}
	  	$table = $table[0];
	  	$table_id = $table[dbFlexTable::field_id];
	  	
	  	$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' ORDER BY %s ASC, FIND_IN_SET(%s, '%s')",
											$dbFlexTableCell->getTableName(),
											dbFlexTableCell::field_table_id,
											$table_id,
											dbFlexTableCell::field_row_id,
											dbFlexTableCell::field_definition_id,
											$table[dbFlexTable::field_definitions]);
			$rows = array();
			if (!$dbFlexTableCell->sqlExec($SQL, $rows)) {
				trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbFlexTableCell->getError()), E_USER_ERROR);
				return false;
			}
		
			$row_id = -1;
			$value = '';
			$image = '';
			foreach ($rows as $row) {
				if ($row_id == -1) $row_id = $row[dbFlexTableCell::field_row_id];
				if ($row_id != $row[dbFlexTableCell::field_row_id]) {
					$search_image = '';
					$width = 0;
					$height = 0;
					if (!empty($image) && get_flex_table_search_image($media_path, $image, $type_array, $search_image, $width, $height)) {
						// Bild anzeigen
						$desc = $parser->get($tpl_description_image, array(	'description' => $table[dbFlexTable::field_description],
																																'page_url'		=> sprintf(	'%s?%s#%s',
																																													$page_url, 
																																													http_build_query(array(
																																														'act' => 'det',
																																														dbFlexTableRow::field_id =>	$row_id,
																																														dbFlexTable::field_id => $table_id)),
																																													$anchor),
																																'image_url'		=> $search_image,
																																'width'				=> $width,
																																'height'			=> $height ));
					}
					else {
						$desc = $parser->get($tpl_description, array('description' => $table[dbFlexTable::field_description])); 
					}
					$result[] = array(
						'url'						=> $page_url,
						'params'				=> sprintf(	'%s#%s', http_build_query(array(
																					'act' => 'det',
																					dbFlexTableRow::field_id =>	$row_id,
																					dbFlexTable::field_id => $table_id
																				)),
																				$anchor),
						'title'					=> $parser->get($tpl_title, array('title' => $table[dbFlexTable::field_title])),
						'description'		=> $desc,
						'text'					=> $value,
						'modified_when'	=> strtotime($row[dbFlexTableCell::field_timestamp]),
						'modified_by'		=> 1
					);
					$row_id = $row[dbFlexTableCell::field_row_id];
					$value = '';
					$image = '';
				}
				if ($row[dbFlexTableCell::field_definition_type] == dbFlexTableDefinition::type_media_link) {
					if (!empty($image)) continue;
					$image = $dbFlexTableCell->getCellValueByType($row);
				}
				else {
					if (!empty($value)) $value .= ' - ';
					$value .= str_replace('||', '', $dbFlexTableCell->getCellValueByType($row));
				}
			}
			
			if ($row_id != -1) {
				$search_image = '';
				$width = 0;
				$height = 0;
				if (!empty($image) && get_flex_table_search_image($media_path, $image, $type_array, $search_image, $width, $height)) {
					// Bild anzeigen
					$desc = $parser->get($tpl_description_image, array(	'description' => $table[dbFlexTable::field_description],
																																'page_url'		=> sprintf(	'%s?%s#%s',
																																													$page_url, 
																																													http_build_query(array(
																																														'act' => 'det',
																																														dbFlexTableRow::field_id =>	$row_id,
																																														dbFlexTable::field_id => $table_id)),
																																													$anchor),
																																'image_url'		=> $search_image,
																																'width'				=> $width,
																																'height'			=> $height ));
				}
				else {
					$desc = $parser->get($tpl_description, array('description' => $table[dbFlexTable::field_description])); 
				}
				$result[] = array(
					'url'						=> $page_url,
					'params'				=> sprintf(	'%s#%s', http_build_query(array(
																				'act' => 'det',
																				dbFlexTableRow::field_id =>	$row_id,
																				dbFlexTable::field_id => $table_id
																			)),
																			$anchor),
					'title'					=> $parser->get($tpl_title, array('title' => $table[dbFlexTable::field_title])),
					'description'		=> $desc,
					'text'					=> $value,
					'modified_when'	=> strtotime($row[dbFlexTableCell::field_timestamp]),
					'modified_by'		=> 1
				);
			}
		}
		return $result;
	} // flex_table_droplet_search() 
}

if (!function_exists('flex_table_droplet_header')) {
	function flex_table_droplet_header($page_id) {
		global $dbFlexTableCell;
  	global $dbFlexTable;
  	
  	$result = array(
			'title'				=> '',
			'description'	=> '',
			'keywords'		=> ''
		);
		// Kopfdaten für Detailseiten von Events
		if ((isset($_REQUEST[tableFrontend::request_action]) && ($_REQUEST[tableFrontend::request_action] == tableFrontend::action_detail)) &&
				isset($_REQUEST[dbFlexTableRow::field_id]) && 
				isset($_REQUEST[dbFlexTableRow::field_table_id])) { 
			$row_id = (int) $_REQUEST[dbFlexTableRow::field_id];
			$table_id = (int) $_REQUEST[dbFlexTableRow::field_table_id];
			
			$SQL = sprintf( "SELECT %s, %s FROM %s WHERE %s='%s' AND %s='%s' AND (%s='%s' OR %s='%s' OR %s='%s')",
											dbFlexTableCell::field_char,
											dbFlexTableCell::field_definition_name,
											$dbFlexTableCell->getTableName(),
											dbFlexTableCell::field_row_id,
											$row_id,
											dbFlexTableCell::field_table_id,
											$table_id,
											dbFlexTableCell::field_definition_name,
											'title',
											dbFlexTableCell::field_definition_name,
											'description',
											dbFlexTableCell::field_definition_name,
											'keywords');
			$header = array();
			if (!$dbFlexTableCell->sqlExec($SQL, $header)) {
				trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbFlexTableCell->getError()), E_USER_ERROR);
				return false;
			}
			if (count($header) > 0) {
				foreach ($header as $head) {
					switch ($head[dbFlexTableCell::field_definition_name]):
					case 'title':
						$result['title'] = $head[dbFlexTableCell::field_char]; break;
					case 'description':
						$result['description'] = $head[dbFlexTableCell::field_char]; break;
					case 'keywords':
						$result['keywords'] = $head[dbFlexTableCell::field_char]; break;
					endswitch;
				}
			}
		}
  	return $result;
	} // flex_table_droplet_header()
}
?>