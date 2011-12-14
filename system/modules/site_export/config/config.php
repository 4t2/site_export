<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['system']['site_export'] = array
(
		'tables'     => array('tl_site_export', 'tl_site_export_rules'),
		'icon'       => 'system/modules/site_export/html/images/html.png',
		'export' => array('SiteExport', 'export')
		#'stylesheet' => 'system/modules/lingolia/html/css/be_wordlist.css',
		#'javascript' => 'system/modules/lingolia/html/js/be_wordlist.js'
);


/* Hooks */
#$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('SiteExportHooks', 'parseTemplate');


$GLOBALS['SITEEXPORT']['ZIP']['BIN'] = '/usr/bin/zip';
