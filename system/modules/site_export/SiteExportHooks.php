<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');


class SiteExportHooks extends Frontend
{
   public function parseTemplate(&$objTemplate)
   {
       global $objPage;

		if ($objPage->outputFormat != '')
		{
			$objTemplate->setFormat($objPage->outputFormat);
		}

		$objTemplate->strTagEnding = ($objTemplate->strFormat == 'xhtml') ? ' />' : '>';
     }
}
