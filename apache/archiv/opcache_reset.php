<?php

// Function to reset Opcode Cache in PHP 5.5 and higer
// Author: sfroemken@jweiland.net
// Version 1.0 - 2014-10-06
// Signature: opcachereset01 - this is just to identify this script and version

// Purpose:
// clear Opcode Cache in PHP when TYPO3 version is updated
// The script opcache_reset.php checks for a file CLEAR_OPCACHE in the TYPO3
// project directory. If this file exists, the opcode cache
// will be cleared when this script is called.
// If the time of day is > 05:00, then the file CLEAR-OPCACHE
// will be deleted, so that caching can resume.

// function_exists was implemented since PHP 4
if (function_exists('opcache_reset')) {
 $clearOpCacheFilePath = 'CLEAR_OPCACHE';

 // DateTime is available since 5.3
 // mktime without arguments throws an E_STRICT notice
 $currentDate = time(); // set to current date
 $deleteFileAtDate = mktime(5); // set to 5 o'clock am

 if (file_exists($clearOpCacheFilePath)) {
  if ($currentDate < $deleteFileAtDate) {
   opcache_reset();
  } else {
   unlink($clearOpCacheFilePath);
  }
 }
}