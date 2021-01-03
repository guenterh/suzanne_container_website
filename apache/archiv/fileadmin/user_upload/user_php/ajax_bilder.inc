<?php
/*
 * Created on 29.10.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$globaler_counter;

 
class user_ajax_bilder {

var $cObj; 
var $lCObj = null;

/*
 * Dies Funktion dient dazu, den HTML Code entsprechend den Angaben im Element inhalt_augenfaellig
 * "zusammenzusetzen".
 */
function getBildLegendeGross($content,$conf)
{	
	

	//Dies ist nat�rlich sehr, sehr unsch�n!!
	//nur weil ich noch nicht kapiere, wie ich das Typo3 - Framework hier verwende 
	//mache ich solche Klimmz�ge

/*	
	$sql = "select tx_templavoila_flex from tt_content where uid = 110";
	$result = mysql_query($sql,$GLOBALS['TYPO3_DB']->link);
	$tx_templavoila_flex = null;
	
	if ($result)
	{
		
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$tx_templavoila_flex = $row['tx_templavoila_flex'];
		
	}
	
*/	

	//etwas weniger unsch�n, aber noch nicht der richtige Umgang mit TemplaVoila

	$conf['ce.']['source'] = $GLOBALS['_GET']['uid'];
	//print_r  ($GLOBALS['_GET']);
	$inhalt = $this->cObj->cObjGetSingle($conf['ce'],$conf['ce.']);
	$flexarray =  t3lib_div::xml2array($inhalt);
	$legende = $flexarray['data']['sDEF']['lDEF']['field_bild_gross_legende']['vDEF'];

	return $legende;
	
}

function getContentID($content,$conf)
{	
	
	
	$contentid = explode(":",($this->cObj->parentRecord['currentRecord']));
	return $contentid[1];
	
	
}


function reihenfolge($content,$conf)
{	
	
	global $globaler_counter;
	$globaler_counter++;
	
	return $globaler_counter;
	
	
}



}



?>