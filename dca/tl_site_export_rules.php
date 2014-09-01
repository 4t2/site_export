<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');



/**
 * Table tl_newsletter
 */
$GLOBALS['TL_DCA']['tl_site_export_rules'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_site_export',
		'enableVersioning'            => true,
		'onload_callback' => array
		(
//			array('tl_site_export_rules', 'loadRulesCallback')
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('sorting', 'id'),
			'panelLayout'             => 'filter;sort,search,limit',
			'headerFields'            => array('title', 'tstamp'),
			'header_callback'         => array('tl_site_export_rules', 'renderHeader'),
			'child_record_callback'   => array('tl_site_export_rules', 'listExportRules')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
#				'button_callback'     => array('tl_site_export_rules', 'headerButtons')
			),
			'export' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export']['export'],
				'href'                => 'key=export&amp;step=preview',
				'class'               => 'header_start_export'
			)
		),
		'label' => array
		(
			#'group_callback' 	=> array('tl_site_export_rules', 'groupCallback')
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export_rules']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export_rules']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export_rules']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export_rules']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export_rules']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="var id=%s; Backend.getScrollOffset(); ($$(\'#rule\'+id).getStyle(\'color\')==\'black\' ? $$(\'#rule\'+id).setStyle(\'color\', \'silver\') : $$(\'#rule\'+id).setStyle(\'color\', \'black\')); return AjaxRequest.toggleVisibility(this, id);"',
				'button_callback'     => array('tl_site_export_rules', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export_rules']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('isRegex'),
		'default'                     => '{title_legend},title,isActive,lateCall;{rules_legend},pattern,replacement;{regex_legend},isRegex'
	),
	
	// Subpalettes
	'subpalettes' => array
	(
		'isRegex'                   => 'modIgnoreCase,modMultiLine,modDotAll,modUngreedy,modUTF8'
	),

	// Fields
	'fields' => array
	(
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export_rules']['title'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'long', 'mandatory'=>true, 'decodeEntities'=>true, 'maxlength'=>255)
		),
		'isActive' => array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_site_export_rules']['isActive'],
			'exclude'		=> true,
			'inputType'		=> 'checkbox',
			'eval'          => array('tl_class'=>'w50 m12')
		),
		'lateCall' => array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_site_export_rules']['lateCall'],
			'exclude'		=> true,
			'inputType'		=> 'checkbox',
			'eval'          => array('tl_class'=>'w50 m12')
		),
		'pattern' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export_rules']['pattern'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'explanation'             => 'insertTags',
			'eval'                    => array(
				'mandatory' 	=> true,
				'rows' 			=> 6,
				'allowHtml'		=> true,
				'preserveTags'	=> true,
				'decodeEntities'=> false
			)
		),
		'replacement' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export_rules']['replacement'],
			'exclude'                 => true,
			'search'                  => false,
			'inputType'               => 'textarea',
			'explanation'             => 'insertTags',
			'eval'                    => array(
				'mandatory' 	=> false,
				'rows' 			=> 6,
				'allowHtml'		=> true,
				'preserveTags'	=> true,
				'decodeEntities'=> false
			)
		),
		'isRegex' => array
		(
			'label'					=> &$GLOBALS['TL_LANG']['tl_site_export_rules']['isRegex'],
			'exclude'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> array('submitOnChange'=>true)
		),
		'modIgnoreCase' => array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_site_export_rules']['modIgnoreCase'],
			'exclude'		=> true,
			'default'		=> 1,
			'inputType'		=> 'checkbox',
			'eval'          => array('tl_class'=>'w50')
		),
		'modMultiLine' => array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_site_export_rules']['modMultiLine'],
			'exclude'		=> true,
			'default'		=> 1,
			'inputType'		=> 'checkbox',
			'eval'          => array('tl_class'=>'w50')
		),
		'modDotAll' => array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_site_export_rules']['modDotAll'],
			'exclude'		=> true,
			'default'		=> 1,
			'inputType'		=> 'checkbox',
			'eval'          => array('tl_class'=>'w50')
		),
		'modUngreedy' => array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_site_export_rules']['modUngreedy'],
			'exclude'		=> true,
			'inputType'		=> 'checkbox',
			'eval'          => array('tl_class'=>'w50')
		),
		'modUTF8' => array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_site_export_rules']['modUTF8'],
			'exclude'		=> true,
			'inputType'		=> 'checkbox',
			'eval'          => array('tl_class'=>'w50')
		)
	)

);


/**
 * Class tl_lingo_wordlist
 */
