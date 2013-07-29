<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['system']['site_export'] = array
(
		'tables'     => array('tl_site_export', 'tl_site_export_rules'),
		'icon'       => 'system/modules/site_export/assets/images/html.png',
		'export' => array('SiteExport', 'export'),
		'stylesheet' => 'system/modules/site_export/assets/styles/site_export.css'
);


/* Hooks */
#$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('SiteExportHooks', 'outputFrontendTemplateHook');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('SiteExportHooks', 'generatePage');

$GLOBALS['SITEEXPORT']['ZIP']['BIN'] = '/usr/bin/zip';