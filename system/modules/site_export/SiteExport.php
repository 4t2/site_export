<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Class SiteExport
 *
 * @copyright  Lingo4you 2013
 * @author     Mario Müller <http://www.lingolia.com/>
 * @package    SiteExport
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */
class SiteExport extends Backend
{
	protected $arrPages = array();
	protected $arrPageList = array();
	protected $strTargetFolder;
	protected $arrParentId = array();
	protected $exportRules = array();
	protected $epubExport = false;
	protected $arrFilename = array();


	/**
	 * Export a theme
	 * @param object
	 */
	public function export($dc)
	{
		global $objPage;

		define('EX_TL_MODE_FE', true);

		// Get the site export data
		$objSiteExport = $this->Database->prepare("SELECT * FROM `tl_site_export` WHERE `id`=?")
			->limit(1)
			->execute($dc->id);

		if (($this->strTargetFolder = $this->getTargetFolder($objSiteExport)) === false)
		{
			return '<div><strong>'.sprintf($GLOBALS['TL_LANG']['MSC']['exportDirectoryError'], $this->targetDir).'</strong></div>';
		}

		$this->exportEpub = $objSiteExport->exportEpub;

		$this->arrPageList = deserialize($objSiteExport->pages, true);

		$html = '<div id="tl_buttons" style="margin-bottom:10px"><a href="contao/main.php?do=site_export" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'" accesskey="b" onclick="Backend.getScrollOffset();">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a></div>';
		$html .= '<div class="tl_panel">';

		if ($this->Input->get('step') == 'preview')
		{
			$html .= sprintf('<div style="float:left; padding-left: 10px;"><div style="padding-top: 6px;">%s</div></div>', $GLOBALS['TL_LANG']['MSC']['previewPagesToExport']);
			$html .= sprintf('<div style="float:right; padding-right: 4px;"><form method="get" class="popup info" id="site_export" action="%s"><div class="tl_formbody"><button type="submit" name="step" value="go" class="tl_submit button_html"><span>%s</span></button><input type="hidden" name="do" value="site_export"><input type="hidden" name="key" value="export"><input type="hidden" name="id" value="'.$dc->id.'"></div></form></div>', $this->Environment->script, $GLOBALS['TL_LANG']['MSC']['startExport']);
			$html .= '<div class="clear"></div>';
			
			$html .= '<div style="padding-top: 8px;">'.sprintf($GLOBALS['TL_LANG']['MSC']['deleteExistingFiles'], $this->strTargetFolder).'</div>';
		}
		elseif ($this->Input->get('step') == 'go')
		{
			$files = Files::getInstance();
			$files->rrdir($this->strTargetFolder);

			if ($this->exportEpub)
			{
				$html .= '<div style="float:left; padding-left: 10px;"><div style="padding-top: 6px;">'.$GLOBALS['TL_LANG']['MSC']['pagesExportedTitle'].'</div></div>';
				
				$html .= '<div style="float:right; padding-right: 4px;"><form method="get" class="popup info" id="site_export" action="'.$this->Environment->script.'"><div class="tl_formbody">';
				
				$html .= '<button type="submit" name="step" value="epub" class="tl_submit button_epub" title="'.$GLOBALS['TL_LANG']['MSC']['generateEpubTitle'].'"><span>'.$GLOBALS['TL_LANG']['MSC']['generateEpub'].'</span></button>';

				$html .= '<input type="hidden" name="do" value="site_export"><input type="hidden" name="key" value="export"><input type="hidden" name="id" value="'.$dc->id.'"></div></form></div>';
				$html .= '<div class="clear"></div>';
			}
			else
			{
				$html .= '<div style="padding-left: 10px;"><div style="padding-top: 6px;">'.$GLOBALS['TL_LANG']['MSC']['pagesExportedTitle'].'</div></div>';
			}	
		}
		elseif ($this->Input->get('step') == 'epub')
		{
			$html .= '<div style="float:left; padding-left: 10px;"><div style="padding-top: 6px;">'.$GLOBALS['TL_LANG']['MSC']['generateEpub'].'</div></div>';
			$html .= '<div style="float:right; padding-right: 4px;"><form method="get" class="popup info" id="site_export" action="' . $objSiteExport->targetDir.'/'.$objSiteExport->ebookFilename.'"><div class="tl_formbody">';
			$html = sprintf('<input type="submit" value="%s" title="%s" class="tl_submit"></div></form></div>', $GLOBALS['TL_LANG']['MSC']['epubDownload'], $GLOBALS['TL_LANG']['MSC']['epubDownloadTitle'] );
			$html .= '<div class="clear"></div>';
		}

		$html .= '</div>';
		$html .= '<div class="tl_listing_container">';

		if ($objSiteExport->recursive && (is_array($this->arrPageList) || count($this->arrPageList) > 0))
		{
			for ($i=count($this->arrPageList)-1; $i>=0; $i--)
			{
				array_splice($this->arrPageList, $i+1, 0, $this->getChildPages($this->arrPageList[$i]));
			}
		}

		if (count($this->arrPageList) > 0)
		{
			foreach ($this->arrPageList as $pageId)
			{
				$objPage = $this->getPageDetails($pageId);

				if ($objPage != null)
				{
					if ($objSiteExport->includeLayout)
					{
						$objPage->includeLayout = $objSiteExport->includeLayout;
						$objPage->layout = $objSiteExport->layout;
					}

					$strFilename = $this->getFilename($objPage);

					$strDomain = $this->Environment->base;

					if ($objPage->domain != '')
					{
						$strDomain = ($this->Environment->ssl ? 'https://' : 'http://') . $objPage->domain . TL_PATH . '/';
					}

					if ($GLOBALS['TL_CONFIG']['addLanguageToUrl'])
					{
						$strUrl = $this->generateFrontendUrl($objPage->row(), null, $objPage->rootLanguage);
					}
					else
					{
						$strUrl = $this->generateFrontendUrl($objPage->row());
					}

					$this->arrFilename[$strUrl] = $strFilename;

					$pageLayout = ($objSiteExport->includeLayout ? $objSiteExport->layout : FALSE);

					$this->arrPages[] = array(
						'title' => $objPage->title,
						'pageTitle' => $objPage->pageTitle,
						'id' => $objPage->id,
						'pid' => $objPage->pid,
						'layout' => $pageLayout,
						'obj' => $objPage,
						'url' => $strDomain.$strUrl,
						'layout' => $pageLayout,
						'exportUrl' => $strDomain.$strUrl.'?export=1'.($pageLayout ? '&layout='.$pageLayout : ''),
						'filename' => $strFilename,
						'level' => $this->getPageLevel($objPage->pid),
						'sort' => (array_search($pageId, $this->arrPageList) !== FALSE ? array_search($pageId, $this->arrPageList) + 9000000 : $objPage->sorting)
					);
				}
				usort($this->arrPages, array($this, 'pageSort'));
			}
		}

		$this->normalizePageLevels();

		/**
		 * generate Epub
		 */
		if ($this->Input->get('step') == 'epub')
		{
			$this->generateEpub($objSiteExport);
			
			if (file_exists(TL_ROOT.'/'.$this->strTargetFolder . '/' . $objSiteExport->ebookFilename))
			{
				$html .= '<p>'.sprintf($GLOBALS['TL_LANG']['MSC']['epubSuccessfullyCreated'], '<tt>'.$objSiteExport->ebookFilename.'</tt>').'</p>';
			}
			else
			{
				$html .= '<p color="red">'.sprintf($GLOBALS['TL_LANG']['MSC']['epubCreateError'], '<tt>'.$objSiteExport->ebookFilename.'</tt>').'</p>';
			}
		}
		elseif (count($this->arrPages))
		{
			// Get the site export rules
			if ($objSiteExport->rulesFrom > 0)
			{
				$objExportRules = $this->Database->prepare("SELECT * FROM tl_site_export_rules WHERE (pid=? OR pid=?) AND isActive='1' ORDER BY `sorting`")
					->execute($dc->id, $objSiteExport->rulesFrom);
			}
			else
			{
				$objExportRules = $this->Database->prepare("SELECT * FROM tl_site_export_rules WHERE pid=? AND isActive='1' ORDER BY `sorting`")
					->execute($dc->id);
			}

			if ($objExportRules->numRows > 0)
			{
				$this->exportRules = $objExportRules->fetchAllAssoc();
			}
			
			if ($this->Input->get('step') == 'go')
			{
				if ($objSiteExport->toc != 'none')
				{
					$toc = '<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>'.$GLOBALS['TL_LANG']['MSC']['tableOfContents'].' '.$objSiteExport->title.'</title>
</head>
<body class="toc">
	<h1>'.$GLOBALS['TL_LANG']['MSC']['se_content'].'</h1>
	<div id="toc">
	<ul>';
				}
			}
			elseif ($objExportRules->numRows > 0)
			{
				$html .= '<p><strong>'.sprintf($GLOBALS['TL_LANG']['MSC']['numberOfRules'], $objExportRules->numRows).'</strong></p>';
			}


			$lastLevel = 0;
			$html .= '<ul class="site_export_tree">';
			
			$arrFlatToc = array();

			foreach ($this->arrPages as $index => $page)
			{
				if ($page['level'] > $lastLevel)
				{
					$html .= str_pad('<ul>', 4*($page['level']-$lastLevel), '<ul>');

					if (in_array($objSiteExport->toc, array('indent', 'json')))
					{
						$toc .= "\n".str_pad("\t", $page['level']+1, "\t").str_pad('<ul>', 4*($page['level']-$lastLevel), '<ul>');
					}
				}
				elseif ($page['level'] < $lastLevel)
				{
					$html .= str_pad('</li></ul>', 10*($lastLevel-$page['level']), '</li></ul>').'</li>';
					
					if (in_array($objSiteExport->toc, array('indent', 'json')))
					{
						$toc .= "\n".str_pad('</li></ul>', 10*($lastLevel-$page['level']), '</li></ul>').'</li>';
					}
				}
				elseif ($index > 0)
				{
					$html .= '</li>';
					$toc .= '</li>'."\n";
				}
				
				$lastLevel = $page['level'];
				
				if ($this->Input->get('step') == 'go')
				{
					$arrFlatToc[] = $page['filename'];

					$file = new File($this->strTargetFolder . '/' . $page['filename']);
/*
					// Fix, because Contao needs the page in global $objPage
					$objPage = $page['obj'];

					$objHandler = new MyPageRegular();

					$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('SiteExportHooks', 'parseTemplate');

					ob_start();
					$objHandler->generate($objPage);	
					$output = ob_get_clean();
*/

					if (function_exists(curl_init))
					{
						$ch = curl_init();
	
						curl_setopt($ch, CURLOPT_URL, $page['exportUrl']);
						curl_setopt($ch, CURLOPT_REFERER, $page['exportUrl']);
						curl_setopt($ch, CURLOPT_USERAGENT, "SiteExport");
						curl_setopt($ch, CURLOPT_HEADER, false);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
						$output = curl_exec($ch);
	
						curl_close($ch);
					}
					else
					{
						$output = file_get_contents($page['exportUrl']);
					}

					if (!empty($output))
					{
						$output = $this->applyRules($output);
		
						$file->write($output);
						
						$html .= '<li>'.sprintf($GLOBALS['TL_LANG']['MSC']['exportFileCompleted'], $page['filename'], strlen($output));
					}
					else
					{
						$html .= '<li>'.sprintf($GLOBALS['TL_LANG']['MSC']['exportFileFailed'], $page['filename']);
					}
					
					$file->close();
				}
				else
				{
					$html .= '<li title="'.$page['url'].'">' . $page['filename'] .'';
				}
				
				if ($objSiteExport->toc != 'none')
				{
					$toc .= '<li><a href="'.$page['filename'].'">' . $page['title'] .'</a>';
				}
			}
			
			$html .= str_pad('</li></ul>', 10*$lastLevel, '</li></ul>');

			if ($objSiteExport->toc != 'none')
			{
				$toc .= str_pad('</li></ul>', 10*$lastLevel, '</li></ul>');
				$toc .= '</li>'."\n".'</ul></div></body></html>';
			}

			if ($this->Input->get('step') == 'go')
			{
				$file = new File($this->strTargetFolder . '/toc.xhtml');

				$file->write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 //EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">');
				$file->write($toc);
				$file->close();
				
				$file = new File($this->strTargetFolder . '/toc.html');
				
				$file->write('<!doctype html><html lang="'.$GLOBALS['TL_LANGUAGE'].'">');
				$file->write($toc);
				$file->close();

				$file = new File($this->strTargetFolder . '/toc.txt');

				$file->write(implode("\n", $arrFlatToc));
				$file->close();

				$this->log('Export ID '.$dc->id.': '.sprintf($GLOBALS['TL_LANG']['MSC']['pagesExported'], count($this->arrPages)), 'SiteExport', TL_FILES);
			}
			
		}
		else
		{
			$this->log(sprintf($GLOBALS['TL_LANG']['MSC']['noPagesFound'], $dc->id), 'SiteExport', TL_FILES);
			return printf($GLOBALS['TL_LANG']['MSC']['noPagesFound'], $dc->id);
		}

		$html .= '</li></ul></div>';


		if (in_array($objSiteExport->toc, array('flat_json', 'json')))
		{
			if ($objSiteExport->toc == 'json')
			{
				$pageList = $this->getPageList($this->arrPages, 0);
			}
			else
			{
				$pageList = $this->arrPages;
			}
			
			$strJSON = '{"toc":['.$this->getJSON($pageList)."\n]}";
			
			$file = new File($this->strTargetFolder . '/toc.json');
			
			$file->write($strJSON);
			$file->close();
		}

		return $html;
	}

/* not used at the time
	protected function generatePdf($objSiteExport)
	{
		// Include library
		require_once(TL_ROOT . '/system/config/tcpdf.php');
		require_once(TL_ROOT . '/plugins/tcpdf/tcpdf.php');

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Mario Müller');
		$pdf->SetTitle($objSiteExport->pdfTitle);
		
		// Prevent font subsetting (huge speed improvement)
		$pdf->setFontSubsetting(false);

		// Remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// Set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

		// Set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		
		// Set font
		$pdf->SetFont(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN);

		// Set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		for ($i=0; $i<count($this->arrPages); $i++)
		{
			$pdf->AddPage();
			
			$fileContent = file_get_contents(TL_ROOT.'/'.$this->strTargetFolder.'/'.$this->arrPages[$i]['filename']);

			$fileContent = str_replace('"./', '"'.TL_ROOT.'/'.$this->strTargetFolder.'/', $fileContent);
			
			$pdf->writeHTML($fileContent, true, false, true, false, '');
		}
		
		$pdf->lastPage();
		
		$pdf->Output(TL_ROOT.'/'.$this->strTargetFolder.'/'.$objSiteExport->pdfFilename, 'I');
	}
*/

