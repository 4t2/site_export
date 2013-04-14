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
	protected $pageList = array();
	protected $targetDir;
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
		
		if (version_compare(VERSION, '3', '>='))
		{
			$objFolder = \FilesModel::findByPk($objSiteExport->targetDir);
			$this->targetDir = $objFolder->path;
		}
		else
		{
			$this->targetDir = $objSiteExport->targetDir;
		}

		if (!is_writeable(TL_ROOT.'/'.$this->targetDir) || !is_dir(TL_ROOT.'/'.$this->targetDir))
		{
			$this->log('Das Export-Verzeichnis '.$this->targetDir.' existiert nicht oder ist nicht beschreibbar.', 'SiteExport', TL_ERROR);
			return '<div><strong>Fehler:</strong> Das Export-Verzeichnis '.$this->targetDir.' existiert nicht oder ist nicht beschreibbar.</div>';
		}

		if ($objSiteExport->exportEpub == '1')
		{
			$this->epubExport = true;
		}

		$this->pageList = deserialize($objSiteExport->pages, true);
/*
		if (!is_array($this->pageList))
		{
			$this->pageList = array($this->pageList);
		}
*/
		$html = '<div id="tl_buttons" style="margin-bottom:10px"><a href="contao/main.php?do=site_export" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'" accesskey="b" onclick="Backend.getScrollOffset();">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a></div>';
		$html .= '<div class="tl_panel">';

		if ($this->Input->get('step') == 'preview')
		{
			$html .= '<div style="float:left; padding-left: 10px;"><div style="padding-top: 6px;">Vorschau der zu exportierenden Seiten.</div></div>';
			$html .= '<div style="float:right; padding-right: 4px;"><form method="get" class="popup info" id="site_export" action="'.$this->Environment->script.'"><div class="tl_formbody"><input type="submit" value="Export starten" title="Export mit angezeigten Seiten starten" class="tl_submit"><input type="hidden" name="do" value="site_export"><input type="hidden" name="key" value="export"><input type="hidden" name="step" value="go"><input type="hidden" name="id" value="'.$dc->id.'"></div></form></div>';
			$html .= '<div class="clear"></div>';
			
			$html .= '<div style="padding-top: 8px;"><strong>Hinweis:</strong> Beim Export werden vorhandene Dateien und Bilder im Verzeichnis '.$this->targetDir.' gelöscht.</div>';
		}
		elseif ($this->Input->get('step') == 'go')
		{
			$files = Files::getInstance();
			$files->rrdir($this->targetDir);

			$html .= '<div style="float:left; padding-left: 10px;"><div style="padding-top: 6px;">Seiten wurden exportiert.</div></div>';
			
			if ($this->epubExport)
			{
				$html .= '<div style="float:right; padding-right: 4px;"><form method="get" class="popup info" id="site_export" action="'.$this->Environment->script.'"><div class="tl_formbody"><input type="submit" value="Epub erzeugen" title="exportierte Seiten als Epub packen" class="tl_submit"><input type="hidden" name="do" value="site_export"><input type="hidden" name="key" value="export"><input type="hidden" name="step" value="epub"><input type="hidden" name="id" value="'.$dc->id.'"></div></form></div>';
			}
#			$html .= '<div style="float:right; padding-right: 4px;"><form method="get" class="popup info" id="site_export" action="'.$this->Environment->script.'"><div class="tl_formbody"><input type="submit" value="Kindle erzeugen" title="exportierte Seiten als Epub packen" class="tl_submit"><input type="hidden" name="do" value="site_export"><input type="hidden" name="key" value="export"><input type="hidden" name="step" value="mobi"><input type="hidden" name="id" value="'.$dc->id.'"></div></form></div>';
			$html .= '<div class="clear"></div>';
		}
		elseif ($this->Input->get('step') == 'epub')
		{
			$html .= '<div style="float:left; padding-left: 10px;"><div style="padding-top: 6px;">Epub Export.</div></div>';
			$html .= '<div style="float:right; padding-right: 4px;"><form method="get" class="popup info" id="site_export" action="' . $objSiteExport->targetDir.'/'.$objSiteExport->ebookFilename.'"><div class="tl_formbody"><input type="submit" value="Epub Download" title="exportierte Seiten als Epub herunterladen" class="tl_submit"></div></form></div>';
			$html .= '<div class="clear"></div>';
		}

		$html .= '</div>';
		$html .= '<div class="tl_listing_container">';
