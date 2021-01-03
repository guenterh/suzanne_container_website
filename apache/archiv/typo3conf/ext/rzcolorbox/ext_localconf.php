<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY,'lib/class.tx_rzcolorbox_ttnews.php','_ttnews','',1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_rzcolorbox_pi2.php', '_pi2', 'list_type', 1);

?>