	protected function generateEpub($objSiteExport)
	{
		$toc = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
	<head>
		<meta name="dc:Title" content="'.$objSiteExport->ebookTitle.'"/>
		<meta name="dtb:uid" content="'.$objSiteExport->ebookIdentifier.'"/>
	</head>
	<docTitle>
		<text>'.$objSiteExport->ebookTitle.'</text>
	</docTitle>
	<navMap>
';

		/**
		 * create ‘table of contents’
		 */
		$lastLevel = -1;

		for ($i=0; $i<count($this->arrPages); $i++)
		{
			if ($this->arrPages[$i]['level'] <= $lastLevel)
			{
				$toc .= str_pad("\t", $this->arrPages[$i]['level']+1, "\t") . str_pad('</navPoint>', 11*($lastLevel-$this->arrPages[$i]['level']+1), '</navPoint>')."\n";
			}

			$toc .= str_pad("\t", $this->arrPages[$i]['level']+1, "\t") . '<navPoint playOrder="'.$i.'" id="'.$this->arrPages[$i]['navId'].'">'."\n";
			$toc .= str_pad("\t", $this->arrPages[$i]['level']+2, "\t") . '<navLabel><text>'.$this->arrPages[$i]['title'].'</text></navLabel>'."\n";
			$toc .= str_pad("\t", $this->arrPages[$i]['level']+2, "\t") . '<content src="'.$this->arrPages[$i]['filename'].'"/>'."\n";

			$lastLevel = $this->arrPages[$i]['level'];
		}

		$toc .= str_pad("\t", $lastLevel+1, "\t") . str_pad('</navPoint>', 11*($lastLevel+1), '</navPoint>')."\n";

		$toc .= '	</navMap>
</ncx>';

		/**
		 * create ‘content.opf’
		 */
		$content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<package xmlns="http://www.idpf.org/2007/opf" xmlns:dc="http://purl.org/dc/elements/1.1/" unique-identifier="'.$objSiteExport->ebookIdentifier.'" version="2.0">
	<metadata>
		<meta name="generator" content="Contao :: Site Export"/>
		<dc:title>'.$objSiteExport->ebookTitle.'</dc:title>
		<dc:title>'.$objSiteExport->ebookDescription.'</dc:title>
		<dc:creator>'.$objSiteExport->ebookCreator.'</dc:creator>
		<dc:publisher>'.$objSiteExport->ebookPublisher.'</dc:publisher>
		<dc:date>'.$objSiteExport->ebookDate.'</dc:date>
		<dc:language>'.$objSiteExport->ebookLanguage.'</dc:language>
		<dc:identifier> id="'.$objSiteExport->ebookIdentifier.'">'.$objSiteExport->ebookIdentifier.'</dc:identifier>
		<dc:subject>'.$objSiteExport->ebookSubject.'</dc:subject>
';

		if ($objSiteExport->ebookCover != '')
		{
			$file = new File($objSiteExport->ebookCover);

			$epubCoverFile = basename($objSiteExport->ebookCover);

			$file->copyTo($this->strTargetFolder.'/images/'.$epubCoverFile);

			$content .= '		<meta name="cover" content="'.$epubCoverFile.'"/>';
		}
		else
		{
			$epubCoverFile = false;
		}

		$content .= '
	</metadata>
	<manifest>
		<item id="ncx" href="toc.ncx" media-type="application/x-dtbncx+xml"/>
';

		foreach ($this->getFiles($this->strTargetFolder.'/images', array('.jpg', '.jpeg')) as $file)
		{
			$content .= "\t\t".'<item id="'.$file.'" href="images/'.$file.'" media-type="image/jpeg"/>'."\n";
		}
		foreach ($this->getFiles($this->strTargetFolder.'/images', array('.png')) as $file)
		{
			$content .= "\t\t".'<item id="'.$file.'" href="images/'.$file.'" media-type="image/png"/>'."\n";
		}
		foreach ($this->getFiles($this->strTargetFolder, array('.css')) as $file)
		{
			$content .= "\t\t".'<item id="'.$file.'" href="'.$file.'" media-type="text/css"/>'."\n";
		}

		$spine = '	<spine toc="ncx">'."\n";
		
		if ($objSiteExport->toc != 'none')
		{
			$content .= "\t\t".'<item id="id_book_toc" href="toc.xhtml" media-type="application/xhtml+xml"/>'."\n";
			$spine .= "\t\t".'<itemref idref="id_book_toc"/>'."\n";
		}
		
		for ($i=0; $i<count($this->arrPages); $i++)
		{
			$content .= "\t\t".'<item id="'.$this->arrPages[$i]['navId'].'" href="'.$this->arrPages[$i]['filename'].'" media-type="application/xhtml+xml"/>'."\n";
			$spine .= "\t\t".'<itemref idref="'.$this->arrPages[$i]['navId'].'"/>'."\n";
		}
		
		$spine .= '	</spine>
';

		$content .= '	</manifest>
' . $spine . '
</package>';



		$arrDataFiles = scandir(TL_ROOT.'/'.$this->strTargetFolder);
		$arrImageFiles = scandir(TL_ROOT.'/'.$this->strTargetFolder.'/images');

		$objArchive = new ZipWriter($this->strTargetFolder.'/'.$objSiteExport->ebookFilename);

		$objArchive->addString('application/epub+zip', 'mimetype');

		$objArchive->addString('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
		<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">
			<rootfiles>
				<rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>
			</rootfiles>
		</container>', 'META-INF/container.xml');

		$objArchive->addString($toc, 'OEBPS/toc.ncx');

		$objArchive->addString($content, 'OEBPS/content.opf');
		
		$files = Files::getInstance();

		foreach ($arrDataFiles as $strFile)
		{
			if (!is_dir(TL_ROOT.'/'.$this->strTargetFolder.'/'.$strFile) && !in_array($strFile, array('.', '..')))
			{
				$file = new File($this->strTargetFolder.'/'.$strFile);

				$objArchive->addString($file->getContent(), 'OEBPS/'.$strFile);
				
				$file->close();
			}
		}

		foreach ($arrImageFiles as $strFile)
		{
			if (!is_dir(TL_ROOT.'/'.$this->strTargetFolder.'/'.$strFile) && !in_array($strFile, array('.', '..')))
			{
				$file = new File($this->strTargetFolder.'/images/'.$strFile);

				$objArchive->addString($file->getContent(), 'OEBPS/images/'.$strFile);
				
				$file->close();
			}
		}
		
		$files->rrdir($this->strTargetFolder, true);

		$objArchive->close();

		/**
		 * zip epub file
		 */
##		chdir($this->strTargetFolder);

##		exec('cd '.$this->strTargetFolder);
##		exec($GLOBALS['SITEEXPORT']['ZIP']['BIN'].' -X0 "'.$this->strTargetFolder.'/'.$objSiteExport->ebookFilename.'" mimetype ');
##		exec($GLOBALS['SITEEXPORT']['ZIP']['BIN'].' -rDX9 "'.$this->strTargetFolder.'/'.$objSiteExport->ebookFilename.'" * -x mimetype -x *.epub');
	}


	/**
	 * create a JSON file with table of contents
	 */
	protected function getJSON($pageList)
	{
		$strData = "";
		$strStart = '';

		for ($i=0; $i<count($pageList); $i++)
		{
			$strData .= $strStart."\n".
				str_pad("\t", $pageList[$i]['level'], "\t").'{"title":"'.str_replace('"', '\"', $pageList[$i]['title']).'","pageTitle":"'.str_replace('"', '\"', $pageList[$i]['pageTitle']).'","file":"'.$pageList[$i]['filename'].'","id":"'.$pageList[$i]['id'].'","pid":"'.$pageList[$i]['pid'].'"';

			if (is_array($pageList[$i]['childs']))
			{
				$strData .= ', "childs":'."\n".str_pad("\t", $pageList[$i]['level'], "\t")."[".$this->getJSON($pageList[$i]['childs'])."\n".str_pad("\t", $pageList[$i]['level'])."]";
			}

			$strData .= "}";
			$strStart = ',';
		}

		$strData .= "";
		
		return $strData;
	}


	/**
	 * get nested page list
	 */
	protected function getPageList($flatList, $level = 0)
	{
		$pageList = array();

		for ($i=0; $i<count($flatList); $i++)
		{
			if ($flatList[$i]['level'] == $level)
			{
				$flatList[$i]['childs'] = false;
				$pageList[] = $flatList[$i];
			}
			elseif ($flatList[$i]['level'] > $level)
			{
				$arrPages = array();

				while ($i<count($flatList) && $flatList[$i]['level'] > $level)
				{
					$arrPages[] = $flatList[$i];
					$i++;
				}
				
				$pageList[count($pageList)-1]['childs'] = $this->getPageList($arrPages, $level+1);
				
				$i--;
			}
			else
			{
				return $pageList;
			}
		}
		
		return $pageList;
	}

	/**
	 * shrink page levels to avoid gaps
	 */
	protected function normalizePageLevels()
	{
		$level = -1;

		for ($i=0; $i<count($this->arrPages); $i++)
		{
			if (($this->arrPages[$i]['level'] - $level) > 1)
			{
				$stopLevel = $this->arrPages[$i]['level'];
				$diffLevel = $this->arrPages[$i]['level'] - $level - 1;

				for ($t=$i; $t<count($this->arrPages) && $this->arrPages[$t]['level']>=$stopLevel; $t++)
				{
					$this->arrPages[$t]['level'] -= $diffLevel;
				}
			}
			$level = $this->arrPages[$i]['level'];
			
			if ($this->exportEpub)
			{
				$this->arrPages[$i]['navId'] = 'id_book_' . $i;
			}
		}
	}


	/**
	 * retrieve the page level
	 */
	protected function getPageLevel($pid)
	{
		$level = 1;
		
		while ($pid > 0)
		{
			if (isset($this->arrParentId[$pid]))
			{
				$pid = $this->arrParentId[$pid];
			}
			else
			{
				$objPage = $this->Database->prepare("SELECT `pid` FROM tl_page WHERE `id`=?")->execute($pid);
				$this->arrParentId[$pid] = $objPage->pid;
				$pid = $objPage->pid;
			}
			
			$level++;
		}
		
		return $level;
	}


	/**
	 * apply all export rules
	 */
	protected function applyRules($strContent)
	{
		$strContent = preg_replace_callback('~(<a.*href=")(.*)(".*>)~isU', 'self::replaceLinks', $strContent);

		foreach ($this->exportRules as $rule)
		{
			if ($rule['isRegex'] == '1')
			{
				$pattern = '~' . str_replace('~', '\~', $rule['pattern']) . '~';
				$pattern .= ($rule['modIgnoreCase'] == '1' ? 'i' : '');
				$pattern .= ($rule['modMultiLine']  == '1' ? 'm' : '');
				$pattern .= ($rule['modDotAll']     == '1' ? 's' : '');
				$pattern .= ($rule['modUngreedy']   == '1' ? 'U' : '');
				$pattern .= ($rule['modUTF8']       == '1' ? 'u' : '');
				
				$strTemp = preg_replace($pattern, $rule['replacement'], $strContent);
				
				if (preg_last_error() == PREG_NO_ERROR)
				{
				    $last_error = false;
				}
				else if (preg_last_error() == PREG_INTERNAL_ERROR)
				{
				    $last_error = 'PREG_INTERNAL_ERROR';
				}
				else if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR)
				{
				    $last_error = 'PREG_BACKTRACK_LIMIT_ERROR';
				}
				else if (preg_last_error() == PREG_RECURSION_LIMIT_ERROR)
				{
				    $last_error = 'PREG_RECURSION_LIMIT_ERROR';
				}
				else if (preg_last_error() == PREG_BAD_UTF8_ERROR)
				{
				    $last_error = 'PREG_BAD_UTF8_ERROR';
				}
				else if (preg_last_error() == PREG_BAD_UTF8_ERROR)
				{
				    $last_error = 'PREG_BAD_UTF8_ERROR';
				}
				
				if ($last_error !== false)
				{
					$this->log('Error: ' . $rule['title'] . ' → ' . $last_error, 'SiteExport', TL_ERROR);
				}
				else
				{
					$strContent = $strTemp;
				}

#				$this->log($pattern . ' → ' . $rule['replacement'], 'SiteExport', TL_ERROR);
			}
			else
			{
				$strContent = str_replace($rule['pattern'], $rule['replacement'], $strContent);
			}
		}
		
		$strContent = str_ireplace(
			array('&nbsp;'),
			array(' '),
			$strContent
		);

		if (!is_writeable(TL_ROOT.'/'.$this->strTargetFolder) && is_dir(TL_ROOT.'/'.$this->strTargetFolder))
		{
			chmod(TL_ROOT.'/'.$this->strTargetFolder, 0777);
		}

		/* copy images files */		
		if (!is_dir(TL_ROOT.'/'.$this->strTargetFolder . '/images'))
		{
			mkdir(TL_ROOT.'/'.$this->strTargetFolder . '/images');

			if (!is_writeable(TL_ROOT.'/'.$this->strTargetFolder . '/images') && is_dir(TL_ROOT.'/'.$this->strTargetFolder . '/images'))
			{
				chmod(TL_ROOT.'/'.$this->strTargetFolder . '/images', 0777);
			}
		}

		if (is_writeable(TL_ROOT.'/'.$this->strTargetFolder . '/images') && is_dir(TL_ROOT.'/'.$this->strTargetFolder . '/images'))
		{
			$strContent = preg_replace_callback('~(<img.*src=")(.*(png|jpg|gif))(")~isU', 'self::processImages', $strContent);
		}

		/* copy audio files */
		if (!is_dir(TL_ROOT.'/'.$this->strTargetFolder . '/audio'))
		{
			mkdir(TL_ROOT.'/'.$this->strTargetFolder . '/audio');

			if (!is_writeable(TL_ROOT.'/'.$this->strTargetFolder . '/audio') && is_dir(TL_ROOT.'/'.$this->strTargetFolder . '/audio'))
			{
				chmod(TL_ROOT.'/'.$this->strTargetFolder . '/audio', 0777);
			}
		}

		if (is_writeable(TL_ROOT.'/'.$this->strTargetFolder . '/audio') && is_dir(TL_ROOT.'/'.$this->strTargetFolder . '/audio'))
		{
			$strContent = preg_replace_callback('~(<source.*src=")(.*(mp3|ogg))(")~isU', 'self::processAudio', $strContent);
		}

		/* copy stylesheets */
		$strContent = preg_replace_callback('~(<link.*href=")(.*)(".*>)~isU', 'self::processStylesheets', $strContent);

		if ($this->exportEpub)
		{
#			$str = str_ireplace('</head>', )
		}

		return $strContent;
	}