#die(var_export($this->pageList, true));
		if ($objSiteExport->recursive && (is_array($this->pageList) || count($this->pageList) > 0))
		{
			for ($i=count($this->pageList)-1; $i>=0; $i--)
			{
				array_splice($this->pageList, $i+1, 0, $this->getChildPages($this->pageList[$i]));
			}
		}

		if (count($this->pageList) > 0)
		{
			foreach ($this->pageList as $pageId)
			{
				#$objPage = $this->Database->prepare("SELECT * FROM `tl_page` WHERE `id`=?")->limit(1)->execute($pageId);
				
				$objPage = $this->getPageDetails($pageId);

				#if ($objPage->numRows > 0)
				if ($objPage != null)
				{
					if ($objSiteExport->includeLayout)
					{
						$objPage->includeLayout = $objSiteExport->includeLayout;
						$objPage->layout = $objSiteExport->layout;
					}
					
					$strFilename = $this->getFilename($objPage);
					
					$this->arrFilename[$url] = $strFilename;
					
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
					
					$pageLayout = ($objSiteExport->includeLayout ? $objSiteExport->layout : FALSE);

					$this->arrPages[] = array(
						'title' => $objPage->title,
						'pageTitle' => $objPage->pageTitle,
						'id' => $objPage->id,
						'pid' => $objPage->pid,
						'layout' => $pageLayout,
						'obj' => $objPage,
						'url' => $strDomain.$strUrl,
						'exportUrl' => $strDomain.$strUrl.'?export=1'.($pageLayout ? '&layout='.$pageLayout : ''),
						'filename' => $strFilename,
						'level' => $this->getPageLevel($objPage->pid),
						'sort' => (array_search($pageId, $this->pageList) !== FALSE ? array_search($pageId, $this->pageList) + 9000000 : $objPage->sorting)
					);
				}
				usort($this->arrPages, array($this, 'pageSort'));
			}
		}

		$this->normalizePageLevels();


		/**
		 * Epub exportieren
		 */
		if ($this->Input->get('step') == 'epub')
		{
			$this->createEpub($objSiteExport);
			
			if (file_exists(TL_ROOT.'/'.$this->targetDir . '/' . $objSiteExport->ebookFilename))
			{
				$html .= '<p>Epub <tt>'.$objSiteExport->ebookFilename.'</tt> erfolgreich erstellt.</p>';
			}
			else
			{
				$html .= '<p color="red">Fehler beim Erstellen von <tt>'.$objSiteExport->ebookFilename.'</tt> aufgetreten!</p>';
			}

#			$html .= '<pre>'.htmlspecialchars(file_get_contents($this->targetDir.'/toc.ncx')).'</pre>';
		}
		elseif (count($this->arrPages))
		{
			if ($this->Input->get('step') == 'go')
			{
				// Get the site export data
				$objExportRules = $this->Database->prepare("SELECT * FROM tl_site_export_rules WHERE pid=? AND isActive='1' ORDER BY `sorting`")
					->execute($dc->id);

				if ($objExportRules->numRows > 0)
				{
					$this->exportRules = $objExportRules->fetchAllAssoc();
				}

				if ($objSiteExport->toc != 'none')
				{
					$toc = '<!doctype html><html lang="de"><head><meta charset="utf-8"><title>Inhaltsverzeichnis '.$objSiteExport->title.'</title><meta charset="utf-8"></head><body><h1>Inhaltsverzeichnis '.$objSiteExport->title.'</h1><ul>';
					$toc = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 //EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Inhaltsverzeichnis '.$objSiteExport->title.'</title>
</head>
<body>
	<h1>Inhalt</h1>
	<ul>';
				}
			}


			$lastLevel = 0;
			$html .= '<ul class="site_export_tree">';

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
					$file = new File($this->targetDir . '/' . $page['filename']);
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
						
						$html .= '<li>Export … ' . $page['filename'] . ' (' . strlen($output) . ' byte)';
					}
					else
					{
						$html .= '<li>Export … ' . $page['filename'] . ' – ERROR –';
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
				$toc .= '</li>'."\n".'</ul></body></html>';
			}

			if ($this->Input->get('step') == 'go')
			{
				$file = new File($this->targetDir . '/toc.html');

				$file->write($toc);
				$file->close();

				$this->log('Export ID '.$dc->id.': '.count($this->arrPages).' pages saved', 'SiteExport', TL_FILES);
			}
			
		}
		else
		{
			$this->log('Export ID '.$dc->id.': No pages found!', 'SiteExport', TL_FILES);
			return 'Export ID '.$dc->id.': No pages found!';
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
			
			$file = new File($this->targetDir . '/toc.json');
			
			$file->write($strJSON);
			$file->close();
		}

		return $html;
	}


	protected function createEpub($objSiteExport)
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

			$file->copyTo($this->targetDir.'/images/'.$epubCoverFile);

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

		foreach ($this->getFiles($this->targetDir.'/images', array('.jpg', '.jpeg')) as $file)
		{
			$content .= "\t\t".'<item id="'.$file.'" href="images/'.$file.'" media-type="image/jpeg"/>'."\n";
		}
		foreach ($this->getFiles($this->targetDir.'/images', array('.png')) as $file)
		{
			$content .= "\t\t".'<item id="'.$file.'" href="images/'.$file.'" media-type="image/png"/>'."\n";
		}
		foreach ($this->getFiles($this->targetDir, array('.css')) as $file)
		{
			$content .= "\t\t".'<item id="'.$file.'" href="'.$file.'" media-type="text/css"/>'."\n";
		}

		$spine = '	<spine toc="ncx">'."\n";
		
		if ($objSiteExport->toc != 'none')
		{
			$content .= "\t\t".'<item id="id_book_toc" href="toc.html" media-type="application/xhtml+xml"/>'."\n";
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



		$arrDataFiles = scandir(TL_ROOT.'/'.$this->targetDir);
		$arrImageFiles = scandir(TL_ROOT.'/'.$this->targetDir.'/images');

		$objArchive = new ZipWriter($this->targetDir.'/'.$objSiteExport->ebookFilename);

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
			if (!is_dir(TL_ROOT.'/'.$this->targetDir.'/'.$strFile) && !in_array($strFile, array('.', '..')))
			{
				$file = new File($this->targetDir.'/'.$strFile);

				$objArchive->addString($file->getContent(), 'OEBPS/'.$strFile);
				
				$file->close();
			}
		}

		foreach ($arrImageFiles as $strFile)
		{
			if (!is_dir(TL_ROOT.'/'.$this->targetDir.'/'.$strFile) && !in_array($strFile, array('.', '..')))
			{
				$file = new File($this->targetDir.'/images/'.$strFile);

				$objArchive->addString($file->getContent(), 'OEBPS/images/'.$strFile);
				
				$file->close();
			}
		}
		
		$files->rrdir($this->targetDir, true);

		$objArchive->close();

		/**
		 * zip epub file
		 */
