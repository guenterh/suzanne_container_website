<?php

/**
 * Info Indexmenu: Displays the index of a specified namespace. 
 *
 * Version: 2.4 
 * last modified: 2006-07-10 11:19:22
 * @license     GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author      Samuele Tognini <samuele@cli.di.unipi.it>
 * 
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('INDEXMENU_IMAGES')) define('INDEXMENU_IMAGES',DOKU_BASE.'lib/plugins/indexmenu/images/');
if(!defined('INDEXMENU_FS_IMAGES')) define('INDEXMENU_FS_IMAGES',realpath(dirname(__FILE__)."/images")."/");
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/search.php');
 
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_indexmenu extends DokuWiki_Syntax_Plugin {
  
  /**
   * return some info
   */
  function getInfo(){
    return array(
		 'author' => 'Samuele Tognini',
		 'email'  => 'samuele@cli.di.unipi.it',
		 'date'   => '2006-07-10',
		 'name'   => 'Indexmenu',
		 'desc'   => 'Insert the index of a specified namespace.
Javascript code: Dtree by Geir Landro.',
		 'url'    => 'http://wiki.splitbrain.org/plugin:indexmenu'
		 );
  }
  
  /**
   * What kind of syntax are we?
   */
  function getType(){
    return 'substition';
  }
 
  /**
   * Where to sort in?
   */
  function getSort(){
    return 138;
  }
 
  /**
   * Connect pattern to lexer
   */
  function connectTo($mode) {
    $this->Lexer->addSpecialPattern('{{indexmenu>.+?}}',$mode,'plugin_indexmenu');
  }
      
  /**
   * Handle the match
   */
  function handle($match, $state, $pos, &$handler){
    $theme="default/";
    $level = 0;
    $nons = true;
    $match = substr($match,12,-2);
    //split namespace,level,theme
    $match = preg_split('/\|/u', $match, 2);
    //split namespace
   if ( preg_match('/(.*)#(\S*)/u',$match[0],$ns_opt)) {
     $ns=$ns_opt[1];
     if (is_numeric($ns_opt[2])) $level=$ns_opt[2];
   }else{
     $ns=$match[0];
   }
    $opts=preg_split('/ /u',$match[1]);
    //plugin options
    $nons = in_array('nons',$opts);
    //javascript option
    $js = in_array('js',$opts);
    if (!$js) {
      //split theme
      if (preg_match('/js#(\S*)/u',$match[1],$tmp_theme) >0) {
	if (is_dir(INDEXMENU_FS_IMAGES.$tmp_theme[1])) {
	  $theme=$tmp_theme[1]."/";
	}
	$js=true;
      } 
    }
    return array($ns,$js,$theme,array('level' => $level,
			   'nons' => $nons
			   )
		 );
  }  
 
  /**
   * Render output
   */
  function render($mode, &$renderer, $data) {
    global $conf;
    if($mode == 'xhtml'){ 
      $n=$this->_indexmenu($data,$renderer);
      if ((!@$n) && isset ($conf['plugin_indexmenu']['empty_msg'])) {
	$n = $conf['plugin_indexmenu']['empty_msg'];
	$n= str_replace('{{ns}}',cleanID($data[0]),$n);
      }
      $renderer->doc .= $n ;
      return true;
    }
    return false;
  }
 
  /**
   * Return the index 
   * @author Samuele Tognini <samuele@cli.di.unipi.it>
   *
   * This function is a simple hack of Dokuwiki html_index($ns)
   * @author Andreas Gohr <andi@splitbrain.org>
   */
  function _indexmenu($myns,&$renderer) {
    global $conf;
    global $ID;
 
    $ns = $myns[0];  
    $js = $myns[1];                                              
    $theme= $myns[2];
    $opts = $myns[3];
    $level=0;
    if ($js) {
      $level=$opts['level'];
      if (!$opts['nons']) $opts['level']=0;
    }

    if($ns == '.') {
      $ns = dirname(str_replace(':','/',$ID));
      if ($ns == '.') $ns = '';
    } else {
      $ns = cleanID($ns);
    }

    $ns  = utf8_encodeFN(str_replace(':','/',$ns));
    $data = array();
    search($data,$conf['datadir'],'indexmenu_search_index',$opts,"/".$ns);

    if ($js) {
      //javascript index
      return $this->_jstree($data,$ns,$level,$theme,$renderer);
    } else {
      //standard dokuwiki index
      return html_buildlist($data,'idx',($conf['plugin_indexmenu']['headpage'])? "indexmenu_html_list_index":"html_list_index","html_li_index");
    }
  }

