<?php
/**
 * Media Management
 *
 * @license     GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author      Bob Baddeley <bob.baddeley@pnl.gov>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC."inc/html.php");
require_once(DOKU_INC."inc/search.php");
require_once(DOKU_INC.'inc/auth.php');


/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_media extends DokuWiki_Syntax_Plugin {

    function syntax_plugin_media(){
    }
    /**
     * return some info
     */
    function getInfo(){
        return array(
            'author' => 'Bob Baddeley',
            'email'  => 'bob.baddeley@pnl.gov',
            'date'   => '2006-06-26',
            'name'   => 'Media Plugin',
            'desc'   => 'Allows users to view and modify media folders',
            'url'    => 'http://bobbaddeley.com/doku.php/projects/programming/wiki/media'
        );
    }

    /**
     * Some Information first.
     */
    function getType() { return 'substition'; }
    function getPType() { return 'block'; }
    function getSort() { return 125; }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{media}}',$mode,'plugin_media');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler){
        $match = substr($match,5,0);
        return array($match);
    }

    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        global $ID;
		global $AUTH;
		if ($_REQUEST['LOCK']<>""){
			$this->lockfile($_REQUEST['LOCK']);
		}
		else if ($_REQUEST['UNLOCK']<>""){
			$this->unlockfile($_REQUEST['UNLOCK']);
		}
		else if ($_REQUEST['DELETE']<>""){
			$this->media_delete($_REQUEST['DELETE']);
		}  //handle upload
		else if ($_REQUEST['DELNS']<>""){
			$this->media_delete_ns($_REQUEST['DELNS']);
		}  //handle upload
		else if ($_REQUEST['MKNS']<>""){
			$this->media_mk_ns($_REQUEST['ns'],$_REQUEST['MKNS'],$AUTH);
		}
		else if($_FILES['upload']['tmp_name']){
			$this->media_upload($_REQUEST['ns'],$AUTH);
		}

        if($mode == 'xhtml'){
            $renderer->info['cache'] = false;
			//_show_namespaces
			$renderer->doc .= "<table><tr><td>";
			$renderer->doc .= $this->_show_namespaces();
			$renderer->doc .= "</td><td>";
			//_show_media
			$renderer->doc .= $this->_show_media();
			$renderer->doc .= "</td></tr></table>";
			$renderer->doc .= "<hr class='mediahr'>";
			if(auth_quickaclcheck($ID) >= AUTH_UPLOAD){
				$renderer->doc .= $this->_show_mk_namespaces();
				$renderer->doc .= $this->_show_upload_form();
				$renderer->doc .= $this->_show_delete_namespace_form();
				$renderer->doc .= $this->_show_delete_file_form();
        	}
			return true;
        }
		return false;
    }

    function _show_namespaces($depth=0){
    	$dir = utf8_encodeFN(str_replace(':','/',$_REQUEST['ns']));
	    global $conf;
		$data = array();
		search($data,$conf['mediadir'],'search_namespaces',array());
		return $this->html_buildlist($data,'idx',media_html_list_namespaces,$dir);
    }

function html_buildlist($data,$class,$func,$curdir,$lifunc='html_li_default'){
        global $ID;
  $level = 0;
  $opens = 0;
  $ret   = '<div class="mediafolders">';
  $ret .= "<h2>".$this->getLang("folders").":</h2>";
  $ret .= '<ul><a href="'.script().'?id='.$ID.'&ns=" class="idx_dir">Home</a>';

  foreach ($data as $item){
  //print $curdir."<>".utf8_encodeFN(str_replace(':','/',$item['id']))."<br>";

    if( $item['level'] > $level ){
      //open new list
      for($i=0; $i<($item['level'] - $level); $i++){
        if ($i) $ret .= "<li class=\"clear\">\n";
        $ret .= "\n<ul class=\"$class\">\n";
      }
    }elseif( $item['level'] < $level ){
      //close last item
      $ret .= "</li>\n";
      for ($i=0; $i<($level - $item['level']); $i++){
        //close higher lists
        $ret .= "</ul>\n</li>\n";
      }
    }else{
      //close last item
      $ret .= "</li>\n";
    }

    //remember current level
    $level = $item['level'];

    //print item
    $ret .= $lifunc($item); //user function
    if ($curdir==utf8_encodeFN(str_replace(':','/',$item['id'])))
    	$ret .= '<div class="li selectedns">';
    else
    	$ret .= '<div class="li">';
    $ret .= $this->$func($item); //user function
    $ret .= '</div>';
  }

  //close remaining items and lists
  for ($i=0; $i < $level; $i++){
    $ret .= "</li></ul>\n";
  }

  $ret .= "</ul>";
  $ret .= "</div>";
  return $ret;
}

