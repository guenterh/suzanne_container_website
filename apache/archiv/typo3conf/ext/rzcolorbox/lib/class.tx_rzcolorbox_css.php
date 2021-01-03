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
  
class tx_rzcolorbox_css {
  function iecss($content, $conf) {
    // Get Style
    $style = $conf['style'];
        
    if($style == '1') {
      $style_output = ''.$GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'].'typo3conf/ext/rzcolorbox/res/style1/css/images/';        
      $content .= '     
<style type="text/css">
  .cboxIE #cboxTopLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderTopLeft.png, sizingMethod=\'scale\');}
  .cboxIE #cboxTopCenter{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderTopCenter.png, sizingMethod=\'scale\');}
  .cboxIE #cboxTopRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderTopRight.png, sizingMethod=\'scale\');}
  .cboxIE #cboxBottomLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderBottomLeft.png, sizingMethod=\'scale\');}
  .cboxIE #cboxBottomCenter{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderBottomCenter.png, sizingMethod=\'scale\');}
  .cboxIE #cboxBottomRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderBottomRight.png, sizingMethod=\'scale\');}
  .cboxIE #cboxMiddleLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderMiddleLeft.png, sizingMethod=\'scale\');}
  .cboxIE #cboxMiddleRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderMiddleRight.png, sizingMethod=\'scale\');}
</style>    
      ';
      
      return $content;
    }
    else if($style == '4') {
      $style_output = ''.$GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'].'typo3conf/ext/rzcolorbox/res/style4/css/images/'; 
      $content = '
<style type="text/css">
  .cboxIE #cboxTopLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderTopLeft.png, sizingMethod=\'scale\');}
  .cboxIE #cboxTopCenter{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderTopCenter.png, sizingMethod=\'scale\');}
  .cboxIE #cboxTopRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderTopRight.png, sizingMethod=\'scale\');}
  .cboxIE #cboxBottomLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderBottomLeft.png, sizingMethod=\'scale\');}
  .cboxIE #cboxBottomCenter{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderBottomCenter.png, sizingMethod=\'scale\');}
  .cboxIE #cboxBottomRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderBottomRight.png, sizingMethod=\'scale\');}
  .cboxIE #cboxMiddleLeft{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderMiddleLeft.png, sizingMethod=\'scale\');}
  .cboxIE #cboxMiddleRight{background:transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.$style_output.'internet_explorer/borderMiddleRight.png, sizingMethod=\'scale\');}
</style>
      ';
      
      return $content;
    }
    else {
      // Do nothing
    }
    
  }
}

?>