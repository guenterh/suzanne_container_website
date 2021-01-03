<?php

class user_nicht_verpassen {

var $cObj;
	
function exec_makelink($content,$conf)
{	
	

	//In $content wird ein Array nach folgendem Beispiel übergeben:
	/*
	Array
	(
    [aTagParams] =>  
    [url] => index.php?id=16
    [targetParams] => 
    [TYPE] => page
    [TAG] => <a href="index.php?id=16" target="FEopenLink" onclick="vHWin=window.open('index.php?id=16','FEopenLink','width=400,height=600');vHWin.focus();return false;" >
	)
	
	$this->cObj->data hat folgenden Inhalt
	Array
	(
	    [field_link_url] => 16 400x600
	    [field_link_title] => Link titel Nr 16
	    [currentValue_kidjls9dksoje] => 16 400x600
	)
	<b>|1|</b>  kommt aus current = 1
	Array  
	(
	    [field_link_url] => 45 400x600
	    [field_link_title] => Link titel Nr 45
	    [currentValue_kidjls9dksoje] => 45 400x600
	)	
	
	*/
	

	//t3lib_div::debug(print_r($content));
	//t3lib_div::debug(print_r($conf));
	//t3lib_div::debug(print_r($this->cObj->data));
	//t3lib_div::debug(print_r($this->cObj->parameters));

	//Da field_link_url als Attribut Typ mit dem Attribut href gemappt wird,
	//muss ich den eigentlichen Tag aus der Rückgabe entfernen
	//im Moment habe ich keine bessere Lösung
	//könnte man z.B. nur den Wert für das Attribut href ohne tag irgendwoher abfragen??
	
	//$test = substr(substr($content['TAG'],8),0,(strlen(substr($content['TAG'],8))-1));
	//t3lib_div::debug(print_r($this->cObj->data);
	return $content['TAG'];
	
}
	
}

?>