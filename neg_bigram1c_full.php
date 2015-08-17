<?php
/*
   Módulo que extrae bigramas "negativos" complementarios 
   a las opiniones
 
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

   for ($j = 0; $j < count($bgm_array)-2; $j++)  //Con -2 extrae trigramas, con -3 cuatrigramas, etc.
   {
	   
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
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
	   }
	   
	   if (($bgm_array[$j+0][0] == "nunca" AND $bgm_array[$j+0][1] == "rg") AND
	      ($bgm_array[$j+1][1][0] == "v" AND $bgm_array[$j+1][1][1] == "a" AND $bgm_array[$j+1][1][6] == "0") AND
	      ($bgm_array[$j+2][1][0] == "v" AND $bgm_array[$j+2][1][1] == "m" AND $bgm_array[$j+2][1][2] == "p" AND $bgm_array[$j+2][1][3] == "0" AND $bgm_array[$j+2][1][4] == "0"))
       {
		   print "\n --> Regla 9: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
	   }

	   if (($bgm_array[$j+0][0] == "no" AND $bgm_array[$j+0][1] == "rn") AND
	      ($bgm_array[$j+1][1][0] == "v" AND $bgm_array[$j+1][1][1] == "a" AND $bgm_array[$j+1][1][6] == "0") AND
	      ($bgm_array[$j+2][1][0] == "v" AND $bgm_array[$j+2][1][1] == "m" AND $bgm_array[$j+2][1][2] == "p" AND $bgm_array[$j+2][1][3] == "0" AND $bgm_array[$j+2][1][4] == "0"))
       {
		   print "\n --> Regla 10: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
	   }

	   if (($bgm_array[$j+0][0] == "no" AND $bgm_array[$j+0][1] == "rn") AND
	      ($bgm_array[$j+1][1][0] == "p" AND $bgm_array[$j+1][1][1] == "p" AND $bgm_array[$j+1][1][6] == "0" AND $bgm_array[$j+1][1][7] == "0") AND
	      ($bgm_array[$j+2][1][0] == "v" AND $bgm_array[$j+2][1][1] == "m" AND $bgm_array[$j+2][1][6] == "0"))
       {
		   print "\n --> Regla 12: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
	   }

	   if (($bgm_array[$j+0][0] == "no" AND $bgm_array[$j+0][1] == "rn") AND
	      ($bgm_array[$j+1][1][0] == "p" AND $bgm_array[$j+1][1][1] == "0" AND $bgm_array[$j+1][1][2] == "0" AND $bgm_array[$j+1][1][5] == "0" AND $bgm_array[$j+0][1][6] == "0" AND $bgm_array[$j+0][1][7] == "0") AND
	      ($bgm_array[$j+2][1][0] == "v" AND $bgm_array[$j+2][1][1] == "m" AND $bgm_array[$j+2][1][6] == "0"))
       {
		   print "\n --> Regla 14: ";
		   print $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0];
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$bgm_array[$j+2][0]."\n");
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
