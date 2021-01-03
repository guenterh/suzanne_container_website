


	    var oDivNav1stAmt ;
	    var oDivNav1stPrivat ;
	    var oDivNav1stVerm ;
		
		
		
		
		function sideload()
		{
//			alert ("in side load");

			var oBildGross =  document.getElementById("idBildGross");
			if (undefined != oBildGross)
			{
				
				//oBildGross.style.visibility="hidden";
			}



			oDivNav1stAmt = document.getElementById("nav1stKastAmt");
			oDivNav1stPrivat = document.getElementById("nav1stKastPrivat");
			//oDivNav1stVerm = document.getElementById("nav1stKastVerm");

			
			oDivNav1stAmt.style.visibility = "hidden";
			oDivNav1stPrivat.style.visibility = "hidden";
			//oDivNav1stVerm.style.visibility = "hidden";


			show_menu_images();

		}
	
	
 
 
		function overImgDiv(theObject)	{	//

//			str = "overImgDiv: " + theObject.id; 
//			alert (str);
			
			switch (theObject.id)
			{
			
				case "imgTalarKlein" : 
				case "nav1stKastAmt":
				case "nav1stAmt":
					oDivNav1stAmt.style.visibility = "visible";
					oDivNav1stPrivat.style.visibility = "hidden";
					//oDivNav1stVerm.style.visibility = "hidden";
					break;
				case "imgPrivatKlein" :
				case "nav1stKastPrivat":
					oDivNav1stAmt.style.visibility = "hidden";
					oDivNav1stPrivat.style.visibility = "visible";
					//oDivNav1stVerm.style.visibility = "hidden";
					break;
				 break;
				case "imgVermischtKlein" :
				case "nav1stKastVerm": 
					oDivNav1stAmt.style.visibility = "hidden";
					oDivNav1stPrivat.style.visibility = "hidden";
					//oDivNav1stVerm.style.visibility = "visible";
					break;
			}

		}



		
		function divshidden()
		{
			//alert ("hallo");
			oDivNav1stAmt.style.visibility = "hidden";
			oDivNav1stPrivat.style.visibility = "hidden";
			//oDivNav1stVerm.style.visibility = "hidden";
		}
		

//wird im Moment nicht mehr ben�tigt
//Klassen f�r den Stil Current der 2nd Navigation wird jetzt �ber Typo3 gesetzt
		function act_navelement_2nd()
		{
			actnavelement = undefined;
			o2ndLevelNav = document.getElementById("Navigation2nd");
			if (undefined != o2ndLevelNav)
			{
				for (i = 0; i < o2ndLevelNav.childNodes.length; i++)
				{
					if (undefined != o2ndLevelNav.childNodes[i].getAttribute("class"))
					{
						if (o2ndLevelNav.childNodes[i].getAttribute("class") == "nav2ndact")
						{
							actnavelement = o2ndLevelNav.childNodes[i];
							break;
						}
					}
				}
			}
			
			return actnavelement;
		}


		function print_content_window(url)
		{
			var handle = window.open(url,null,"width=900,height=600,left=100,top=200,scrollbars=yes,resizable=yes,menubar=yes");
			handle.focus();
		}


		function print_content(url)
		{
			window.print();
		}

