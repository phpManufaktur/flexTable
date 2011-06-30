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

define('ft_cell_false',													'Nein');
define('ft_cell_true',													'Ja');

define('ft_cfg_currency',												'%s €');
define('ft_cfg_date_separator',									'.'); 
define('ft_cfg_date_str',												'd.m.Y');
define('ft_cfg_datetime_str',										'd.m.Y H:i');
define('ft_cfg_day_names',											"Sonntag, Montag, Dienstag, Mittwoch, Donnerstag, Freitag, Samstag");
define('ft_cfg_decimal_separator',          		',');
define('ft_cfg_month_names',										"Januar,Februar,März,April,Mai,Juni,Juli,August,September,Oktober,November,Dezember");
define('ft_cfg_thousand_separator',							'.');
define('ft_cfg_time_long_str',									'H:i:s');
define('ft_cfg_time_str',												'H:i');
define('ft_cfg_time_zone',											'Europe/Berlin');
define('ft_cfg_title',													'Herr,Frau');

define('ft_error_preset_not_exists',						'<p>Das Preset <b>%s</b> existiert nicht!</p>');
define('ft_error_table_name_missing',						'<p>Verwenden Sie den Parameter <b>name</b> um anzugeben, welche Tabelle geladen werden soll!</p>');
define('ft_error_template_error',								'<p>Fehler bei der Ausführung des Template <b>%s</b>:</p><p>%s</p>');
define('ft_error_row_id_missing',								'<p>Es wurde keine ID für einen Datensatz übergeben!</p>');
define('ft_error_table_id_missing',							'<p>Es wurde keine ID für die Tabelle übergeben!</p>');

define('ft_filter_none',												'- unsortiert -');
define('ft_filter_asc',													'Aufsteigend');
define('ft_filter_desc',												'Absteigend');

define('ft_header_table_edit',									'Tabelle bearbeiten');
define('ft_header_table_list',									'Aktive Tabellen');

define('ft_hint_add_definition',								'Um der Tabelle eine weitere Spalte hinzuzufügen, wählen sie den gewünschten Datentyp aus.');
define('ft_hint_ftd_active',										'');
define('ft_hint_ftd_desc',											'');
define('ft_hint_ftd_head',											'');
define('ft_hint_ftd_name',											'');
define('ft_hint_ftd_id',												'');
define('ft_hint_ftd_cell',											'');
define('ft_hint_ftd_table_id',									'');
define('ft_hint_ftd_title',											'');
define('ft_hint_ftd_type',											'');
define('ft_hint_ft_defs',												'');
define('ft_hint_ft_desc',												'');
define('ft_hint_ft_id',													'');
define('ft_hint_ft_name',												'Eindeutiger Bezeichner für die Tabelle. Dieser Bezeichner wird beim Aufruf der Tabelle im Frontend von dem Droplet verwendet.');
define('ft_hint_ft_stamp',											'');

define('ft_intro_table_edit',										'Erstellen oder bearbeiten Sie die Tabelle nach ihren Wünschen.');
define('ft_intro_table_list',										'Klicken Sie auf den Bezeichner einer Tabelle um diese zu bearbeiten. Wählen Sie <b>Bearbeiten</b> um eine neue Tabelle zu erstellen.');
define('ft_intro_definition_sort',							'Fügen Sie weitere Tabellenspalten hinzu und sortieren Sie die gewünschte Reihenfolge durch Drag & Drop.');
define('ft_intro_row_add',											'Fügen Sie der Tabelle einen weiteren Eintrag hinzu, in dem Sie die folgenden Felder ausfüllen und auf <i>Übernehmen</i> klicken.');
define('ft_intro_row_edit',											'Sie bearbeiten den existieren Eintrag <b>ID %05d</b>!<br />Führen Sie die gewünschten Änderungen durch und klicken Sie anschließend auf <b>Übernehmen</b>.');
define('ft_intro_rows_list',										'Um die Inhalte eines Eintrag zu ändern klicken Sie auf <b>Edit</b>, um einen Eintrag zu entfernen setzen sie ein Häkchen die Checkbox und klicken Sie anschließend auf <b>Übernehmen</b>.');

