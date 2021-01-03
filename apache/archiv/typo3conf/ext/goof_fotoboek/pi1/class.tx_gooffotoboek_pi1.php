<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2003-2006 Arco (arco@appeltaart.mine.nu)
	*  All rights reserved
	*
	*  This script is part of the Typo3 project. The Typo3 project is
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
	* Plugin 'photobook' for the 'goof_fotoboek' extension.
	*
	* @author Arco <arco@appeltaart.mine.nu>
	*
	*/

	require_once(PATH_tslib.'class.tslib_pibase.php');
	require_once(PATH_t3lib.'class.t3lib_stdgraphic.php');
	/**
	 * Extention class for the photobook plugin
	 *
	 */
	class tx_gooffotoboek_pi1 extends tslib_pibase {
		var $prefixId = 'tx_gooffotoboek_pi1';
		// Same as class name
		var $scriptRelPath = 'pi1/class.tx_gooffotoboek_pi1.php'; // Path to this script relative to the extension
		var $extKey = 'goof_fotoboek';
		// The extension key.
		var $content = '';
		var $debugcontent='';
		var $tempdir = 'typo3temp/fotoboek';
		var $urlvars = '';
		var $local_cObj = '';
		var $PBDEBUG=0;
		var $array_buggy=0;
		/**
 * Fotoboek main section, here the choices are made which type of page is shown
 *
 * @param	string		$content: html code
 * @param	array		$conf: configuration array
 * @return	string		Photobook html code
 */
		function main($content, $conf) {
			global $TSFE;
			global $TYPO3_CONF_VARS;
			// nocache might not be needed
			#$TSFE->set_no_cache();
			ob_start(); #try to stop those irritating error messages
			$this->conf = $conf;
			$this->popup=0;

			$this->init();
				//Debug('Sessie '.$this->array_buggy);

			//fill dirs and files array
			$this->getFiles();
			//create navigation
			$this->createLinks();
			//get the template file
			$this->getTemplate();
			($this->mayComment()) || $this->code='';
			 if ($GLOBALS['TSFE']->loginUser){
				$this->sesType='user';
			} else {
				$this->sesType='ses';
			}

			if ($this->conf['basketEnable'] == 1 ) {
				$this->pbData = $GLOBALS['TSFE']->fe_user->getKey($this->sesType,'pbData');
				#debug ($GLOBALS['TSFE']->fe_user);
				if ( $this->pbData['basketID'] ) {
					( $this->PBDEBUG ) && ( $this->pbDebug('Sessie '.$pbData['basketID']) );
				} else {

					$this->pbData['pid'] = $GLOBALS['TSFE']->id;
					## #$this->pbData['basketID'] =  md5(time(now).$GLOBALS['TSFE']->id.$GLOBALS['TSFE']->fe_user['id']);
					$this->pbData['basketID'] =  md5($GLOBALS['TSFE']->id.$GLOBALS['TSFE']->fe_user->id);
					$GLOBALS['TSFE']->fe_user->setKey($this->sesType,'pbData', $pbData);
				}
				( $this->PBDEBUG ) &&  $this->pbDump($pbData);


				if ($this->urlvars['func'] == 'createzip') {
					if ($this->conf['basketEnable'] == 0 ) {
						return $this->pi_wrapInBaseClass($this->pi_getLL('basketDisabled'));
					} else {
						$this->content .= $this->zipBasket();
						$this->popup=1;
						$this->skipShow=1;
					}
				}
			}

		
			if ($this->urlvars['func'] == 'basket') {
				if ($this->conf['basketEnable'] == 0 ) {
					return $this->pi_wrapInBaseClass($this->pi_getLL('basketDisabled'));
				} else {
					$this->content .= $this->basketContent();
					$this->popup=1;
					$this->skipShow=1;
				}
			}

			if ($this->urlvars['func'] == 'removeitem') {
				if ($this->conf['basketEnable'] == 0 ) {
					return $this->pi_wrapInBaseClass($this->pi_getLL('basketDisabled'));
				} else {
				$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db,
				'delete from tx_gooffotoboek_basket where ( img_id='.$this->urlvars['item'].' and is_on_page='.$this->pbData['pid']
				.' and session_id="'.$this->pbData['basketID'].'" )');
					$this->content .= $this->basketContent();
					$this->popup=1;
					$this->skipShow=1;
				}
			}


			if ($this->urlvars['func'] == 'removeall') {
				if ($this->conf['basketEnable'] == 0 ) {
					return $this->pi_wrapInBaseClass($this->pi_getLL('basketDisabled'));
				} else {
				$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db,
				'delete from tx_gooffotoboek_basket where  is_on_page='.$this->pbData['pid']
				.' and session_id="'.$this->pbData['basketID'].'" ');
					$this->content .= $this->basketContent();
					$this->popup=1;
					$this->skipShow=1;
				}
			}

			if ($this->urlvars['func'] == 'basketadd') {
				if ($this->conf['basketEnable'] == 0 ) {
					return $this->pi_wrapInBaseClass($this->pi_getLL('basketDisabled'));
				} else {
			$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db,
				'select count(is_on_page) as count from tx_gooffotoboek_basket where is_on_page='.$this->pbData['pid']
				.' and session_id="'.$this->pbData['basketID'].'" and image="'.$this->urlvars['img'].'" ;');
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) $count = $row['count'];

					if ($count==0) {

						$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db,
					 	'INSERT INTO tx_gooffotoboek_basket (is_on_page,session_id,image,add_date)
						VALUES ('.$this->pbData['pid'].',"'.$this->pbData['basketID'].'","'.$this->urlvars['img'].'",'.time().'))') ;
						#$this->content .= htmlspecialchars($this->urlvars['img']).' added to basket';
					}
					$this->content .= $this->basketContent();
					$this->popup=1;
					$this->skipShow=1;
				}
			}
			if ($this->conf['basketEnable'] == 0 ) {
				
				# clear the basket tags.
				$this->markerArray['###ADD_TO_BASKET###']='';
				$this->markerArray['###BASKET###']='';
			}


			if ($this->code == 'comment') {
				$this->comment();
			}

			// alternate root templates
			$this->templatePrefix='';
			//if (($this->conf['alternateRootPrefix']) & ($this->urlvars['srcdir']=="")) {
			if ($this->urlvars['srcdir'] == "") {
				$this->templatePrefix=$this->conf['alternateRootPrefix'];
			}

			if (! $this->skipShow ) {
				($this->conf['CombinedView'] == 1 ) && ($this->urlvars['func'] = 'combine');
				$this->showDirTitle();
				$this->showDirs();
				if ($this->urlvars['func'] == 'thumb') {
					$this->showThumbs();
					$this->content .= $this->parseTemplate($this->templatePrefix.'THUMBTPL');
				} elseif ($this->urlvars['func'] == 'combine') {
					$this->showCombine();
					$this->content .= $this->parseTemplate($this->templatePrefix.'COMBINETPL');

				} else {
					$this->showFile();
					$this->content .= $this->parseTemplate($this->templatePrefix.'SINGLE');
				}
			}

			# temporary patch for xhtml strict compliancy
			if ($GLOBALS['TSFE']->config['config']['doctype'] == 'xhtml_strict') {
				$this->content = preg_replace('/(<img[^>]*) border="0"/', '\1 style="border-width:0"', $this->content);
			}
			# /patch
			# pbDebug
			$this->debugcontent = ob_get_contents();
			ob_end_clean();
			if ( $this->debugcontent ) {
				$this->debugcontent = '<div style="background-color: red;border: 1px solid black">'
				. $this->debugcontent
				. '<hr />If you are the owner of this site you can contact arco(at)appeltaart.mine.nu for help</div><br />';
				 if (  $this->conf['hide_errors'] == 1 ) {
					 $this->debugcontent = '<!-- '.$this->debugcontent.' -->';
				 }
				$this->content = $this->debugcontent . $this->content;
			}
			#/pbdebug
			if ( $this->popup ) {
				$this->markerArray['###POPUP_TITLE###'] = 'Photobook Popup';
				$this->markerArray['###POPUP_CONTENT###'] = $this->content;
				echo $this->parseTemplate('POPUP_TEMPLATE');
				die;
			} else {
				return $this->pi_wrapInBaseClass($this->content);
			}
		}
		#/main
		function pbDump(&$var,$text='') {
			echo '<pre>';
			if ($text) echo $text."\n";
			debug($var);
			echo '</pre>';
		}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$string: ...
	 * @return	[type]		...
	 */
		function pbDebug($string='') {
			$this->debugcontent .= $string.'</br>';
		}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
		function mayComment() {
			GLOBAL $BE_USER,$TSFE;
			if ( $this->conf['be_comment_list'].$this->conf['fe_comment_list']=='' ) return true;
			if ( $this->conf['be_comment_list']) {
				if ( ($this->conf['be_comment_list'] == 'any') && ($BE_USER->user['uid'])) {
					return true;
				}

				$be_array=explode(' ',$this->conf['be_comment_list']);
				if ( is_array( $be_array ) ){
					if ( in_array( $BE_USER->user['username'], $be_array ) ){
						return true;
					}
				}
			}
			if ($this->conf['fe_comment_list'] ) {
			if ( ($this->conf['fe_comment_list']=='any' )
				&& ($this->hasPermission(array('-2'),$aGroupDir))
			) return true;

			$aGroupUser = explode( ',', $TSFE->gr_list );
			$aGroupDir = explode(' ',$this->conf['fe_comment_list']);
			if ($this->hasPermission($aGroupUser,$aGroupDir)) return true;
			}
			return false;
		} #/maycomment


		function editFields($commentarray = array(), $lang = '') {
			$commentarray['rest'] = preg_replace('/\r/', '', $commentarray['rest']);
			$langid = $lang ? ('_'.$lang) :
			 '';
			$editfields = '<p>';
			$editfields .= ($lang ? $lang : $this->pi_getLL('default'));
			$editfields .= ' - <label for="'.$this->prefixId.'_editsave'.$langid.'">'.$this->pi_getLL('save').'</label>';
			$editfields .= '<input type="checkbox" id="'.$this->prefixId.'_editsave'.$langid.'" name="'.$this->prefixId.'[editsave'.$langid.']" /><br />';

			$change = '';
			#  $change='onkeyup="goof_fotoboek_form.'.$this->prefixId.'_editsave'.$langid.'.checked = true;" ';
			#  $change='onkeyup="goof_fotoboek_form.'.$this->prefixId.'_editsave'.$langid.'.checked = true;" ';
			#  $change='onkeyup="goof_checkbox('."'".$this->prefixId.'_editsave'.$langid."'".');"';
			#  $change="onkeyup=\"goof_getObject('saveme').checked = true;\"";

			#  $editfields .= '<label for="'.$this->prefixId.'[editheader'.$langid.']">'.$this->pi_getLL('title').'</label>';
			$editfields .= '<input '.$change.' type="text" size="' .$this->conf['commentFieldSize'].'" name="'.$this->prefixId.'[editheader'.$langid.']" value="'.htmlspecialchars($commentarray['header']).'" /><br />';

			#  $editfields .= '<label for="'.$this->prefixId.'_editcomment'.$langid.'">'.$this->pi_getLL('comment').'</label>';
			$rest = is_array($commentarray['rest'])?implode('', $commentarray['rest']):
			$commentarray['rest'];
			$editfields .= '<textarea '.$change.'name="'.$this->prefixId.'[editcomment'.$langid.']"  cols="'.$this->conf['commentFieldSize'].'" rows="4"  >'.htmlspecialchars($rest);
			$editfields .= '</textarea>';
			$editfields .= '</p>';
			$editfields .= ''."\n\r" ;
			return $editfields;
		}

		/**
 * Get or create comment
 *
 * @return	void		Fills the content
 */
		function comment() {
			#    $GLOBALS['TSFE']->additionalHeaderData['goof_fotoboek_script'] = '
			#<script type="text/javascript">

			#function goof_getObject(obj){if(document.getElementById){return document.getElementById(obj);}else{if(document.all){return document.all[obj];}}}
			#function goof_checkbox(checkboxname){goof_getObject(checkboxname).checked = true};}

			#</script>
			#';
			$this->editfunc = $this->urlvars['editfunc'];
			if ($this->editfunc == 'dir' ) {
				$this->skipShow = 1;
				$this->content .= '<h2>'.$this->pi_getLL('edit').' '.$this->urlvars['srcdir'].'</h2>';
				$commentarray = $this->loadComment($this->startdir.'/'.$this->urlvars['srcdir'], 'default', 0);
				$commentarray['rest'] = preg_replace('/\r/', '', $commentarray['rest']);
				// removed maxlength="80" maxlength="1024" wrap="physical"
				$this->content .= '<form method="post" action="" id="goof_fotoboek_form">'."\n\r";
				#$this->content .= '<p><input type="checkbox" id="saveme" name="saveme"/></p>';

				$this->content .= $this->editFields($commentarray);

				$editlanguages = explode(' ', $this->conf['comment_languages']);
				if (is_array($editlanguages)) {
					sort($editlanguages);
					foreach($editlanguages as $lang) {
						if ($lang != '') {
							$commentarray = $this->loadComment($this->startdir.'/'.$this->urlvars['srcdir'], $lang, 1);
							$this->content .= $this->editFields($commentarray, $lang);

						}
					}
				}
				$this->content .= '<p><input type="hidden"  name="'.$this->prefixId.'[srcdir]" value="'.$this->urlvars['srcdir'].'" />' .'<input type="hidden"  name="'.$this->prefixId.'[editfunc]" value="dirsave" />&nbsp;' .'<input type="submit" name="" value="'.$this->pi_getLL('saveSelected').'" /></p>'
				.'</form>';
				$this->urlvars['editfunc'] = '';
				$this->content .= $this->createLink($this->pi_getLL('cancel'), $this->urlvars, '', 1).'<br/>';
			}
			//save function
			if ($this->editfunc == 'dirsave' ) {
				$this->saveComment($this->startdir.'/'.$this->urlvars['srcdir'] , $this->urlvars['editheader'] , $this->urlvars['editcomment'] , '');
				$this->urlvars['editheader'] = '';
				$this->urlvars['editcomment'] = '';
				$this->editfunc = '';
				$editlanguages = explode(' ', $this->conf['comment_languages']);
				if ($this->conf['comment_languages'] != "" & is_array($editlanguages)) {
					sort($editlanguages);
					foreach($editlanguages as $lang) {
						$this->saveComment($this->startdir.'/'.$this->urlvars['srcdir'] , $this->urlvars['editheader_'.$lang] , $this->urlvars['editcomment_'.$lang] , $lang);
						$this->urlvars['editsave_'.$lang] = '';
						$this->urlvars['editheader_'.$lang] = '';
						$this->urlvars['editcomment_'.$lang] = '';
					}
				}
			}

			if ($this->editfunc == 'imgsave' ) {
				if ($this->urlvars['editsave'] ) {
					$this->saveComment($this->startdir.'/'.$this->urlvars['srcdir'].'/'.$this->files[$this->urlvars['fid']-1] , $this->urlvars['editheader'] , $this->urlvars['editcomment'] , '');
				}
				$this->urlvars['editsave'] = '';
				$this->urlvars['editheader'] = '';
				$this->urlvars['editcomment'] = '';
				$editlanguages = explode(' ', $this->conf['comment_languages']);
				if ($this->conf['comment_languages'] != "" & is_array($editlanguages)) {
					sort($editlanguages);
					foreach($editlanguages as $lang) {
						if ($this->urlvars['editsave_'.$lang] ) {
							# debug('save '.$lang);
							$this->saveComment($this->startdir.'/'.$this->urlvars['srcdir'].'/'.$this->files[$this->urlvars['fid']-1] , $this->urlvars['editheader_'.$lang] , $this->urlvars['editcomment_'.$lang] , $lang);
							$this->urlvars['editsave_'.$lang] = '';
							$this->urlvars['editheader_'.$lang] = '';
							$this->urlvars['editcomment_'.$lang] = '';
						}
					}
				}
				$this->editfunc = '';
			}
		}
		#/comment




		/**
 * Check if thumbnail cache file exists
 * Create tempfile if nessesary
 * Generate Img tag/Link
 * If resize failed or original is too small the img tag points to the original
 *
 * @param	string		$imgfile: path to the original image file
 * @param	[type]		$fullpath: ...
 * @param	[type]		$option: ...
 * @return	string		Link tag to single photo page with image tag of thumbnail
 */
		function show_thumb($imgfile,$fullpath='',$option='') {
			$image = $this->conf['image.'];
if ($fullpath) {
			$image['file'] = $this->startdir.'/'.$imgfile;
} else {
			$image['file'] = $this->startdir.'/'.$this->urlvars['srcdir'].'/'.$imgfile;
}
			list($width, $height, $type, $attr) = @getimagesize($image['file']);
			$width /= 10;
			$height /= 10;
			if ($width == $height) {
				$this->orientation = 'square';
			} elseif ($width > $height ) {
				$this->orientation = 'landscape';
			} else {
				$this->orientation = 'portret';
			}

			$rotateparam = $this->getRotateParameter( $this->startdir.'/'.$this->urlvars['srcdir'].'/'.$imgfile );
			$image['file.']['params']=$this->conf['thumbIMoptions'].' '.$rotateparam;
			($this->conf['thumbIMsample']) && ( $image['file.']['sample']=true);
			if ($this->conf['thumb_watermark']) {
				$image['file.']['m.'] = array(
				'bgImg' => $image['file'] ,
					'mask' => $this->conf['thumb_watermark_mask'] ,
					'bottomImg' => $this->conf['thumb_watermark_bottomimg'] ,
					'bottomImg_mask' => $this->conf['thumb_watermark_bottomimg_mask'] ,
					);
			}

			$image['file.']['maxW'] = $this->conf['thumb_maxw'].'m';
			$image['file.']['maxH'] = $this->conf['thumb_maxh'].'m';
			$image['altText'] = $this->pi_getLL('thumb_alt');
			if ($option != 'nolink') {
			$image['altText'] = $this->pi_getLL('thumb_link_alt');
				$image['linkWrap'] = $this->createLink('|', array('func' => '' , 'fid' => ($this->fid+1) , 'srcdir' => $this->urlvars['srcdir']));
			}
			return $this->cObj->IMAGE($image);
		}
		#/show_thumb

		/**
 * Check if resized cache file exists
 * Create tempfile if nessesary
 * Generate Img tag/Link
 * If resize failed or original is too small the img tag points to the original
 *
 * @param	string		$imgfile: path to the original image file
 * @return	string		Optional Link tag to the original photo page with image tag of resized image
 */
		function show_small($imgfile) {
			$this->singlefile = $imgfile;
			$image = $this->conf['image.'];
			$image['file'] = $this->startdir.'/'.$this->urlvars['srcdir'].'/'.$imgfile;
			if ($this->conf['watermark']) {
				$image['file.']['m.'] = array(
				'bgImg' => $image['file'] ,
					'mask' => $this->conf['watermark_mask'] ,
					'bottomImg' => $this->conf['watermark_bottomimg'] ,
					'bottomImg_mask' => $this->conf['watermark_bottomimg_mask'] ,
					);
			}
			$image['file.']['maxW'] = $this->conf['img_maxw'].'m';
			$image['file.']['maxH'] = $this->conf['img_maxh'].'m';
			$rotateparam = $this->getRotateParameter( $this->startdir.'/'.$this->urlvars['srcdir'].'/'.$imgfile );
			$image['file.']['params']=$this->conf['singleIMoptions'].' '.$rotateparam;
			$src .= $this->urlvars['srcdir'] ? $this->urlvars['srcdir'].'/' :
			'';
			if ((! $this->conf['no_full']) or ($this->known_user & $this->conf['user_full'])) {
				$image['linkWrap'] = $this->crlf.'<a href="'.$this->htmldir.'/'.$src.$imgfile.'"' .' target="picturefull">|</a>'."\n";
			}
#			$image['alttext'] = $imgfile;
			$image['alttext'] = $this->pi_getLL('single_alt');
			$imgcode = $this->fastWrap($this->cObj->IMAGE($image), $this->conf['image_wrap']);

			$this->pathToSingle = preg_replace( '/^.*src\=\"/i','',$imgcode);
			$this->pathToSingle = preg_replace( '/\".*$/','',$this->pathToSingle);
			if ($this->code == 'comment' ) {
				$commentarray = array();
				$commentarray = $this->loadComment($this->htmldir.'/'.$src.$imgfile, 'default');
				$this->imagecomment .= '<form method="post" id="goof_fotoboek_form" action="" >' ;
				$this->imagecomment .= $this->editFields($commentarray);

				$editlanguages = explode(' ', $this->conf['comment_languages']);
				if (is_array($editlanguages)) {
					sort($editlanguages);
					foreach($editlanguages as $lang) {
						if ($lang != '') {
							unset($commentarray);
							$commentarray = array();
							$commentarray = $this->loadComment($this->htmldir.'/'.$src.$imgfile, $lang, 1);
							#$commentarray['rest'] = preg_replace('/\r/', '', $commentarray['rest']);
							$this->imagecomment .= $this->editFields($commentarray, $lang);
						}
					}
				}

				$this->imagecomment .= '<p><input type="hidden" name="'.$this->prefixId.'[srcdir]" value="'.$this->urlvars['srcdir'].'" />' .'<input type="hidden" name="'.$this->prefixId.'[editfunc]" value="imgsave" />&nbsp;' .'<input type="submit" alt="'.$this->pi_getLL('save').'" name="" value="'.$this->pi_getLL('save').'" />' .'</p></form>'.$this->crlf;
			} else {
				$commentarray = array();
				$commentarray = $this->loadComment($this->htmldir.'/'.$src.$imgfile);
				$this->imagetitle = $this->fastWrap($commentarray['header'], $this->conf['comment_title_wrap']);

				if ($commentarray['rest']) {
					$rest = is_array($commentarray['rest'])?implode('', $commentarray['rest']):$commentarray['rest'];
					$this->imagecomment .= $this->fastWrap(preg_replace('!\n!', '<br />', htmlspecialchars($rest)), $this->conf['comment_wrap']);
				}
				if ($commentarray['extra']) {
					$rest = is_array($commentarray['extra'])?implode('', $commentarray['extra']):$commentarray['extra'];
					$this->imageextra .= $this->fastWrap(preg_replace('!\n!', '<br />', htmlspecialchars($rest)), $this->conf['extra_wrap']);
				}



				}
			//show exif information table
			if ($this->conf['show_exif'] == 1 ) {
				$exiftxt = $this->getExif($this->startdir.'/'.$this->urlvars['srcdir'].'/'.$imgfile);
				$this->imagecomment .= $exiftxt;
			}


			$link_extra = "onclick=\"pbwindow=window.open(this.href, 'popupwindow','width=500,height=600,scrollbars,resizable');if (window.focus) { pbwindow.focus()};return false;\"";

			if ($this->conf['basketEnable'] == 1 ) {

				$this->markerArray['###BASKET###'] = $this->createLink($this->pi_getLL('basket'), array('func' => 'basket'),$link_extra );
				#$this->markerArray['###BASKET###'] = $this->createLink($this->pi_getLL('basket'), array('func' => 'basket'),'target="_goof_photobook_basket"' );
				$this->markerArray['###ADD_TO_BASKET###'] = $this->createLink($this->pi_getLL('basketAdd'), array('img' => $this->urlvars['srcdir'].'/'.$imgfile , 'func' => 'basketadd', ) ,$link_extra );
			}

			return $imgcode;
		}
		#/show_small

		function fastWrap($string, $wrap = '|') {
			$wraparray = explode('|', stripslashes($wrap));
			return $wraparray[0].$string.$wraparray[1];
		}
		#/fastwrap

		function getRotateParameter( $file ) {
		  if ($this->conf['autorotate'] == 0 )  return '';
		  $exif = $this->getExifArray($file);

		  //t3lib_div::debug($exif);
		  $orientationvalue = null;
		  foreach ($exif as $key => $value) {
		    if ( preg_match('/Orientation/' , $key) ){
		      $orientationvalue = $value;
		      break;
		    }
		  }

		  switch ( intval($orientationvalue) ) {
		  case 1:
		    return "";
		  case 2:
		    return "-flip horizontal";
		  case 3:
		    return "-rotate 180";
		  case 4:
		    return "-flip vertical";
		  case 5:
		    return "-transpose";
		  case 6:
		    return "-rotate 90";
		  case 7:
		    return "-transverse";
		  case 8:
		    return "-rotate 270";
		  default:
		    return "";
		  }
		}#/getRotateParameter

		function loadComment($file, $language = '', $nodefault = 0) {
			$comment = '';
			$carray = array();
			//check if it's a directory
			$isdir = @is_dir($file);

			$language = ($language)?$language:
			strtolower($GLOBALS['TSFE']->config['config']['language']);
			if ($isdir) {
				$carray['commentfile_lang'] = $file.'.'.$language.'.txt';
				$carray['commentfile'] = $file.'.txt';
			} else {
				$carray['commentfile_lang'] = preg_replace("/\.[^\.]+$/", '.'.$language.'.txt', $file);
				$carray['commentfile'] = preg_replace("/\.[^\.]+$/", '.txt', $file);
			}
			//default back to the languageless comment file
			if ($nodefault or @file_exists($carray['commentfile_lang'])) {
				$carray['commentfile'] = $carray['commentfile_lang'];
			}
			if (@file_exists($carray['commentfile'])) {
				//comment textfile is leading over metadata
				$comment = explode("\n" , $this->cObj->fileResource($carray['commentfile']));
				$carray['header'] = $comment[0];
				$i = 1;
				while (sizeof($comment) > $i) {
					$carray['rest'][$i] = $comment[$i].$this->crlf;
					$i++;
				}
				$carray['rest'] = preg_replace('/\r/', '', $carray['rest']);
			} else {
				if (! $isdir) {
					//exif only for files
					if (@function_exists(exif_read_data)) {
						$txt = @exif_read_data($file, 'IFD0');
						if (is_array($txt)) {
							foreach ($txt as $name => $val) {
								$exif[$name] = $val;
							}
							$carray['header'] = $exif['Title'];
# Test section for the "One character bug"
#if (!extension_loaded('exif')) die('skip exif extension not available');
#	if (!extension_loaded('mbstring')) die('skip mbstring extension not available');
#	if (!defined('EXIF_USE_MBSTRING') || !EXIF_USE_MBSTRING) die ('skip mbstring loaded by dl');


#if (defined('EXIF_USE_MBSTRING')) {
#		$d=EXIF_USE_MBSTRING;
#}
							$carray['rest'] = $exif['Comments'];
							$carray['extra'] = $exif['Subject'];
						}
					}
				}
			}
			if ($isdir and $carray['header'] == '') {
				$carray['header'] = preg_replace('!.*/!', '', $file);
			}
			return $carray;
		}
		#/loadComment

		function saveComment($file, $title = '', $comment = '', $language = '') {
			$carray = array();
			//check if it's a directory
			$isdir = @is_dir($file);
			$langpart = ($language)? ('.'.$language):
			'';
			if ($isdir) {
				$carray['commentfile'] = $file.$langpart.'.txt';
			} else {
				$carray['commentfile'] = preg_replace("/\.[^\.]+$/", $langpart.'.txt', $file);
			}
			//test write rights
			// error =1/0
			//write $title and $comment to $carray['commentfile']

			$header = $title;
			if (is_array($rest)) {
				$rest = $comment;
			} else {
				$rest = explode("\n", $comment);
			}
			$rest2 = is_array($commentarray['rest'])?implode('', $commentarray['rest']):
			$commentarray['rest'];

			if ($header.$rest2 != '' ) {
				$fd = fopen ($carray['commentfile'], 'w');
				if ($fd) {
					fwrite($fd, $header);
					$i = sizeof($rest);
					while (preg_match("/^\W*$/", $rest[$i]) && ($i >= 0) ) {
						$i--;
					}
					$j = 0;
					while ($i >= 0) {
						$rest[$j] = preg_replace('/\r/', '', $rest[$j]);
						fwrite($fd, $this->crlf.$rest[$j]);
						$i--;
						$j++;

					}
					fclose ($fd);
				} else {
					t3lib_div::debug(' FAILED '.$this->startdir.'/'.$this->urlvars['srcdir'].'/'.$dum);
				}

			} else {
				#check existence
				#delete file
				@unlink($carray['commentfile']);
			}
			$this->editfunc = '';
			return 1;
		}
		#/saveComment
		 		/**
 * Create a RootLine from the current image categorie
 * Added by j.parree@team-networks.de, Date: 11.10.2005 - 15:04
 *
 * @return	void
 */
		 function getPathToRoot() {
		 	#mail('j.parree@team-networks.de', 'typo-debug', $this->createUrl(array('srcdir'=>'')));
		 	$dirBaseArray = explode('/', $this->urlvars['srcdir']);
			$i = 0;
			foreach($dirBaseArray as $k=>$v) {
				$currentDir .= ($i<(count($dirBaseArray)-1)) ? $v.'/' : $v;
				$dir .= '/ <a href="'.$this->createUrl(array('srcdir'=>$currentDir)).'">'.trim($v).'</a> ';
				$i++;
			}
			$this->dirpathtxt = $dir;
		 } #/getPathToRoot


		 function getExifArray($file) {
		  $exif = array();
		  // for older php version an external program is needed
		  // I chose metacam and jhead to get the exif information
		  if (@file_exists($this->conf['exif_metacam'])) {
		    exec( $this->conf['exif_metacam']." -a '".$file."'" , $txt);
		    if (is_array($txt)) {
		      foreach ($txt as $row) {
			preg_match('/^([^:]+):(.*)$/', $row, $match);
			$exif[ $match[1] ] = $match[2];
		      }
		    }
		  } elseif (@file_exists($this->conf['exif_jhead'])) {
		    exec($this->conf['exif_jhead']." '".$file."'" , $txt);
		    if (is_array($txt)) {
		      foreach ($txt as $row) {
			preg_match('/^([^:]+):(.*)$/', $row, $match);
			$exif[ $match[1] ] = $match[2];
		      }
		    }
		  } elseif (function_exists(exif_read_data)) {
		    $txt = @exif_read_data($file, 'EXIF', false);
		    if (is_array($txt)) {
		      foreach ($txt as $name => $val) {
			$val = preg_replace('/[^\w\d\s=\/:]/' , '', $val);
			$exif[$name] = $val;
		      }
		    }
		  }
		  return $exif;
		}
		/**
 * Get EXIF information if possible
 * method priority is metacam, jhead and PHP internal EXIF support
 *
 * @param	string		$file: path to the original image
 * @return	string		Exif information in html format
 */
		function getExif($file) {
		  $exif = $this->getExifArray($file);
		  if ( count( $exif ) == 0 ) {
		    $ret .= 'NO EXIF Information metacam or jhead not configured and no exif support in this php version<br />';
		  }
		  else {
		    $exiftable = '';
		    $exif_fields = '/Image Capture Date|Make$|Model|ISO Speed Rating|Image Description|'
				.'Focal Length|Flash$|Aperture|Exposure Time|Shutter Speed Value|'
				.'Date\/?Time$|ISO equiv|Flash used/i';
		    foreach ($exif as $key => $value) {
		      if (preg_match($exif_fields , $key) or $this->conf['show_exif_all'] == 1) {
			if ($value) {
			  $exiftable .= $this->fastWrap($key,$this->conf['exif_tag_wrap'])
			    .$this->fastWrap($value,$this->conf['exif_value_wrap']);
			}
		      }
		    }
		    if ($exiftable != '') {
		      $ret .=$this->fastWrap($exiftable,$this->conf['exif_all_wrap']);
		    }
		  }
		  return $ret;
		}




		/**
 * Get EXIF information if possible
 * method priority is metacam, jhead and PHP internal EXIF support
 *
 * @param	string		$file: path to the original image
 * @return	string		Exif information in html format
 */
		function getExifnot($file) {
			$ret = '';
			// for older php version an external program is needed
			// I chose metacam and jhead to get the
			if (@file_exists($this->conf['exif_metacam'])) {
				exec('"'.$this->conf['exif_metacam'].'" "'.$file.'"' , $txt);
				if (is_array($txt)) {
					foreach ($txt as $row) {
						preg_match('/^([^:]+):(.*)$/', $row, $match);
						$exif[ $match[1] ] = $match[2];
					}
				}
			} elseif (@file_exists($this->conf['exif_jhead'])) {
				exec('"'.$this->conf['exif_jhead'].'" "'.$file.'"' , $txt);
				if (is_array($txt)) {
					foreach ($txt as $row) {
						preg_match('/^([^:]+):(.*)$/', $row, $match);
						$exif[ $match[1] ] = $match[2];
					}
				}
			} elseif (@function_exists(exif_read_data)) {
				$txt = @exif_read_data($file, 'EXIF', false);
				if (is_array($txt)) {
					foreach ($txt as $name => $val) {
						$val = preg_replace('/[^\w\d\s=\/:]/' , '', $val);
						$exif[$name] = $val;
					}
				}
			} else {
				$ret .= 'NO EXIF Information metacam or jhead not configured and no exif support in this php version<br />';
			}
			if (is_array($exif)) {
				$exiftable = '';
				// time patch by Sven
				$exif_fields = '/Image Capture Date|Make$|Model|ISO Speed Rating|Image Description|' .'Focal Length|Flash$|Aperture|Exposure Time|Shutter Speed Value|' .'Date\/?Time$|ISO equiv|Flash used/i';
				foreach ($exif as $key => $value) {
					if (preg_match($exif_fields , $key) or $this->conf['show_exif_all'] == 1) {
						if ($value) {
							$exiftable .= $this->fastWrap($key, $this->conf['exif_tag_wrap'])
							.$this->fastWrap($value, $this->conf['exif_value_wrap']);
						}
					}
				}
				if ($exiftable != '') {
					$ret .= $this->fastWrap($exiftable, $this->conf['exif_all_wrap']);
				}
			}
			return $ret;
		}

		/**
 * Create a full link
 *
 * @param	[string]		$linktext: This ist the 'text' between the <a> and </a> tags.
 * @param	[array]		$urlarray: 	The parameter array included in the url.
 * @param	[string]		$linkextra: extra text inside the <a> tag like class or style options
 * @param	[boolean]$removeall:		is the option for the pi_linkTP_keepPIvars_url function
 * @return	[string]		return a complete link.
 */
		function createLink($linktext = '', $urlarray = array(), $linkextra = '', $removeall = 1 ) {
			return '<a href="'.$this->createUrl($urlarray, $removeall).'" '.$linkextra.'>'.$linktext.'</a>';
		}

		/**
 * Create URl from an array with parameters
 * Empty options will not show
 *
 * @param	array		$urlarray: array with optional parameters
 * @param	[type]		$removeall:  is the option for the pi_linkTP_keepPIvars_url function
 * @return	string		complete URL
 */
		function createUrl($urlarray = array(), $removeall = 1 ) {
			// removeall temp to 1
			if ($GLOBALS['TSFE']->config['config']['tx_realurl_enable'] == '1') {
				$urlarray['srcdir'] = preg_replace ('/\//' , '||' , $urlarray['srcdir']);
			}
			$url = htmlspecialchars($this->pi_linkTP_keepPIvars_url($urlarray, 1, $removeall));
			if ($this->conf['use_anchor'] == 1) {
					$url .= '#';
					# Versions above 4.0 should have a 'c' in the anchor.
					$major=preg_replace('/\..*$/','',$GLOBALS['TYPO_VERSION']);					
					if ( $major >= 4 ) {
						$url .= 'c';
					}
					$url .= $this->cObj->data['uid'];
			}
			return $url;
		}

		/**
 * Initialize
 * Create tempdirectory if needed (disabled)
 * cleanup path variable (remove double dots)
 * parse url vars or set defaults otherwise
 *
 * @return	boolean		true, always true
 */
		function init() {
		
			# http://bugs.php.net/bug.php?id=41372 serialize problem with the link creation.
			# noted by Thomas Gemperle
			$version = array();
			$v = phpversion();
			//$v = "4.4.1";
			foreach(explode('.', ( preg_replace( '/-.*$/' , '' , $v ) ) ) as $vbit)  {
				if(is_numeric($vbit)) {
					$version[] = $vbit;
				}
			}
			//Debug('Versie '.$v);
	
			if ( ( $version[0] == "4" ) && ( $version[1] == "4" )  &&  ( $version[2] > 1 )  ) {
				$this->array_buggy = 1;
				//Debug('BUG 4.x '.$v);
			}
	
			if ( $version[0] == "5" && $version[1] == 1 && $version[2] > 1 ) {
				$this->array_buggy = 1;
			 	//Debug('BUG 5.1 '.$version[1]);
			}

			if ( $version[0] == 5 && $version[1] ==  2  &&  $version[2] < 5 ) {
				//Debug('BUG 5.2 '.$v);
				$this->array_buggy = 1;
			}
	
			
			$this->pi_setPiVarDefaults();
			$this->pi_loadLL();
			$this->code = $this->cObj->data['tx_gooffotoboek_function'];
			$this->icons = $this->conf['icons'];
			$this->crlf = "\r\n";
			// for transitional purposes...
			$this->urlvars = $this->piVars;

			//take the path from the plugin options and fall back to the template config.
			$this->startdir = $this->cObj->data['tx_gooffotoboek_path'] ? $this->cObj->data['tx_gooffotoboek_path'] :
			$this->conf['path'];

			$abs_path_start = @realpath(t3lib_div::getFileAbsFileName($this->startdir));
			//cleanup backpath SRCDIR
			// make sure srcdir is at least an empty string
			$this->urlvars['srcdir'] ? $this->urlvars['srcdir'] : '';
			# replace the url directory seperator by the DIRECTORY_SEPARATOR
			$this->urlvars['srcdir'] = preg_replace ( '/\|\|/' , DIRECTORY_SEPARATOR ,  $this->urlvars['srcdir'] ); 

			$abs_path_src = @realpath($abs_path_start . DIRECTORY_SEPARATOR . $this->urlvars['srcdir']);

			// check if result is a valid path
			if (!$abs_path_start 
				|| !$abs_path_src 
				|| stripos($abs_path_src .  DIRECTORY_SEPARATOR, $abs_path_start . DIRECTORY_SEPARATOR) !== 0
			) {
				t3lib_div::debug("BAD SRCDIR PATH!");
				$this->urlvars['srcdir']= "";
			}

			//cleanup backpath IMG
			// make sure imgdir is at least an empty string
			$this->urlvars['img'] ? $this->urlvars['img'] : '';

			$abs_path_img = @realpath($abs_path_start . DIRECTORY_SEPARATOR . $this->urlvars['imgdir']);

			// check if result is a valid path
			if (!$abs_path_start 
				|| !$abs_path_img 
				|| stripos($abs_path_img .  DIRECTORY_SEPARATOR, $abs_path_start . DIRECTORY_SEPARATOR) !== 0
			) {
				t3lib_div::debug("BAD IMG PATH!");
				$this->urlvars['img']= "";
			}

			//set default function
			if (($this->conf['default_thumb'] == '1' ) and (! $this->urlvars['func']) and (! $this->urlvars['fid']) ) {
				$this->urlvars['func'] = 'thumb';
			}
			//set fid
			$this->urlvars['fid'] = $this->urlvars['fid']?$this->urlvars['fid']:
			1;
			$this->local_cObj = t3lib_div::makeInstance('tslib_cObj');
			// Local cObj.
			//path from server view
			$this->htmldir = $this->cObj->data['tx_gooffotoboek_path'] ? $this->cObj->data['tx_gooffotoboek_path'] :
			$this->conf['webpath'];
			$this->htmldir = $this->cObj->data['tx_gooffotoboek_webpath'] ? $this->cObj->data['tx_gooffotoboek_webpath'] :
			$this->htmldir;
			unset ($this->user);
			$this->known_user = 0;
			$this->user = Array();
			$this->grouplist = Array();
			if (is_object($GLOBALS['TSFE']->fe_user)) {

				if (is_array($GLOBALS['TSFE']->fe_user->user)) {
					$this->user = $GLOBALS['TSFE']->fe_user->user;
				}
				if (is_array($GLOBALS['TSFE']->fe_user->groupData['uid'])) {
					$this->grouplist = $GLOBALS['TSFE']->fe_user->groupData['uid'];
				}
				if ($this->user['uid'] != '') {
					$this->known_user = 1;
				}
			}

			#   debug($this->grouplist);
			($this->conf['debug'] ) && (t3lib_div::debug('startdir='.$this->startdir) );
			($this->conf['debug'] ) && (t3lib_div::debug('htmldir='.$this->htmldir) );
			($this->conf['debug'] ) && (t3lib_div::debug($this->conf) );
			($this->conf['debug'] ) && (t3lib_div::debug($this->urlvars) );
			($this->conf['debug'] ) && (t3lib_div::debug($GLOBALS['TYPO3_CONF_VARS']['GFX']) );
			($this->conf['debug'] ) && (t3lib_div::debug($GLOBALS['TYPO3_LOADED_EXT']['goof_fotoboek']['siteRelPath']) );
			// decode the slash back (encoding in createUrl)
			if ($GLOBALS['TSFE']->config['config']['tx_realurl_enable'] == '1') {
				$this->urlvars['srcdir'] = preg_replace('/\|\|/' , '/' , $this->urlvars['srcdir']);
			}
			// end decode
			$this->getPathToRoot();
			return true;
		}
		#/init

		/**
 * Get files in the current photobook directory and split them in image files and directories
 * To avoid thumbnail directories, .small, .xvpics ,.DAV are ignored
 * Only files in [GFX][imagefile_ext] get through.
 *
 * @return	void
 */
		function getFiles() {
			global $TSFE;

			$aGroupUser = explode( ',', $TSFE->gr_list );

			$aGroupDir = $this->getGroupsOfDir( $this->startdir.'/'.$this->urlvars['srcdir'] );

			if ( $this->hasPermission( $aGroupUser, $aGroupDir ) &&
					 $dir = @opendir($this->startdir.'/'.$this->urlvars['srcdir']) ) {
				while (($file = readdir($dir)) !== false) {
					if (is_dir($this->startdir.'/'.$this->urlvars['srcdir'].'/'.$file)) {
						if ($file != "." and $file != ".." and $file != ".small"  and $file != '.xvpics' and $file != '.DAV') {
							if ( $this->hasPermission( $aGroupUser, $this->getGroupsOfDir( $this->startdir.'/'.$this->urlvars['srcdir'].'/'.$file ) ) )
								$this->dirs[] = $file;
						}
					} else {
						#[GFX][imagefile_ext] = gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai,psd
						$types = preg_replace('/,/', '|', $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']);
						if (preg_match('{('.$types.")$}i", $file)) {
							if (($this->conf['show_teaser'] == 1) and ($file == '_teaser.jpg')) {
								#skip teaser
							} else {
								//if ( $this->hasPermission( $aGroupUser, $aGroupDir ) )
								$this->files[] = $file;
							}
						}
					}
				}
				closedir($dir);
			} else {
				$content .= $this->pi_getLL('nodir').'<br />'.$this->crlf;
			}
			$this->filecount = sizeof($this->files);
			if (is_array($this->files)) {
				if ($this->conf['rev_sort_files'] == 0) {
					array_multisort(array_map(strtolower,$this->files), SORT_ASC, SORT_STRING, $this->files);
				} else {
					array_multisort(array_map(strtolower,$this->files), SORT_DESC, SORT_STRING, $this->files);
				}
			}
 
#			if (is_array($this->files)) {
#				if ($this->conf['rev_sort_files'] == 0) {
#					sort($this->files);
#				} else {
#					rsort($this->files);
#				}
#			}
			if (is_array($this->dirs)) {
				if (
						(($this->conf['rev_sort_dirs'] == 1) && ($this->urlvars['srcdir'] != ''))
						|| (($this->conf['rev_sort_root'] == 1) && ($this->urlvars['srcdir'] == ''))
						) {
					rsort($this->dirs);
				} else {
					sort($this->dirs);
				}
			}
			$this->urlvars['fid'] = $this->urlvars['fid'] >= (sizeof($this->files)) ? (sizeof($this->files)):
			$this->urlvars['fid'];
		}#/getfiles

		/**
 * Fill dirtitletxt with directory name or the directory comment if available
 *
 * @return	void
 */
		function showDirTitle() {
			$commentarray = $this->loadComment($this->startdir.'/'.$this->urlvars['srcdir']);
			if ( $this->conf['replaceTitleUnderscores'] ) {
				$this->dirtitletxt = $this->fastWrap(str_replace('_',' ',$commentarray['header']), $this->conf['dir_title_wrap']);
			} else {
				$this->dirtitletxt = $this->fastWrap($commentarray['header'], $this->conf['dir_title_wrap']);
			}
		}


		/**
 * Fill dirtxt with list of directories or their comment
 *
 * @return	void
 */
		function showDirs() {
			$this->dirtxt = '';
			$dirtitletxt = '';
			if (is_array($this->dirs)) {
				$dirLinkWrap = explode('|', stripslashes($this->conf['dir_link_wrap']));
				$dirCommentWrap = explode('|', stripslashes($this->conf['dir_comment_wrap']));
				$dirAllCommentWrap = explode('|', stripslashes($this->conf['dir_all_comment_wrap']));
				$dirWrap = explode('|', stripslashes($this->conf['dir_wrap']));
				foreach($this->dirs as $dum) {
					if ($this->urlvars['srcdir']) {
						$nextdir = $this->urlvars['srcdir'].'/'.$dum;
					} else {
						$nextdir = $dum;
					}

					$commentarray = array();
					$commentarray = $this->loadComment($this->startdir.'/'.$this->urlvars['srcdir'].'/'.$dum);
					$dirtitlecomment = '';
					$dirtitletxt = $commentarray['header'];
					if ( $this->conf['replaceTitleUnderscores'] ) {
						$dirtitletxt = str_replace( '_',' ',$dirtitletxt );
					}
					if (is_array($commentarray['rest'])) {

						$dirtitlecomment .= $dirAllCommentWrap[0];
						foreach ($commentarray['rest'] as $row) {
							$dirtitlecomment .= $dirCommentWrap[0].$row.$dirCommentWrap[1].$this->crlf;
						}
						$dirtitlecomment .= $dirAllCommentWrap[1].$this->crlf;
					}
					$durl = $this->createUrl(array('srcdir' => $nextdir) );
					$editTxt = '';
					if ($this->code == 'comment' and $this->conf['disable_dircomment'] == 0) {
						$editTxt = '&nbsp;'.$this->createLink('('.$this->pi_getLL('edit').')', array('fid' => 1,'srcdir' => $nextdir, 'editfunc' => 'dir') );
					}
					$dirdum = $dirLinkWrap[0].$this->createLink($dirtitletxt, array('fid' => 1,'srcdir' => $nextdir,'func'=>$this->urlvars['func']) ).$editTxt.$dirLinkWrap[1];

					$teaser = '';
					if ($this->conf['show_teaser'] == 1) {
						if (@file_exists($this->startdir.'/'.$this->urlvars['srcdir'].'/'.$dum.'/_teaser.jpg')) {
							unset($img);
							$img['file'] = $this->startdir.'/'.$this->urlvars['srcdir'].'/'.$dum.'/_teaser.jpg';
							$teaser = $dirLinkWrap[0].'<a href="'.$durl.'">'.$this->cObj->IMAGE($img).'</a>'.$dirLinkWrap[1];
						}
					}

					if ($dirtitlecomment) {
						$dirdum .= $dirtitlecomment;
					}
					$this->dirtxt .= $dirWrap[0].$teaser.$dirdum.$dirWrap[1].$this->crlf;
				}
				($this->conf['debug'] ) && (t3lib_div::debug($dirtxt) );
			}
		}

		/**
 * Combined view
 *
 * @return	void
 */
		function showCombine() {
			$this->showThumbs();
			$this->showFile();

		} #/showCombine


		/**
 * Fill thumbnailtxt with the html code for the thumbnails in current directory
 *
 * @return	void
 */
		function showThumbs() {
			##@@
			if (is_array($this->files)) {
				$start = ($this->urlvars['fid']) ? $this->urlvars['fid'] :
				 0;
				$thumbsPerPage = $this->conf['thumb_per_row'] * $this->conf['thumb_rows'];
				//directories with less then the maximum thumbs per page should start with the first

				$start = $this->thumbstart;
				$thumbsPerPage = $this->thumbsPerPage ;

				$this->thumbnailstxt = '';

				//replace ###orientation### with the thumbnail orientation.
				$rowWrap = explode('|', stripslashes($this->conf['thumb_row_wrap']));
				for($i = 0; $i < $this->conf['thumb_rows'] ; $i++) {
					$rowtxt = '';
					$j = 0;
					for ($this->fid = ($start + ($i * $this->conf['thumb_per_row']) )
					;
					$this->fid < ($start + ($i * $this->conf['thumb_per_row'] ) + $this->conf['thumb_per_row']  )
					;
					$this->fid++) {
						$t = '';
						if ($this->fid < $this->filecount) {
							$t = $this->show_thumb($this->files[$this->fid]);
						} else {
							#       $t = '&nbsp;'; //some cells disapear when nothing's in it.
							$t = ''; //some cells disapear when nothing's in it.
							$this->orientation = 'empty';
						}
						if ($this->conf['useThumbnailTemplate'] == 0) {
						 	$thumbWrap = explode('|', stripslashes(
							preg_replace(
								array('/###orientation###/i', '/###thumbid###/i' ),
								array($this->orientation, 'r'.$i.'c'.$j ),
								$this->conf['thumb_wrap'])
							));
							$rowtxt .= $thumbWrap[0].$t;
							($this->conf['thumb_filenames']) && ($rowtxt .= '<br />'.htmlspecialchars($this->files[$this->fid]));
							$rowtxt .= $thumbWrap[1].$this->crlf ;
						} else {
							$file = $this->startdir.'/'.$this->urlvars['srcdir'].'/'.$this->files[$this->fid];
							$comment=$this->loadComment($file, $language = '', $nodefault = 0);
							$globalMarkerArray['###ORIENTATION###'] = $this->orientation;
							$globalMarkerArray['###THUMBID###'] = 'r'.$i.'c'.$j;
							$globalMarkerArray['###THUMBNAIL_IMAGE###'] = $t;
							$globalMarkerArray['###THUMBNAIL_COMMMENT_HEADER###'] = $comment['header'];
							$globalMarkerArray['###THUMBNAIL_FILENAME###'] = htmlspecialchars($this->files[$this->fid]);
							$globalMarkerArray['###THUMBNAIL_FILESIZE###'] = filesize($file);
							$rowtxt .= $this->cObj->substituteMarkerArray($this->local_cObj->getSubpart($this->totalTemplate, '###THUMBNAIL###'), $globalMarkerArray);
						}
						$j++;
					}
					$this->thumbnailstxt .= $rowWrap[0].$rowtxt.$rowWrap[1].$this->crlf;
				}
			}
		}
		#/showthumbs

		/**
 * Fill imgtxt with the htmlcode to show a single resized photo
 *
 * @return	void
 */
		function showFile() {
			if (is_array($this->files)) {
				($this->conf['debug'] ) && (t3lib_div::debug($this->files[$this->urlvars['fid']-1]) );
				$this->imagetxt = $this->show_small($this->files[$this->urlvars['fid']-1] );
			}
		}
		#/showfile

		/**
 * fill totaltemplate with the html from the template file
 *
 * @return	void
 */
		function getTemplate() {
			$this->totalTemplate = $this->cObj->fileResource($this->conf['templateFile']);
		}
		#/gettemplate

		/**
 * Parse the subpart of the template into content with the various variables
 *
 * @param	string		$subpart: Needed subpart of the template
 * @return	void
 */
		function parseTemplate($subpart) {
			$template = $this->local_cObj->getSubpart($this->totalTemplate, "###".$subpart.'###');
			$globalMarkerArray = $this->markerArray;
			$globalMarkerArray['###COUNT_STRING###'] =  htmlspecialchars($this->pi_getLL('image').' '.$this->urlvars['fid'].' '.$this->pi_getLL('of').' '.$this->filecount.' '.$this->pi_getLL('images'));
			$globalMarkerArray['###COUNT_COUNT###'] = $this->filecount;
			$globalMarkerArray['###COUNT_NR###'] = $this->urlvars['fid'];
			$globalMarkerArray['###DIRTITLE###'] = $this->dirtitletxt;
			$globalMarkerArray['###DIRS###'] = $this->dirtxt;
			$globalMarkerArray['###INDEX###'] = $this->indextxt;
			#   $globalMarkerArray['###SPACER###'] = $this->spacertxt;
			$globalMarkerArray['###SLIDESHOW###'] = $this->slidetxt;
			$globalMarkerArray['###UP###'] = $this->uptxt;
			$globalMarkerArray['###PREV###'] = $this->prevtxt;
			$globalMarkerArray['###NEXT###'] = $this->nexttxt;
			$globalMarkerArray['###PAGES###'] = $this->pagestxt;
			$globalMarkerArray['###THUMBS###'] = $this->thumbtxt;
			$globalMarkerArray['###THUMBNAILS###'] = $this->thumbnailstxt;
			$globalMarkerArray['###IMAGE###'] = $this->imagetxt;
			$globalMarkerArray['###TITLE###'] = $this->imagetitle;
			$globalMarkerArray['###COMMENT###'] = $this->imagecomment;
			$globalMarkerArray['###EXTRA###'] = $this->imageextra;
			$globalMarkerArray['###NAVSTART###'] = $this->navstart;
			$globalMarkerArray['###NAVEND###'] = $this->navend;
			$globalMarkerArray['###FILENAME###'] = $this->singlefile;
			$globalMarkerArray['###DIRPATH###'] = $this->dirpathtxt;
			$globalMarkerArray['###PATH_TO_ORIGINAL###'] = $this->startdir.'/'.($this->urlvars['srcdir']?$this->urlvars['srcdir'].'/':'').$this->singlefile;
			$globalMarkerArray['###PATH_TO_SINGLE###'] = $this->pathToSingle;
			$globalMarkerArray['###FASTNAV###'] = $this->fastNavigation;

			$template = $this->cObj->substituteMarkerArray($template, $globalMarkerArray);
			return $template;
		}
		#/parsetemplate



	function getGroupsOfDir ( $dir ){
		$file_permissions = $dir . '/.access';
		if ( file_exists( $file_permissions ) ){
			$groupsPermitted = array();
			if ( $fp = fopen( $file_permissions, 'r' ) ){
				while( $sLine = fgets( $fp ) ){
					$sLine = preg_replace( '/#.*/', '', $sLine );
					if ( intval( $sLine ) > 0 ){
						$groups[] = intval( $sLine );
					}
				}

				fclose( $fp );
			}

			return $groups;
		}
		return false;
	}#/getGroupsOfDir

	function hasPermission( $aGroupUser, $aGroupDir ){
		$bRet = false;
		if ( is_array( $aGroupDir ) ){
			foreach ($aGroupUser as $val) {
				if ( in_array( $val, $aGroupDir ) ){
					$bRet = true;
				}
			}
		}else{
			$bRet = true; // no permission specified
		}
		return $bRet;
	}#/hasPermission













		/**
 * Create the navigation links with the text names or images
 *
 * @return	void
 */
		function createLinks() {
			//this section has been moved to hussle the thumbpages before the links are generated
			$this->thumbstart = ($this->urlvars['fid']) ? $this->urlvars['fid'] :
			 1;
			$this->thumbsPerPage = $this->conf['thumb_per_row'] * $this->conf['thumb_rows'];
			//directories with less then the maximum thumbs per page should start with the first
			if ($this->thumbstart > $this->filecount) {
				$this->thumbstart = $this->filecount;
			}
			$this->thumbstart = floor(($this->thumbstart-1)/$this->thumbsPerPage) * $this->thumbsPerPage ;

			if ($this->urlvars['func'] == 'thumb') {
				if ($this->conf['fill_thumb_page'] ) {
					$this->thumbstart--;
					if ($this->filecount < $this->thumbsPerPage ) {
						$this->thumbstart = 0;
					} elseif ($this->thumbstart > ($this->filecount - $this->thumbsPerPage  ) ) {
						//the last page should be a full one if possible
						$this->thumbstart = $this->filecount - $this->thumbsPerPage ;
					}
				} else {
					$this->urlvars['fid'] = $this->thumbstart;
				}
			}

			$icons = $this->icons;
			//spacer
			$img['file'] = $this->conf['img_spacer'];
			$this->markerArray['###SPACER###'] = ($icons) ? $this->cObj->IMAGE($img) :
			 $this->conf['txt_spacer'];

			//up and index
			#$dumurl = $this->array_buggy ? unserialize(serialize($this->urlvars)) : $this->urlvars;
			$dumurl = array();
			foreach($this->urlvars as $key => $val) {
				$dumurl[$key]=$val;
			}

			
			
			if (preg_match("/\//", $dumurl['srcdir'])) {
				$dumurl['srcdir'] = preg_replace("{/[^\/]+$}" , '' , $dumurl['srcdir']);
			} else {
				$dumurl['srcdir'] = '';
			}

			$dumurl['fid'] = '';
			$dumurl['func'] = '';
			$dumurl['slide'] = '';
			$dumurl['editfunc'] = '';
			$dumurl['editheader'] = '';
			$dumurl['editcomment'] = '';

			if ($this->urlvars['srcdir']) {
				if ($this->icons) {
					unset($img);
					$img['file'] = $this->conf['img_up_on'];
					$img['alttext'] = $this->pi_getLL('up');
					$img['linkWrap'] = $this->createLink('|', $dumurl, 'accesskey="u"').$this->crlf;
					$this->uptxt = $this->cObj->IMAGE($img);

					$img['file'] = $this->conf['img_index_on'];
					$img['alttext'] = $this->pi_getLL('index');
					#fix for PHP5 and my empty array
					$img['linkWrap'] = $this->createLink('|', array('srcdir' => ''), 'accesskey="h"', 1).$this->crlf;
					$this->indextxt = $this->cObj->IMAGE($img);
					($this->conf['patchAltTag']) && $this->patchAlt($this->uptxt);
					($this->conf['patchAltTag']) && $this->patchAlt($this->indextxt);
				} else {
					$this->indextxt = '<a  accesskey="h" href="'.$this->createUrl('').'">'.$this->pi_getLL('index').'</a>';
					$this->uptxt = $this->createLink($this->pi_getLL('up'), $dumurl, 'accesskey="u"');
				}
			} else {
				if ($this->icons) {
					unset($img);
					$img['file'] = $this->conf['img_index_off'];
					$img['altText'] = $this->pi_getLL('index_off');
					$this->indextxt = $this->cObj->IMAGE($img);
					$img['file'] = $this->conf['img_up_off'];
					$img['altText'] = $this->pi_getLL('up_off');
					$this->uptxt = $this->cObj->IMAGE($img);
				}
			}
			#navstart and navend
			if ($this->icons) {
				unset($img);
				$img['file'] = $this->conf['img_nav_start'];
				$this->navstart = $this->cObj->IMAGE($img);
				$img['file'] = $this->conf['img_nav_end'];
				$this->navend = $this->cObj->IMAGE($img);
			} else {
				$this->navstart = '';
				$this->navend = '';
			}
			#remove +1
			$step = ($this->urlvars['func'] != 'thumb') ? 1 :
			($this->conf['thumb_per_row'] * $this->conf['thumb_rows']);

			#previous
			#$dumurl = $this->array_buggy ? unserialize(serialize($this->urlvars)) : $this->urlvars;

			$dumurl = array();
			foreach($this->urlvars as $key => $val) {
				$dumurl[$key]=$val;
			}

			$dumurl['editfunc'] = '';
			$dumurl['editheader'] = '';
			$dumurl['editcomment'] = '';
			if ($this->urlvars['func'] == 'thumb') {
				$dumurl['fid']++;
			}
			$dumurl['fid'] -= $step;
			if (($this->urlvars['func'] == 'thumb') & (($dumurl['fid'] > (1-$step) )
				& ($dumurl['fid'] <= 0) )
			) {
				$dumurl['fid'] = 1;
			}
			if (($dumurl['fid'] > 0) ) {
				if ($this->conf['loadnext']) {
					$GLOBALS['TSFE']->additionalHeaderData['goof_fotoboek_headprev'] = '<link href="'.$this->createUrl($dumurl).'" rel="prev" />'.$this->crlf;
				}
				if ($this->icons) {
					unset($img);
					$img['file'] = $this->conf['img_prev_on'];
					$img['alttext'] = $this->pi_getLL('prev');
					$img['linkWrap'] = '<a accesskey="-" href="'.$this->createUrl($dumurl).'"' .'>|</a>'.$this->crlf;
					$this->prevtxt = $this->cObj->IMAGE($img);
					($this->conf['patchAltTag']) && $this->patchAlt($this->prevtxt);

				} else {
					$this->prevtxt = '<a accesskey="-" href="'.$this->createUrl($dumurl).'">'.$this->pi_getLL('prev').'</a>';
				}
			} else {
				if ($this->icons) {
					unset($img);
					$img['file'] = $this->conf['img_prev_off'];
					$img['altText'] = $this->pi_getLL('prev_off');
					$this->prevtxt = $this->cObj->IMAGE($img);
				}
			}
			#next
			#$dumurl = $this->array_buggy ? unserialize(serialize($this->urlvars)) : $this->urlvars;
			$dumurl = array();
			foreach($this->urlvars as $key => $val) {
				$dumurl[$key]=$val;
			}

			$dumurl['editfunc'] = '';
			$dumurl['editheader'] = '';
			$dumurl['editcomment'] = '';
			if ($this->urlvars['func'] == 'thumb') {
				$dumurl['fid']++;
			}

			$dumurl['fid'] += $step;
			if ($this->filecount >= $dumurl['fid']  ) {
				if ($this->conf['loadnext']) {
					$GLOBALS['TSFE']->additionalHeaderData['goof_fotoboek_headnext'] .= '<link href="'.$this->createUrl($dumurl).'" rel="next" />'.$this->crlf;
				}

				if ($this->icons) {
					unset($img);
					$img['file'] = $this->conf['img_next_on'];
					$img['alttext'] = $this->pi_getLL('next');
					$img['linkWrap'] = '<a accesskey="+" href="'.$this->createUrl($dumurl).'"' .'>|</a>'.$this->crlf;
					$this->nexttxt = $this->cObj->IMAGE($img);
					($this->conf['patchAltTag']) && $this->patchAlt($this->nexttxt);

				} else {
					$this->nexttxt = '<a accesskey="+" href="'.$this->createUrl($dumurl).'">'.$this->pi_getLL('next').'</a>';
				}
			} else {
				if ($this->icons) {
					unset($img);
					$img['file'] = $this->conf['img_next_off'];
					$img['altText'] = $this->pi_getLL('next_off');
					$this->nexttxt = $this->cObj->IMAGE($img);
				}
			}
			#thumb
			if ($this->filecount > 0 && $this->urlvars['func'] != 'thumb') {
			$dumurl = $this->array_buggy ? unserialize(serialize($this->urlvars)) : $this->urlvars;
				$dumurl['editfunc'] = '';
				$dumurl['editheader'] = '';
				$dumurl['editcomment'] = '';
				$dumurl['fid'] = $this->thumbstart + 1;
				$dumurl['func'] = 'thumb';
				#
				#$dumurl['fid']-- ;
				if ($this->icons) {
					unset($img);
					$img['file'] = $this->conf['img_thumb_on'];
					$img['alttext'] = $this->pi_getLL('thumb');
					$img['linkWrap'] = $this->createlink('|', $dumurl, 'accesskey="t"').$this->crlf;
					$this->thumbtxt = $this->cObj->IMAGE($img);
					($this->conf['patchAltTag']) && $this->patchAlt($this->thumbtxt);
				} else {
					$this->thumbtxt = '<a  accesskey="t" href="'.$this->createUrl($dumurl).'">'.$this->pi_getLL('thumb').'</a>';
				}
			} else {
				if ($this->icons) {
					unset($img);
					$img['file'] = $this->conf['img_thumb_off'];
					$img['altText'] = $this->pi_getLL('thumb_off');
					$this->thumbtxt = $this->cObj->IMAGE($img);
				}
			}
			#slideshow

			$this->slidetxt = '';
			if (($this->conf['slideshow']) && ($this->filecount > 1) ) {
			$dumurl = $this->array_buggy ? unserialize(serialize($this->urlvars)) : $this->urlvars;
				$dumurl['editfunc'] = '';
				$dumurl['editheader'] = '';
				$dumurl['editcomment'] = '';

				unset($img);
				if ($this->urlvars['func'] == 'slide' ) {
					$dumurl['func'] = '';
					$dumll = $this->pi_getLL('stopslide');
					$img['file'] = $this->conf['img_slide_stop'];
					$img['alttext'] = $dumll;
				} else {
					$dumurl['func'] = 'slide';
					$dumll = $this->pi_getLL('startslide');
					$img['alttext'] = $dumll;
					$img['file'] = $this->conf['img_slide_start'];
				}

				if ($this->icons) {
					$img['linkWrap'] = $this->createLink('|', $dumurl, ' accesskey="*" ').$this->crlf;
					$this->slidetxt = $this->cObj->IMAGE($img);
					($this->conf['patchAltTag']) && $this->patchAlt($this->slidetxt);
				} else {
					$this->slidetxt = $this->createLink($dumll, $dumurl, ' accesskey="*" ');
				}

				if ($this->urlvars['func'] == 'slide')  {
			$dumurl = $this->array_buggy ? unserialize(serialize($this->urlvars)) : $this->urlvars;
					$dumurl['editfunc'] = '';
					$dumurl['editheader'] = '';
					$dumurl['editcomment'] = '';
					$dumurl['fid'] += $step;
					if ($this->filecount < $dumurl['fid']  ) {
						if ($this->conf['slideshowLoop'] ) {
							$dumurl['fid'] = '1';
						} else {
							$dumurl['fid'] = '';
							$dumurl['func'] = '';
						}
					}
					# a BUG! it should be relative in normal mode and absolute in realurl
#absolute in both forms?
#!==
#				if ($GLOBALS['TSFE']->config['config']['tx_realurl_enable'] == '1') {
						$basedir=preg_replace('/\/[^\/]*$/','/',$_SERVER['PHP_SELF']);
						$basedir=preg_replace('/\/\//','/',$basedir);

						$GLOBALS['TSFE']->additionalHeaderData['goof_felog_headslide'] = '<meta http-equiv="refresh" content="'.$this->conf['slidetime'].';URL=\''.$basedir.$this->createUrl($dumurl).'\'" />';
#					} else {
#						$GLOBALS['TSFE']->additionalHeaderData['goof_felog_headslide'] = '<meta http-equiv="refresh" content="'.$this->conf['slidetime'].";URL='".$this->createUrl($dumurl)."'\" />";
#					}
				}
			}


			// page navigation
			$this->pagestxt='';
			if ($this->conf['pageNav'] == 1 && $this->filecount>0) {
			$dumurl = $this->array_buggy ? unserialize(serialize($this->urlvars)) : $this->urlvars;
				$dumurl['editfunc'] = '';
				$dumurl['editheader'] = '';
				$dumurl['editcomment'] = '';
				$totalpages = ceil($this->filecount / $this->thumbsPerPage);
				$curpage = floor($this->piVars['fid'] / $this->thumbsPerPage)+1;

				if ($this->conf['pageNavJumping'] == 1) {
					$this->conf['pageNavJumpingBeforeAndAfter'] = (empty($this->conf['pageNavJumpingBeforeAndAfter'])?5:$this->conf['pageNavJumpingBeforeAndAfter']);

					if ($curpage!=1) {
						foreach (range($curpage-($curpage<=$this->conf['pageNavJumpingBeforeAndAfter']?$curpage-1:$this->conf['pageNavJumpingBeforeAndAfter']), $curpage-1) as $number) {
							$dumurl['fid'] = (($number-1) * $this->thumbsPerPage) + 1;
							$this->pagestxt .= $this->createLink($number, $dumurl).' ';
						}
					}

					$this->pagestxt .= $curpage.' ';

					if ($curpage!=$totalpages) {
						foreach (range($curpage+1, ($curpage+$this->conf['pageNavJumpingBeforeAndAfter']>$totalpages?$totalpages:$curpage+$this->conf['pageNavJumpingBeforeAndAfter'])) as $number) {
							$dumurl['fid'] = (($number-1) * $this->thumbsPerPage) + 1;
							$this->pagestxt .= $this->createLink($number, $dumurl).' ';
						}
					}
				} else {
					foreach (range(1, $totalpages) as $number) {
						if ($number==$curpage) {
							$this->pagestxt .= $curpage.' ';
						} else {
							$dumurl['fid'] = (($number-1) * $this->thumbsPerPage) + 1;
							$this->pagestxt .= $this->createLink($number, $dumurl).' ';
						}
					}
				}
			}

			// fast navigation
			$this->fastNavigation = '';
			if ($this->conf['navFast'] == 1) {
			$dumurl = $this->array_buggy ? unserialize(serialize($this->urlvars)) : $this->urlvars;
				$dumurl['editfunc'] = '';
				$dumurl['editheader'] = '';
				$dumurl['editcomment'] = '';
				$focusStart = $this->urlvars['fid'] - $this->conf['navFocus'];
				$focusStop = $this->urlvars['fid'] + $this->conf['navFocus'];
				if ($focusStart < 1 ) {
					$focusStart = 1;
				}
				if ($focusStop > $this->filecount ) {
					$focusStop = $this->filecount;
				}
				$prevStop = $focusStart - 1;
				if ($this->conf['navBigStep'] && ($focusStart > 1) ) {
					for ($i = 1; $i <= $prevStop; $i += $this->conf['navBigStep'] ) {
						$dumurl['fid'] = $i;
						$this->fastNavigation .= $this->fastWrap($this->createLink($i, $dumurl), $this->conf['navFocusWrap']);
					}
				}


				for ($i = $focusStart; $i <= $focusStop; $i++) {
					$dumurl['fid'] = $i;
					$this->fastNavigation .= $this->fastWrap($this->createLink($i, $dumurl), $this->conf['navFocusWrap']);
				}
				$nextStart = $focusStop +1;
				$nextStop = $this->filecount;

				if ($this->conf['navBigStep'] && ($focusStop < $this->filecount ) ) {
					for ($i = $nextStart; $i <= $nextStop; $i += $this->conf['navBigStep'] ) {
						$dumurl['fid'] = $i;
						$this->fastNavigation .= $this->fastWrap($this->createLink($i, $dumurl), $this->conf['navFocusWrap']);
					}
				}

			}



		}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$$tag: ...
	 * @return	[type]		...
	 */
		function patchAlt(&$tag) {
				preg_match('/alt=\"[^"]+"/',$tag,$match);
				$alt=html_entity_decode($match[0]);
				preg_match('/title=\"[^"]+"/',$tag,$match);
				$title=html_entity_decode($match[0]);
				$tag=preg_replace('/alt=\"[^"]+"/',$alt,$tag);
				$tag=preg_replace('/title=\"[^"]+"/',$title,$tag);
#				str_replace(html_entity_decode($match[0]),$match[0],$this->nexttxt);
		}#/patchAlt

		/**
 * Get the contents of the basket
 *
 * @return	page		content of the basket
 */
		function basketContent() {
			$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, 'select count(is_on_page) as count from tx_gooffotoboek_basket where is_on_page='.$this->pbData['pid'].' and session_id="'.$this->pbData['basketID'].'" order by image ;');
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) $count = $row['count'];
			$this->markerArray['###BASKET_CLOSE###'] = '<a href="javascript:window.close()">'.$this->pi_getLL('basketClose').'</a>';

			$content='';
			#debug($this->pbData);
			$this->markerArray['###BASKET_NAME###'] = $this->pi_getLL('basket');
			$dumurl['func']='removeall';
			$this->markerArray['###BASKET_EMPTY###'] = $this->createLink($this->pi_getLL('basketEmpty'), $dumurl);
			$dumurl['func']='createzip';
			$this->markerArray['###BASKET_GET_ZIP###'] = $this->createLink($this->pi_getLL('basketGenerateZip'), $dumurl);

			if ( $count > 0 ) {
				$i=1;
				$this->markerArray['###BASKET_COUNT###'] = $this->pi_getLL('basketCount').$count;
				$dumurl['func']='basket';
				$this->markerArray['###BASKET_FORM_URL###'] =$this->createUrl($dumurl);

				$dumurl['func']='removeitem';

				$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, 'select * from tx_gooffotoboek_basket where is_on_page='.$this->pbData['pid'].' and session_id="'.$this->pbData['basketID'].'" order by image ;');
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$this->markerArray['###BASKET_FILENAME###'] = $row['image'];
					$this->markerArray['###BASKET_THUMB###'] = $this->show_thumb($row['image'],1,'nolink');
					$dumurl['item']=$row['img_id'];
					$this->markerArray['###BASKET_REMOVE###'] = $this->createLink($this->pi_getLL('removeItem'), $dumurl);
					$this->markerArray['###BASKET_ITEMS###'] .= $this->parseTemplate('BASKET_ITEM') ;
					$i++;
				}
			} else {
				$this->markerArray['###BASKET_COUNT###'] = $this->pi_getLL('basketIsEmpty');
				$this->markerArray['###BASKET_ITEMS###'] = '';
				$this->markerArray['###BASKET_EMPTY###'] = '';
				$this->markerArray['###BASKET_GET_ZIP###'] = '';
			}

			$content .= $this->parseTemplate('BASKET_TPL');
			return $content;
		}

		/**
 * Create a zip of the basket
 *
 * @return	page		with link to the zipfile
 */
		function zipBasket() {
			$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, 'select count(is_on_page) as count from tx_gooffotoboek_basket where is_on_page='.$this->pbData['pid'].' and session_id="'.$this->pbData['basketID'].'" order by image ;');
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) $count = $row['count'];

			$pwd=exec('pwd');

			$content='';
			$ziptmp='typo3temp/gfbasket_'.$this->pbData['basketID'];
			$zip='typo3temp/'.$this->pbData['basketID'].'.zip';
			$this->markerArray['###BASKET_CLOSE###'] = '<a href="javascript:window.close()">'.$this->pi_getLL('basketClose').'</a>';


			if ( $count > 0 ) {
				$content = '<h1>'.$this->pi_getLL('basketZipGenerating').'</h1>';
				echo exec('/bin/rmdir '.escapeshellarg($ziptmp))."<br>\n";
				exec('/bin/mkdir -m 775 -v '.escapeshellarg($ziptmp),$out,$ret);
				$this->pbDump($out);
				#exec('/bin/mkdir -v '.escapeshellarg($ziptmp),$out,$ret);
				echo exec('/bin/pwd')."<br>\n";

				echo "ZIP: ".exec('which zip')."<br>\n";
				echo "ZIP: ".exec('ls -l /usr/bin/zip')."<br>\n";
				echo exec('/bin/ls -l '.escapeshellarg($ziptmp))."<br>\n";
				echo exec('/bin/ls -ld typo3temp')."<br>\n";
				echo exec('/bin/ls -ld '.escapeshellarg($ziptmp))."<br>\n";
				echo "RET: $ret<br>\n";
				if ($ret == 0 ) {
					echo "remove zip<br>";
					echo exec ('/bin/rm '.escapeshellarg($zip));
					$i=1;
					$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, 'select * from tx_gooffotoboek_basket where is_on_page='.$this->pbData['pid'].' and session_id="'.$this->pbData['basketID'].'" order by image ;');
					echo "start copy<br>";
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						$newname=preg_replace('[\/]','_',$row['image']);
						$newname=preg_replace('/^_/','',$newname);
						echo "copy $newname<br>";
						exec('/bin/cp -v '.escapeshellarg('fileadmin/fotoboek/'.$row['image']).' '.escapeshellarg($ziptmp.'/'.$newname),$out,$ret);
						$this->pbDump($out);


						exec('/bin/ls -l '.escapeshellarg('fileadmin/fotoboek/'.$row['image']).' '.escapeshellarg($ziptmp.'/'.$newname),$out,$ret);
						$this->pbDump($out);
#						exec('cp -v '.escapeshellarg('fileadmin/fotoboek/'.$row['image']).' '.escapeshellarg($ziptmp.'/'.$newname),$out,$ret );
						($ret == 0) && $content .= $row['image'].' '.$this->pi_getLL('added').' <br />';
						$i++;
					}
						echo "ls zipdir<br>";

				echo exec('/bin/ls -l '.escapeshellarg($ziptmp).' 2>&1')."<br>\n";