		protected function replaceLinks($match)
		{
			$link = preg_replace('~https?://.*/~isU', '', $match[2]);

			if (isset($this->arrFilename[$link]))
			{
				$link = $this->arrFilename[$link];
			}
			else
			{
				$link = str_replace('/', '_', $link);

				if (substr($link, -5) != '.html')
				{
					$link .= '.html';
				}
			}
			
			return $match[1].'./'.$link.$match[3];
		}


		protected function processImages($match)
		{
			$src_image = $match[2];
			$filename = 'images/' . str_replace(array('/', ' '), '_', $match[2]);
			$dest_image = $this->strTargetFolder . '/' . $filename;
			
			$this->import('Files');

			if (file_exists(TL_ROOT.'/'.$src_image) && !file_exists(TL_ROOT.'/'.$dest_image))
			{
				$this->Files->copy($src_image, $dest_image);
			}
			
			$arrPathInfo = pathinfo($src_image);
			$strRetinaFilename = $arrPathInfo['dirname'].'/'.$arrPathInfo['filename'].'@2x.'.$arrPathInfo['extension'];
			
			if (file_exists(TL_ROOT.'/'.$strRetinaFilename))
			{
				$this->Files->copy($strRetinaFilename, $this->strTargetFolder.'/images/'.str_replace(array('/', ' '), '_', $strRetinaFilename));
			}

			return $match[1].'./'.$filename.$match[4];
		}


