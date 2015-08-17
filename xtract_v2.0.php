<?php

  //$filename = "../Bosch_WAQ_24468_EE__Opinion_2127177.html"; //origen HTML a procesar. $useUTFdec = true;
  
  //$filename = "Zanussi_ZWH6120P__Opinion_1912394.html"; 
  $filename = "Bluesky_BLF_1009__Opinion_1740046.html";
  
  //$filename = "../Edesa_L_2106__Opinion_1665326.html";
  //$filename = "../Whirlpool_AWT_5090__Opinion_1704975.html";
  //$filename = "../New_Pol_S_1211__Opinion_1191936.html";
  //$filename = "../LG_WF_9501TPT__Opinion_1926011.html";
  //$filename = "../Lynx_4TS_718_B__Opinion_1133904.html";
  //$filename = "../Fagor_1F_206__Opinion_1190072.html";
  //$filename = "../AEG_L_W_1000_new__Opinion_1016805.html";
  //$filename = "../Whirlpool_AWE_2239__Opinion_2138051.html";  
  
  sleep(2);
  
  $useUTFdec = false;
  
  print "\nProcessing ".$filename.". ";
  
  /** 1a Parte. Quitamos los tags HTML *********************************************/

  $hndIn = fopen($filename, "r");
  $hndOut = fopen($filename.".tmp", "w");

  print "\nRemoving tags...\n";
  //$actualTag = "";
  
  $xml_desc   = false;
  $xml_title  = false;
  $xml_date   = false;
  $xml_pros   = false;
  $xml_cons   = false;
  $xml_reco   = false;
  $xml_rating = false;
  $xml_about  = false;
  $xml_uname  = false;
  $xml_qlpr   = false;
  $xml_stars  = false;
  $xml_oprat  = false;
  $xml_ustats = false;
  
  $tagFrom = "";
  
  $globalStr = "";

  fprintf ($hndOut, '<?xml version="1.0" encoding="utf-8"?>'); //XML Header
  fprintf ($hndOut, "\n<opinion>");

  while (!feof($hndIn)) 
  {
      $char = fgetc($hndIn);
      
      //$tag = "";
      if ($char == '<')
      {
            $tag = "";
            $tag = $tag.$char;
            do
            {
                $char = fgetc($hndIn);
                $tag = $tag.$char;
            } while ($char != '>');

            $tag_p_start   = stripos(strtolower($tag), '<p');
            $tag_p_end     = stripos(strtolower($tag), '</p');
            $tag_br_start  = stripos(strtolower($tag), '<br');
            $tag_span_start = stripos(strtolower($tag), '<span');
            $tag_span_end   = stripos(strtolower($tag), '</span');
            $tag_div_start    = stripos(strtolower($tag), '<div');
            $tag_div_end    = stripos(strtolower($tag), '</div');
            
            //$tag_desc_start = stripos(strtolower($tag), '<span property="v:description"');
            //$tag_desc_end   = stripos(strtolower($tag), '<div class="clearfix"');
            
            
            $user_title = stripos(strtolower($tag), '<span property="v:summary');
            $user_date = stripos(strtolower($tag), '<span property="v:dtreviewed"');
            $user_desc = stripos(strtolower($tag), '<span property="v:description"');
            $user_pros = stripos(strtolower($tag), '<p class="pros"');
            $user_cons = stripos(strtolower($tag), '<p class="cons"');
            $user_reco = stripos(strtolower($tag), '<p class="recommendable');
            $user_rating = stripos(strtolower($tag), '<div class="opinionRating"');
            $user_abouttext = stripos(strtolower($tag), '<span id="MemberAboutMeText"');
            $user_stats = stripos(strtolower($tag), '<div class="userStats"'); 
            $user_uname = stripos(strtolower($tag), '<span property="v:reviewer"');
            $user_qltprc = stripos(strtolower($tag), '<div class="opinionRatingTooltip clearfix"');
            $user_stars = stripos(strtolower($tag), '<img class="ratingStars'); //end: </p>
            $user_oprat = stripos(strtolower($tag), '<span style="width:');
            
            if ($user_stars !== false) 
            { 
				  if ($tagFrom == "optitle")
				  {
			 	     $tagtok1 = strtok($tag, ' ');
				     while ($tagtok1 != false)
				     {
					     if (stripos(strtolower($tagtok1), "rank") !== false)
					     {
							 $tagtok2 = strtok($tagtok1, '_"');
							 while ($tagtok2 != false)
				             {
								 $tagtok2 = strtok('_"');
								 if (trim($tagtok2))
								   $lasttt2 = $tagtok2;
						     }
							 print "\n[STARS]".$lasttt2."[/STARS]";
							 fprintf ($hndOut, "\n<stars>\n".$lasttt2."\n</stars>");
						 }
					     $tagtok1 = strtok(' ');
				     }
				     $tagFrom = "userstars";
			      }
            }
            if ($user_title !== false) { $xml_title = true; print "\n[TITLE]"; $tagFrom = "optitle"; fprintf ($hndOut, "\n<optitle>\n"); }
            if ($user_date  !== false) { $xml_date = true; print "\n[DATE]"; fprintf ($hndOut, "\n<opdate>\n"); }
            if ($user_desc  !== false) { $xml_desc = true; print "\n[DESC]"; fprintf ($hndOut, "\n<description>\n"); }
            if ($user_pros  !== false) { $xml_pros = true; print "\n[PROS]"; fprintf ($hndOut, "\n<pros>\n"); }
            if ($user_cons  !== false) { $xml_cons = true; print "\n[CONS]"; fprintf ($hndOut, "\n<cons>\n"); }
            if ($user_reco  !== false) { $xml_reco = true; print "\n[RECO]"; fprintf ($hndOut, "\n<recomendable>\n"); }
            if ($user_uname !== false) { $xml_uname = true; print "\n[USR]"; fprintf ($hndOut, "\n<reviser>\n"); }
            if ($user_abouttext !== false) { $xml_about = true; print "\n[ABOUT]"; fprintf ($hndOut, "\n<personaltext>\n"); }
            if ($user_stats !== false) { $xml_ustats = true; print "\n[USTATS]"; }
            if ($user_rating !== false){ $xml_rating = true; print "\n[RATING]"; fprintf ($hndOut, "\n<rating>\n"); }
            
            if ($xml_rating == true and $user_oprat !== false) 
            { 
				//$globalStr .= "$tag"; /* print $tag;*/ 
				$tagtok1 = strtok($tag, ':>');
				$tagtok1 = strtok(':>');
				$tagtok1 = rtrim($tagtok1, '"');
				$tagtok1 = rtrim($tagtok1, '%');
				$globalStr .= $tagtok1;
            }
            
            if ($tag_span_end !== false and $xml_title == true) { $xml_title = false; p(); print "[/TITLE]"; fprintf ($hndOut, "\n</optitle>"); }
            if ($tag_span_end !== false and $xml_date  == true) { $xml_date  = false; p(); print "[/DATE]";  fprintf ($hndOut, "\n</opdate>");}
            if ($tag_span_end !== false and $xml_desc  == true) { $xml_desc  = false; p(); print "[/DESC]";  fprintf ($hndOut, "\n</description>");}
            if ($tag_span_end !== false and $xml_uname  == true) { $xml_uname  = false; p(); print "[/USR]"; fprintf ($hndOut, "\n</reviser>"); }
            if ($tag_span_end !== false and $xml_about  == true) { $xml_about  = false; p("about"); print "[/ABOUT]"; fprintf ($hndOut, "</personaltext>");}
            
            if ($tag_p_end !== false and $xml_pros  == true) { $xml_pros = false; p("pros"); print "[/PROS]"; fprintf ($hndOut, "\n</pros>");}
            if ($tag_p_end !== false and $xml_cons  == true) { $xml_cons = false; p("cons"); print "[/CONS]";  fprintf ($hndOut, "\n</cons>");}
            if ($tag_p_end !== false and $xml_reco  == true) { $xml_reco = false; p("reco"); print "[/RECO]"; fprintf ($hndOut, "\n</recomendable>");}
            
            if ($tag_div_end !== false and $xml_ustats == true) { $xml_ustats = false; p("ust"); print "[/USTATS]";}
            //if ($tag_div_end !== false and $xml_rating == true) { $xml_rating = false; print "[/RATING]";}
            
            if ($user_qltprc !== false and $xml_rating == true) { $xml_rating = false; p("rat"); print "[/RATING]"; fprintf ($hndOut, "\n</rating>");
                                                                  $xml_qlpr   = true;            print "\n[QLPR]";  fprintf ($hndOut, "\n<qualityprice>\n");}
                                                                  
            if ($tag_div_end !== false and $xml_qlpr == true)  { $xml_qlpr = false; p("qlpr"); print "[/QLPR]";  fprintf ($hndOut, "\n</qualityprice>");}
            
            /*
            if ($tag_p_start  !== false and $xml_desc == true) { print "\n[P]"; }
            if ($tag_p_end    !== false and $xml_desc == true) { print "[/P]\n"; }
            if ($tag_br_start !== false and $xml_desc == true) { print "\n[BR]"; }
            */
            
            /*
            if ($tag_p_start  !== false and $xml_desc == true) { $globalStr = trim($globalStr); $globalStr .= "\n"; print "\n[P]";}
            if ($tag_p_end    !== false and $xml_desc == true) { $globalStr = trim($globalStr); $globalStr .= "\n"; print "[/P]\n"; }
            if ($tag_br_start !== false and $xml_desc == true) { $globalStr = trim($globalStr); $globalStr .= "\n"; print "\n[BR]"; }
            */
            
            if ($tag_p_start  !== false and $xml_desc == true) { $globalStr .= "\n"; print "\n[P]";}
            if ($tag_p_end    !== false and $xml_desc == true) { $globalStr .= "\n"; print "[/P]\n"; }
            if ($tag_br_start !== false and $xml_desc == true) { $globalStr .= "\n"; print "\n[BR]"; }

            //$actualTag = $tag;
      }
      
      if ($char != '>')
      { 
		  /*
		  if ($xml_title == true)
		    print utf8_encode($char);
		    
		  if ($xml_date == true)
		    print utf8_encode($char);
		    
		  if ($xml_pros == true)
		    print utf8_encode($char);
		    
		  if ($xml_cons == true)
		    print utf8_encode($char);
		    
		  if ($xml_reco == true)
		    print utf8_encode($char);
		    
		  if ($xml_uname == true)
		    print utf8_encode($char);
		    
		  if ($xml_ustats == true)
		  {
		     print utf8_encode($char);
		  }  
		  
		  if ($xml_rating == true)
		  {
		     print utf8_encode($char);
		     
		     //if ($user_oprat !== false) print $tag;
		  }  
		  
		  if ($xml_qlpr == true)
		  {
			  print utf8_encode($char);
		  }

		  
		  if ($xml_desc == true)
		    print utf8_encode($char);
		  */
		  

		  if ($xml_title OR $xml_date OR $xml_pros OR $xml_cons OR $xml_reco OR $xml_uname OR $xml_ustats OR
		      $xml_rating OR $xml_qlpr OR $xml_rating OR $xml_desc OR $xml_about)  
		  {
		      $globalStr .= $char;
		      print $char;
		  }

		 /*
         if ($char == PHP_EOL)
         {
			 print $globalStr;
			 $globalStr = "";
	     }
	     else
	     {	       
			 if ($xml_title OR $xml_date OR $xml_pros OR $xml_cons OR $xml_reco OR $xml_uname OR $xml_ustats OR
		         $xml_rating OR $xml_qlpr OR $xml_rating OR $xml_desc)  
		         $globalStr .= $char;
         }
         */
		  
		    
      }
  }
  
  function p($something = "nothing")
  {
     global $globalStr, $hndOut;

	 if ($something == "pros")
	 {
	     print utf8_encode(trim(substr(trim($globalStr),strlen("Ventajas:"), strlen($globalStr))));
	     fprintf ($hndOut, utf8_encode(trim(substr(trim($globalStr),strlen("Ventajas:"), strlen($globalStr)))));
	     $globalStr = ""; return 0;
	 }
	 if ($something == "cons")
	 {
	     print utf8_encode(trim(substr(trim($globalStr),strlen("Desventajas:"), strlen($globalStr))));
	     fprintf ($hndOut, utf8_encode(trim(substr(trim($globalStr),strlen("Desventajas:"), strlen($globalStr)))));
	     $globalStr = ""; return 0;
	 }
	 if ($something == "reco")
	 {
	     print utf8_encode(trim(substr(trim($globalStr),strlen("Recomendable:"), strlen($globalStr))));
	     fprintf ($hndOut, utf8_encode(trim(substr(trim($globalStr),strlen("Recomendable:"), strlen($globalStr)))));
	     $globalStr = ""; return 0;
	 }
	 if ($something == "qlpr")
	 {
	     print utf8_encode(trim(substr(trim($globalStr),strlen("Relación calidad precio:")-1, strlen($globalStr))));
	     fprintf ($hndOut, utf8_encode(trim(substr(trim($globalStr),strlen("Relación calidad precio:")-1, strlen($globalStr)))));
	     $globalStr = ""; return 0;
	 }
	 if ($something == "ust")
	 {
		 //print "(".$globalStr.")";
		 
		 $Ud = stripos($globalStr, "usuario desde:");
		 if ($Ud !== false)
	       $globalStr = substr_replace($globalStr, "<usersince>\n", $Ud, strlen("usuario desde:"));
	     
		 $To = stripos($globalStr, "Opiniones:");
		 if ($To !== false)
	       $globalStr = substr_replace($globalStr, "<totalopinions>\n", $To, strlen("Opiniones:"));
	     
	     $Co = stripos($globalStr, "Confianza conseguida:");
	     if ($Co !== false)
	       $globalStr = substr_replace($globalStr, "<confidence>\n", $Co, strlen("Confianza conseguida:"));
		 
         //print $globalStr;
         if ($Ud !== false)
         {
            print strtok($globalStr, " \n\r\t");
            print strtok(" \n\r\t")."[/USRSINCE]";
	     }
         if ($To !== false)
         {
			print strtok(" \n\r\t");
            print strtok(" \n\r\t")."[/TOTALOP]";
         }
         if ($Co !== false)
         {
			print strtok(" \n\r\t");
            print strtok(" \n\r\t")."[/CONFIDENCE]";
         }
         
         if ($Ud !== false)
         {
            fprintf ($hndOut, "\n".strtok($globalStr, " \n\r\t")."\n");
            fprintf ($hndOut, strtok(" \n\r\t")."\n</usersince>");
	     }
         if ($To !== false)
         {
			fprintf ($hndOut, "\n".strtok(" \n\r\t")."\n");
            fprintf ($hndOut, strtok(" \n\r\t")."\n</totalopinions>");
         }
         if ($Co !== false)
         {
			fprintf ($hndOut, "\n".strtok(" \n\r\t")."\n");
            fprintf ($hndOut, strtok(" \n\r\t")."\n</confidence>");
         }
         
	     $globalStr = ""; return 0;
	 }
	 if ($something == "rat")
	 {
	     $globalStr = trim(substr(trim($globalStr),strlen("Detalles:"), strlen($globalStr)));
	     
		 $Rap = stripos($globalStr, "Rapidez");
	     $globalStr = substr_replace($globalStr, "<quick>", $Rap, strlen("Rapidez"));
	     
		 $Cap = stripos($globalStr, "Capacidad del tambor");
	     $globalStr = substr_replace($globalStr, "<capacity>", $Cap, strlen("Capacidad del tambor"));
	     
		 $Fac = stripos($globalStr, "Facilidad de Manejo");
	     $globalStr = substr_replace($globalStr, "<easy>", $Fac, strlen("Facilidad de Manejo"));

		 $Vib = stripos(utf8_encode($globalStr), "Vibración");
		 $globalStr = substr_replace(utf8_encode($globalStr), "<vibration>", $Vib, strlen(utf8_encode("Vibración")));
		 
		 $Rui = stripos($globalStr, "Ruido");
		 $globalStr = substr_replace($globalStr, "<noise>", $Rui, strlen("Ruido"));
		 
		 $Mas = stripos($globalStr, "m&aacute;s");
		 if ($Mas !== false)
		   $globalStr = substr_replace($globalStr, "", $Mas, strlen("m&aacute;s"));
		 
		 print strtok($globalStr, " \n\r\t");
		 print strtok(" \n\r\t")."[/QUICK]";
		 print strtok(" \n\r\t");
		 print strtok(" \n\r\t")."[/CAPACITY]";
		 print strtok(" \n\r\t");
		 print strtok(" \n\r\t")."[/EASY]";
		 print strtok(" \n\r\t");
		 print strtok(" \n\r\t")."[/VIBRATION]";
		 print strtok(" \n\r\t");
		 print strtok(" \n\r\t")."[/NOISE]";
		 
		 fprintf ($hndOut, strtok($globalStr, " \n\r\t")."\n");
		 fprintf ($hndOut, strtok(" \n\r\t")."\n</quick>\n");
		 fprintf ($hndOut, strtok(" \n\r\t")."\n");
		 fprintf ($hndOut, strtok(" \n\r\t")."\n</capacity>\n");
		 fprintf ($hndOut, strtok(" \n\r\t")."\n");
		 fprintf ($hndOut, strtok(" \n\r\t")."\n</easy>\n");
		 fprintf ($hndOut, strtok(" \n\r\t")."\n");
		 fprintf ($hndOut, strtok(" \n\r\t")."\n</vibration>\n");
		 fprintf ($hndOut, strtok(" \n\r\t")."\n");
		 fprintf ($hndOut, strtok(" \n\r\t")."\n</noise>");
		 
	     //print $globalStr;
	     $globalStr = ""; return 0;
	 }
	 
	 if ($something == "about")
	 {
		 if (trim($globalStr) != "")
		   fprintf ($hndOut, $globalStr."\n");
		$globalStr = "";
		return 0;
	 }
	 
	  
	 print "[".trim(utf8_encode($globalStr))."]";
	 fwrite ($hndOut, trim(utf8_encode($globalStr)));
	 $globalStr = "";
  }

 fprintf ($hndOut, "\n</opinion>");
 fclose ($hndIn);
 fclose ($hndOut);
 
 
 
  /** 2da Parte. Cambiamos códigos & por acentos ***********************************/

 $hndIn = fopen($filename.".tmp", "r");
 $hndOut = fopen($filename.".xml", "w");

 print "\nNormalizing...";
 $theLine = 0;
 while (!feof($hndIn))
 {
        $theLine++;
        $buff = fgets($hndIn);
        if ($useUTFdec)
        {
           $buff = str_replace('&aacute;', utf8_decode('á'), $buff);
           $buff = str_replace('&eacute;', utf8_decode('é'), $buff);
           $buff = str_replace('&iacute;', utf8_decode('í'), $buff);
           $buff = str_replace('&oacute;', utf8_decode('ó'), $buff);
           $buff = str_replace('&uacute;', utf8_decode('ú'), $buff);
           $buff = str_replace('&Aacute;', utf8_decode('Á'), $buff);
           $buff = str_replace('&Eacute;', utf8_decode('É'), $buff);
           $buff = str_replace('&Iacute;', utf8_decode('Í'), $buff);
           $buff = str_replace('&Oacute;', utf8_decode('Ó'), $buff);
           $buff = str_replace('&Uacute;', utf8_decode('Ú'), $buff);
           $buff = str_replace('&ntilde;', utf8_decode('ñ'), $buff);
           $buff = str_replace('&Ntilde;', utf8_decode('Ñ'), $buff);
           $buff = str_replace('&iuml;', utf8_decode('ï'), $buff);
           $buff = str_replace('&uuml;', utf8_decode('ü'), $buff);
           $buff = str_replace('&Uuml;', utf8_decode('Ü'), $buff);

           $buff = str_replace('&#237;', utf8_decode('í'),  $buff);
	    }
	    else
	    {
           $buff = str_replace('&aacute;', 'á', $buff);
           $buff = str_replace('&eacute;', 'é', $buff);
           $buff = str_replace('&iacute;', 'í', $buff);
           $buff = str_replace('&oacute;', 'ó', $buff);
           $buff = str_replace('&uacute;', 'ú', $buff);
           $buff = str_replace('&Aacute;', 'Á', $buff);
           $buff = str_replace('&Eacute;', 'É', $buff);
           $buff = str_replace('&Iacute;', 'Í', $buff);
           $buff = str_replace('&Oacute;', 'Ó', $buff);
           $buff = str_replace('&Uacute;', 'Ú', $buff);
           $buff = str_replace('&ntilde;', 'ñ', $buff);
           $buff = str_replace('&Ntilde;', 'Ñ', $buff);
           $buff = str_replace('&iuml;', 'ï', $buff);
           $buff = str_replace('&uuml;', 'ü', $buff);
           $buff = str_replace('&Uuml;', 'Ü', $buff);
		}

        $buff = str_replace('&nbsp;', ' ', $buff);
        $buff = str_replace('&Ccedil;', 'Ç', $buff);
        $buff = str_replace('&ccedil;', 'ç', $buff);
        $buff = str_replace('&amp;', '&', $buff);

        $buff = str_replace('&#130;', ',', $buff);
        $buff = str_replace('&#161;', '¡', $buff);
        $buff = str_replace('&#163;', '£', $buff);
        $buff = str_replace('&#164;', '*', $buff);
        $buff = str_replace('&#173;', '-', $buff);
        $buff = str_replace('&#191;', '¿', $buff);
        $buff = str_replace('&#009;', '',  $buff); //tab ¿?
        $buff = str_replace('&#237;', 'í',  $buff);
        
        //&#237; --> í

        $buff = str_replace('&#186;', 'º', $buff);

        $buff = str_replace('&#180;', '´', $buff);
        $buff = str_replace('&#170;', 'ª', $buff);
        
        $buff = str_replace('&copy;', '(C)', $buff);
        $buff = str_replace('&rarr;', '→', $buff);
        $buff = str_replace('&euro;', 'EUR', $buff);
        $buff = str_replace('&iquest;', utf8_decode('¿'), $buff);
        
        $buff = str_replace('&gt;', '>', $buff);
        $buff = str_replace('&lt;', '<', $buff);
        $buff = str_replace('&middot;', utf8_decode('·'), $buff);
        $buff = str_replace('&quot', '"', $buff);
        
        $buff = str_replace('', 'EUR', $buff);
        $buff = str_replace('', '"', $buff);
        $buff = str_replace('', '"', $buff);
        $buff = str_replace('´', "'", $buff);
/*
        $pos = stripos($buff, "&#");
        if ($pos !== false)
        {   
               print "\n> Unknown numeric code [$filename.txt:$theLine]: $buff"; 
               //fwrite($hndLog, "\n> Unknown numeric code [$filename.txt:$theLine]: $buff");
        }
        */

        //$buff = ltrim($buff);
        
        if ($buff != "")
          fwrite($hndOut, $buff);
        //fwrite($hndOut, $buff);
 }
 fclose ($hndIn);
 fclose ($hndOut);
 //fclose ($hndLog);

 unlink($filename.".tmp"); //Borramos

 print "\nDone!\n";
  


?>
