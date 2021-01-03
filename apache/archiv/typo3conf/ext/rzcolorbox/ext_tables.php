<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (t3lib_div::int_from_ver(TYPO3_version) >= 4005000) {
  t3lib_extMgm::addStaticFile($_EXTKEY,'static/base/4.5/','4.5 jQuery ColorBox Base');  
}

else {
  t3lib_extMgm::addStaticFile($_EXTKEY,'static/base/','jQuery ColorBox Base');
}

if (t3lib_div::int_from_ver(TYPO3_version) >= 4005000) {
  t3lib_extMgm::addStaticFile($_EXTKEY,'static/t3jquery/4.5/','4.5 jQuery ColorBox Base for t3jquery');
}

else {
  t3lib_extMgm::addStaticFile($_EXTKEY,'static/t3jquery/','jQuery ColorBox Base for t3jquery');  
}
t3lib_extMgm::addStaticFile($_EXTKEY,'static/style1/','jQuery ColorBox Style 1');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/style2/','jQuery ColorBox Style 2');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/style3/','jQuery ColorBox Style 3');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/style4/','jQuery ColorBox Style 4');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/style5/','jQuery ColorBox Style 5');
t3lib_extMgm::addStaticFile($_EXTKEY,'pi2/static/','jQuery ColorBox for Content');

$tempColumns = array (
  'tx_rzcolorbox_slideshow' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:rzcolorbox/locallang_db.xml:tt_content.tx_rzcolorbox_slideshow',		
		'config' => array (
			'type' => 'check',
		)
	),
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

$GLOBALS['TCA']['tt_content']['palettes']['7']['showitem'] .= ',tx_rzcolorbox_slideshow';
# Raphael: Quickfix for TYPO3 4.5
$GLOBALS['TCA']['tt_content']['palettes']['imagelinks']['showitem'] .= ', tx_rzcolorbox_slideshow';


t3lib_div::loadTCA('tt_content'); 
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages';

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2','FILE:EXT:'.$_EXTKEY.'/ff_data_pi2.xml');  

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:rzcolorbox/locallang_db.xml:tt_content.list_type_pi2',
	$_EXTKEY . '_pi2',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_rzcolorbox_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_rzcolorbox_pi2_wizicon.php';
}

// Include the dbrelation userfunc for the flexform
include_once(t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_rzcolorbox_dbrelation.php'); 

?>