function _show_mk_namespaces(){
  global $ID;
  global $lang;
  $out .= '<div class="mediacreatedir">';
  $out .= '<fieldset><legend>'.$this->getLang("create_folder").':</legend><form action="'.script().'" id="'.$ID.'"'.' method="post">';
  $out .= '<input type="hidden" name="ns" value="'.htmlspecialchars($_REQUEST['ns']).'" />';
  $out .= '<input type="hidden" name="id" value="'.$ID.'" />';
  $out .= '<input type="text" name="MKNS" class="edit" />';
  $out .= '<input type="submit" class="button" value="'.$lang['btn_upload'].'" accesskey="s" />';
  $out .= '</form></fieldset>';
  $out .= '</div>';
  return $out;
}

/**
 * Userfunction for html_buildlist
 *
 * Prints available media namespaces
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function media_html_list_namespaces($item){
  global $ID;
  global $lang;
  $ret  = '';
  $ret .= '<a href="'.script().'?id='.$ID.'&ns='.idfilter($item['id']).'" class="idx_dir">';
  $pos = strrpos($item['id'], ':');
  $ret .= substr($item['id'], $pos > 0 ? $pos + 1 : 0);
  $ret .= '</a>';
  return $ret;
}

function _show_upload_form(){
  global $ID;
  global $lang;
  $out .= '<div class="mediaupload">';
  $out .= '<fieldset><legend>'.$this->getLang("upload_file").':</legend><form action="'.script().'" id="'.$ID.'"'.' method="post" enctype="multipart/form-data">';
  $out .= $lang['txt_upload'].':<br />';
  $out .= '<input type="file" name="upload" class="file" onchange="suggestWikiname();" />';
  $out .= '<input type="hidden" name="ns" value="'.htmlspecialchars($_REQUEST['ns']).'" /><br />';
  $out .= '<input type="hidden" name="id" value="'.$ID.'" />';
  $out .= $lang['txt_filename'].'<br />';
  $out .= '<input type="text" name="fileid" class="edit" />';
  $out .= '<input type="submit" class="button" value="'.$lang['btn_upload'].'" accesskey="s" />';
  if(auth_quickaclcheck(idfilter($item['id'])) >= AUTH_DELETE){
    $out .= '<br /><label for="ow"><input type="checkbox" name="ow" value="1" id="ow" />'.$lang['txt_overwrt'].'</label>';
  }
  $out .= '</form></fieldset>';
  $out .= '</div>';
  return $out;
}

function _show_delete_namespace_form(){
  global $ID;
  global $lang;
  global $conf;
  $out .= '<div class="mediadelnamespace">';
  $out .= '<fieldset><legend>'.$this->getLang("delete_folder").':</legend><form action="'.script().'" id="'.$ID.'"'.' method="post" enctype="multipart/form-data">';
  $out .= $this->getLang("select_folder").':<br />';
  $out .= '<input type="hidden" name="ns" value="'.htmlspecialchars($_REQUEST['ns']).'" /><br />';
  $out .= '<input type="hidden" name="id" value="'.$ID.'" />';
  $out .= '<select name="DELNS">';
  		$data = array();
		search($data,$conf['mediadir'],'search_namespaces',array());
		  foreach ($data as $item){
		  $out .= '<option value="'.idfilter($item['id']).'">'.utf8_encodeFN(str_replace(':','/',$item['id']))."</option>";
		}
  $out .= '</select>';
  $out .= '<input type="submit" class="button" value="'.$lang['btn_delete'].'" accesskey="s" />';
  $out .= '</form></fieldset>';
  $out .= '</div>';
  return $out;
}

function _show_delete_file_form(){
  global $ID;
  global $lang;
  global $conf;
  $out .= '<div class="mediadelfile">';
  $out .= '<fieldset><legend>'.$this->getLang("delete_file").':</legend><form action="'.script().'" id="'.$ID.'"'.' method="post" enctype="multipart/form-data">';
  $out .= $this->getLang("select_file").':<br />';
  $out .= '<input type="hidden" name="ns" value="'.htmlspecialchars($_REQUEST['ns']).'" /><br />';
  $out .= '<input type="hidden" name="id" value="'.$ID.'" />';
  $out .= '<select name="DELETE">';
   		$data = array();
		$dir = utf8_encodeFN(str_replace(':','/',$_REQUEST['ns']));
		search($data,$conf['mediadir'],'search_media',array(),$dir);
		foreach($data as $item){
			$fn   = mediaFN($_REQUEST['ns'].':'.$item['file']);
			$lockfn   = $fn.'.lock';
			if (!file_exists($lockfn)){
				$out .= '<option value="'.idfilter($item['id']).'">'.utf8_encodeFN(str_replace(':','/',$item['id']))."</option>";
			}
		}
  $out .= '</select>';
  $out .= '<input type="submit" class="button" value="'.$lang['btn_delete'].'" accesskey="s" />';
  $out .= '</form></fieldset>';
  $out .= '</div>';
  return $out;
}

/**
 * Print a list of mediafiles in the current namespace
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function _show_media(){
  global $conf;
  global $lang;
  global $AUTH;
  global $ID;
  $out   = '<div class="mediafiles">';
  $out .= "<h2>".$this->getLang("files").":</h2>";
  $dir = utf8_encodeFN(str_replace(':','/',$_REQUEST['ns']));

  $data = array();
  search($data,$conf['mediadir'],'search_media',array(),$dir);

  $out .= '<ul>';
  foreach($data as $item){
    if(!$item['isimg']){
      // add file icons
      list($ext,$mime) = mimetype($item['file']);
      $class = preg_replace('/[^_\-a-z0-9]+/i','_',$ext);
      $class .= ' class="mediafile mf_'.$class.'"';
    }

    $out .= '<li><div class="li"><a href="'.ml($_REQUEST['ns'].':'.$item['file']).'">';
    $out .= utf8_decodeFN($item['file']);
	$out .= '</a>';
	$out .= ' ('.filesize_h($item['size']).')';
    $fn   = mediaFN($_REQUEST['ns'].':'.$item['file']);
    $out .= ' '.$this->getLang("modified").' '. date ("F d Y H:i:s.", filemtime($fn));
    $lockfn   = $fn.'.lock';
    if (file_exists($lockfn)){
    	$out .= ' '.$this->getLang("locked_by").' '.file_get_contents($lockfn).' '.$this->getLang("on").' '. date ("F d Y H:i:s.", filemtime($lockfn)).' <a href="'.script().'?ns='.$_REQUEST['ns'].'&id='.$ID.'&UNLOCK='.($_REQUEST['ns'].':'.$item['file']).'">'.$this->getLang("unlock").'</a>';
    }
    else{
		if(auth_quickaclcheck(idfilter($item['file'])) >= AUTH_EDIT){
    	  $out .= ' <a href="'.script().'?ns='.$_REQUEST['ns'].'&id='.$ID.'&LOCK='.($_REQUEST['ns'].':'.$item['file']).'">'.$this->getLang("lock").'</a>  ';
        }
    }
    $out .= '</div></li>';
  }
  $out .= '</ul>';
  $out .= '</div>';
  return $out;
}


/**
 * Locks a file for editing
 */
