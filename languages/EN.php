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

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

define('ft_btn_edit',														'Edit');

define('ft_cell_false',													'No');
define('ft_cell_true',													'Yes');

define('ft_cfg_currency',												'%s €');
define('ft_cfg_date_separator',									'.'); 
define('ft_cfg_date_str',												'd.m.Y');
define('ft_cfg_datetime_str',										'd.m.Y H:i');
define('ft_cfg_day_names',											"Sunday, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday");
define('ft_cfg_decimal_separator',          		',');
define('ft_cfg_month_names',										"January,February,March,April,May,June,July,August,September,October,November,December");
define('ft_cfg_thousand_separator',							'.');
define('ft_cfg_time_long_str',									'H:i:s');
define('ft_cfg_time_str',												'H:i');
define('ft_cfg_time_zone',											'Europe/Berlin');
define('ft_cfg_title',													'Mr.,Mrs.');

define('ft_desc_cfg_exec',											'Use flexTable? (1=YES, 0=NO)');
define('ft_desc_cfg_image_file_types',					'Image file types to be accepted by flexTable'); 
define('ft_desc_cfg_doc_file_types',						'Document file types to be accepted by flexTable'); 
define('ft_desc_cfg_anchor_table',							'Anchor to be used in tables {$anchor_table}.');
define('ft_desc_cfg_anchor_detail',							'Anchor to be used for details {$anchor_detail}.');
define('ft_desc_cfg_media_directory',						'Folder in /MEDIA to be used by flexTable for images and documents.');

define('ft_error_preset_not_exists',						'<p>The preset <b>%s</b> doesnt exist!</p>');
define('ft_error_table_name_missing',						'<p>Use the parameter <b>name</b> to define the table to be loaded !</p>');
define('ft_error_template_error',								'<p>An error occurred when executing the template <b>%s</b>:</p><p>%s</p>');
define('ft_error_row_id_missing',								'<p>Warning: Dataset ID is missing!</p>');
define('ft_error_table_id_missing',							'<p>Warning: Table ID is missing!</p>');
define('ft_error_copy_file',										"<p>The file couldn't be copied from %s to %s.</p>");
define('ft_error_touch',												'<p>Date and Time couldn\'t be set for the file <b>%s</b>.</p>');

define('ft_filter_none',												'- unsorted -');
define('ft_filter_asc',													'Ascending');
define('ft_filter_desc',												'Descending');

define('ft_header_table_edit',									'Edit table');
define('ft_header_table_list',									'Active tables');

define('ft_hint_add_definition',								'Please choose the kind of data field you wish to add to the table.');
define('ft_hint_ftd_active',										'');
define('ft_hint_ftd_desc',											'');
define('ft_hint_ftd_head',											'The header line will be used in the table.');
define('ft_hint_ftd_name',											'The identifier has to be unique and will be used fo the identification of the data fields as well as for the automatic assignment of CSS classes.');
define('ft_hint_ftd_id',												'');
define('ft_hint_ftd_cell',											'');
define('ft_hint_ftd_table_id',									'');
define('ft_hint_ftd_title',											'The headline will be used on the details page.');
define('ft_hint_ftd_type',											'');
define('ft_hint_ft_defs',												'');
define('ft_hint_ft_desc',												'The table description will be will be used for the page description and is available as a parameter in the template.');
define('ft_hint_ft_homepage',										'Teilen Sie <b>flexTable</b> mit, auf welcher Seite Sie das Droplet für die Anzeige der Tabelle verwenden. Diese Information wird benötigt, damit <b>flexTable</b> mit Hilfe von <b>permaLink</b> <i>permanente Links</i> für die Detailseiten anlegen kann.');
define('ft_hint_ft_id',													'');
define('ft_hint_ft_title',											'The title will be used for the page title and is available as a parameter in the template.');
define('ft_hint_ft_keywords',										'The keywords will be used as an addition to the page description.');
define('ft_hint_ft_name',												'Unique identifier for the table. This identifier will be used by the droplet when displaying the table in the frontend.');
define('ft_hint_ft_stamp',											'');

