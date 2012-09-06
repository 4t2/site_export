<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Site_export
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
#	'FrontendHook' 		=> 'system/modules/site_export/FrontendHook.php',
	'SiteExportHooks' 	=> 'system/modules/site_export/SiteExportHooks.php',
	'SiteExport'      	=> 'system/modules/site_export/SiteExport.php',
));