function lockfile($id){
  $lock = $id.'.lock';
  $lock = mediaFN($lock);
  if($_SERVER['REMOTE_USER']){
    io_saveFile($lock,$_SERVER['REMOTE_USER']);
  }else{
    io_saveFile($lock,clientIP());
  }
}

/**
 * Unlocks a file if it was locked by the user
 */
function unlockfile($id){
  $lock = $id.'.lock';
  $lock = mediaFN($lock);
  if(@file_exists($lock)){
    $ip = io_readFile($lock);
    if( ($ip == clientIP()) || ($ip == $_SERVER['REMOTE_USER']) ){
      @unlink($lock);
    }
	else{
		msg("Only the person who locked the file can unlock it");
	}
  }
}
/**
 * Unlocks a file if it was locked by the user
 */
 function media_delete($delid){
  global $lang;
  $lock = $id.'.lock';
  $lock = mediaFN(cleanID($_REQUEST['ns'].':'.$lock));
  if(@file_exists($lock)){
  msg(str_replace('%s',$file,$lang['deletefail']),-1);
	return false;
  }
  $file = mediaFN($delid);
  if(@unlink($file)){
    msg(str_replace('%s',noNS($delid),$lang['deletesucc']),1);
    return true;
  }
  //something went wrong
  msg(str_replace('%s',$file,$lang['deletefail']),-1);
  return false;
}

