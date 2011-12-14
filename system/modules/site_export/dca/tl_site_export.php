<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

$GLOBALS['TL_DCA']['tl_site_export'] = array
(
	// Allgemein Konfiguration
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ctable'                      => array('tl_site_export_rules'),
		'switchToEdit'                => true,
		'enableVersioning'            => true
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('title'),
			'flag'                    => 1,
			'panelLayout'             => 'search,limit'
		),
		'label' => array
		(
			'fields'                  => array('title'),
			'format'                  => '%s'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export']['edit'],
				'href'                => 'table=tl_site_export_rules',
				'icon'                => 'edit.gif',
				'attributes'          => 'class="contextmenu"'
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
				'button_callback'     => array('tl_site_export', 'editHeader'),
				'attributes'          => 'class="edit-header"'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				'button_callback'     => array('tl_site_export', 'copyTopic') /* copyChannel */
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
				'button_callback'     => array('tl_site_export', 'deleteTopic') /* deleteChannel*/
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
			'export' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_site_export']['export'],
				'href'                => 'key=export&amp;step=preview',
				'icon'                => 'system/modules/site_export/html/images/html_go.png'
			)
		)
	),
	
	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('includeLayout', 'exportEpub'),
		'default'                     => '{title_legend},title;{page_legend},pages,recursive;{export_legend},targetDir,includeLayout,toc;{epub_legend},exportEpub'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'includeLayout'               => 'layout',
		'exportEpub'                  => 'ebookFilename,ebookCover,ebookTitle,ebookDescription,ebookIdentifier,ebookSubject,ebookLanguage,ebookCreator,ebookPublisher,ebookDate'
	),
	
	// Fields
	'fields' => array
	(
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['title'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'maxlength'=>255)
		),
		'pages' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['pages'],
			'exclude'                 => true,
			'inputType'               => 'pageTree',
			'eval'                    => array(
				'mandatory' => false,
				'fieldType'=>'checkbox'
			)
		),
		'recursive' => array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_site_export']['recursive'],
			'exclude'		=> true,
			'inputType'		=> 'checkbox'
			#'eval'          => array('tl_class'=>'w50')
		),
		'includeLayout' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['includeLayout'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true)
		),
		'layout' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['layout'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'eval'                    => array('tl_class'=>'w50'),
			'options_callback'        => array('tl_site_export', 'getPageLayouts')
		),
		'toc' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['toc']['title'],
			'search'                  => true,
			'inputType'               => 'select',
			'options'                 => array('none', 'flat', 'indent'),
			'reference'				  => &$GLOBALS['TL_LANG']['tl_site_export']['toc']['reference'],
			'eval'                    => array('maxlength'=>32, 'tl_class'=>'w50')
		),
		'targetDir' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['targetDir'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'tl_class'=>'clr')
		),
		'exportEpub' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['exportEpub'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true)
		),
		'ebookFilename' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookFilename'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>255)
		),
		'ebookCover' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookCover'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'tl_class'=>'clr', 'filesOnly'=>true, 'extensions'=>'jpg,jpeg,png')
		),
		'ebookTitle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookTitle'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'long')
		),
		'ebookDescription' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookDescription'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'long')
		),
		'ebookIdentifier' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookIdentifier'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50')
		),
		'ebookSubject' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookSubject'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50')
		),
		'ebookLanguage' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookLanguage'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>5, 'tl_class'=>'w50')
		),
		'ebookCreator' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookCreator'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50')
		),
		'ebookPublisher' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookPublisher'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50')
		),
		'ebookDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_site_export']['ebookDate'],
			'search'                  => true,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'decodeEntities'=>true, 'maxlength'=>10, 'tl_class'=>'w50')
		),
	)
);




/**
 * Class tl_lingo_wordlist_topic
 */
class tl_site_export extends Backend
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
	 * Return all page layouts grouped by theme
	 * @return array
	 */
	public function getPageLayouts()
	{
		$objLayout = $this->Database->execute("SELECT l.id, l.name, t.name AS theme FROM tl_layout l LEFT JOIN tl_theme t ON l.pid=t.id ORDER BY t.name, l.name");

		if ($objLayout->numRows < 1)
		{
			return array();
		}

		$return = array();

		while ($objLayout->next())
		{
			$return[$objLayout->theme][$objLayout->id] = $objLayout->name;
		}

		return $return;
	}

	/**
	 * Check permissions to edit table tl_lingo_wordlist_topic
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}
	}


	/**
	 * Return the edit header button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function editHeader($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || count(preg_grep('/^tl_site_export::/', $this->User->alexf)) > 0) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : '';
	}


	/**
	 * Return the copy channel button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function copyTopic($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('create', 'newsletterp')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}


	/**
	 * Return the delete channel button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function deleteTopic($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('delete', 'newsletterp')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}
}


?>