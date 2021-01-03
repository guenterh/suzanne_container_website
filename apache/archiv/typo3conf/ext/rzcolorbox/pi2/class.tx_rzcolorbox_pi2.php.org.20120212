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
 
class tx_rzcolorbox_pi2 extends tslib_pibase {
    var $prefixId      = 'tx_rzcolorbox_pi2';
    var $scriptRelPath = 'pi2/class.tx_rzcolorbox_pi2.php';
    var $extKey        = 'rzcolorbox';
    var $pi_checkCHash = true;
         
    function main($content, $conf) {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();     
        
        // Read Flexform	
      	$this->pi_initPIflexForm();
      	$text = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text', 'sDEF');
      	$width = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'width', 'options');
      	$height = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'height', 'options');
      	$deactivate_width = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'deactivate_width', 'options');
      	$deactivate_height = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'deactivate_height', 'options');
      	$template_file = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template', 'options');
      	$type = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'type', 'sDEF'); 
      	$transition = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'transition', 'options'); 
        $open = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'open', 'options');  
      	$link = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'iframe', 'sDEF');
        $link_type = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'linktext_type', 'options');
        $link_text = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'linktext', 'options');
        $link_image = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'linktext_image', 'options');
        $link_image_width = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'linktext_image_width', 'options');
        $opacity = $this->conf['opacity'];
        if(empty($opacity)) $opacity = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'opacity', 'options');
        if(empty($link_image_width)) $link_image_width = $this->conf['link_image_width'];
      	
        // Typolink configuration                
        //$typolink_conf['value'] = $this->pi_getLL('link_text');
        $link = htmlspecialchars($link);
        $typolink_conf['typolink.']['parameter'] = $link;
        //$typolink_conf['typolink.']['title'] = $this->pi_getLL('link_text');
        $typolink_conf['typolink.']['no_cache'] = '0';
        $typolink_conf['typolink.']['useCacheHash'] = '1';
        $typolink_conf['typolink.']['returnLast'] = 'url';
        
        $link = $this->cObj->TEXT($typolink_conf);
        
        $html = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'html', 'sDEF');         
        $html_output = str_replace("\n","",$html);
           
      	$ce = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'ce', 'sDEF');        
        $ce_id = $this->cObj->data['uid'];
      	
      	// TypoScript values
      	$ts_linkClass = $this->conf['linkClass'];
      	$ts_contentClass = $this->conf['contentClass'];
      	
      	if(empty($ts_linkClass)) {
          $ts_linkClass = 'rzcolorbox-content';
        }
      	
      	if(empty($ts_contentClass)) {
          $ts_contentClass = 'rzce';
        }
      	
      	$linkClass = ''.$ts_linkClass.''.$ce_id;
      	$contentClass = ''.$ts_contentClass.''.$ce_id;
      	
        $width = $this->floatVal($width);    
      	if($width == '') {
          $width = $this->floatVal($this->conf['colorboxWidth']);
          if($width == '') {
            $width = '100%';
          }
        }
        
        $height = $this->floatVal($height); 
        if($height == '') {
          $height = $this->floatVal($this->conf['colorboxHeight']);
          if($height == '') {
            $height = '100%';
          }
        }
        
        // Output the Content Elements        
        $ce_conf = array('tables' => 'tt_content','source' => $ce,'dontCheckPid' => 1);
        $ce_output = $this->cObj->RECORDS($ce_conf);
        
        // Template
        $template_file = htmlspecialchars($template_file);
        $singleTemplate = $template_file;
		    if($singleTemplate == '') {
          $singleTemplate = $this->conf['templateFile'];
        }
        
        $this->templateCode = $this->cObj->fileResource($singleTemplate);
      	if($this->templateCode == '') {
      		return "<h3>No HTML-Template found:</h3>".$singleTemplate;
        } 
        
        // Set the type
        if($type == 'iframe') {
          $type_js = 'iframe:true';  
        }
        else if($type == 'inline') {
          $type_js = 'inline:true, href:".'.$contentClass.'"';
        }                                                    
        else if($type == 'html') {
          $type_js = 'html:"'.$html_output.'"';
        }
        
        // Automatically open ColorBox
        if($open == '1') {
          $open_js = 'open:true,';
        }
        else {
          $open_js = '';
        }
        
        // Opacity
        if($opacity == '') {
          $opacity = '0.85';
        }
        else {
          $opacity = $opacity;
        }
        
        // Set the transistion
        if($transition == 'elastic') {
          $transition_js = 'transition: "elastic",';
        }
        else if($transition == 'fade') {
          $transition_js = 'transition: "fade",';
        }
        else if($transition == 'none') {
          $transition_js = 'transition: "none",';
        }
        
        // JS for the Content
        if($deactivate_width == '1' && $deactivate_height == '0' ) {
          $js = 'jQuery(".'.$linkClass.'").colorbox({'.$open_js.''.$transition_js.'height:"'.$height.'", opacity:"'.$opacity.'", '.$type_js.'})';
        }
        else if($deactivate_height == '1' && $deactivate_width == '0') {
          $js = 'jQuery(".'.$linkClass.'").colorbox({'.$open_js.''.$transition_js.'width:"'.$width.'", opacity:"'.$opacity.'", '.$type_js.'})';  
        }
        else if($deactivate_width == '1' && $deactivate_height == '1') {
          $js = 'jQuery(".'.$linkClass.'").colorbox({'.$open_js.''.$transition_js.'inline:true, opacity:"'.$opacity.'", '.$type_js.'})';   
        }
        else {
          $js = 'jQuery(".'.$linkClass.'").colorbox({'.$open_js.''.$transition_js.'width:"'.$width.'", height:"'.$height.'", opacity:"'.$opacity.'", '.$type_js.'})'; 
        } 
        
        // Include JS to footer
        if($this->conf['moveJsFromHeaderToFooter'] == 1) { 
          $GLOBALS['TSFE']->additionalFooterData['rzcolorbox_begin'] = '
            <script type="text/javascript">
              jQuery(document).ready(function(){ 
          ';
          $GLOBALS['TSFE']->additionalFooterData['rzcolorbox_middle'] .= '           
            	 '.$js.'
          ';
          $GLOBALS['TSFE']->additionalFooterData['rzcolorbox_end'] = '
            	});	
            </script>
          ';
        }
        // Include JS to header
        else {
          $GLOBALS['TSFE']->additionalHeaderData['rzcolorbox_begin'] = '
            <script type="text/javascript">
              jQuery(document).ready(function(){ 
          ';
          $GLOBALS['TSFE']->additionalHeaderData['rzcolorbox_middle'] .= '           
            	 '.$js.'
          ';
          $GLOBALS['TSFE']->additionalHeaderData['rzcolorbox_end'] = '
            	});	
            </script>
          ';
        }  
        
        /*
        
        $content = '
          <script type="text/javascript">
           (function($){
              jQuery(document).ready(function(){
            	 '.$js.'
            	});
            }(jQuery));
          </script>
        ';  
        
        */ 
                            
        // Set the template and define the markers
        $template['main'] = $this->cObj->getSubpart($this->templateCode,'###TEMPLATE###');
        $markerArray['###TEXT###'] = $this->pi_RTEcssText($text);
                
        // Set the link appropriate to the type
        if($type == 'iframe') {
          $markerArray['###LINK_OPEN###'] = '<a href="'.$link.'" class="'.$linkClass.'">';
        }
        else if($type == 'inline' || $type == 'html') {
          $markerArray['###LINK_OPEN###'] = '<a href="#" class="'.$linkClass.'">';
        }
        
        $markerArray['###LINK_TEXT###'] = $this->pi_getLL('link_text');
        
        // Link text (flexform)
        if(!empty($link_text) && $link_type == 'text') {
          $markerArray['###LINK_TEXT###'] = htmlspecialchars($link_text);
        }
        
        // Link image (flexform)
        else if(!empty($link_image) && $link_type == 'image') {
          $image['file'] = 'uploads/pics/'.$link_image;
          $image['file.']['width'] = $link_image_width;
          $markerArray['###LINK_TEXT###'] = $this->cObj->IMAGE($image);
        }

        $markerArray['###LINK_CLOSE###'] = '</a>';
        
        $content .= $this->cObj->substituteMarkerArrayCached($template['main'], $markerArray, array());     
    
        // Only include the Content Elements, if the type "inline" is chosen
        if($type == 'inline') {
          $content .= '
            <div style="display:none;">
              <div class="'.$contentClass.'">
                '.$ce_output.'
              </div>
            </div>
          ';
        }
    
        return $this->pi_wrapInBaseClass($content);
    }
    
    function floatVal($var) {
        $var_ext = str_replace(floatval($var),"",$var);
        $var_ext = trim($var_ext);
        
        if($var_ext == '%' || $var_ext == 'px') {
            return floatval($var) . $var_ext;
        }
        else {
            return floatval($var);
        }
    } 
}              

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzcolorbox/pi2/class.tx_rzcolorbox_pi2.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzcolorbox/pi2/class.tx_rzcolorbox_pi2.php']);
}

?>