function media_delete_ns($delid){
  global $lang;
  $lock = $id.'.lock';
  $lock = mediaFN(cleanID($_REQUEST['ns'].':'.$lock));
  if(@file_exists($lock)){
  msg(str_replace('%s',$file,$lang['deletefail']),-1);
	return false;
  }
  $file = mediaFN($delid);
  if(@rmdir($file)){
    msg(str_replace('%s',noNS($delid),$lang['deletesucc']),1);
    return true;
  }
  //something went wrong
  msg(str_replace('%s',$file,$lang['deletefail']),-1);
  return false;
}

function media_mk_ns($NS,$NEWNS,$AUTH){
  $file = mediaFN($NS.":".$NEWNS);
  if(@mkdir($file)){
    msg(str_replace('%s',noNS($NEWNS),"Successfully Created"),1);
    return true;
  }
  //something went wrong
  msg(str_replace('%s',$file,"Creation Failed"),-1);
  return false;
}

function media_upload($NS,$AUTH){
  require_once(DOKU_INC.'inc/confutils.php');
  global $lang;
  global $conf;

  // get file
  $id   = $_POST['fileid'];
  $file = $_FILES['upload'];
  // get id
  if(empty($id)) $id = $file['name'];
  $id   = cleanID($NS.':'.$id);
  // get filename
  $fn   = mediaFN($id);

  // get filetype regexp
  $types = array_keys(getMimeTypes());
  $types = array_map(create_function('$q','return preg_quote($q,"/");'),$types);
  $regex = join('|',$types);

  // we set the umask here but this doesn't really help
  // because a temp file was created already
  umask($conf['umask']);
  if(preg_match('/\.('.$regex.')$/i',$fn)){
    //check for overwrite
    if(@file_exists($fn.'.lock')){
      msg("Cannot overwrite a locked file",-1);
      return false;
    }
    // prepare directory
    io_makeFileDir($fn);
    if(move_uploaded_file($file['tmp_name'], $fn)) {
      // set the correct permission here
      chmod($fn, 0777 - $conf['umask']);
      msg($lang['uploadsucc'],1);
      return true;
    }else{
      msg($lang['uploadfail'],-1);
    }
  }else{
    msg($lang['uploadwrong'],-1);
  }
  return false;
}

}
//Setup Vim: tabstop=4 enc=utf8
?>
