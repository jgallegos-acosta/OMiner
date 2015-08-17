<?php
/* 
 * Módulo para verificar la intersección y diferencia de la lista de opiniones vs la lista de entrenamiento
 */

 $hndIn1 = fopen ("train_sets.lst", "r");
 $hndIn2 = fopen ("opinions_utf.lst", "r");
 
 $ar1 = array();
 $ar2 = array();
 $arIntersect = array();
 $arDiff1to2 = array();
 
 print "Loading opinions...";
 
 while (($buffer = fgets($hndIn1, 4096)) !== false)
 {
     $buffer = trim($buffer);
     $buffer = strtok($buffer, " \n\r\t");
     $buffer = strtok(" \n\r\t");
     $ar1[] = $buffer;
 }
 
 fclose($hndIn1);
//var_dump($ar1);exit(0); 
 print "\n Done. \n Loading training set...";
 
 while (($buffer = fgets($hndIn2, 4096)) !== false)
 {
     $buffer = trim($buffer);
     $buffer = strtok($buffer, " \n\r\t");
     $buffer = strtok(" \n\r\t");
     $ar2[] = $buffer;
 }

 fclose($hndIn2);
//var_dump($ar2);exit(0);
 print "\n Done. \nIntersecting...";
 $arIntersect = array_intersect($ar1, $ar2);
 
 //--- Intersección
 $hndOut = fopen("op_utf_inter.lst", "w");
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
 $hndOut = fopen("op_utf_diff.lst", "w");
 
 foreach ($arDiff1to2 as $arIval)
   fwrite($hndOut, $arIval."\n");
   
 fclose($hndOut);
 
 print "COUNT: ".(count($arDiff1to2));
 
 print "\nDone.";
 
 unset($ar1);
 unset($ar2);
 unset($arIntersect);

?>