		protected function processAudio($match)
		{
			$src_audio = $match[2];
			$filename = 'audio/' . str_replace(array('/', ' '), '_', $src_audio);
			$dest_audio = $this->strTargetFolder . '/' . $filename;

			$this->import('Files');

			if (file_exists(TL_ROOT.'/'.$src_audio) && !file_exists(TL_ROOT.'/'.$dest_audio))
			{
				$this->Files->copy($src_audio, $dest_audio);
			}

			return $match[1].'./'.$filename.$match[4];
		}


		protected function processStylesheets($match)
		{
			if (stristr($match[1], 'rel="static-stylesheet"') !== FALSE)
			{
				return str_ireplace('rel="static-stylesheet"', 'rel="stylesheet"', $match[1]).$match[2].$match[3];
			}
			elseif (stristr($match[1], 'rel="stylesheet"') !== FALSE || stristr($match[3], 'rel="stylesheet"') !== FALSE)
			{
				$src_stylesheet = TL_ROOT.'/'.$match[2];
				$filename = str_replace(array('/', ' '), '_', $match[2]);
				$dest_stylesheet = TL_ROOT.'/'.$this->strTargetFolder . '/' . $filename;
	
				if (file_exists($src_stylesheet) && !file_exists($dest_stylesheet))
				{
					copy($src_stylesheet, $dest_stylesheet);
				}
	
				return $match[1].'./'.$filename.$match[3];
			}
			else
			{
				return $match[1].$match[2].$match[3];
			}
		}

	

