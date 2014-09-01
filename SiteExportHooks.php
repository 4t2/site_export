<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

class SiteExportHooks extends \PageRegular
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


	public function outputFrontendTemplateHook($strContent, $strTemplate)
	{
		if (!empty($_GET['export']) && substr($strTemplate, 0, 3) == 'fe_')
		{
			$strContent = $this->replaceInsertTags($strContent, true);
			$strContent = $this->replaceDynamicScriptTags($strContent);
		}

		return $strContent;
	}


	public function getPageLayoutHook(&$objPage, &$objLayout, &$objPageRegular)
	{
		if ($this->Input->get('export') == '1')
		{
			$GLOBALS['SITE_EXPORT'] = true;
			$GLOBALS['TL_CONFIG']['enableSearch'] = false;

			if ($this->Input->get('layout') != '' && is_numeric($this->Input->get('layout')))
			{
				$objPage->layout = (int)$this->Input->get('layout');
				$objLayout = $this->getPageLayout($objPage);
			}
			
			unset($GLOBALS['SITE_EXPORT']);
		}
	}


	public function generatePageHook(&$objPage, &$objLayout, &$objPageRegular)
	{
		if ($this->Input->get('export') == '1')
		{
			$GLOBALS['SITE_EXPORT'] = true;
			$GLOBALS['TL_CONFIG']['enableSearch'] = false;

			if ($this->Input->get('layout') != '' && is_numeric($this->Input->get('layout')))
			{
				$objPage->layout = (int)$this->Input->get('layout');

				$objLayout = $this->getPageLayout($objPage->layout);

				$objPage->template = ($objLayout->template != '') ? $objLayout->template : 'fe_page';
				$objPage->templateGroup = $objLayout->templates;
		
				// Store the output format
				list($strFormat, $strVariant) = explode('_', $objLayout->doctype);
				$objPage->outputFormat = $strFormat;
				$objPage->outputVariant = $strVariant;
		
				// Initialize the template
				$objPageRegular->createTemplate($objPage, $objLayout);
		
				// Initialize modules and sections
				$arrCustomSections = array();
				$arrSections = array('header', 'left', 'right', 'main', 'footer');
				$arrModules = deserialize($objLayout->modules);

				// Generate all modules
				foreach ($arrModules as $arrModule)
				{
					if (in_array($arrModule['col'], $arrSections))
					{
						// Filter active sections (see #3273)
						if ($arrModule['col'] == 'header' && !$objLayout->header)
						{
							continue;
						}
						if ($arrModule['col'] == 'left' && $objLayout->cols != '2cll' && $objLayout->cols != '3cl')
						{
							continue;
						}
						if ($arrModule['col'] == 'right' && $objLayout->cols != '2clr' && $objLayout->cols != '3cl')
						{
							continue;
						}
						if ($arrModule['col'] == 'footer' && !$objLayout->footer)
						{
							continue;
						}
		
						$objPageRegular->Template->$arrModule['col'] .= $objPageRegular->getFrontendModule($arrModule['mod'], $arrModule['col']);
					}
					else
					{
						$arrCustomSections[$arrModule['col']] .= $objPageRegular->getFrontendModule($arrModule['mod'], $arrModule['col']);
					}
				}
		
				$objPageRegular->Template->sections = $arrCustomSections;
			}
			
			unset($GLOBALS['SITE_EXPORT']);
		}
	}
}
