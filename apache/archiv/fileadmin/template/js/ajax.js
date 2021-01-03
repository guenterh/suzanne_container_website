/*

dieses script basiert auf:

Copyright: Ralph Steyer, www.rjs.de
Die Quelltexte d�rfen beliebig eingesetzt und 
ver�ndert werden, allerdings muss bei einem �ffentlichen Einsatz ein Verweis auf RJS EDV-KnowHow (http://www.rjs.de), das Portal AJAX-NET.de (http://www.ajax-net.de) oder das 
Buch "AJAX mit PHP" vom Verlag Addison-Wesley erfolgen.
*/


var resObjekt = null;
var imgGross = null;


function erzXMLHttpRequestObject(){
  var resObjekt = null;
  try {
    resObjekt = new ActiveXObject("Microsoft.XMLHTTP");
    //alert("Objekt Microsoft.XMLHTTP erstellt");

  }
  catch(Error){
    try {
		resObjekt = new ActiveXObject("MSXML2.XMLHTTP");
		//alert("Objekt MSXML2.XMLHTTP erstellt");
    }
    catch(Error){
      try {
	      resObjekt = new XMLHttpRequest();
		  //alert("Objekt XMLHttpRequest erstellt");
      }
      catch(Error){
      //alert("Erzeugung des XMLHttpRequest-Objekts ist nicht möglich");
      }
    }
  }
  return resObjekt;
}

function sndReq(contentid,bildname,position) {
	//alert("contentid: " + contentid + " bildname: " + bildname);
	//var anker = document.getElementById("c" + contentid).nextSibling;

    //alert ("contentid: " + contentid + " bildnahme: " + bildname + " position: " + position );

	bildgross = document.getElementById("idBildGross");
	bildgross.src = "http://" + location.host + "/fileadmin/template/images/bilder/" + bildname ;
	
	bildgrossRahmen = document.getElementById("rechteSpalteBildBreit");
	
	dynhoehe = 0;
	
	dynTeil = (position - 1) * 115
	
/*	
	
	switch (position)
	{
		
		case "1": 
			dynHoehe = 30;
			break;
		case "2": 
			dynHoehe = 140;
			break;
		case "3": 
			dynHoehe = 250;
			break;
		case "4": 
			dynHoehe = 360;
			break;
		case "5": 
			dynHoehe = 470;
			break;
		case "6": 
			dynHoehe = 580;
			break;
		case "7": 
			dynHoehe = 690;
			break;
		default: 
			dynHoehe = 30;
			break;	
		
	}
	
*/
	//Berechnung der Position des grossen Bildes
	document.getElementById("idBildGross").style.visibility="visible";
	dynHoehe = 30 + dynTeil;
	bildgrossRahmen.style.marginTop = dynHoehe + "px";
	location.href = "#c" + contentid; 
	
	
	//Ermitteln der Legende mittels Ajax
    resObjekt.open('get', 'index.php?id=108&uid=' + contentid ,true);
    resObjekt.onreadystatechange = handleResponse;
    	
	resObjekt.send(null);
}

function handleResponse() {
	
  if(resObjekt.readyState == 4){
  	
  	document.getElementById("legende-bild-gross").innerHTML = resObjekt.responseText;
 
  } 
  
}


resObjekt=erzXMLHttpRequestObject();


$(function() {

    $("div#c166 > div > a > p > img").click();


});


