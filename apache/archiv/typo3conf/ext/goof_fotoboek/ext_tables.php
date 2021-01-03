<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");



$tempColumns = Array (
	'tx_gooffotoboek_path' => Array (
		'label' => 'LLL:EXT:goof_fotoboek/locallang_db.php:tt_content.tx_gooffotoboek_path',
		'config' => Array (
			'type' => 'input',
			'size' => '80',
			'max' => '128',
			'eval' => 'trim'
		)
	),
	'tx_gooffotoboek_webpath' => Array (
		'label' => 'LLL:EXT:goof_fotoboek/locallang_db.php:tt_content.tx_gooffotoboek_webpath',
		'config' => Array (
			'type' => 'input',
			'size' => '80',
			'max' => '128',
			'eval' => 'trim'
		)
	),

"tx_gooffotoboek_function" => Array (		
"exclude" => 0,		
"label" => "LLL:EXT:goof_fotoboek/locallang_db.php:tt_content.tx_gooffotoboek_function",		
"config" => Array (
	"type" => "select",
			"items" => Array (
				Array("LLL:EXT:goof_fotoboek/locallang_db.php:tt_content.tx_gooffotoboek_function.I.0", "show"),
				Array("LLL:EXT:goof_fotoboek/locallang_db.php:tt_content.tx_gooffotoboek_function.I.2", "comment"),
				Array("LLL:EXT:goof_fotoboek/locallang_db.php:tt_content.tx_gooffotoboek_function.I.3", "basket")
		)
	)
)
);

# Wegens problemen even uitgezet...
#$TCA["tx_gooffotoboek_basket"] = Array (
#    "feInterface" => Array (
#        "fe_admin_fieldList" => "session_id, is_on_page, image, add_date,img_id",
#    )
#);



t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);

#mgmApi new style static template.
#t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Photobook");

$TCA["tt_content"]["types"]["list"]["subtypes_addlist"][$_EXTKEY."_pi1"]="tx_gooffotoboek_function;;;;1-1-1,tx_gooffotoboek_path;;;;1-1-1,tx_gooffotoboek_webpath;;;;1-1-1";

t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key,pages";

#typo3 3.5 didn't like this function.
if (function_exists('t3lib_extMgm::addLLrefForTCAdescr')) {
	t3lib_extMgm::addLLrefForTCAdescr('tt_content','EXT:goof_fotoboek/lang/locallang_csh.php');
}
t3lib_extMgm::addPlugin(Array("LLL:EXT:goof_fotoboek/locallang_db.php:tt_content.list_type", $_EXTKEY."_pi1"),"list_type");

#t3lib_div::debug($TCA_DESCR);



if (TYPO3_MODE=="BE")	{
#experimental
#	t3lib_extMgm::addModule("tools","txgooffotoboekM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
#/experimental


#	require_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_gooffotoboek_feuser.php');

}


if (TYPO3_MODE=="BE")    $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_gooffotoboek_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_gooffotoboek_pi1_wizicon.php';
?>
