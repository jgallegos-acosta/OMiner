<?php
 /*
  * ciao_xtract v0.2.0 -> quita tags y cambia códigos en todos los archivos del corpus
  * * Mejoras: 
  *   + Codificación correcta utilizando if (!mb_check_encoding($globalStr, 'UTF-8'))
  *     tanto para el texto de la opinión como para el cambio de códigos a acentos
  * 
  * 
  * */

//HTML to XML
//*** STACK VARS *************************************************************************************
  $st_stack = array();
  $sp = -1; //stack pointer
//****************************************************************************************************

$html_path = '/home/john/Cursos/PLN/project/ciao/OpinionsHTML';
$xml_path = './OpinionsXML';

sleep(1);

$fc = 1;

$time_start = microtime(true);

if ($handle = opendir($html_path)) 
{
    $hff = 0;
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'html')
         $hff++;
    }
    closedir($handle);
}

$hndLog = fopen("logfile.log", "w");
if ($handle = opendir($html_path)) 
{
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'html')
        {  
            $filename = basename($entry, ".html");
            print "Processing ".$filename.".html. File $fc of $hff";
            fprintf ($hndLog, "Processing ".$filename.".html. File $fc of $hff");


  $useUTFdec = false;

  $fnbase = $filename;
  $filename = $filename.".html";
  
  
  
  
  
  //$filename = "../Bosch_WAQ_24468_EE__Opinion_2127177.html"; //origen HTML a procesar. $useUTFdec = true;
  //$filename = "../Whirlpool_AWE_2239__Opinion_2138051.html";  
  
  //sleep(2);
  
  //print "\nProcessing ".$filename.". ";
  
  /** 1a Parte. Quitamos los tags HTML *********************************************/

  $hndIn = fopen($html_path."/".$filename, "r");
  $hndOut = fopen($xml_path."/".$filename.".tmp", "w");

  print "\nRemoving tags...";
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

  /*fprintf ($hndOut, '<?xml version="1.0" encoding="utf-8"?>'); */ //XML Header
  fprintf ($hndOut, '<?xml version="1.0" encoding="iso-8859-1" ?>'); //XML Header
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
							 /*print "\n[STARS]".$lasttt2."[/STARS]";*/
							 fprintf ($hndOut, "\n<stars>\n".$lasttt2."\n</stars>");
						 }
					     $tagtok1 = strtok(' ');
				     }
				     $tagFrom = "userstars";
			      }
            }
            if ($user_title !== false) { $xml_title = true; /*print "\n[TITLE]";*/ $tagFrom = "optitle"; fprintf ($hndOut, "\n<optitle>\n"); }
            if ($user_date  !== false) { $xml_date = true; /*print "\n[DATE]";*/ fprintf ($hndOut, "\n<opdate>\n"); }
            if ($user_desc  !== false) { $xml_desc = true; /*print "\n[DESC]";*/ fprintf ($hndOut, "\n<description>\n"); }
            if ($user_pros  !== false) { $xml_pros = true; /*print "\n[PROS]";*/ fprintf ($hndOut, "\n<pros>\n"); }
            if ($user_cons  !== false) { $xml_cons = true; /*print "\n[CONS]";*/ fprintf ($hndOut, "\n<cons>\n"); }
            if ($user_reco  !== false) { $xml_reco = true; /*print "\n[RECO]";*/ fprintf ($hndOut, "\n<recomendable>\n"); }
            if ($user_uname !== false) { $xml_uname = true; /*print "\n[USR]";*/ fprintf ($hndOut, "\n<reviser>\n"); }
            if ($user_abouttext !== false) { $xml_about = true; /*print "\n[ABOUT]";*/ fprintf ($hndOut, "\n<personaltext>\n"); }
            if ($user_stats !== false) { $xml_ustats = true; /*print "\n[USTATS]";*/ }
            if ($user_rating !== false){ $xml_rating = true; /*print "\n[RATING]";*/ fprintf ($hndOut, "\n<rating>\n"); }
            
            if ($xml_rating == true and $user_oprat !== false) 
            { 
				$tagtok1 = strtok($tag, ':>');
				$tagtok1 = strtok(':>');
				$tagtok1 = rtrim($tagtok1, '"');
				$tagtok1 = rtrim($tagtok1, '%');
				$globalStr .= $tagtok1;
            }
            
            if ($tag_span_end !== false and $xml_title == true) { $xml_title = false; p(); /*print "[/TITLE]";*/ fprintf ($hndOut, "\n</optitle>"); }
            if ($tag_span_end !== false and $xml_date  == true) { $xml_date  = false; p(); /*print "[/DATE]";*/  fprintf ($hndOut, "\n</opdate>");}
            if ($tag_span_end !== false and $xml_desc  == true) { $xml_desc  = false; p(); /*print "[/DESC]";*/  fprintf ($hndOut, "\n</description>");}
            if ($tag_span_end !== false and $xml_uname  == true) { $xml_uname  = false; p(); /*print "[/USR]";*/ fprintf ($hndOut, "\n</reviser>"); }
            if ($tag_span_end !== false and $xml_about  == true) { $xml_about  = false; p("about"); /*print "[/ABOUT]";*/ fprintf ($hndOut, "</personaltext>");}
            
            if ($tag_p_end !== false and $xml_pros  == true) { $xml_pros = false; p("pros"); /*print "[/PROS]";*/ fprintf ($hndOut, "\n</pros>");}
            if ($tag_p_end !== false and $xml_cons  == true) { $xml_cons = false; p("cons"); /*print "[/CONS]";*/  fprintf ($hndOut, "\n</cons>");}
            if ($tag_p_end !== false and $xml_reco  == true) { $xml_reco = false; p("reco"); /*print "[/RECO]";*/ fprintf ($hndOut, "\n</recomendable>");}
            
            if ($tag_div_end !== false and $xml_ustats == true) { $xml_ustats = false; p("ust"); /*print "[/USTATS]";*/  }
            
            if ($user_qltprc !== false and $xml_rating == true) { $xml_rating = false; p("rat"); /*print "[/RATING]";*/ fprintf ($hndOut, "\n</rating>");
                                                                  $xml_qlpr   = true;            /*print "\n[QLPR]";*/  fprintf ($hndOut, "\n<qualityprice>\n");}
                                                                  
            if ($tag_div_end !== false and $xml_qlpr == true)  { $xml_qlpr = false; p("qlpr"); /*print "[/QLPR]";*/  fprintf ($hndOut, "\n</qualityprice>");}
            
            if ($tag_p_start  !== false and $xml_desc == true) { $globalStr .= "\n"; /*print "\n[P]"; */}
            if ($tag_p_end    !== false and $xml_desc == true) { $globalStr .= "\n"; /*print "[/P]\n";*/ }
            if ($tag_br_start !== false and $xml_desc == true) { $globalStr .= "\n"; /*print "\n[BR]";*/ }

      }
      
      if ($char != '>')
      { 
		  if ($xml_title OR $xml_date OR $xml_pros OR $xml_cons OR $xml_reco OR $xml_uname OR $xml_ustats OR
		      $xml_rating OR $xml_qlpr OR $xml_rating OR $xml_desc OR $xml_about)  
		  {
		      $globalStr .= $char;
		  }
      }
  }

 fprintf ($hndOut, "\n</opinion>");
 fclose ($hndIn);
 fclose ($hndOut);
 
 
 
  /** 2da Parte. Cambiamos códigos & por acentos ***********************************/

 $hndIn = fopen($xml_path."/".$filename.".tmp", "r");
 $hndOut = fopen($xml_path."/".$filename.".xml", "w");

 print "\nNormalizing...";
 $theLine = 0;
 while (!feof($hndIn))
 {
        $theLine++;
        $buff = fgets($hndIn);
        //if ($useUTFdec)
        if (mb_check_encoding($globalStr, 'UTF-8'))
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
        
        
        /*
        $buff = str_replace('&#39;', "'", $buff);
        $buff = str_replace('&#149;', "•", $buff);
        $buff = str_replace('&#1136;', "Ѱ", $buff);
        $buff = str_replace('&#8195;', " ", $buff);
        $buff = str_replace('&#8211;', "–", $buff);
        $buff = str_replace('&#8220;', '"', $buff);
        $buff = str_replace('&#8221;', '"', $buff);
        $buff = str_replace('&#8364;', "€", $buff);
        $buff = str_replace('&#9472;', '─', $buff);
        $buff = str_replace('&#9632;', "■", $buff);
        $buff = str_replace('&#9633;', "□", $buff);
        $buff = str_replace('&#9644;', '▬', $buff);
        $buff = str_replace('&#9658;', '►', $buff);
        $buff = str_replace('&#9668;', '◄', $buff);
        $buff = str_replace('&#9679;', '●', $buff);
        $buff = str_replace('&#9689;', '◙', $buff);
        $buff = str_replace('&#9788;', '☼', $buff);
        $buff = str_replace('&#61514;', '', $buff);
        */
        
        
        $buff = str_replace('&#130;', ',', $buff);
        $buff = str_replace('&#161;', '¡', $buff);
        $buff = str_replace('&#163;', '£', $buff);
        $buff = str_replace('&#164;', '*', $buff);
        $buff = str_replace('&#173;', '-', $buff);
        $buff = str_replace('&#191;', '¿', $buff);
        $buff = str_replace('&#009;', '',  $buff); //tab ¿?
        $buff = str_replace('&#237;', 'í',  $buff);

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

        //$buff = ltrim($buff);
        
        if ($buff != "")
          fwrite($hndOut, $buff);
 }
 fclose ($hndIn);
 fclose ($hndOut);

 unlink($xml_path."/".$filename.".tmp"); //Borramos

 print "\nDone!";
 
 
 
 
 
 

 print "\n";
 fprintf ($hndLog, "\n");

  $fc++;
  
        }
	}
}
fclose($hndLog);

 function p($something = "nothing")
  {
     global $globalStr, $hndOut;

	 if ($something == "pros")
	 {
		 if (!mb_check_encoding($globalStr, 'UTF-8'))
            fprintf ($hndOut, trim(substr(trim($globalStr),strlen("Ventajas:"), strlen($globalStr))));
         else
	        fprintf ($hndOut, utf8_encode(trim(substr(trim($globalStr),strlen("Ventajas:"), strlen($globalStr)))));
	     $globalStr = ""; return 0;
	 }
	 if ($something == "cons")
	 {
		 if (!mb_check_encoding($globalStr, 'UTF-8'))
		    fprintf ($hndOut, trim(substr(trim($globalStr),strlen("Desventajas:"), strlen($globalStr))));
		 else
		    fprintf ($hndOut, utf8_encode(trim(substr(trim($globalStr),strlen("Desventajas:"), strlen($globalStr)))));
	     $globalStr = ""; return 0;
	 }
	 if ($something == "reco")
	 {
		 if (!mb_check_encoding($globalStr, 'UTF-8'))
	        fprintf ($hndOut, trim(substr(trim($globalStr),strlen("Recomendable:"), strlen($globalStr))));
	     else
	        fprintf ($hndOut, utf8_encode(trim(substr(trim($globalStr),strlen("Recomendable:"), strlen($globalStr)))));
	     $globalStr = ""; return 0;
	 }
	 if ($something == "qlpr")
	 {
		 if (!mb_check_encoding($globalStr, 'UTF-8'))
		    fprintf ($hndOut, trim(substr(trim($globalStr),strlen("Relación calidad precio:")-1, strlen($globalStr))));
		 else
	        fprintf ($hndOut, utf8_encode(trim(substr(trim($globalStr),strlen("Relación calidad precio:")-1, strlen($globalStr)))));
	     $globalStr = ""; return 0;
	 }
	 if ($something == "ust")
	 {
		 $Ud = stripos($globalStr, "usuario desde:");
		 if ($Ud !== false)
	       $globalStr = substr_replace($globalStr, "<usersince>\n", $Ud, strlen("usuario desde:"));
	     
		 $To = stripos($globalStr, "Opiniones:");
		 if ($To !== false)
	       $globalStr = substr_replace($globalStr, "<totalopinions>\n", $To, strlen("Opiniones:"));
	     
	     $Co = stripos($globalStr, "Confianza conseguida:");
	     if ($Co !== false)
	       $globalStr = substr_replace($globalStr, "<confidence>\n", $Co, strlen("Confianza conseguida:"));
         
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
		 
	     $globalStr = ""; return 0;
	 }
	 
	 if ($something == "about")
	 {
		 if (trim($globalStr) != "")
		 {
			 if (!mb_check_encoding($globalStr, 'UTF-8'))
			   fprintf ($hndOut, $globalStr."\n");
			 else
		       fprintf ($hndOut, utf8_encode($globalStr)."\n");
		 }
		$globalStr = "";
		return 0;
	 }

	 if (!mb_check_encoding($globalStr, 'UTF-8'))
         fwrite ($hndOut, trim(($globalStr)));
     else
	     fwrite ($hndOut, utf8_encode($globalStr));
	 //fwrite ($hndOut, trim(utf8_encode($globalStr)));
	 $globalStr = "";
  }
  
?>
