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

// try to include LEPTON class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {	
	if (defined('LEPTON_VERSION')) include(WB_PATH.'/framework/class.secure.php');
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
	include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php'); 
} else {
	$subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));	$dir = $_SERVER['DOCUMENT_ROOT'];
	$inc = false;
	foreach ($subs as $sub) {
		if (empty($sub)) continue; $dir .= '/'.$sub;
		if (file_exists($dir.'/framework/class.secure.php')) { 
			include($dir.'/framework/class.secure.php'); $inc = true;	break; 
		} 
	}
	if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include LEPTON class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}
// end include LEPTON class.secure.php

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

define('ft_desc_cfg_exec',											'Legen Sie fest, ob flexTable ausgeführt wird (1=JA, 0=NEIN)');
define('ft_desc_cfg_image_file_types',					'Dateiendungen, die flexTable als Bild Datei akzeptiert'); 
define('ft_desc_cfg_doc_file_types',						'Dateiendungen, die flexTable als Dokumenten Datei akzeptiert'); 
define('ft_desc_cfg_anchor_table',							'Anker der bei der Tabelle als Ansprungpunkt {$anchor_table} verwendet wird.');
define('ft_desc_cfg_anchor_detail',							'Anker der bei den Details als Ansprungpunkt {$anchor_detail} verwendet wird.');
define('ft_desc_cfg_media_directory',						'Ordner innerhalb des /MEDIA Verzeichnis, das flexTable auf Bilder und Dokumente durchsucht.');

define('ft_error_preset_not_exists',						'<p>Das Preset <b>%s</b> existiert nicht!</p>');
define('ft_error_table_name_missing',						'<p>Verwenden Sie den Parameter <b>name</b> um anzugeben, welche Tabelle geladen werden soll!</p>');
define('ft_error_template_error',								'<p>Fehler bei der Ausführung des Template <b>%s</b>:</p><p>%s</p>');
define('ft_error_row_id_missing',								'<p>Es wurde keine ID für einen Datensatz übergeben!</p>');
define('ft_error_table_id_missing',							'<p>Es wurde keine ID für die Tabelle übergeben!</p>');
define('ft_error_copy_file',										'<p>Konnte die Datei nicht von %s nach %s kopieren.</p>');
define('ft_error_touch',												'<p>Datum und Zeit konnte für die Datei <b>%s</b> nicht gesetzt werden.</p>');

define('ft_filter_none',												'- unsortiert -');
define('ft_filter_asc',													'Aufsteigend');
define('ft_filter_desc',												'Absteigend');

define('ft_header_table_edit',									'Tabelle bearbeiten');
define('ft_header_table_list',									'Aktive Tabellen');

define('ft_hint_add_definition',								'Um der Tabelle ein weiteres Datenfeld hinzuzufügen, wählen sie den gewünschten Datentyp aus.');
define('ft_hint_ftd_active',										'');
define('ft_hint_ftd_desc',											'');
define('ft_hint_ftd_head',											'Die Kopfzeile wird in der Tabelle verwendet');
define('ft_hint_ftd_name',											'Der Bezeichner muss eindeutig sein und wird für die Identifizierung der Datenfelder sowie für das automatische Setzen von CSS Klassen verwendet.');
define('ft_hint_ftd_id',												'');
define('ft_hint_ftd_cell',											'');
define('ft_hint_ftd_table_id',									'');
define('ft_hint_ftd_title',											'Die Überschrift wird auf der Detailseite verwendet');
define('ft_hint_ftd_type',											'');
define('ft_hint_ft_defs',												'');
define('ft_hint_ft_desc',												'Die Beschreibung der Tabelle wird von flexTable für die Seitenbeschreibung verwendet und steht als Parameter in den Templates zur Verfügung.');
define('ft_hint_ft_homepage',										'Teilen Sie <b>flexTable</b> mit, auf welcher Seite Sie das Droplet für die Anzeige der Tabelle verwenden. Diese Information wird benötigt, damit <b>flexTable</b> mit Hilfe von <b>permaLink</b> <i>permanente Links</i> für die Detailseiten anlegen kann.');
define('ft_hint_ft_id',													'');
define('ft_hint_ft_title',											'Der Titel wird von flexTable für das Setzen von Seitentiteln verwendet und steht als Parameter in den Templates zur Verfügung.');
define('ft_hint_ft_keywords',										'Die Schlüsselwörter werden von flexTable ergänzend zur Seitenbeschreibung verwendet.');
define('ft_hint_ft_name',												'Eindeutiger Bezeichner für die Tabelle. Dieser Bezeichner wird beim Aufruf der Tabelle im Frontend von dem Droplet verwendet.');
define('ft_hint_ft_stamp',											'');

