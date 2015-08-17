<?php
/* 
 * Crea una lista de palabras únicas de todo el sistema
 * 
 */

 $fso_path = './OpinionsTAG';
 
 $fExt = "tag";
 
 $arWords = array();
 $arWordsUniq = array();

 $fc = 1;

 if ($handle = opendir($fso_path))
 {
    $hff = 0;
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == $fExt)
         $hff++;
    }
    closedir($handle);
 }

print "Counting words...";

$lc = 0;
if ($handle = opendir($fso_path)) //Cuenta el número de palabras totales del sistema. Incluye signos de puntuación.
{
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == $fExt)
        {  
            $filename = basename($entry, ".".$fExt);
            //print "$fc. $filename: ";
            
            $perc = ($fc * 100) / $hff;
            $nf = number_format($perc, 2);
            print "[$fc / $hff | ".$nf."%] Processing ".$filename.".".$fExt.". ";
            //fprintf ($hndAc, "Processing ".$filename.".2gm. [$fc / $hff | ".$nf."%%]");
            //fprintf ($hndAc, $fc.". ".$filename.".".$fExt." ");
            
            $hndIn = fopen($fso_path."/".$filename.".".$fExt, "r");
            while (($buffer = fgets($hndIn, 4096)) !== false)
            {
               if (trim($buffer) != "")
               {
				  $tok = strtok($buffer, " \n\r\t");
				  //print $tok." ";
				  $arWords[] = $tok;
		          $lc++;
	           }
            } 
            fclose($hndIn);
            
            print $lc."\n";
            print ".";
            
            $fc++;
	    }
	}
}

print " done!";

print "\nCreating unique words array...";

$arWordsUniq = $ar1a = array_unique($arWords);
$nlc = count($arWordsUniq);
print " done!";

print "\nSorting... ";
sort($arWordsUniq);
print " done!";

print "\nWriting to file...";

$hndOut = fopen ("full_words.lst", "w");
$wc = 1;
foreach ($arWordsUniq as $arIval)
{
   $perc = ($wc * 100) / $nlc;
   $nw = number_format($perc, 2);
   print "\nWriting ".$nw."%";
   if  ($wc > 1)
     fwrite($hndOut, $arIval."\n");
   $wc++;
} 
fclose($hndOut);

//print "[".$arWordsUniq[0]."]";

?>
