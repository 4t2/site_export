<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_site_export_rules']['title_legend'] = 'Export Ersetzungsregeln';
$GLOBALS['TL_LANG']['tl_site_export_rules']['rules_legend'] = 'Ersetzungsregeln';
$GLOBALS['TL_LANG']['tl_site_export_rules']['regex_legend'] = 'Regulärer Ausdruck';
$GLOBALS['TL_LANG']['tl_site_export_rules']['apply_legend'] = 'Regel anwenden';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_site_export_rules']['title'] = array('Titel', 'Bitte geben Sie den Titel der Regel ein.');
$GLOBALS['TL_LANG']['tl_site_export_rules']['pattern'] = array('Suchmuster', 'Zu suchender Ausdruck. Bei regulären Ausdrücken ohne Begrenzer und Modifikator.');
$GLOBALS['TL_LANG']['tl_site_export_rules']['replacement'] = array('Ersetzungsausdruck', 'Zu ersetzender Ausdruck.');

$GLOBALS['TL_LANG']['tl_site_export_rules']['isRegex'] = array('Regel ist ein Regulärer Ausdruck', 'Die Regel ist ein Regulärer Ausdruck.');
$GLOBALS['TL_LANG']['tl_site_export_rules']['modIgnoreCase'] = array('Modifikator i', 'Groß- und Kleinschreibung ignorieren.');
$GLOBALS['TL_LANG']['tl_site_export_rules']['modMultiLine'] = array('Modifikator m', 'Zeilenumbrüche ignorieren.');
$GLOBALS['TL_LANG']['tl_site_export_rules']['modDotAll'] = array('Modifikator s', 'Punkt umfasst alle Zeichen.');
$GLOBALS['TL_LANG']['tl_site_export_rules']['modUngreedy'] = array('Modifikator U', 'Gier von Quantifikatoren umkehren.');
$GLOBALS['TL_LANG']['tl_site_export_rules']['modUTF8'] = array('Modifikator u', 'Suchmuster werden als UTF-8 behandelt.');

$GLOBALS['TL_LANG']['tl_site_export_rules']['isActive'] = array('Regel beim Export anwenden', 'Die Regel beim Export anwenden.');

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_site_export_rules']['new']        = array('Neue Regel', 'Eine neue Regel erstellen');