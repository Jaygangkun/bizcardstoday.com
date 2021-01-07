<?php
	session_start();
	$width=1053;
	$height=600;
//	$background = "image/template/" . str_replace("_",  " ", $HTTP_GET_VARS["1"]);
	$background = "images/template/" . str_replace("_",  " ", $templatefile);
	$image=imagecreatefrompng($background);

	//$image = imagecreate($width, $height);
	$cardnamecolor = imagecolorallocate($image,$namecolor[0], $namecolor[1], $namecolor[2]);
//	$cardtollcolor = imagecolorallocate($image,0, 0, 0);
	$cardtitlecolor=imagecolorallocate($image, $titlecolor[0], $titlecolor[1], $titlecolor[2]);
	$cardphonecolor=imagecolorallocate($image, $phonecolor[0], $phonecolor[1], $phonecolor[2]);
	$cardtollcolor=imagecolorallocate($image, $tollcolor[0], $tollcolor[1], $tollcolor[2]);
	$cardcellcolor=imagecolorallocate($image, $cellcolor[0], $cellcolor[1], $cellcolor[2]);
	$cardfaxcolor=imagecolorallocate($image, $faxcolor[0], $faxcolor[1], $faxcolor[2]);
	$cardemailcolor=imagecolorallocate($image, $emailcolor[0], $emailcolor[1], $emailcolor[2]);
	$cardaddresscolor=imagecolorallocate($image, $addresscolor[0], $addresscolor[1], $addresscolor[2]);
	$cardcitycolor=imagecolorallocate($image, $citycolor[0], $citycolor[1], $citycolor[2]);
	$cardheadercolor=imagecolorallocate($image, $headercolor[0], $headercolor[1], $headercolor[2]);
	$black = imagecolorallocate($image,0,0,0);
	
	switch(strtoupper($namealign))
	{
		case "L":
			imagettftext($image, $namesize*1.5, 0, $namecoord[0], $namecoord[1], $cardnamecolor, "fonts/" . $namefont, str_replace("_",  " ",$HTTP_GET_VARS['name']));
			break;
		case "C":
			$box = imagettfbbox ($namesize*1.5, 0, "fonts/" . $namefont, str_replace("_",  " ",$HTTP_GET_VARS['name'])); 
			$tw=$box[2]-$box[0]; //image width 
			// x position 
			$px = (imagesx($image) -$tw)/2; 
			imagettftext($image, $namesize*1.5, 0, $px, $namecoord[1], $cardnamecolor, "fonts/" . $namefont, str_replace("_",  " ",$HTTP_GET_VARS['name']));
			break;
		case "R":
			imagettftext($image, $namesize*1.5, 0, ($namecoord[0] - $namesize * strlen(str_replace("_",  " ",$HTTP_GET_VARS['name']))), $namecoord[1], $cardnamecolor, "fonts/" . $namefont, str_replace("_",  " ",$HTTP_GET_VARS['name']));
			break;
		default:
			imagettftext($image, $namesize*1.5, 0, $namecoord[0], $namecoord[1], $cardnamecolor, "fonts/" . $namefont, str_replace("_",  " ",$HTTP_GET_VARS['name']));
	}
	
	switch(strtoupper($titlealign))
	{
		case "L":
			imagettftext($image, $titlesize*1.5, 0, $titlecoord[0], $titlecoord[1], $cardtitlecolor, "fonts/" . $titlefont, str_replace("_",  " ",$HTTP_GET_VARS['title']));
			break;
		case "C":
			$box = imagettfbbox ($titlesize*1.5, 0, "fonts/" . $titlefont, str_replace("_",  " ",$HTTP_GET_VARS['title'])); 
			$tw=$box[2]-$box[0]; //image width 
			// x position 
			$px = (imagesx($image) -$tw)/2; 
			imagettftext($image, $titlesize*1.5, 0, $px, $titlecoord[1], $cardtitlecolor, "fonts/" . $titlefont, str_replace("_",  " ",$HTTP_GET_VARS['title']));
			break;
		case "R":
			imagettftext($image, $titlesize*1.5, 0, ($titlecoord[0] - $titlesize * strlen(str_replace("_",  " ",$HTTP_GET_VARS['title']))), $titlecoord[1], $cardtitlecolor, "fonts/" . $titlefont, str_replace("_",  " ",$HTTP_GET_VARS['title']));
			break;
		default:
			imagettftext($image, $titlesize*1.5, 0, $titlecoord[0], $titlecoord[1], $cardtitlecolor, "fonts/" . $titlefont, str_replace("_",  " ",$HTTP_GET_VARS['title']));
	}
		
	if($addressfont!="" && $HTTP_GET_VARS['address']!="")
	{
		switch(strtoupper($addressalign))
		{
			case "L":
				imagettftext($image, $addresssize*1.5, 0, $addresscoord[0], $addresscoord[1], $cardaddresscolor, "fonts/" . $addressfont,  str_replace("_", " ", $HTTP_GET_VARS['address']));
				break;
			case "C":
				$box = imagettfbbox ($addresssize*1.5, 0, "fonts/" . $addressfont, str_replace("_",  " ",$HTTP_GET_VARS['address'])); 
				$tw=$box[2]-$box[0]; //image width 
				// x position 
				$px = (imagesx($image) -$tw)/2; 
				if($citycoord[1]==$addresscoord[1])
				{
					$seperator = " - ";
					$display=str_replace("_",  " ",$HTTP_GET_VARS['address']) . $seperator . str_replace("_", " ", $HTTP_GET_VARS['city']) . ", " . $HTTP_GET_VARS['state'] . "  " . $HTTP_GET_VARS['zip'];
					$box = imagettfbbox ($citysize*1.5, 0, "fonts/" . $cityfont, $display); 
					$tw=$box[2]-$box[0]; //image width 
					$px = (imagesx($image) - $tw)/2;
				}
				imagettftext($image, $addresssize*1.5, 0, $px, $addresscoord[1], $cardaddresscolor, "fonts/" . $addressfont, str_replace("_",  " ",$HTTP_GET_VARS['address']));
				//imagettftext($image, $addresssize*1.5, 0, $px, $addresscoord[1], $cardaddresscolor, "fonts/" . $addressfont, $display);
				break;
			case "R":
				imagettftext($image, $addresssize*1.5, 0, ($addresscoord[0] - $addresssize * strlen(str_replace("_",  " ",$HTTP_GET_VARS['address']))), $addresscoord[1], $cardaddresscolor, "fonts/" . $addressfont, str_replace("_",  " ",$HTTP_GET_VARS['address']));
				break;
			default:
				imagettftext($image, $addresssize*1.5, 0, $addresscoord[0], $addresscoord[1], $cardaddresscolor, "fonts/" . $addressfont,  str_replace("_", " ", $HTTP_GET_VARS['address']));
		}
	}

	if($cityfont!="" && $HTTP_GET_VARS['city']!="")
	{
		$display="";
		
		switch($cityalign)
		{
			case "L":
				if($citycoord[1]==$addresscoord[1])
					$seperator = " - ";
				$display=$seperator . str_replace("_", " ", $HTTP_GET_VARS['city']) . ", " . $HTTP_GET_VARS['state'] . "  " . $HTTP_GET_VARS['zip'];
				imagettftext($image, $citysize*1.5, 0, $citycoord[0], $citycoord[1], $cardcitycolor, "fonts/" . $cityfont, $display);
				break;
			case "C":
				$box = imagettfbbox ($addresssize*1.5, 0, "fonts/" . $addressfont, str_replace("_",  " ",$HTTP_GET_VARS['address'])); 
				$tw=$box[2]-$box[0]; //image width 
				// x position 
				$px = (imagesx($image) -$tw)/2; 
				if($citycoord[1]==$addresscoord[1])
				{
					$seperator = " - ";
					$display=str_replace("_",  " ",$HTTP_GET_VARS['address']) . $seperator . str_replace("_", " ", $HTTP_GET_VARS['city']) . ", " . $HTTP_GET_VARS['state'] . "  " . $HTTP_GET_VARS['zip'];
					$box = imagettfbbox ($citysize*1.5, 0, "fonts/" . $cityfont, $display); 
					$tw2=$box[2]-$box[0]; //image width 
					$px = (imagesx($image) - $tw2)/2;
					$px += $tw; //Subtract the width of the address box from proper placement.
				}
				$display=$seperator . str_replace("_", " ", $HTTP_GET_VARS['city']) . ", " . $HTTP_GET_VARS['state'] . "  " . $HTTP_GET_VARS['zip'];
				imagettftext($image, $citysize*1.5, 0, $px, $citycoord[1], $cardcitycolor, "fonts/" . $cityfont, $display);
				break;
			case "R":
				$display=$seperator . str_replace("_", " ", $HTTP_GET_VARS['city']) . ", " . $HTTP_GET_VARS['state'] . "  " . $HTTP_GET_VARS['zip'];
				imagettftext($image, $citysize*1.5, 0, ($citycoord[0] - (($citysize) * strlen($display))), $citycoord[1], $cardcitycolor, "fonts/" . $cityfont, $display);
				break;
			default:
				if($citycoord[1]==$addresscoord[1])
					$seperator = " &#149; ";
				$display=$seperator . str_replace("_", " ", $HTTP_GET_VARS['city']) . ", " . $HTTP_GET_VARS['state'] . "  " . $HTTP_GET_VARS['zip'];
				imagettftext($image, $citysize*1.5, 0, $citycoord[0], $citycoord[1], $cardcitycolor, "fonts/" . $cityfont, $display);
		}
		
	}
			
	//Centering calculations for contact numbers on one-line.
	if($phonecoord[1]==$cellcoord[1] && $phonecoord[1]==$tollcoord[1] && $phonecoord[1]==$faxcoord[1] && $HTTP_GET_VARS['phone']!="" && $HTTP_GET_VARS['cell']!="" && $HTTP_GET_VARS['fax']!="" && $HTTP_GET_VARS['tollfree']!="")
	{
		if($phoneheader!='n')
		{
			switch($phoneheader)
			{
				case "Y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "PHONE: ");	
					break;
				case "y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "phone: ");	
					break;
				default:
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "Phone: ");	
			}
		}
		$phonebox = imagettfbbox($phonesize*1.5, 0, "fonts/" . $phonefont, str_replace("_",  " ",$HTTP_GET_VARS['phone'])); 
		if($cellheader!='n')
		{
			switch($cellheader)
			{
				case "Y":
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  CELL: ");	
					break;
				case "y":
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  cell: ");	
					break;
				default:
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Cell: ");	
			}
		}
		$cellbox = imagettfbbox($cellsize*1.5, 0, "fonts/" . $cellfont, str_replace("_",  " ",$HTTP_GET_VARS['cell'])); 
		if($tollheader!='n')
		{
			switch($tollheader)
			{
				case "Y":
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  TOLLFREE: ");	
					break;
				case "y":
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  tollfree: ");	
					break;
				default:
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Tollfree: ");	
			}
		}
		$tollbox = imagettfbbox($tollsize*1.5, 0, "fonts/" . $tollfont, str_replace("_",  " ",$HTTP_GET_VARS['tollfree'])); 
		if($faxheader!='n')
		{
			switch($faxheader)
			{
				case "Y":
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  FAX: ");	
					break;
				case "y":
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  fax: ");	
					break;
				default:
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Fax: ");	
			}
		}
		$faxbox = imagettfbbox($faxsize*1.5, 0, "fonts/" . $faxfont, str_replace("_",  " ",$HTTP_GET_VARS['fax'])); 
		$px = (imagesx($image) - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]) - ($cellheaderbox[2]-$cellheaderbox[0]) - ($cellbox[2]-$cellbox[0]) - ($tollheaderbox[2]-$tollheaderbox[0]) - ($tollbox[2]-$tollbox[0]) - ($faxheaderbox[2]-$faxheaderbox[0]) - ($faxbox[2]-$fax[0]))/2;
	}else if($phonecoord[1]==$cellcoord[1] && $phonecoord[1]==$tollcoord[1] && $HTTP_GET_VARS['phone']!="" && $HTTP_GET_VARS['cell']!="" && $HTTP_GET_VARS['tollfree']!="")
	{
		if($phoneheader!='n')
		{
			switch($phoneheader)
			{
				case "Y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "PHONE: ");	
					break;
				case "y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "phone: ");	
					break;
				default:
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "Phone: ");	
			}
		}
		$phonebox = imagettfbbox($phonesize*1.5, 0, "fonts/" . $phonefont, str_replace("_",  " ",$HTTP_GET_VARS['phone'])); 
		if($cellheader!='n')
		{
			switch($cellheader)
			{
				case "Y":
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  CELL: ");	
					break;
				case "y":
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  cell: ");	
					break;
				default:
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Cell: ");	
			}
		}
		$cellbox = imagettfbbox($cellsize*1.5, 0, "fonts/" . $cellfont, str_replace("_",  " ",$HTTP_GET_VARS['cell'])); 
		if($tollheader!='n')
		{
			switch($tollheader)
			{
				case "Y":
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  TOLLFREE: ");	
					break;
				case "y":
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  tollfree: ");	
					break;
				default:
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Tollfree: ");	
			}
		}
		$tollbox = imagettfbbox($tollsize*1.5, 0, "fonts/" . $tollfont, str_replace("_",  " ",$HTTP_GET_VARS['tollfree'])); 
		$px = (imagesx($image) - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]) - ($cellheaderbox[2]-$cellheaderbox[0]) - ($cellbox[2]-$cellbox[0]) - ($tollheaderbox[2]-$tollheaderbox[0]) - ($tollbox[2]-$tollbox[0]))/2;
	}else if($phonecoord[1]==$cellcoord[1] && $phonecoord[1]==$faxcoord[1] && $HTTP_GET_VARS['phone']!="" && $HTTP_GET_VARS['cell']!="" && $HTTP_GET_VARS['fax']!="")
	{
		if($phoneheader!='n')
		{
			switch($phoneheader)
			{
				case "Y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  PHONE: ");	
					break;
				case "y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  phone: ");	
					break;
				default:
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Phone: ");	
			}
		}
		$phonebox = imagettfbbox($phonesize*1.5, 0, "fonts/" . $phonefont, str_replace("_",  " ",$HTTP_GET_VARS['phone'])); 
		if($cellheader!='n')
		{
			switch($cellheader)
			{
				case "Y":
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  CELL: ");	
					break;
				case "y":
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  cell: ");	
					break;
				default:
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Cell: ");	
			}
		}
		$cellbox = imagettfbbox($cellsize*1.5, 0, "fonts/" . $cellfont, str_replace("_",  " ",$HTTP_GET_VARS['cell'])); 
		if($faxheader!='n')
		{
			switch($faxheader)
			{
				case "Y":
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  FAX: ");	
					break;
				case "y":
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  fax: ");	
					break;
				default:
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Fax: ");	
			}
		}
		$faxbox = imagettfbbox($faxsize*1.5, 0, "fonts/" . $faxfont, str_replace("_",  " ",$HTTP_GET_VARS['fax'])); 
		$px = (imagesx($image) - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]) - ($cellheaderbox[2]-$cellheaderbox[0]) - ($cellbox[2]-$cellbox[0]) - ($faxheaderbox[2]-$faxheaderbox[0]) - ($faxbox[2]-$fax[0]))/2;
	}else if($phonecoord[1]==$tollcoord[1] && $phonecoord[1]==$faxcoord[1] && $HTTP_GET_VARS['phone']!="" && $HTTP_GET_VARS['fax']!="" && $HTTP_GET_VARS['tollfree']!="")
	{
		if($phoneheader!='n')
		{
			switch($phoneheader)
			{
				case "Y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "PHONE: ");	
					break;
				case "y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "phone: ");	
					break;
				default:
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "Phone: ");	
			}
		}
		$phonebox = imagettfbbox($phonesize*1.5, 0, "fonts/" . $phonefont, str_replace("_",  " ",$HTTP_GET_VARS['phone'])); 
		if($tollheader!='n')
		{
			switch($tollheader)
			{
				case "Y":
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  TOLLFREE: ");	
					break;
				case "y":
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  tollfree: ");	
					break;
				default:
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Tollfree: ");	
			}
		}
		$tollbox = imagettfbbox ($tollsize*1.5, 0, "fonts/" . $tollfont, str_replace("_",  " ",$HTTP_GET_VARS['tollfree'])); 
		if($faxheader!='n')
		{
			switch($faxheader)
			{
				case "Y":
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  FAX: ");	
					break;
				case "y":
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  fax: ");	
					break;
				default:
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Fax: ");	
			}
		}
		$faxbox = imagettfbbox($faxsize*1.5, 0, "fonts/" . $faxfont, str_replace("_",  " ",$HTTP_GET_VARS['fax'])); 
		$px = (imagesx($image) - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]) - ($tollheaderbox[2]-$tollheaderbox[0]) - ($tollbox[2]-$tollbox[0]) - ($faxheaderbox[2]-$faxheaderbox[0]) - ($faxbox[2]-$fax[0]))/2;
	}else if($phonecoord[1]==$cellcoord[1] && $HTTP_GET_VARS['phone']!="" && $HTTP_GET_VARS['cell']!="")
	{
		if($phoneheader!='n')
		{
			switch($phoneheader)
			{
				case "Y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "PHONE: ");	
					break;
				case "y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "phone: ");	
					break;
				default:
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "Phone: ");	
			}
		}
		$phonebox = imagettfbbox($phonesize*1.5, 0, "fonts/" . $phonefont, str_replace("_",  " ",$HTTP_GET_VARS['phone'])); 
		if($cellheader!='n')
		{
			switch($cellheader)
			{
				case "Y":
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  CELL: ");	
					break;
				case "y":
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  cell: ");	
					break;
				default:
					$cellheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Cell: ");	
			}
		}
		$cellbox = imagettfbbox($cellsize*1.5, 0, "fonts/" . $cellfont, str_replace("_",  " ",$HTTP_GET_VARS['cell'])); 
		$px = (imagesx($image) - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]) - ($cellheaderbox[2]-$cellheaderbox[0]) - ($cellbox[2]-$cellbox[0]))/2;
	}else if($phonecoord[1]==$tollcoord[1] && $HTTP_GET_VARS['phone']!="" && $HTTP_GET_VARS['tollfree']!="")
	{
		if($phoneheader!='n')
		{
			switch($phoneheader)
			{
				case "Y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "PHONE: ");	
					break;
				case "y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "phone: ");	
					break;
				default:
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "Phone: ");	
			}
		}
		$phonebox = imagettfbbox($phonesize*1.5, 0, "fonts/" . $phonefont, str_replace("_",  " ",$HTTP_GET_VARS['phone'])); 
		if($tollheader!='n')
		{
			switch($tollheader)
			{
				case "Y":
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  TOLLFREE: ");	
					break;
				case "y":
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  tollfree: ");	
					break;
				default:
					$tollheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Tollfree: ");	
			}
		}
		$tollbox = imagettfbbox($tollsize*1.5, 0, "fonts/" . $tollfont, str_replace("_",  " ",$HTTP_GET_VARS['tollfree'])); 
		$px = (imagesx($image) - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]) - ($tollheaderbox[2]-$tollheaderbox[0]) - ($tollbox[2]-$tollbox[0]))/2;
	}else if($phonecoord[1]==$faxcoord[1] && $HTTP_GET_VARS['phone']!="" && $HTTP_GET_VARS['fax']!="")
	{
		if($phoneheader!='n')
		{
			switch($phoneheader)
			{
				case "Y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "PHONE: ");	
					break;
				case "y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "phone: ");	
					break;
				default:
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "Phone: ");	
			}
		}
		$phonebox = imagettfbbox($phonesize*1.5, 0, "fonts/" . $phonefont, str_replace("_",  " ",$HTTP_GET_VARS['phone'])); 
		if($faxheader!='n')
		{
			switch($faxheader)
			{
				case "Y":
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  FAX: ");	
					break;
				case "y":
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  fax: ");	
					break;
				default:
					$faxheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "  Fax: ");	
			}
		}
		$faxbox = imagettfbbox($faxsize*1.5, 0, "fonts/" . $faxfont, str_replace("_",  " ",$HTTP_GET_VARS['fax'])); 
		$px = (imagesx($image) - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]) - ($faxheaderbox[2]-$faxheaderbox[0]) - ($faxbox[2]-$faxbox[0]))/2;
	}else
	{
		if($phoneheader!='n')
		{
			switch($phoneheader)
			{
				case "Y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "PHONE: ");	
					break;
				case "y":
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "phone: ");	
					break;
				default:
					$phoneheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "Phone: ");	
			}
		}
		$phonebox = imagettfbbox($phonesize*1.5, 0, "fonts/" . $phonefont, str_replace("_",  " ",$HTTP_GET_VARS['phone'])); 
		$px = (imagesx($image) - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]))/2;
	}
