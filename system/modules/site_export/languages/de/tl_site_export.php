<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_site_export']['title_legend'] = 'Export-Set';
$GLOBALS['TL_LANG']['tl_site_export']['page_legend'] = 'Seitenauswahl';
$GLOBALS['TL_LANG']['tl_site_export']['export_legend'] = 'Export-Einstellungen';
$GLOBALS['TL_LANG']['tl_site_export']['epub_legend'] = 'Epub Einstellungen';


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_site_export']['title'] = array('Titel', 'Bitte geben Sie den Titel des Exports ein.');
$GLOBALS['TL_LANG']['tl_site_export']['pages'] = array('Seiten', 'Alle zu exportierenden Seiten auswählen.');
$GLOBALS['TL_LANG']['tl_site_export']['recursive'] = array('Unterseiten automatisch einbeziehen', 'Alle Unterseiten von ausgewählten Seiten beim Export mit einbeziehen.');
$GLOBALS['TL_LANG']['tl_site_export']['includeLayout'] = array('Ein Layout zuweisen', 'Den exportierten Seiten ein anderes Layout zuweisen.');
$GLOBALS['TL_LANG']['tl_site_export']['layout'] = array('Seitenlayout', 'Seitenlayouts können mit dem Modul "Themes" verwaltet werden.');
$GLOBALS['TL_LANG']['tl_site_export']['toc']['title'] = array('Inhaltsverzeichnis', 'Inhaltsverzeichnis toc.html erstellen');
$GLOBALS['TL_LANG']['tl_site_export']['toc']['reference'] = array(
	'none' => 'kein Inhaltsverzeichnis',
	'flat' => 'flaches Inhaltsverzeichnis',
	'indent' => 'eingerücktes Inhaltsverzeichnis',
	'json' => 'eingerücktes Inhaltsverzeichnis + JSON',
	'flat_json' => 'flaches Inhaltsverzeichnis + JSON'
);
$GLOBALS['TL_LANG']['tl_site_export']['targetDir'] = array('Exportverzeichnis', 'Bitte wählen Sie einen Ordner aus der Dateiübersicht.');
$GLOBALS['TL_LANG']['tl_site_export']['exportEpub'] = array('Epub erstellen', 'Ein E-Book im Format Epub erstellen.');
$GLOBALS['TL_LANG']['tl_site_export']['ebookCover'] = array('Cover-Bild festlegen', 'Ein Cover-Bild für das E-Book festlegen.');
$GLOBALS['TL_LANG']['tl_site_export']['ebookFilename'] = array('Dateinaname', 'Dateiname des Buchs ohne Pfad mit Endung: datei.epub');
$GLOBALS['TL_LANG']['tl_site_export']['ebookTitle'] = array('Titel', 'Titel des Buchs.');
$GLOBALS['TL_LANG']['tl_site_export']['ebookDescription'] = array('Beschreibung', 'Beschreibung des Buchs.');
$GLOBALS['TL_LANG']['tl_site_export']['ebookIdentifier'] = array('Identifikator', 'Eindeutige ID des Buchs, z.B. die Webseite des Buchs.');
$GLOBALS['TL_LANG']['tl_site_export']['ebookSubject'] = array('Thema', 'Themen des Buchs, z.B. Non-fiction, tutorial');
$GLOBALS['TL_LANG']['tl_site_export']['ebookLanguage'] = array('Sprache', 'Sprache des Buchs, z.B. de');
$GLOBALS['TL_LANG']['tl_site_export']['ebookCreator'] = array('Autor', 'Autor des Buchs.');
$GLOBALS['TL_LANG']['tl_site_export']['ebookPublisher'] = array('Herausgeber', 'Herausgeber des Buchs.');
$GLOBALS['TL_LANG']['tl_site_export']['ebookDate'] = array('Datum', 'Datum im Format YYYY oder YYYY-MM-DD');


$GLOBALS['TL_LANG']['tl_site_export']['tstamp']    = array('Zeit', 'Zeit und Datum der letzten Bearbeitung.');


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_site_export']['new']        = array('Neues Export-Set', 'Eine neues Export-Set erstellen');
$GLOBALS['TL_LANG']['tl_site_export']['show']       = array('Exportdetails', 'Details des Export-Sets ID %s anzeigen');
$GLOBALS['TL_LANG']['tl_site_export']['edit']       = array('Export-Set bearbeiten', 'Export-Set ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_site_export']['editheader'] = array('Export-Einstellungen bearbeiten', 'Einstellungen des Export-Sets ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_site_export']['copy']       = array('Export-Set duplizieren', 'Export-Set ID %s duplizieren');
$GLOBALS['TL_LANG']['tl_site_export']['delete']     = array('Export-Set löschen', 'Export-Set ID %s löschen');
$GLOBALS['TL_LANG']['tl_site_export']['export']     = array('Export starten', 'Den Export jetzt starten');