define('ft_intro_table_edit',										'Create or edit tables as you wish.');
define('ft_intro_table_list',										'To edit a table click on its identifier. Choose <b>Edit</b> to create a new table.');
define('ft_intro_definition_sort',							'<p>Fügen Sie weitere Datenfelder hinzu und sortieren Sie die gewünschte Reihenfolge durch Drag & Drop.</p><p><b>Hinweis:</b> Datenfelder vom Typ <i>Zeichenkette</i> mit den Bezeichnern <b>title</b>, <b>description</b> und <b>keywords</b> werden von <b>flexTable</b> verwendet um auf Detailseiten den Seitentitel, die Kurzbeschreibung sowie Schlüsselwörter zu setzen.<br />Ein Datenfeld vom Typ <i>Zeichenkette</i> mit dem Bezeichner <b>permalink</b> wird von <b>flexTable</b> verwendet um den angegebenen <i>permanenten Link</i> auf die jeweilige Detailseite zu setzen.</p>');
define('ft_intro_row_add',											'Add a new item to the table by completing the following fields and clicking <i>Save</i>.<br /><b>Note:</b> If the first field stays empty, the dataset will not be saved.');
define('ft_intro_row_edit',											'Your are editing the dataset <b>ID %05d</b>!<br /> After making changes please click <b>Save</b>.');
define('ft_intro_rows_list',										'To edit the content of an entry please click <b>Edit</b>. To delete an entry check the checkbox and click <b>Save</b>.');

define('ft_label_add_definition',								'Add data field');
define('ft_label_cfg_exec',											'Execute flexTable');
define('ft_label_cfg_image_file_types',					'Images');
define('ft_label_cfg_anchor_table',							'Anchor table');
define('ft_label_cfg_anchor_detail',						'Anchor details page');
define('ft_label_cfg_doc_file_types',						'Documents');
define('ft_label_cfg_media_directory',					'Media folder');
define('ft_label_ftd_active',										'Active');
define('ft_label_ftd_desc',											'Description');
define('ft_label_ftd_head',											'Header');
define('ft_label_ftd_name',											'Identifier');
define('ft_label_ftd_id',												'ID');
define('ft_label_ftd_cell',											'Show in table');
define('ft_label_ftd_table_id',									'Table ID');
define('ft_label_ftd_title',										'Headline');
define('ft_label_ftd_type',											'File type');
define('ft_label_ft_defs',											'Define data fields');
define('ft_label_ft_desc',											'Description');
define('ft_label_ft_id',												'ID');
define('ft_label_ft_homepage',									'Homepage');
define('ft_label_ft_title',											'Table title');
define('ft_label_ft_keywords',									'Keywords');
define('ft_label_ft_name',											'Identifier');
define('ft_label_ft_stamp',											'Last changes');

define('ft_msg_cell_definition_added',					'<p>A news data field has been added.</p>');
define('ft_msg_record_inserted',								'<p>The dataset with the <b>ID %05d</b> has been added.</p>');
define('ft_msg_record_updated',									'<p>The dataset with the <b>ID %05d</b> has been updated.</p>');
define('ft_msg_table_deleted',									'<p>The table <b>ID %05d</b> has been deleted.</p>');
define('ft_msg_table_name_empty',								'<p>Please define a unique identifier for the table!</p>');
define('ft_msg_table_name_rename_rejected',			'<p>The identifier could not be changed. <b>%s</b> is already in use by the table wit the <b>ID %03d</b>.</p>');
define('ft_msg_table_name_rejected',						'<p>The identifier <b>%s</b> is already in use by the table wit the <b>ID %03d</b>, please choose a different identifier.</p>');
define('ft_msg_cell_name_empty',								'<p>Please define an identifier for this data field!</p>');
define('ft_msg_cell_head_empty',								'<p>Please define a header line!</p>');
define('ft_msg_cell_definition_updated',				'<p>The definitions for the data field <b>ID %05d</b> have been updated.</p>');
define('ft_msg_cell_definition_removed',				'<p>The defintions for the data field <b>ID %05d</b> have been <b>deleted</b>.</p>');
define('ft_msg_cell_updated',										'<p>Data field <b>ID %5d</b> has been updated.</p>');
define('ft_msg_row_copied',											'<p>Die Daten der Zeile <b>ID %05d</b> wurde in die neue Zeile <b>ID %05d</b> kopiert.</p>');
define('ft_msg_row_deleted',										'<p>Data line <b>ID %05d</b> has been deleted.</p>');

define('ft_tab_cfg',														'Settings');
define('ft_tab_edit',														'Edit');
define('ft_tab_list',														'List');
define('ft_tab_about',													'?');

define('ft_text_active',												'Active');
define('ft_text_copy',													'Copy');
define('ft_text_select_file',										'- Choose file -');
define('ft_text_select_page',										'- Seite auswählen -');
define('ft_text_table_delete',									'Delete table');

define('ft_th_id',															'ID');
define('ft_th_name',														'Identifier'); 
define('ft_th_description',											'Description');
define('ft_th_timestamp',												'Last changed');

define('ft_type_undefined',											'- Choose file type -');
define('ft_type_integer',												'Integer');
define('ft_type_float',													'Decimal');
define('ft_type_datetime',											'Date/Time');
define('ft_type_char',													'Character string');
define('ft_type_text',													'Text');
define('ft_type_media_link',										'Media link');
define('ft_type_html',													'HTML');

?>