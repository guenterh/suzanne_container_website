<?php
/*
 * Created on 30.07.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
class user_augenfaellig {

var $cObj; 
 
/*
 * Dies Funktion dient dazu, den HTML Code entsprechend den Angaben im Element inhalt_augenfaellig
 * "zusammenzusetzen".
 */
function make_augenfaellig($content,$conf)
{	




	$text_augenfaellig = $this->cObj->data['field_text_augenfaellig'];
	$name_bild = $this->cObj->data['field_name_bild'];
	$hoehe_transp = $this->cObj->data['field_hoehe_trans_gif'] ?  $this->cObj->data['field_hoehe_trans_gif'] : 125 ;
	$breite_transp = $this->cObj->data['field_breite_trans_gif'] ?  $this->cObj->data['field_breite_trans_gif'] : 1;
	$link_bild = $this->cObj->data['field_link_bild'] ? $this->cObj->data['field_link_bild'] : '#';
	
	//t3lib_div::debug($hoehe_transp . '<br>'. $breite_transp . '<br>');
	

	
	$inhalt = '<p>' . '<a class="augen" href="' . $link_bild ;
	
	if ($link_bild != '#')
	{
		$inhalt .= '" target="_blank"><img src="fileadmin/template/images/augenfaellig/' . $name_bild;
	}
	else
	{
		$inhalt .= '"> <img src="fileadmin/template/images/augenfaellig/' . $name_bild;
	}	
	$inhalt .= '" class="bildRechtsEinzeln">';
	$inhalt .= '<span><img src="fileadmin/template/images/transp.gif" width="' . $breite_transp;
	$inhalt .= '" height="' . $hoehe_transp . '">' . $text_augenfaellig . '</span></a>';
	$inhalt .= '</p><p></p>';
    
    //folgende Zeile war ursprünglich Teil der HTML-Struktur
    //soll lt. Manuela mal wegfallen
	//$inhalt .= '<img src="fileadmin/template/images/transp.gif" width="500" height="10"></p><p></p>';

	return $inhalt;
	
}
 
} 
 
?>