//Actual placement of text in the graphic
	if($phonefont!="" && $HTTP_GET_VARS['phone']!="")
	{
		switch($phonealign)
		{
			case "L":
				if($phoneheader!='n')
				{
					switch($phoneheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0, $phonecoord[0] - ($phoneheaderbox[2]-$phoneheaderbox[0]), $phonecoord[1], $cardheadercolor, "fonts/" . $headerfont, "PHONE: ");
							$px += ($phoneheaderbox[2]-$phoneheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0, $phonecoord[0] - ($phoneheaderbox[2]-$phoneheaderbox[0]), $phonecoord[1], $cardheadercolor, "fonts/" . $headerfont, "phone: ");
							$px += ($phoneheaderbox[2]-$phoneheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0, $phonecoord[0] - ($phoneheaderbox[2]-$phoneheaderbox[0]), $phonecoord[1], $cardheadercolor, "fonts/" . $headerfont, "Phone: ");
							$px += ($phoneheaderbox[2]-$phoneheaderbox[0]);
					}	
				}
				imagettftext($image, $phonesize*1.5,0, $phonecoord[0], $phonecoord[1], $cardphonecolor, "fonts/" . $phonefont, str_replace("_", " ", $HTTP_GET_VARS['phone']));
				break;
			case "C":
				if($phoneheader!='n')
				{
					switch($phoneheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$px, $phonecoord[1], $cardheadercolor, "fonts/" . $headerfont, "PHONE: ");
							$px += ($phoneheaderbox[2]-$phoneheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$px, $phonecoord[1], $cardheadercolor, "fonts/" . $headerfont, "phone: ");
							$px += ($phoneheaderbox[2]-$phoneheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$px, $phonecoord[1], $cardheadercolor, "fonts/" . $headerfont, "Phone: ");
							$px += ($phoneheaderbox[2]-$phoneheaderbox[0]);
					}
				}
				imagettftext($image, $phonesize*1.5,0,$px, $phonecoord[1], $cardphonecolor, "fonts/" . $phonefont, str_replace("_", " ", $HTTP_GET_VARS['phone']));
				$px += ($phonebox[2]-$phonebox[0]);
				break;
			case "R":
				if($phoneheader!='n')
				{
					switch($phoneheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0, $phonecoord[0] - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]), $phonecoord[1], $cardheadercolor, "fonts/" . $headerfont, "PHONE: ");
							$px += ($phoneheaderbox[2]-$phoneheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0, $phonecoord[0] - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]), $phonecoord[1], $cardheadercolor, "fonts/" . $headerfont, "phone: ");
							$px += ($phoneheaderbox[2]-$phoneheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0, $phonecoord[0] - ($phonebox[2]-$phonebox[0]) - ($phoneheaderbox[2]-$phoneheaderbox[0]), $phonecoord[1], $cardheadercolor, "fonts/" . $headerfont, "Phone: ");
							$px += ($phoneheaderbox[2]-$phoneheaderbox[0]);
					}	
				}
				imagettftext($image, $phonesize*1.5,0, $phonecoord[0] - ($phonebox[2]-$phonebox[0]), $phonecoord[1], $cardphonecolor, "fonts/" . $phonefont, str_replace("_", " ", $HTTP_GET_VARS['phone']));
				break;
		}
	}
	if($tollfont!="" && $HTTP_GET_VARS['tollfree']!="")
	{
		switch($tollalign)
		{
			case "L":
				if($tollheader!='n')
				{
					switch($tollheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$tollcoord[0] - ($tollheaderbox[2] - $tollheaderbox[0]), $tollcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  TOLLFREE: ");
							$px += ($tollheaderbox[2]-$tollheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$tollcoord[0] - ($tollheaderbox[2] - $tollheaderbox[0]), $tollcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  tollfree: ");
							$px += ($tollheaderbox[2]-$tollheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$tollcoord[0] - ($tollheaderbox[2] - $tollheaderbox[0]), $tollcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  Tollfree: ");
							$px += ($tollheaderbox[2]-$tollheaderbox[0]);
					}
				}
				imagettftext($image, $tollsize*1.5,0,$tollcoord[0], $tollcoord[1], $cardtollcolor, "fonts/" . $tollfont, str_replace("_",  " ", $HTTP_GET_VARS['tollfree']));
				break;
			case "C":
				if($tollheader!='n')
				{
					switch($tollheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$px, $tollcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  TOLLFREE: ");
							$px += ($tollheaderbox[2]-$tollheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$px, $tollcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  tollfree: ");
							$px += ($tollheaderbox[2]-$tollheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$px, $tollcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  Tollfree: ");
							$px += ($tollheaderbox[2]-$tollheaderbox[0]);
					}
				}
				imagettftext($image, $tollsize*1.5,0,$px, $tollcoord[1], $cardtollcolor, "fonts/" . $tollfont, str_replace("_",  " ", $HTTP_GET_VARS['tollfree']));
				$px += ($tollbox[2]-$tollbox[0]);
				break;
			case "R":
				if($tollheader!='n')
				{
					switch($tollheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$tollcoord[0] - ($tollbox[2]-$tollbox[0]) - ($tollheaderbox[2] - $tollheaderbox[0]), $tollcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  TOLLFREE: ");
							$px += ($tollheaderbox[2]-$tollheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$tollcoord[0] - ($tollbox[2]-$tollbox[0]) - ($tollheaderbox[2] - $tollheaderbox[0]), $tollcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  tollfree: ");
							$px += ($tollheaderbox[2]-$tollheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$tollcoord[0] - ($tollbox[2]-$tollbox[0]) - ($tollheaderbox[2] - $tollheaderbox[0]), $tollcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  Tollfree: ");
							$px += ($tollheaderbox[2]-$tollheaderbox[0]);
					}
				}
				imagettftext($image, $tollsize*1.5,0,$tollcoord[0] - ($tollbox[2]-$tollbox[0]), $tollcoord[1], $cardtollcolor, "fonts/" . $tollfont, str_replace("_",  " ", $HTTP_GET_VARS['tollfree']));
		}
	}
	
	if($cellfont!="" && $HTTP_GET_VARS['cell']!="")
	{
		switch($cellalign)
		{
			case "L":
				if($cellheader!='n')
				{
					switch($cellheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$cellcoord[0]-($cellheaderbox[2]-$cellheaderbox[0]), $cellcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  CELL: ");
							$px += ($cellheaderbox[2]-$cellheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$cellcoord[0]-($cellheaderbox[2]-$cellheaderbox[0]), $cellcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  cell: ");
							$px += ($cellheaderbox[2]-$cellheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$cellcoord[0]-($cellheaderbox[2]-$cellheaderbox[0]), $cellcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  Cell: ");
							$px += ($cellheaderbox[2]-$cellheaderbox[0]);
					}
				}
				imagettftext($image, $cellsize*1.5,0,$cellcoord[0], $cellcoord[1], $cardcellcolor, "fonts/" . $cellfont, str_replace("_",  " ", $HTTP_GET_VARS['cell']));
				break;
			case "C":
				if($cellheader!='n')
				{
					switch($cellheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$px, $cellcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  CELL: ");
							$px += ($cellheaderbox[2]-$cellheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$px, $cellcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  cell: ");
							$px += ($cellheaderbox[2]-$cellheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$px, $cellcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  Cell: ");
							$px += ($cellheaderbox[2]-$cellheaderbox[0]);
					}
				}
				imagettftext($image, $cellsize*1.5,0,$px, $cellcoord[1], $cardcellcolor, "fonts/" . $cellfont, str_replace("_",  " ", $HTTP_GET_VARS['cell']));
				$px += ($cellbox[2]-$cellbox[0]);
				break;
			case "R":
				if($cellheader!='n')
				{
					switch($cellheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$cellcoord[0] - ($cellbox[2]-$cellbox[0]) -($cellheaderbox[2]-$cellheaderbox[0]), $cellcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  CELL: ");
							$px += ($cellheaderbox[2]-$cellheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$cellcoord[0] - ($cellbox[2]-$cellbox[0]) -($cellheaderbox[2]-$cellheaderbox[0]), $cellcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  cell: ");
							$px += ($cellheaderbox[2]-$cellheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$cellcoord[0] - ($cellbox[2]-$cellbox[0]) -($cellheaderbox[2]-$cellheaderbox[0]), $cellcoord[1], $cardheadercolor, "fonts/" . $headerfont, "  Cell: ");
							$px += ($cellheaderbox[2]-$cellheaderbox[0]);
					}
				}
				imagettftext($image, $cellsize*1.5,0,$cellcoord[0] - ($cellbox[2]-$cellbox[0]) , $cellcoord[1], $cardcellcolor, "fonts/" . $cellfont, str_replace("_",  " ", $HTTP_GET_VARS['cell']));
		}
	}
	
	if($faxfont!="" && $HTTP_GET_VARS['fax']!="")
	{
		switch($faxalign)
		{
			case "L":
				if($faxheader!='n')
				{
					switch($faxheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5, 0, $faxcoord[0] - ($faxheaderbox[2]-$faxheaderbox[0]), $faxcoord[1], $cardheadercolor, "fonts/" . $headerfont, " FAX: ");
							$px += ($faxheaderbox[2]-$faxheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5, 0, $faxcoord[0] - ($faxheaderbox[2]-$faxheaderbox[0]), $faxcoord[1], $cardheadercolor, "fonts/" . $headerfont, " fax: ");
							$px += ($faxheaderbox[2]-$faxheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5, 0, $faxcoord[0] - ($faxheaderbox[2]-$faxheaderbox[0]), $faxcoord[1], $cardheadercolor, "fonts/" . $headerfont, " Fax: ");
							$px += ($faxheaderbox[2]-$faxheaderbox[0]);
					}
				}
				imagettftext($image, $faxsize*1.5,0,$faxcoord[0], $faxcoord[1], $cardfaxcolor, "fonts/" . $faxfont, str_replace("_",  " ", $HTTP_GET_VARS['fax']));
				break;
			case "C":
				if($faxheader!='n')
				{
					switch($faxheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5, 0, $px, $faxcoord[1], $cardheadercolor, "fonts/" . $headerfont, " FAX: ");
							$px += ($faxheaderbox[2]-$faxheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5, 0, $px, $faxcoord[1], $cardheadercolor, "fonts/" . $headerfont, " fax: ");
							$px += ($faxheaderbox[2]-$faxheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5, 0, $px, $faxcoord[1], $cardheadercolor, "fonts/" . $headerfont, " Fax: ");
							$px += ($faxheaderbox[2]-$faxheaderbox[0]);
					}
				}
				imagettftext($image, $faxsize*1.5,0,$px, $faxcoord[1], $cardfaxcolor, "fonts/" . $faxfont, str_replace("_",  " ", $HTTP_GET_VARS['fax']));
				$px += ($faxbox[2]-$faxbox[0]);
				break;
			case "R":
				if($faxheader!='n')
				{
					switch($faxheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5, 0, $faxcoord[0] - ($faxbox[2]-$faxbox[0]) - ($faxheaderbox[2]-$faxheaderbox[0]), $faxcoord[1], $cardheadercolor, "fonts/" . $headerfont, " FAX: ");
							$px += ($faxheaderbox[2]-$faxheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5, 0, $faxcoord[0] - ($faxbox[2]-$faxbox[0]) - ($faxheaderbox[2]-$faxheaderbox[0]), $faxcoord[1], $cardheadercolor, "fonts/" . $headerfont, " fax: ");
							$px += ($faxheaderbox[2]-$faxheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5, 0, $faxcoord[0] - ($faxbox[2]-$faxbox[0]) - ($faxheaderbox[2]-$faxheaderbox[0]), $faxcoord[1], $cardheadercolor, "fonts/" . $headerfont, " Fax: ");
							$px += ($faxheaderbox[2]-$faxheaderbox[0]);
					}
				}
				imagettftext($image, $faxsize*1.5,0,$faxcoord[0] - ($faxbox[2]-$faxbox[0]), $faxcoord[1], $cardfaxcolor, "fonts/" . $faxfont, str_replace("_",  " ", $HTTP_GET_VARS['fax']));
		}
	}
	
	if($emailfont!="" && $HTTP_GET_VARS['email']!="")
	{
		if($emailheader!='n')
			{
				switch($emailheader)
				{
					case "Y":
						$emailheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "EMAIL: ");	
						break;
					case "y":
						$emailheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "email: ");	
						break;
					default:
						$emailheaderbox = imagettfbbox($headersize*1.5,0, "fonts/" . $headerfont, "Email: ");	
				}
			}
			$emailbox = imagettfbbox($emailsize*1.5, 0, "fonts/" . $emailfont, str_replace("_",  " ",$HTTP_GET_VARS['email'])); 
			$px = (imagesx($image) - ($emailheaderbox[2] - $emailheaderbox[0]) - ($emailbox[2]-$emailbox[0]))/2;
		switch($emailalign)
		{
			case "L":
				if($emailheader!='n')
				{
					switch($emailheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$emailcoord[0] - ($emailheaderbox[2] - $emailheaderbox[0]), $emailcoord[1], $cardheadercolor, "fonts/" . $headerfont, "EMAIL: ");
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$emailcoord[0] - ($emailheaderbox[2] - $emailheaderbox[0]), $emailcoord[1], $cardheadercolor, "fonts/" . $headerfont, "email: ");
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$emailcoord[0] - ($emailheaderbox[2] - $emailheaderbox[0]), $emailcoord[1], $cardheadercolor, "fonts/" . $headerfont, "Email: ");
					}
				}
				imagettftext($image, $emailsize*1.5,0,$emailcoord[0], $emailcoord[1], $cardemailcolor, "fonts/" . $emailfont, str_replace("_",  " ", $HTTP_GET_VARS['email']));
				break;
			case "C":		
				if($emailheader!='n')
				{
					switch($emailheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$px, $emailcoord[1], $cardheadercolor, "fonts/" . $headerfont, "EMAIL: ");
							$px += ($emailheaderbox[2]-$emailheaderbox[0]);
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$px, $emailcoord[1], $cardheadercolor, "fonts/" . $headerfont, "email: ");
							$px += ($emailheaderbox[2]-$emailheaderbox[0]);
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$px, $emailcoord[1], $cardheadercolor, "fonts/" . $headerfont, "Email: ");
							$px += ($emailheaderbox[2]-$emailheaderbox[0]);
					}
				}
				imagettftext($image, $emailsize*1.5,0,$px, $emailcoord[1], $cardemailcolor, "fonts/" . $emailfont, str_replace("_",  " ", $HTTP_GET_VARS['email']));				
				break;
			case "R":
				if($emailheader!='n')
				{
					switch($emailheader)
					{
						case "Y":
							imagettftext($image, $headersize*1.5,0,$emailcoord[0] - ($emailbox[2] - $emailbox[0]) - ($emailheaderbox[2] - $emailheaderbox[0]), $emailcoord[1], $cardheadercolor, "fonts/" . $headerfont, "EMAIL: ");
							break;
						case "y":
							imagettftext($image, $headersize*1.5,0,$emailcoord[0] - ($emailbox[2] - $emailbox[0]) - ($emailheaderbox[2] - $emailheaderbox[0]), $emailcoord[1], $cardheadercolor, "fonts/" . $headerfont, "email: ");
							break;
						default:
							imagettftext($image, $headersize*1.5,0,$emailcoord[0] - ($emailbox[2] - $emailbox[0]) - ($emailheaderbox[2] - $emailheaderbox[0]), $emailcoord[1], $cardheadercolor, "fonts/" . $headerfont, "Email: ");
					}
				}
				imagettftext($image, $emailsize*1.5,0,$emailcoord[0] - ($emailbox[2] - $emailbox[0]), $emailcoord[1], $cardemailcolor, "fonts/" . $emailfont, str_replace("_",  " ", $HTTP_GET_VARS['email']));
				break;
		}
	}
	/**/
	header("Content-type: image/png");
	imagepng($image);
	//imagepng($image, "image/finished/1-" . $HTTP_GET_VARS['comp'] . ".png");
?>
