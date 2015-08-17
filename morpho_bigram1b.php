<?php
/* Indesit_W_93__Opinion_1180152.2gm
 */

 $tag_path = './OpinionsTAG';
 $mfb_path = './OpinionsMFB';

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

$hndLog = fopen("parse_morpho2gm.log", "w");
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
   $fname2 = $mfb_path."/".$filename.".2gm";
   
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

   for ($j = 0; $j < count($bgm_array)-2; $j++)
   {
	   $rule = $bgm_array[$j+0][1]{0}.$bgm_array[$j+1][1]{0}.$bgm_array[$j+2][1]{0};

	   if ($rule{0} == 'a' and $rule{1} == 'n') //Adjetivo Nombre Cualquiera
	   {
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$rule."\n");
		   //print "\n[".$bgm_array[$j+0][0]."][".$bgm_array[$j+1][0]."]"." - [$rule]"; 
	   }
	   if ($rule{0} == 'n' and $rule{1} == 'a' and $rule{2} != 'n') //Nombre Adjetivo No es nombre
	   {
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$rule."\n");
		   //print "\n[".$bgm_array[$j+0][0]."][".$bgm_array[$j+1][0]."]"." - [$rule]"; 
	   }
	   if ($rule{0} == 'r' and $rule{1} == 'a' and $rule{2} != 'n') //Adverbio Adjetivo No es nombre
	   {
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$rule."\n");
		   //print "\n[".$bgm_array[$j+0][0]."][".$bgm_array[$j+1][0]."]"." - [$rule]"; 
	   }
	   if ($rule{0} == 'r' and $rule{1} == 'v') //Adverbio Verbo Cualquiera
	   {
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$rule."\n");
		   //print "\n[".$bgm_array[$j+0][0]."][".$bgm_array[$j+1][0]."]"." - [$rule]"; 
	   }
	   if ($rule{0} == 'v' and $rule{1} == 'r') //Verbo Adverbio Cualquiera
	   {
		   fwrite($hndOut, $bgm_array[$j+0][0]." ".$bgm_array[$j+1][0]." ".$rule."\n");
		   //print "\n[".$bgm_array[$j+0][0]."][".$bgm_array[$j+1][0]."]"." - [$rule]"; 
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