	protected function getFilename($objPage)
	{
		$arrParts = explode('/', $objPage->alias);

		return $arrParts[count($arrParts)-1].'_'.$objPage->id.'.html';
	}


	protected function getFiles($path, $extensions = false)
	{
		$files = array();

		$handle = opendir(TL_ROOT.'/'.$path);
	
		while ($tmp = readdir($handle))
		{
			if (($tmp!='.') && ($tmp!='..') && (!$extensions || in_array(strrchr($tmp, '.'), $extensions)))
			{
				$files[] = $tmp;
			}
		}
		
		return $files;
	}


	/**
	 * delete all files in target folder
	 */
	protected function deleteFiles($path, $test=false)
	{
		$fileCount = 0;

		if (!is_writeable(TL_ROOT.'/'.$path) && is_dir(TL_ROOT.'/'.$path))
		{
			chmod(TL_ROOT.'/'.$path, 0777);
		}

		if (is_writeable(TL_ROOT.'/'.$path) && is_dir(TL_ROOT.'/'.$path))
		{
			$handle = opendir(TL_ROOT.'/'.$path);
	
			while ($tmp = readdir($handle))
			{
				if (in_array(strrchr($tmp, '.'), array('.html', '.xhtml', '.html5', '.css', '.jpg', 'png', 'gif')))
				{
					if (is_writeable(TL_ROOT.'/'.$path.'/'.$tmp) && is_file(TL_ROOT.'/'.$path.'/'.$tmp))
					{
						if (!$test)
						{
							if (unlink(TL_ROOT.'/'.$path.'/'.$tmp))
							{
								$fileCount++;
							}
						}
						else
						{
							$fileCount++;
						}
					}
					elseif (!is_writeable(TL_ROOT.'/'.$path.'/'.$tmp) && is_file(TL_ROOT.'/'.$path.'/'.$tmp))
					{
						if (!$test)
						{
							chmod(TL_ROOT.'/'.$path.'/'.$tmp, 0666);
							if (unlink(TL_ROOT.'/'.$path.'/'.$tmp))
							{
								$fileCount++;
							}
						}
						else
						{
							$fileCount++;
						}
					}
				}
			}

			closedir($handle);
		}
		
		return $fileCount;
	}