define('ft_intro_table_edit',										'Erstellen oder bearbeiten Sie die Tabelle nach ihren Wünschen.');
define('ft_intro_table_list',										'Klicken Sie auf den Bezeichner einer Tabelle um diese zu bearbeiten. Wählen Sie <b>Bearbeiten</b> um eine neue Tabelle zu erstellen.');
define('ft_intro_definition_sort',							'<p>Fügen Sie weitere Datenfelder hinzu und sortieren Sie die gewünschte Reihenfolge durch Drag & Drop.</p><p><b>Hinweis:</b> Datenfelder vom Typ <i>Zeichenkette</i> mit den Bezeichnern <b>title</b>, <b>description</b> und <b>keywords</b> werden von <b>flexTable</b> verwendet um auf Detailseiten den Seitentitel, die Kurzbeschreibung sowie Schlüsselwörter zu setzen.<br />Ein Datenfeld vom Typ <i>Zeichenkette</i> mit dem Bezeichner <b>permalink</b> wird von <b>flexTable</b> verwendet um den angegebenen <i>permanenten Link</i> auf die jeweilige Detailseite zu setzen.</p>');
define('ft_intro_row_add',											'<p>Fügen Sie der Tabelle einen weiteren Eintrag hinzu, in dem Sie die folgenden Datenfelder ausfüllen und auf <i>Übernehmen</i> klicken.<br /><b>Wichtig:</b> Das erste Feld darf nicht leer sein, sonst wird der Datensatz nicht übernommen.</p><p>Wenn Sie einen Eintrag aus der Tabelle bearbeiten, können Sie rechts einzelne Felder als <b>Copy</b> markieren. Mit <i>Übernehmen</i> fügt <b>flexTable</b> eine neue Zeile in die Tabelle ein und übernimmt die Inhalte der markierten Felder.</p>');
define('ft_intro_row_edit',											'Sie bearbeiten den existieren Eintrag <b>ID %05d</b>!<br />Führen Sie die gewünschten Änderungen durch und klicken Sie anschließend auf <b>Übernehmen</b>.');
define('ft_intro_rows_list',										'Um die Inhalte eines Eintrag zu ändern klicken Sie auf <b>Edit</b>, um eine Zeile zu löschen entfernen Sie das Häkchen in der Spalte <b>Actice</b>.</p><p>Um die Inhalte einer Zeile (auch die nicht in der Tabelle angezeigten) in eine neue Zeile zu kopieren, setzen Sie ein Häkchen bei <b>Copy</b> und klicken Sie anschließend auf <b>Übernehmen</b>.</p>');

define('ft_label_add_definition',								'Datenfeld hinzufügen');
define('ft_label_cfg_exec',											'flexTable ausführen');
define('ft_label_cfg_image_file_types',					'Bilder');
define('ft_label_cfg_anchor_table',							'Ankerpunkt Tabelle');
define('ft_label_cfg_anchor_detail',						'Ankerpunkt Details');
define('ft_label_cfg_doc_file_types',						'Dokumente');
define('ft_label_cfg_media_directory',					'Medien Verzeichnis');
define('ft_label_ftd_active',										'Aktiv');
define('ft_label_ftd_desc',											'Beschreibung');
define('ft_label_ftd_head',											'Kopfzeile');
define('ft_label_ftd_name',											'Bezeichner');
define('ft_label_ftd_id',												'ID');
define('ft_label_ftd_cell',											'Anzeige in der Tabelle');
define('ft_label_ftd_table_id',									'Tabellen ID');
define('ft_label_ftd_title',										'Überschrift');
define('ft_label_ftd_type',											'Datentyp');
define('ft_label_ft_defs',											'Datenfelder definieren');
define('ft_label_ft_desc',											'Beschreibung');
define('ft_label_ft_homepage',									'Homepage');
define('ft_label_ft_id',												'ID');
define('ft_label_ft_title',											'Titel der Tabelle');
define('ft_label_ft_keywords',									'Schlüselwörter');
define('ft_label_ft_name',											'Bezeichner');
define('ft_label_ft_stamp',											'Letzte Änderung');

