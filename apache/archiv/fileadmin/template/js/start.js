




var oPrivat;
var oVermischt;
var oAmt;
var oNavBalken;
var oBildGross;
var oNavText;
var oObjectArray = new Array(6);
var oBildAmt;
var oBildPrivat;
var oBildVerm;
var oLogo;



function BildAmt () {

  this.aktiv = function () {
	oAmt.aktiv();
  }

  this.deaktiv = function () {
	oAmt.deaktiv();
  }


}

function BildPrivat () {


  this.aktiv = function () {
	oPrivat.aktiv();
  }

  this.deaktiv = function () {
	oPrivat.deaktiv();
  }


}

function BildVerm () {

  this.aktiv = function () {
	oVermischt.aktiv();
  }

  this.deaktiv = function () {
	oVermischt.deaktiv();
  }

}



function Logo () {

	var oBildLogo = document.getElementById("logo");
	var pfad = "fileadmin/template/images/";
	var defaultBild = "logo_120_2.gif";	

	this.setLogo = function () {
	//	oBildLogo.src = pfad + defaultBild;			
		oBildLogo.style.visibility = "visible";			
	
	}
	
	this.hideLogo = function () {
		oBildLogo.style.visibility = "hidden";			
	}

}

function BildGross () {

	var oBildGross = document.getElementById("imgbildGross");
	var pfad = "fileadmin/template/images/nav/";
	var defaultBild = "einstiegAnim.gif";	
	

	this.setBild = function (vBildName) {
		oBildGross.src = pfad + vBildName;
	}

	this.setDefault = function () {
		oBildGross.src = pfad + defaultBild;
	}


}


function NavText () {

	var oErklaerung = document.getElementById("logoErkl");
	this.setNavText = function (vNavText) {
		oErklaerung.innerHTML = vNavText;
	}

}


function NavBalken () {

	var farbeneutral = "#333333";
	
	var oBalken = document.getElementById("navbalken");
	
	this.setDefault = function () {
		oBalken.style.backgroundColor = farbeneutral;	
	}

	this.setFarbe = function (vFarbe) {
		oBalken.style.backgroundColor = vFarbe;	
	}


}


function Privat () {

  var oNavKast = document.getElementById("nav1stKastPrivat");
  var farbe =  "#990033";
  var text = "Die Fülle der Zwischenzeiten";
  var bild = "privat_100_transp2.gif";

  this.aktiv = function () {
	oNavBalken.setFarbe (farbe);
	oNavKast.style.visibility = "visible";
	oNavText.setNavText(text);
	oBildGross.setBild(bild);
  }
  
  this.deaktiv = function () {
	oNavKast.style.visibility = "hidden";
	//oNavBalken.setDefault();
	//alert ("in Privat");
  }


}



function Vermischt () {
  var farbe =  "#666666";
  var oNavKast = document.getElementById("nav1stKastVerm");
  var text = "Koordinaten für viele Lebenslagen";
  var bild = "verm_100_transp2.gif";


  this.aktiv = function () {
	oNavBalken.setFarbe (farbe);
	oNavKast.style.visibility = "visible";
	oNavText.setNavText(text);
	oBildGross.setBild(bild);
  }

  this.deaktiv = function () {
	oNavKast.style.visibility = "hidden";
	//oNavBalken.setDefault();
	//alert ("in Vermischt");
  }


}


function Amt () {
  var farbe =  "#000000";
  var oNavKast = document.getElementById("nav1stKastAmt");
  var text = "Von Werk- und Feiertagen";
  var bild = "amt_100_transp2.gif";

  this.aktiv = function () {
	oNavBalken.setFarbe (farbe);
	oNavKast.style.visibility = "visible";
	oNavText.setNavText(text);
	oBildGross.setBild(bild);
  }
  
  this.deaktiv = function () {
	oNavKast.style.visibility = "hidden";
	//oNavBalken.setDefault();
 	//alert ("in Amt");
  }

}




function startLoad()  {

	oPrivat = new Privat();
	//oVermischt = new Vermischt();
	oAmt = new Amt();
	oNavBalken = new NavBalken();
	oBildGross = new BildGross();
	oNavText = new NavText();
	oBildAmt = new BildAmt();
	oBildPrivat = new BildPrivat();
	//oBildVerm = new BildVerm();
	oLogo = new Logo();

	oObjectArray["nav1stKastAmt"] = oAmt;
	oObjectArray["nav1stKastPrivat"] = oPrivat;
	//oObjectArray["nav1stKastVerm"] = oVermischt;
	oObjectArray["imgTalarKlein"] = oBildAmt;
	oObjectArray["imgPrivatKlein"] = oBildPrivat;
	//oObjectArray["imgVermischtKlein"] = oBildVerm;
	
	
	oAmt.deaktiv();
	//oVermischt.deaktiv();
	oPrivat.deaktiv();
	oNavText.setNavText("&nbsp;");
}


function divshidden()
{

	oObjectArray["nav1stKastAmt"].deaktiv();
	oObjectArray["nav1stKastPrivat"].deaktiv();
//	oObjectArray["nav1stKastVerm"].deaktiv();
	oObjectArray["imgTalarKlein"].deaktiv();
	oObjectArray["imgPrivatKlein"].deaktiv();
	//oObjectArray["imgVermischtKlein"].deaktiv();
	oNavBalken.setDefault();
	oNavText.setNavText("&nbsp;");
//	oNavText.setNavText(" ");
	oBildGross.setDefault();
	oLogo.setLogo();
	
}


function overImgDiv (vName)
{
	//alert ("in Div");
	//alert (vName.id);
	oObjectArray[vName.id].aktiv();
	oLogo.hideLogo();
	

}