	/**
	 * Gets all child pages if article_list_recursive is set
	 */
	protected function getChildPages($pageId, $recursive = true, $level=0)
	{
		$pageArray = array();

		$objPages = $this->Database->prepare("SELECT `id` FROM `tl_page` WHERE `pid`=? AND `published`='1' ORDER BY `sorting`")->execute($pageId);

		while ($objPages->next())
		{
			$pageArray[] = $objPages->id;
			
			if ($recursive)
			{
				$pageArray = array_merge($pageArray, $this->getChildPages($objPages->id, $recursive, $level+1));
			}
		}

		return $pageArray;
	}
	
	
		/**
		 * Helper function for usort
		 */
		protected function pageSort($a, $b)
		{
			if ($a['sort'] == $b['sort']) {
			    return 0;
			}
			return ($a['sort'] < $b['sort']) ? -1 : 1;
		}


	/**
	 * get the target folder for export files
	 */
	protected function getTargetFolder($objSiteExport)
	{
		if (version_compare(VERSION, '3', '>='))
		{
			$objFolder = \FilesModel::findByPk($objSiteExport->targetDir);
			$strFolder = $objFolder->path;
		}
		else
		{
			$strFolder = $objSiteExport->targetDir;
		}

		if (!is_writeable(TL_ROOT.'/'.$strFolder) || !is_dir(TL_ROOT.'/'.$strFolder))
		{
			$this->log(sprintf($GLOBALS['TL_LANG']['MSC']['exportDirectoryError'], $strFolder), 'SiteExport', TL_ERROR);
			return false;
		}
		
		return $strFolder;
	}
}

/*
class MyPageRegular extends PageRegular
{
	public function __construct()
	{
		parent::__construct();
	}
}
*/