class tl_site_export_rules extends \Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	/**
	 * Check permissions to edit table tl_newsletter
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		$this->redirect('contao/main.php?act=error');
	}

	public function headerButtons($row, $href, $label, $title, $icon, $attributes)
	{
#die('<pre>'.var_export($label, true));
	}

	public function renderHeader($header, $dc)
	{
		$objSiteExport = $this->Database
			->prepare('SELECT `title`,`rulesFrom` FROM `tl_site_export` WHERE id=?')
			->limit(1)
			->execute($dc->id);

		if ($objSiteExport->rulesFrom > 0)
		{
			$objSiteExport = $this->Database
				->prepare('SELECT `title`,`id` FROM `tl_site_export` WHERE id=?')
				->limit(1)
				->execute($objSiteExport->rulesFrom);

			$objSiteExportRules = $this->Database
				->prepare('SELECT count(*) AS `count` FROM `tl_site_export_rules` WHERE `pid`=?')
				->execute($objSiteExport->id);

			$header[sprintf($GLOBALS['TL_LANG']['MSC']['rulesFrom'], $objSiteExportRules->count)] = '<a href="contao/main.php?do=site_export&table=tl_site_export_rules&id=' . $objSiteExport->id . '">' . $objSiteExport->title . '</a>';
		}
		
		return $header;
	}

	public function groupCallback($group, $sortingMode, $firstOrderBy, $row, $dc)
	{
#die('<pre>'.var_export($group, true));
	}

	public function loadRulesCallback($dc)
	{
		$arrRoot = array();
		$objSiteExport = $this->Database
			->prepare('SELECT `rulesFrom` FROM `tl_site_export` WHERE `id`=?')
			->limit(1)
			->execute($dc->id);

#die('<pre>'.var_export($objSiteExport, true));

		if ($objSiteExport->rulesFrom > 0)
		{
			$objSiteExportRules = $this->Database
				->prepare('SELECT `id` FROM `tl_site_export_rules` WHERE `pid` IN (?,?)')
				->execute($dc->id, $objSiteExport->rulesFrom);
		}
		else
		{
			$objSiteExportRules = $this->Database
				->prepare('SELECT `id` FROM `tl_site_export_rules` WHERE `pid`=?')
				->execute($dc->id);
		}

		while ($objSiteExportRules->next())
		{
			$arrRoot[] = $objSiteExportRules->id;
		}
#die('<pre>'.var_export($dc, true));
#die('<pre>'.var_export($arrRoot, true));
		
		$GLOBALS['TL_DCA']['tl_site_export_rules']['list']['sorting']['root'] = $arrRoot;

#die('<pre>'.var_export($GLOBALS['TL_DCA']['tl_site_export_rules']['list']['sorting']['root'], true));
	}

	/**
	 * Return the "toggle active" button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_site_export_rules::isActive', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['isActive'] ? '' : 1);

		if (!$row['isActive'])
		{
			$icon = 'invisible.gif';
		}		

		$objPage = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
								  ->limit(1)
								  ->execute($row['pid']);

		if (!$this->User->isAdmin && !$this->User->isAllowed(4, $objPage->row()))
		{
			return $this->generateImage($icon) . ' ';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}


	/**
	 * Disable/enable a user group
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnActive)
	{
		// Check permissions to edit
		$this->Input->setGet('id', $intId);
		$this->Input->setGet('act', 'toggle');
		$this->checkPermission();

		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_site_export_rules::isActive', 'alexf'))
		{
			$this->log('Not enough permissions to change export_rule ID "'.$intId.'"', 'tl_site_export_rules toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$this->createInitialVersion('tl_site_export_rules', $intId);
	
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_site_export_rules']['fields']['isActive']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_site_export_rules']['fields']['isActive']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnActive, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_site_export_rules SET tstamp=". time() .", isActive='" . ($blnActive ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$this->createNewVersion('tl_site_export_rules', $intId);
	}


	/**
	 * Add an image to each page in the tree
	 * @param array
	 * @param string
	 * @return string
	 */
	public function addIcon($row, $label)
	{
		$time = time();
		$isActive = ($row['isActive'] && ($row['start'] == '' || $row['start'] < $time) && ($row['stop'] == '' || $row['stop'] > $time));

		return $this->generateImage('articles'.($isActive ? '' : '_').'.gif') .' '. $label;
	}

	/**
	 * List records
	 * @param array
	 * @return string
	 */
	public function listExportRules($arrRow)
	{
		$arrAttributes = array();

		if ($arrRow['isRegex'])
		{
			$arrAttributes[] = 'RegEx';
		}

		if ($arrRow['lateCall'])
		{
			$arrAttributes[] = 'late';
		}

		$strBuffer = $arrRow['title'];

		if (count($arrAttributes))
		{
			$strBuffer .= ' ('. implode(', ', $arrAttributes) . ')';
		}

		if ($arrRow['isActive'] == '1')
		{
			return '<span id="rule'.$arrRow['id'].'" style="color:black">'.$strBuffer.'</span>';
		}
		else
		{
			return '<span id="rule'.$arrRow['id'].'" style="color:silver">'.$strBuffer.'</span>';
		}
	}

}

