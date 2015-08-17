<?php
/*
 * Este módulo revisa los Hits de google de la lista de bigramas
 * que no estuvieron bien calculados.
 * El formato debe ser:
 * 
 * word1 word2 hitsPositive hitsNegative semanticOrientation
 * 
 * Los bigramas que no cumplan ese formato (cuando falte alguno de los
 * 5 elementos) se recalculan.
 * 
 */

 print "Loading bigrams rank... \n";
 $bigList = array();
 $hndBL = fopen ("bigrams.rank", "r");
 $hndOut = fopen ("left_bg.lst", "w");
 $c = 1;
 $l = 1;
 while(!feof($hndBL))
 {
    $line = fgets($hndBL);
    if (trim($line))
    {
	   $tok1 = strtok($line, " \n");
	   $tok2 = strtok(" \n");
	   $tok3 = strtok(" \n");
	   $tok4 = strtok(" \n");
	   $tok5 = strtok(" \n");
	   if (trim($tok1) == "" OR trim($tok2) == "" OR trim($tok3) == "" OR trim($tok4) == "" OR trim($tok5) == "")
	   {
          print $l." (".$c.") [".$tok1."] "." [".$tok2."] "." [".$tok3."] "." [".$tok4."] "." [".$tok5."] "."\n";
          fwrite ($hndOut, $tok1." ".$tok2."\n");
          $l++;
       }
    }
    $c++;
 }
 fclose($hndBL);
 fclose($hndOut);

?>
