<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::addPageTSConfig('
	#Default Page TSconfig
');
t3lib_extMgm::addUserTSConfig('
	#Default User TSconfig
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,"editorcfg","
	tt_content.CSS_editor.ch.tx_gooffotoboek_pi1 = < plugin.tx_gooffotoboek_pi1.CSS_editor
",43);


t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_gooffotoboek_pi1.php","_pi1","list_type",0);
?>