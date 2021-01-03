<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Arco van Geest (arco@appeltaart.mine.nu)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Module 'Fotobook' for the 'goof_fotobook' extension.
 *
 * @author	Arco van Geest <arco@appeltaart.mine.nu>
 */

// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:goof_fotoboek/mod1/locallang.php');
#include ('locallang.php');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
require_once (PATH_t3lib.'class.t3lib_div.php');
require_once (PATH_t3lib.'class.t3lib_treeview.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

// Front end 
require_once (PATH_t3lib.'class.t3lib_page.php');
require_once (PATH_t3lib.'class.t3lib_tstemplate.php');
require_once (PATH_t3lib.'class.t3lib_tsparser_ext.php');
// end of frontend




class tx_gooffotoboek_module1 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 * @return	[type]		...
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP('clear_all_cache'))	{
			$this->include_once[]=PATH_t3lib.'class.t3lib_tcemain.php';
		}
		*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	[type]		...
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
				'1' => $LANG->getLL('title'),
				'2' => $LANG->getLL('function2'),
				'3' => $LANG->getLL('function3'),
			)
		);
		parent::menuConfig();
	}

		// If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		global $TSFE,$CONF;
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
				</script>
			';

			$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	[type]		...
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	[type]		...
	 */
	function moduleContent()	{
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 1:
				$content="<div align=center><strong>The Photobook backend</strong></div><br />
					This backend module is still very experimental and should be used with caution.<br />
					It can be enabled or disabled in '"
			.substr(t3lib_extMgm::extPath('goof_fotoboek'),strlen(PATH_site))."ext_tables.php'					<HR>
					<br />This is the GET/POST vars sent to the script:<BR>".
					'GET:'.t3lib_div::view_array($GLOBALS['HTTP_GET_VARS']).'<BR>'.
					'POST:'.t3lib_div::view_array($GLOBALS['HTTP_POST_VARS']).'<BR>'.
					'';
				$this->content.=$this->doc->section('Photobook backend introduction:',$content,0,1);
			break;
			case 2:
				$content=$this->preCache();
				$this->content.=$this->doc->section('Pre-cache',$content,0,1);
			break;
			case 3:
				$content='<div align=center><strong>Menu item #3...</strong></div>';
				$this->content.=$this->doc->section('Message #3:',$content,0,1);
			break;
		}
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function preCache ()		{
		$bla = '<div align=center><strong>Pre caching of images</strong></div><br />';
		$bla .= "The 'Kickstarter' has made this module automatically, it contains a default framework for a backend module but apart from it does nothing useful until you open the script '".substr(t3lib_extMgm::extPath('goof_fotoboek'),strlen(PATH_site))."mod1/index.php' and edit it!
					<HR>
					<BR>This is the GET/POST vars sent to the script:<BR>".
					'GET:'.t3lib_div::view_array($GLOBALS['HTTP_GET_VARS']).'<BR>'.
					'POST:'.t3lib_div::view_array($GLOBALS['HTTP_POST_VARS']).'<BR>'.
					'<hr />';
// SELECT:
		$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, 
			'SELECT pid,uid,tx_gooffotoboek_function,tx_gooffotoboek_path,tx_gooffotoboek_webpath '
			.'FROM tt_content ' 
			.'where deleted=0 '
			.'and tx_gooffotoboek_function <> "" '
			.'LIMIT 5');
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

			#$bla .= t3lib_div::view_array(t3lib_BEfunc::getPagesTSconfig(3,'plugin.',1));
			#$testarray = t3lib_positionmap::getModConfig($row['uid']);
			$bla .= $row['uid'].' - '.$row['pid'].' - '.$row['tx_gooffotoboek_function'].'<br />'
#				.t3lib_div::view_array(t3lib_treeview::getrecord($row['uid']))
	
				.'<br />';

#    			$bla .= 'Row: '.$row['uid'].' -- ' .'Row: '.$row['pid'].' -- ' . $row['tx_gooffotoboek_function'].' - '.$row['tx_gooffotoboek_path'].'<br />';
			$bla .= t3lib_div::view_array($row).'<br />';
$perms_clause = $GLOBALS["BE_USER"]->getPagePermsClause(1);    

  // integer id value
$id = intval($row['uid']);

  // The page record of the current page (set by $id). 
  // $perms_clause ensures that the BE user has read access to this module.
#$pageinfo = t3lib_BEfunc::readPageAccess($id,$perms_clause);
#$pageinfo = t3lib_BEfunc::getPagesTSconfig($id,'',0);
#$pageinfo=  t3lib_BEfunc::getModTSconfig($id,"");




  // Has access??
#if ($id && is_array($pageinfo)) {
	
    // Do your main things here
#    	$bla .= t3lib_div::view_array($pageinfo);
#}
#    	$bla .= t3lib_div::view_array($row);

$pageUid = $row['pid']; // You need to set the page id, if it's a web BE module, 
                //just use $this->id

$sysPageObj = t3lib_div::makeInstance ('t3lib_pageSelect');
$rootLine = $sysPageObj->getRootLine ($pageUid);

$templateObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
$templateObj->tt_track = 0;
$templateObj->init();
$templateObj->runThroughTemplates ($rootLine);
$templateObj->generateConfig();

#debug ($templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']);
#$bla .= t3lib_div::view_array($templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']).'<br />';
$bla .= 'tx_gooffotoboek_path: '.$templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['path'].'<br />';
$bla .= 'tx_gooffotoboek_path(db): '.$row['tx_gooffotoboek_path'].'<br />';
$bla .= 'tx_gooffotoboek_webpath: '.$templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['webpath'].'<br />';
$bla .= 'tx_gooffotoboek_webpath(db): '.$row['tx_gooffotoboek_webpath'].'<br />';

$path = $row['tx_gooffotoboek_path'] ? $row['tx_gooffotoboek_path'] : $templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['path'];
if ( $row['tx_gooffotoboek_path'] ) {
	$webpath = $row['tx_gooffotoboek_webpath'] ? $row['tx_gooffotoboek_webpath']:$row['tx_gooffotoboek_path'];
} else {
	$webpath = $templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['webpath'] ? $templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['webpath']:$path;
}

$bla .= 'path: '.$path.'<br />';
$bla .= 'webpath: '.$webpath.'<br />';





$bla .= 'img_maxw: '.$templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['img_maxw'].'<br />';
$bla .= 'img_maxh: '.$templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['img_maxh'].'<br />';

$bla .= 'thumb_maxw: '.$templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['thumb_maxw'].'<br />';
$bla .= 'thumb_maxh: '.$templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['thumb_maxh'].'<br />';

$bla .= 'thumb_per_row: '.$templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['thumb_per_row'].'<br />';
$bla .= 'thumb_rows: '.$templateObj->setup['plugin.']['tx_gooffotoboek_pi1.']['thumb_rows'].'<br />';
$bla .= '<hr />';




######################################################3

}
		return $bla;

	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/goof_fotoboek/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/goof_fotoboek/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_gooffotoboek_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>