/**
 * Build the browsable index of pages using javascript
 *
 *
 * @author  Samuele Tognini <samuele@cli.di.unipi.it>
 */
  function _jstree($data,$ns,$level,$theme,&$renderer) {
    global $conf;
    $headpage=$conf['plugin_indexmenu']['headpage'];
    $nslink='';
    $nstitle=false;
    if (empty($data)) return false;
    //root index
    if (empty($ns)) {
      $ns='..';
      $nspage=$conf['start'];
      $nstitle=$conf['title'];
    } else {
      $ns  = str_replace('/',':',$ns);
      $nspage=$ns.":".noNS($ns);
      $nstitle=noNS($ns);
    }
    if ($headpage && @file_exists(wikiFN($nspage)) && auth_quickaclcheck($nspage) >= AUTH_READ) {
      $nslink=wl($nspage);
      $nstitle_tmp=addslashes(p_get_first_heading($nspage));
      if ($nstitle_tmp) $nstitle=$nstitle_tmp;
    } 
    $extra="";
    $js_name="indexmenu_".uniqid(rand());
    $out = "<div class='dtree'>\n";
    $out .= "<script type='text/javascript'>\n";
    //Copyright here
    $out .="
/*--------------------------------------------------|
| dTree 2.05 | www.destroydrop.com/javascripts/tree/|
|---------------------------------------------------|
| Copyright (c) 2002-2003 Geir Landro               |
|                                                   |
| This script can be used freely as long as all     |
| copyright messages are intact.                    |
|                                                   |
| Updated: 17.04.2003                               |
|--------------------------------------------------*/
\n";
    $out .= $js_name." = new dTree('".$js_name."','".INDEXMENU_IMAGES.$theme."');\n";
    $out .= $js_name.".add(0,-1,'$nstitle','$nslink')\n";
    $q=array('0');
    foreach ($data as $i=>$item){
      $i++;
      $title="";
      $dirlink=false;
      //directory check for indexmenu headpage configuration
      $nspage=$item['id'].":".noNS($item['id']);
      if ($item['type']=='d' && $headpage && @file_exists(wikiFN($nspage)) && auth_quickaclcheck($nspage) >= AUTH_READ) {
	$title=addslashes(p_get_first_heading($nspage));
	$dirlink=wl($nspage);
      }
      //dokuwiki get title
      if (empty($title)) {
	if ($item['type']=='d') {
	  $title=noNS($item['id']);
	} else {
	  $title=$renderer->_getLinkTitle(NULL, $renderer->_simpleTitle($item['id']),$isImage,$item['id']);
	}
      }
      //remove highest level items
      while ($item['level'] <= $data[end($q)-1]['level']) {
	array_pop($q);  
      }

      if ($item['level']==1) {
	//father node
	$father='0';
      } else {
	$father=end($q);
      }
      //add node
      $out .= $js_name.".add($i,".$father.",'".$title."'";
      if ($item['type']=='f') $out .= ",'".wl($item['id'])."'";
      //namespace with indexmenu headpage configuration
      if ($dirlink) $out .= ",'".$dirlink."'";
      $out .= ");\n";
      if ($item['type']=='d') {
	//item il last position
	array_push($q,$i);
	//open level
	if ($item['level']<$level) $extra .= $js_name.".openTo(".$i.",false);\n";
      }
    }
    $out .= "document.write(".$js_name.");\n";
    //level 1 closes the tree
    if ($level==1) $extra =$js_name.".closeAll();\n";
    //no opened levels
    if (empty($extra)) $extra=$js_name.".openAll();\n";
    $out .= $extra;
    $out .= "</script>\n";
    $out .= "</div>\n";
    return $out;
  }


} //Indexmenu class end  

/**
 * Build the browsable index of pages
 *
 * $opts['ns'] is the current namespace
 *
 * @author  Andreas Gohr <andi@splitbrain.org>
 * modified by Samuele Tognini <samuele@cli.di.unipi.it>
 */
function indexmenu_search_index(&$data,$base,$file,$type,$lvl,$opts){
  global $conf;
  $return = true;
 
  $item = array();
 
  if($type == 'd'){
    if ($opts['level'] == $lvl) $return=false;
    if ($opts['nons']) return $return;
  }elseif($type == 'f' && !preg_match('#\.txt$#',$file)){
    //don't add
    return false;
  }
 
  $id = pathID($file);
 
  //check hiddens
  if($type=='f' && isHiddenPage($id)){
    return false;
  }
 
  //check ACL (for namespaces too)
  if(auth_quickaclcheck($id) < AUTH_READ){
    return false;
  }

  //check if it's a headpage (acrobatic check)
  if(!$opts['nons'] && $type=='f' && $conf['plugin_indexmenu']['headpage'] && $conf['plugin_indexmenu']['hide_headpage']) {
    if (noNS(getNS($id))==noNS($id) || $id==$conf['start']){
      return false;
    }
  }
 
  //Set all pages at first level
  if ($opts['nons']) {
    $lvl=1;    
  }

  $data[]=array( 'id'    => $id,
		 'type'  => $type,
		 'level' => $lvl,
		 'open'  => $return );
  return $return;
}  


/**
 * Index item formatter
 *
 * User function for html_buildlist()
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * modified by Samuele Tognini <samuele@cli.di.unipi.it>
 */
function indexmenu_html_list_index($item){
  global $conf;
  $ret = '';
  $link='';
  $base = ':'.$item['id'];
  $base = substr($base,strrpos($base,':')+1);
  $nspage=$item['id'].":".noNS($item['id']);
  //namespace
  if($item['type']=='d'){
    //headpage exists
    if (@file_exists(wikiFN($nspage)) && auth_quickaclcheck($nspage) >= AUTH_READ) {
      //headpage heading title
      $title=p_get_first_heading($nspage);
      if ($title) $base=$title;
      //link to headpage
      $ret .= html_wikilink(':'.$nspage,$base);
    } else {
      //namespace index link
      $ret .= '<a href="'.wl($ID,'idx='.$item['id']).'" class="idx_dir">';
      $ret .= $base;
      $ret .= '</a>';
    }
  }else{
    //page link
    $ret .= html_wikilink(':'.$item['id']);
  }
  return $ret;
}
 
//Setup VIM: ex: et ts=4 enc=utf-8 :
?>
