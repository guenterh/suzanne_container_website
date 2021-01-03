<?php


class user_menu_links_variables {
	
	static  $family_jahres_uids = array();
	
}




class user_menu_links {



var $cObj; 
var $lCObj;



function search_menu($content,$conf)
{

	for ($i = 0; $i< count($content);$i++)
	{
		user_menu_links_variables::$family_jahres_uids[$content[$i]['uid']] = $content[$i]['title'];
	}

}



function family_menu($content,$conf)
{	

	$new_menu_array = array();
	for ($i = 0; $i< count($content);$i++)
	{
		if ($content[$i]['uid'] == 9)
		{

			$this->lCObj = t3lib_div::makeInstance('tslib_cObj');
			$id = $conf['parentObj']->conf['specialMenu.']['id'];
			
			//die Funktion gibt ein leeres array zurück, da ich nur an den IDs der untergeordneten Seiten interessiert bin (kein return in $this->search_menu)
			$added_menu = $this->lCObj->cObjGetSingle($conf['parentObj']->conf['specialMenu.']['config'],$conf['parentObj']->conf['specialMenu.']['config.']);
			
			$keys = array_keys(user_menu_links_variables::$family_jahres_uids);
			
			for ($j = 0; $j < count($keys); $j++)
			{
				
				$new_item = $content[$i];
				$new_item['uid'] = $keys[$j];
				$new_item['title'] = user_menu_links_variables::$family_jahres_uids[$keys[$j]];
				$new_menu_array [] = $new_item;
			}
			
		}
		else 
		{
			$new_menu_array [] = $content[$i];
		}
	}
	
	return $new_menu_array;

}




}

?>