define('ft_msg_cell_definition_added',					'<p>Es wurde ein neues Datenfeld hinzugefügt.</p>');
define('ft_msg_record_inserted',								'<p>Der Datensatz mit der <b>ID %05d</b> wurde eingefügt.</p>');
define('ft_msg_record_updated',									'<p>Der Datensatz mit der <b>ID %05d</b> wurde aktualisiert.</p>');
define('ft_msg_table_deleted',									'<p>Die Tabelle <b>ID %05d</b> wurde gelöscht.</p>');
define('ft_msg_table_name_empty',								'<p>Der Bezeichner für die Tabelle darf nicht leer sein!</p>');
define('ft_msg_table_name_rename_rejected',			'<p>Der Bezeicher für die Tabelle kann nicht in in <b>%s</b> geändert werden, dieser wird bereits von der Tabelle mit der <b>ID %03d</b> verwendet.</p>');
define('ft_msg_table_name_rejected',						'<p>Der Bezeichner <b>%s</b> wird bereits von der Tabelle mit der <b>ID %03d</b> verwendet, bitte verwenden Sie einen anderen Bezeichner.</p>');
define('ft_msg_cell_name_empty',								'<p>Der Bezeichner für das Datenfeld darf nicht leer sein!</p>');
define('ft_msg_cell_head_empty',								'<p>Die Kopfzeile darf nicht leer sein!</p>');
define('ft_msg_cell_definition_updated',				'<p>Die Definition für das Datenfeld <b>ID %05d</b> wurde aktualisiert.</p>');
define('ft_msg_cell_definition_removed',				'<p>Die Definition für das Datenfeld <b>ID %05d</b> wurde <b>gelöscht</b></p>');
define('ft_msg_cell_updated',										'<p>Das Datenfeld <b>ID %5d</b> wurde aktualisiert.</p>');
define('ft_msg_cells_copied_to_row',						'<p>Die Datenfelder <b>%s</b> aus der Zeile mit der <b>ID %05d</b> wurden in eine neue Zeile mit der <b>ID %05d</b> kopiert.</p>');
define('ft_msg_permalink_created',							'<p>Der <b>permaLink %s</b> wurde angelegt und kann verwendet werden.</p>');
define('ft_msg_permalink_deleted',							'<p>Der <b>permaLink %s</b> wurde gelöscht.</p>');
define('ft_msg_permalink_missing_homepage',			'<p>In den Tabelleneigenschaften ist keine <b>Homepage</b> festgelegt, der <b>permaLink</b> kann nicht übernommen werden.<br />Definieren Sie eine Zielseite und versuchen Sie es dann erneut.</p>');
define('ft_msg_row_copied',											'<p>Die Daten der Zeile <b>ID %05d</b> wurde in die neue Zeile <b>ID %05d</b> kopiert.</p>');
define('ft_msg_row_deleted',										'<p>Die Zeile mit der <b>ID %05d</b> wurde gelöscht.</p>');

define('ft_tab_cfg',														'Einstellungen');
define('ft_tab_edit',														'Bearbeiten');
define('ft_tab_list',														'Liste');
define('ft_tab_about',													'?');

define('ft_text_active',												'Active');
define('ft_text_copy',													'Copy');
define('ft_text_select_file',										'- Datei auswählen -');
define('ft_text_select_page',										'- Seite auswählen -');
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