#					exec('cd '.escapeshellarg($ziptmp).' && /usr/bin/zip -m '.escapeshellarg($pwd.'/'.$zip).' *' ,$out,$ret);
					echo "cd and echo<br>";

					exec('cd '.escapeshellarg($ziptmp).' && echo -v -m '.escapeshellarg($pwd.'/'.$zip).' *',$out,$ret);
						$this->pbDump($out);


						echo "cd and zip<br>";

					exec('cd '.escapeshellarg($ziptmp).' && /usr/bin/zip -v -m '.escapeshellarg($pwd.'/'.$zip).' *',$out,$ret);
						$this->pbDump($out);

					if ($ret == 0 ) {
						$content .= '<a href="'.$zip.'">'.htmlspecialchars($this->pi_getLL('basketGetZip')).'</a><br />';
					} else {
						$this->pbDump($out);
					}
					exec('rmdir '.escapeshellarg($ziptmp),$out,$ret);
				} else {
					echo "Directory creation failed\n\r";
				}
			} else {
				$content = $this->pi_getLL('basketIsEmpty');
			}
			return $content;
		}
	}
	#End of class

	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/goof_fotoboek/pi1/class.tx_gooffotoboek_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/goof_fotoboek/pi1/class.tx_gooffotoboek_pi1.php']);
	}
?>