define('ft_label_add_definition',								'Spalte hinzufügen');
define('ft_label_ftd_active',										'Aktiv');
define('ft_label_ftd_desc',											'Beschreibung');
define('ft_label_ftd_head',											'Kopfzeile');
define('ft_label_ftd_name',											'Bezeichner');
define('ft_label_ftd_id',												'ID');
define('ft_label_ftd_cell',											'Anzeige in der Tabelle');
define('ft_label_ftd_table_id',									'Tabellen ID');
define('ft_label_ftd_title',										'Überschrift');
define('ft_label_ftd_type',											'Datentyp');
define('ft_label_ft_defs',											'Spaltendefinitionen');
define('ft_label_ft_desc',											'Beschreibung');
define('ft_label_ft_id',												'ID');
define('ft_label_ft_name',											'Bezeichner');
define('ft_label_ft_stamp',											'Letzte Änderung');

define('ft_msg_cell_definition_added',					'<p>Es wurde eine neue Spaltendefinition hinzugefügt.</p>');
define('ft_msg_record_inserted',								'<p>Der Datensatz mit der <b>ID %05d</b> wurde eingefügt.</p>');
define('ft_msg_record_updated',									'<p>Der Datensatz mit der <b>ID %05d</b> wurde aktualisiert.</p>');
define('ft_msg_table_deleted',									'<p>Die Tabelle <b>ID %05d</b> wurde gelöscht.</p>');
define('ft_msg_table_name_empty',								'<p>Der Bezeichner für die Tabelle darf nicht leer sein!</p>');
define('ft_msg_table_name_rename_rejected',			'<p>Der Bezeicher für die Tabelle kann nicht in in <b>%s</b> geändert werden, dieser wird bereits von der Tabelle mit der <b>ID %03d</b> verwendet.</p>');
define('ft_msg_table_name_rejected',						'<p>Der Bezeichner <b>%s</b> wird bereits von der Tabelle mit der <b>ID %03d</b> verwendet, bitte verwenden Sie einen anderen Bezeichner.</p>');
define('ft_msg_cell_name_empty',								'<p>Der Bezeichner für die Spalte darf nicht leer sein!</p>');
define('ft_msg_cell_head_empty',								'<p>Die Kopfzeile darf nicht leer sein!</p>');
define('ft_msg_cell_definition_updated',				'<p>Die Spaltendefinition <b>ID %05d</b> wurde aktualisiert.</p>');
define('ft_msg_cell_definition_removed',				'<p>Die Spaltendefinition <b>ID %05d</b> wurde <b>gelöscht</b></p>');
define('ft_msg_cell_updated',										'<p>Die Zelle <b>ID %5d</b> wurde aktualisiert.</p>');
define('ft_msg_row_deleted',										'<p>Die Zeile <b>ID %05d</b> wurde gelöscht.</p>');

define('ft_tab_edit',														'Bearbeiten');
define('ft_tab_list',														'Liste');
define('ft_tab_about',													'?');

define('ft_text_select_file',										'- Datei auswählen -');
define('ft_text_table_delete',									'Tabelle löschen');

define('ft_th_id',															'ID');
define('ft_th_name',														'Bezeichner');
define('ft_th_description',											'Beschreibung');
define('ft_th_timestamp',												'letzte Änderung');

define('ft_type_undefined',											'- bitte Typ auswählen -');
define('ft_type_integer',												'Ganzzahl');
define('ft_type_float',													'Dezimalzahl');
define('ft_type_datetime',											'Datum / Zeit');
define('ft_type_char',													'Zeichenkette');
define('ft_type_text',													'Text');
define('ft_type_media_link',										'Media Link');
define('ft_type_html',													'HTML');

?>