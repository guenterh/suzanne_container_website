<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Raphael Zschorsch <rafu1987@gmail.com>
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

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'jQuery Colorbox' for the 'rzcolorbox' extension.
 *
 * @author    Raphael Zschorsch <rafu1987@gmail.com>
 * @package    TYPO3
 * @subpackage    rzcolorbox
 */
 
class tx_rzcolorbox_ttnews extends tslib_pibase {
    var $prefixId      = 'tx_rzcolorbox_ttnews';
    var $scriptRelPath = 'lib/class.tx_rzcolorbox_ttnews.php';
    var $extKey        = 'rzcolorbox';
    var $pi_checkCHash = true;
    var $conf = array();
         
    /**
     * This function is a copy from Benjamin Niediek´s perfectlightbox. 
     * Example function that sets the register var "IMAGE_NUM_CURRENT" to the the current image number.
     *
     * @param    array         $paramArray: $markerArray and $config of the current news item in an array
     * @param    [type]        $conf: ...
     * @return    array        the processed markerArray
     */
     
    function ttnewsImageMarkerFunc($paramArray,$conf) {    
        $markerArray = $paramArray[0];
        $lConf = $paramArray[1];
        $pObj = &$conf['parentObj'];
        $row = $pObj->local_cObj->data;
    
        $imageNum = isset($lConf['imageCount']) ? $lConf['imageCount']:1;
        $imageNum = t3lib_div::intInRange($imageNum, 0, 100);
        $theImgCode = '';
        $imgs = t3lib_div::trimExplode(',', $row['image'], 1);
        $imgsCaptions = explode(chr(10), $row['imagecaption']);
        $imgsAltTexts = explode(chr(10), $row['imagealttext']);
        $imgsTitleTexts = explode(chr(10), $row['imagetitletext']);

        reset($imgs);

        $cc = 0;
        
        // BEN: We need to mark these items prior to arrayshifting
        if (count($imgs) == 1 && 
            $pObj->config['firstImageIsPreview'] && 
            $pObj->config['code'] == 'SINGLE' && 
            !$pObj->config['forceFirstImageIsPreview'])
        {
            $markedAsSpecial = 1;
        }
        // END.
        
        // remove first img from the image array in single view if the TSvar firstImageIsPreview is set
        if ((    (count($imgs) > 1 && $pObj->config['firstImageIsPreview'])
                ||
                (count($imgs) >= 1 && $pObj->config['forceFirstImageIsPreview'])
            ) && $pObj->config['code'] == 'SINGLE') {
            array_shift($imgs);
            array_shift($imgsCaptions);
            array_shift($imgsAltTexts);
            array_shift($imgsTitleTexts);
        }        
        // get img array parts for single view pages
        if ($this->piVars[$pObj->config['singleViewPointerName']]) {
            $spage = $this->piVars[$pObj->config['singleViewPointerName']];
            $astart = $imageNum*$spage;
            $imgs = array_slice($imgs,$astart,$imageNum);
            $imgsCaptions = array_slice($imgsCaptions,$astart,$imageNum);
            $imgsAltTexts = array_slice($imgsAltTexts,$astart,$imageNum);
            $imgsTitleTexts = array_slice($imgsTitleTexts,$astart,$imageNum);
        }

        while (list(, $val) = each($imgs)) {
            if ($cc == $imageNum) break;
            if ($val) {

                $lConf['image.']['altText'] = $imgsAltTexts[$cc];
                $lConf['image.']['titleText'] = $imgsTitleTexts[$cc];
                $lConf['image.']['file'] = 'uploads/pics/' . $val;
                // BEN: We check count of images >(=) 0 here because the array got shifted before!!! (See above)
                if ((    (count($imgs) > 0 && $pObj->config['firstImageIsPreview'])
                                ||
                                (count($imgs) >= 0 && $pObj->config['forceFirstImageIsPreview'])
                            ) && $pObj->config['code'] == 'SINGLE') {
                    // BEN: Additionally we need to check our special case
                    if (count($imgs) == 1 && $markedAsSpecial) {
                        $GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = $cc;
                    } else {
                        $GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = $cc+1;
                    }
                } else {
                    $GLOBALS['TSFE']->register['IMAGE_NUM_CURRENT'] = $cc;
                }
                // END.
            }
            
            $theImgCode .= $pObj->local_cObj->IMAGE($lConf['image.']) . $pObj->local_cObj->stdWrap($imgsCaptions[$cc], $lConf['caption_stdWrap.']);
            $cc++;
        }
        $markerArray['###NEWS_IMAGE###'] = '';
        if ($cc) {
            $markerArray['###NEWS_IMAGE###'] = $pObj->local_cObj->wrap(trim($theImgCode), $lConf['imageWrapIfAny']);
        } else {
            $markerArray['###NEWS_IMAGE###'] = $pObj->local_cObj->stdWrap($markerArray['###NEWS_IMAGE###'],$lConf['image.']['noImage_stdWrap.']);
        }
        return $markerArray;
    }
    
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzcolorbox/lib/class.tx_rzcolorbox_ttnews.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzcolorbox/lib/class.tx_rzcolorbox_ttnews.php']);
}

?>