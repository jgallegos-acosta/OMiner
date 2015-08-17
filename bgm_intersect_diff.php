<?php
/* 
 * Módulo para verificar la intersección y diferencia de la lista de bigramas vs bigramas negativos
 */

 $hndIn1 = fopen ("bigrams.lst", "r");
 $hndIn2 = fopen ("negbigrams.lst", "r");
 
 $ar1 = array();
 $ar2 = array();
 $arIntersect = array();
 $arDiff1to2 = array();
 
 print "Loading bigrams...";
 
 while (($buffer = fgets($hndIn1, 4096)) !== false)
 {
     $buffer = trim($buffer);
     $ar1[] = $buffer;
 }
 
 fclose($hndIn1);

 print "\n Done. \n Loading negative bigrams...";
 
 while (($buffer = fgets($hndIn2, 4096)) !== false)
 {
     $buffer = trim($buffer);
     $ar2[] = $buffer;
 }

 fclose($hndIn2);
 
 print "\n Done. \nIntersecting...";
 $arIntersect = array_intersect($ar1, $ar2);
 
 //--- Intersección
 $hndOut = fopen("bg_intersection.lst", "w");
 /*
 for ($i = 0; $i < count($arIntersect); $i++)
 {
   fwrite($hndOut,$arIntersect[$i]."\n");
 } 
 */
 foreach ($arIntersect as $arIval)
 {
	 fwrite($hndOut, $arIval."\n");
 }
 fclose($hndOut);
 
 print "COUNT: ".count($arIntersect).". \nDifference...";
 
 $arDiff1to2 = array_diff($ar2, $ar1);
 
 //--- Diferencia
 $hndOut = fopen("bg_difference.lst", "w");
 
 foreach ($arDiff1to2 as $arIval)
   fwrite($hndOut, $arIval."\n");
   
 fclose($hndOut);
 
 print "COUNT: ".(count($arDiff1to2));
 
 print "\nDone.";
 
 unset($ar1);
 unset($ar2);
 unset($arIntersect);

?>