##		chdir($this->targetDir);

##		exec('cd '.$this->targetDir);
##		exec($GLOBALS['SITEEXPORT']['ZIP']['BIN'].' -X0 "'.$this->targetDir.'/'.$objSiteExport->ebookFilename.'" mimetype ');
##		exec($GLOBALS['SITEEXPORT']['ZIP']['BIN'].' -rDX9 "'.$this->targetDir.'/'.$objSiteExport->ebookFilename.'" * -x mimetype -x *.epub');
	}


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
#die(var_export($pageList[$i]['childs'], true));
				$strData .= ', "childs":'."\n".str_pad("\t", $pageList[$i]['level'], "\t")."[".$this->getJSON($pageList[$i]['childs'])."\n".str_pad("\t", $pageList[$i]['level'])."]";
			}

			$strData .= "}";
			$strStart = ',';
		}

		$strData .= "";
		
		return $strData;
	}


	protected function getPageList($flatList, $level = 0)
	{
		$pageList = array();

		for ($i=0; $i<count($flatList); $i++)
		{
			if ($flatList[$i]['level'] == $level)
			{
				$flatList[$i]['childs'] = false;
#				$arrTest = array('title'=>$flatList[$i]['title'], 'childs'=>false);
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
			
			if ($this->epubExport)
			{
				$this->arrPages[$i]['navId'] = 'id_book_' . $i;
			}
		}
	}


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


	protected function applyRules($str)
	{
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
				
				$str = preg_replace($pattern, $rule['replacement'], $str);

#				$this->log($pattern . ' → ' . $rule['replacement'], 'SiteExport', TL_ERROR);
			}
			else
			{
				$str = str_replace($rule['pattern'], $rule['replacement'], $str);
			}
		}
		
		$str = str_ireplace(
			array('&nbsp;'),
			array(' '),
			$str
		);

		$str = preg_replace_callback('~(<a.*href=")(.*)(".*>)~isU', 'self::replaceLinks', $str);

		if (!is_writeable(TL_ROOT.'/'.$this->targetDir) && is_dir(TL_ROOT.'/'.$this->targetDir))
		{
			chmod(TL_ROOT.'/'.$this->targetDir, 0777);
		}

		/* copy images files */		
		if (!is_dir(TL_ROOT.'/'.$this->targetDir . '/images'))
		{
			mkdir(TL_ROOT.'/'.$this->targetDir . '/images');

			if (!is_writeable(TL_ROOT.'/'.$this->targetDir . '/images') && is_dir(TL_ROOT.'/'.$this->targetDir . '/images'))
			{
				chmod(TL_ROOT.'/'.$this->targetDir . '/images', 0777);
			}
		}

		if (is_writeable(TL_ROOT.'/'.$this->targetDir . '/images') && is_dir(TL_ROOT.'/'.$this->targetDir . '/images'))
		{
			$str = preg_replace_callback('~(<img.*src=")(.*(png|jpg|gif))(")~isU', 'self::processImages', $str);
		}

		/* copy audio files */
		if (!is_dir(TL_ROOT.'/'.$this->targetDir . '/audio'))
		{
			mkdir(TL_ROOT.'/'.$this->targetDir . '/audio');

			if (!is_writeable(TL_ROOT.'/'.$this->targetDir . '/audio') && is_dir(TL_ROOT.'/'.$this->targetDir . '/audio'))
			{
				chmod(TL_ROOT.'/'.$this->targetDir . '/audio', 0777);
			}
		}

		if (is_writeable(TL_ROOT.'/'.$this->targetDir . '/audio') && is_dir(TL_ROOT.'/'.$this->targetDir . '/audio'))
		{
			$str = preg_replace_callback('~(<source.*src=")(.*(mp3|ogg))(")~isU', 'self::processAudio', $str);
		}

		/* copy stylesheets */
		$str = preg_replace_callback('~(<link.*href=")(.*)(".*>)~isU', 'self::processStylesheets', $str);

		if ($this->epubExport)
		{
#			$str = str_ireplace('</head>', )
		}

		return $str;
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
			$dest_image = $this->targetDir . '/' . $filename;
			
			$this->import('Files');

			if (file_exists(TL_ROOT.'/'.$src_image) && !file_exists(TL_ROOT.'/'.$dest_image))
			{
				$this->Files->copy($src_image, $dest_image);
			}
			
			$arrPathInfo = pathinfo($src_image);
			$strRetinaFilename = $arrPathInfo['dirname'].'/'.$arrPathInfo['filename'].'@2x.'.$arrPathInfo['extension'];
			
			if (file_exists(TL_ROOT.'/'.$strRetinaFilename))
			{
				$this->Files->copy($strRetinaFilename, $this->targetDir.'/images/'.str_replace(array('/', ' '), '_', $strRetinaFilename));
			}

			return $match[1].'./'.$filename.$match[4];
		}


		protected function processAudio($match)
		{
			$src_audio = $match[2];
			$filename = 'audio/' . str_replace(array('/', ' '), '_', $src_audio);
			$dest_audio = $this->targetDir . '/' . $filename;

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
				$dest_stylesheet = TL_ROOT.'/'.$this->targetDir . '/' . $filename;
	
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