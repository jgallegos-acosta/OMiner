<?php
/*
   Módulo que extrae bigramas "negativos" complementarios 
   a las opiniones
    
   --> extrae 5-gramas para abarcar todas las reglas de los negativos
 
 */

 $tag_path = './OpinionsTAG';
 $mfb_path = './OpinionsNBG';

 $fc = 1;

if ($handle = opendir($tag_path)) 
{
    $hff = 0;
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'tag')
         $hff++;
    }
    closedir($handle);
}

$time_start = microtime(true);

$hndLog = fopen("parse_morphonbg.log", "w");
if ($handle = opendir($tag_path)) 
{
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'tag')
        {  
            $filename = basename($entry, ".tag");

            $perc = ($fc * 100) / $hff;
            $nf = number_format($perc, 2);
            print "[$fc / $hff | ".$nf."%] Processing ".$filename.".tag. ";
            fprintf ($hndLog, "Processing ".$filename.".txt. [$fc / $hff | ".$nf."%%]");

            print "\nParsing morphological bigrams...";
            fprintf ($hndLog, " Parsing bigrams...");


   $fname = $tag_path."/".$filename.".tag";
   $fname2 = $mfb_path."/".$filename.".nbg";
   
   $hndIn = fopen($fname, "r");
   $hndOut = fopen($fname2, "w");
   $bgm_array = array(array());
   $c = 1;
   while (($buffer = fgets($hndIn, 4096)) !== false)
   {
	  if (trim($buffer))
      {
         $tok = strtok($buffer, " ");
         $tc = 0;
         $ac = 0;
         while ($tok !== false)
         {
			$tc++;
            if ($tc == 1 or $tc == 3)
            {
			   $bgm_array[$c-1][$ac] = trim(strToLowerSP($tok));
			   $ac++;
			}
            $tok = strtok(" ");
         }
         $c++;
	  }
   }

   for ($j = 0; $j < count($bgm_array)-4; $j++)  //Con -2 extrae trigramas, con -3 cuatrigramas, etc.
   {
	   
/*

        ninguna DI0MS0    XXX XXX NCXX000
         ningún DI0MS0    XXX XXX NCXX000
        ninguno DI0MS0    XXX XXX NCXX000
   nada    nada PI0CS000  XXX XXX AQ0XX0
   jamàs jamás RG  XXX XXX VMXXXX0
   nunca nunca RG  XXX XXX VMXXXX0
      no    no RN  XXX XXX VMXXXX0
  jamás jamás RG  YYY YYY VAXXXX0   XXX XXX VMP00XX 
  nunca nunca RG  YYY YYY VAXXXX0   XXX XXX VMP00XX 
        no no RN  YYY YYY VAXXXX0   XXX XXX VMP00XX 
        no no RN   YY  YY PPXXXX00  XXX XXX VMXXXX0 
        no no RN   YY  YY PPXXXX00  XXX XXX VMXXXX0

 nunca nunca RG  YY YY PPXXXX00  YYY YYY VAXXXX0  XXX XXX VMP00XX
 nunca nunca RG  YY YY P00XX000  YYY YYY VAXXXX0  XXX XXX VMP00XX
 no no RN  YY YY P00XX000  YY YY PPXXXX00  YYY YYY VAXXXX0  XXX XXX VMP00XX

no se la
no se las
no se le
no se lo
no se me


nunca la había
nunca la he
nunca la hubiese
nunca las había
nunca le ha
nunca le he
nunca lo había
nunca lo he
nunca lo hemos
nunca los he
nunca me ha
nunca me había
nunca me han
nunca me he
nunca me hubiera
nunca nos ha
nunca nos hemos
nunca se ha
nunca se han

 */
	   
	   
	   if (($bgm_array[$j+0][0] == "ninguna" AND $bgm_array[$j+0][1] == "di0ms0") AND
	      ($bgm_array[$j+1][1][0] == "n" AND $bgm_array[$j+1][1][1] == "c" AND $bgm_array[$j+1][1][4] == "0" AND $bgm_array[$j+1][1][5] == "0" AND $bgm_array[$j+1][1][6] == "0"))
       {
		   //fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$rule."\n");
		   print "\n --> Regla 1: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]."\n");
	   }

	   if (($bgm_array[$j+0][0] == "ningún" AND $bgm_array[$j+0][1] == "di0ms0") AND
	      ($bgm_array[$j+1][1][0] == "n" AND $bgm_array[$j+1][1][1] == "c" AND $bgm_array[$j+1][1][4] == "0" AND $bgm_array[$j+1][1][5] == "0" AND $bgm_array[$j+1][1][6] == "0"))
       {
		   print "\n --> Regla 2: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]."\n");
	   }
	   
	   if (($bgm_array[$j+0][0] == "ninguno" AND $bgm_array[$j+0][1] == "di0ms0") AND
	      ($bgm_array[$j+1][1][0] == "n" AND $bgm_array[$j+1][1][1] == "c" AND $bgm_array[$j+1][1][4] == "0" AND $bgm_array[$j+1][1][5] == "0" AND $bgm_array[$j+1][1][6] == "0"))
       {
		   print "\n --> Regla 3: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]."\n");
	   }


	   if (($bgm_array[$j+0][0] == "nada" AND $bgm_array[$j+0][1] == "pi0cs000") AND
	      ($bgm_array[$j+1][1][0] == "a" AND $bgm_array[$j+1][1][1] == "q" AND $bgm_array[$j+1][1][2] == "0" AND $bgm_array[$j+1][1][5] == "0"))
       {
		   print "\n --> Regla 4: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]."\n");
	   }

	   if (($bgm_array[$j+0][0] == "jamás" AND $bgm_array[$j+0][1] == "rg") AND
	      ($bgm_array[$j+1][1][0] == "v" AND $bgm_array[$j+1][1][1] == "m" AND $bgm_array[$j+1][1][6] == "0"))
       {
		   print "\n --> Regla 5: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]."\n");
	   }

	   if (($bgm_array[$j+0][0] == "nunca" AND $bgm_array[$j+0][1] == "rg") AND
	      ($bgm_array[$j+1][1][0] == "v" AND $bgm_array[$j+1][1][1] == "m" AND $bgm_array[$j+1][1][6] == "0"))
       {
		   print "\n --> Regla 6: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]."\n");
	   }
	   
	   if (($bgm_array[$j+0][0] == "no" AND $bgm_array[$j+0][1] == "rn") AND
	      ($bgm_array[$j+1][1][0] == "v" AND $bgm_array[$j+1][1][1] == "m" AND $bgm_array[$j+1][1][6] == "0"))
       {
		   print "\n --> Regla 7: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]."\n");
	   }
	   
	   
	   

	   if (($bgm_array[$j+0][0] == "jamás" AND $bgm_array[$j+0][1] == "rg") AND
	      ($bgm_array[$j+1][1][0] == "v" AND $bgm_array[$j+1][1][1] == "a" AND $bgm_array[$j+1][1][6] == "0") AND
	      ($bgm_array[$j+2][1][0] == "v" AND $bgm_array[$j+2][1][1] == "m" AND $bgm_array[$j+2][1][2] == "p" AND $bgm_array[$j+2][1][3] == "0" AND $bgm_array[$j+2][1][4] == "0"))
       {
		   print "\n --> Regla 8: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
	   }
	   
	   if (($bgm_array[$j+0][0] == "nunca" AND $bgm_array[$j+0][1] == "rg") AND
	      ($bgm_array[$j+1][1][0] == "v" AND $bgm_array[$j+1][1][1] == "a" AND $bgm_array[$j+1][1][6] == "0") AND
	      ($bgm_array[$j+2][1][0] == "v" AND $bgm_array[$j+2][1][1] == "m" AND $bgm_array[$j+2][1][2] == "p" AND $bgm_array[$j+2][1][3] == "0" AND $bgm_array[$j+2][1][4] == "0"))
       {
		   print "\n --> Regla 9: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
		   anykey();
	   }

	   if (($bgm_array[$j+0][0] == "no" AND $bgm_array[$j+0][1] == "rn") AND
	      ($bgm_array[$j+1][1][0] == "v" AND $bgm_array[$j+1][1][1] == "a" AND $bgm_array[$j+1][1][6] == "0") AND
	      ($bgm_array[$j+2][1][0] == "v" AND $bgm_array[$j+2][1][1] == "m" AND $bgm_array[$j+2][1][2] == "p" AND $bgm_array[$j+2][1][3] == "0" AND $bgm_array[$j+2][1][4] == "0"))
       {
		   print "\n --> Regla 10: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
		   anykey();
	   }

	   if (($bgm_array[$j+0][0] == "no" AND $bgm_array[$j+0][1] == "rn") AND
	      ($bgm_array[$j+1][1][0] == "p" AND $bgm_array[$j+1][1][1] == "p" AND $bgm_array[$j+1][1][6] == "0" AND $bgm_array[$j+1][1][7] == "0") AND
	      ($bgm_array[$j+2][1][0] == "v" AND $bgm_array[$j+2][1][1] == "m" AND $bgm_array[$j+2][1][6] == "0"))
       {
		   print "\n --> Regla 12: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
		   anykey();
	   }

	   if (($bgm_array[$j+0][0] == "no" AND $bgm_array[$j+0][1] == "rn") AND
	      ($bgm_array[$j+1][1][0] == "p" AND $bgm_array[$j+1][1][1] == "0" AND $bgm_array[$j+1][1][2] == "0" AND $bgm_array[$j+1][1][5] == "0" AND $bgm_array[$j+1][1][6] == "0" AND $bgm_array[$j+1][1][7] == "0") AND
	      ($bgm_array[$j+2][1][0] == "v" AND $bgm_array[$j+2][1][1] == "m" AND $bgm_array[$j+2][1][6] == "0"))
       {
		   print "\n --> Regla 14: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
		   anykey();
	   }
	   




       //11.  nunca nunca  RG        YY YY   PPXXXX00   YYY YYY VAXXXX0    XXX XXX VMP00XX
	   if (($bgm_array[$j+0][0] == "nunca" AND $bgm_array[$j+0][1] == "rg")  AND
	       ($bgm_array[$j+1][1][0] == "p"  AND $bgm_array[$j+1][1][1] == "p" AND $bgm_array[$j+1][1][6] == "0"  AND $bgm_array[$j+1][1][7] == "0") AND
	       ($bgm_array[$j+2][1][0] == "v"  AND $bgm_array[$j+2][1][1] == "a" AND $bgm_array[$j+2][1][6] == "0") AND
	       ($bgm_array[$j+3][1][0] == "v"  AND $bgm_array[$j+3][1][1] == "m" AND $bgm_array[$j+3][1][2] == "p"  AND $bgm_array[$j+3][1][3] == "0"  AND $bgm_array[$j+3][1][4] == "0"))
       {
		   print "\n --> Regla 11: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]." ".$bgm_array[$j+3][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]." ".$bgm_array[$j+3][0]."\n");
		   anykey();
	   }
	   
      //13.  nunca nunca  RG        YY YY   P00XX000   YYY YYY VAXXXX0    XXX XXX VMP00XX
	   if (($bgm_array[$j+0][0] == "nunca"  AND $bgm_array[$j+0][1] == "rg")  AND
	       ($bgm_array[$j+1][1][0] == "p"   AND $bgm_array[$j+1][1][1] == "0" AND $bgm_array[$j+1][1][2] == "0"  AND $bgm_array[$j+1][1][5] == "0" AND $bgm_array[$j+1][1][6] == "0" AND $bgm_array[$j+1][1][7] == "0") AND
	       ($bgm_array[$j+2][1][0] == "v"   AND $bgm_array[$j+2][1][1] == "a" AND $bgm_array[$j+2][1][6] == "0") AND
	       ($bgm_array[$j+3][1][0] == "v"   AND $bgm_array[$j+3][1][1] == "m" AND $bgm_array[$j+3][1][2] == "p"  AND $bgm_array[$j+3][1][3] == "0" AND $bgm_array[$j+3][1][4] == "0"))
       {
		   print "\n --> Regla 13: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]." ".$bgm_array[$j+3][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]." ".$bgm_array[$j+3][0]."\n");
		   anykey();
	   }
	   
	   //15.  no no        RN        YY YY   P00XX000   YYY YYY PPXXXX00   YYY YYY VAXXXX0  XXX XXX VMP00XX 
	   if (($bgm_array[$j+0][0] == "no"   AND $bgm_array[$j+0][1] == "rn")  AND
	       ($bgm_array[$j+1][1][0] == "p" AND $bgm_array[$j+1][1][1] == "0" AND $bgm_array[$j+1][1][2] == "0"  AND $bgm_array[$j+1][1][5] == "0" AND $bgm_array[$j+1][1][6] == "0"  AND $bgm_array[$j+1][1][7] == "0") AND
	       ($bgm_array[$j+2][1][0] == "p" AND $bgm_array[$j+2][1][1] == "p" AND $bgm_array[$j+2][1][6] == "0" AND $bgm_array[$j+2][1][7] == "0") AND
	       ($bgm_array[$j+3][1][0] == "v" AND $bgm_array[$j+3][1][1] == "a" AND $bgm_array[$j+3][1][6] == "0") AND
	       ($bgm_array[$j+4][1][0] == "v" AND $bgm_array[$j+4][1][1] == "m" AND $bgm_array[$j+4][1][2] == "p"  AND $bgm_array[$j+4][1][3] == "0" AND $bgm_array[$j+4][1][4] == "0"))
       {
		   print "\n --> Regla 15: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]." ".$bgm_array[$j+3][0]." ".$bgm_array[$j+4][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]." ".$bgm_array[$j+3][0]." ".$bgm_array[$j+4][0]."\n");
		   anykey();
	   }

   }


   fclose($hndIn);
   fclose($hndOut);

   unset($bgm_array);
            

            print "done ";
            fprintf ($hndLog, " done ");
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            print "[".number_format($time, 2)." s] \n";
            fprintf ($hndLog, "[".number_format($time, 2)." s] \n");
            $fc++;

         }
    }
}

$time_end = microtime(true);
$time = $time_end - $time_start;
print "Total Time [".number_format($time, 2)." s]";
fprintf ($hndLog, "Total Time [".number_format($time, 2)." s]");

fclose($hndLog);

exit(0);

function anykey($s='Press any key to continue...') 
{
    //echo "\n".$s."\n";
    //fgetc(STDIN);
}  
   
function strToLowerSP($string)
{
     $string = strtolower($string);
     $patterns[0] = '/Á/';
     $patterns[1] = '/É/';
     $patterns[2] = '/Í/';
     $patterns[3] = '/Ó/';
     $patterns[4] = '/Ú/';
     $patterns[5] = '/Ñ/';
     $patterns[6] = '/Ü/';
     $replacements[0] = 'á';
     $replacements[1] = 'é';
     $replacements[2] = 'í';
     $replacements[3] = 'ó';
     $replacements[4] = 'ú';
     $replacements[5] = 'ñ';
     $replacements[6] = 'ü';
     $string = preg_replace($patterns, $replacements, $string);
     return $string;
}   
   
?>
