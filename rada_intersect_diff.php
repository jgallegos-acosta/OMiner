<?php
/* 
 * Módulo para verificar la intersección y diferencia de la lista de Rada vs 
 * 1. la lista de bigramas
 * 2. la lista completa de palabras únicas
 */
 
 $fullOrBG = "bigrams";
 
if ($fullOrBG != "bigrams")
{
 //--- Palabras de todo el sistema
 $fileDiff      = "rada_full_diff.lst";
 $fileIntersect = "rada_full_intersect.lst";

 $hndIn1 = fopen ("full_words.lst", "r");
 $hndIn2 = fopen ("fullStrengthLexicon.txt", "r");
}
else
{
 //--- Solo las palabras de los bigramas
 $fileDiff      = "rada_diff.lst";
 $fileIntersect = "rada_intersect.lst";

 $hndIn1 = fopen ("wordList.lst", "r");
 $hndIn2 = fopen ("fullStrengthLexicon.txt", "r");
}

 $ar1 = array();
 $ar1a = array();
 $ar2 = array();
 $arIntersect = array();
 $arDiff1to2 = array();
 
 print "Loading word list...";
 
 while (($buffer = fgets($hndIn1, 4096)) !== false)
 {
     $buffer = trim($buffer);
     $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                             'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                             'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                             'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                             'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
     $buffer = strToLowerSP($buffer);
     $buffer = strtr($buffer, $unwanted_array );
     $ar1[] = $buffer;
     print "\n".$buffer;
 }
 
 fclose($hndIn1);

 print "\n Done. \n Loading Rada's dictionary...";
 
 while (($buffer = fgets($hndIn2, 4096)) !== false)
 {
     $buffer = trim($buffer);
     $tok = strtok($buffer, " \n\r\t");
     $ar2[] = $tok;
     print "\n".$tok;
 }

 fclose($hndIn2);
 
 
 $ar1a = array_unique($ar1); //quita los probables repetidos que pueda tener porque se quitaron acentos
                             //y eso pudo haber generado repetidos. 
  
 
 print "\n Done. \nIntersecting...\n";
 $arIntersect = array_intersect($ar1a, $ar2);
 
 //--- Intersección
 $hndOut = fopen($fileIntersect, "w");
 /*
 for ($i = 0; $i < count($arIntersect); $i++)
 {
   fwrite($hndOut,$arIntersect[$i]."\n");
 } 
 */
 foreach ($arIntersect as $arIval)
 {
	//print $arIval."\n";
	fwrite($hndOut, $arIval."\n");
 }
 fclose($hndOut);
 
 print "COUNT: ".count($arIntersect).". \nDifference...";
 
 $arDiff1to2 = array_diff($ar2, $ar1a);
 
 //--- Diferencia
 $hndOut = fopen($fileDiff, "w");
 
 foreach ($arDiff1to2 as $arIval)
 {
   fwrite($hndOut, $arIval."\n");
 }
   
 fclose($hndOut);
 
 print "COUNT: ".(count($arDiff1to2));
 
 print "\nDone.";
 
 unset($ar1);
 unset($ar2);
 unset($arIntersect);
 
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
