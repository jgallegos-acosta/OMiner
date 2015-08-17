<?php
/*
 Módulo que extrae todas las palabras de la lista de bigramas
 y genera una lista de palabras únicas
*/

$hndIn = fopen("bigrams.lst", "r");

$wList = array();

$c = 1;
while(!feof($hndIn))
{
   $line = fgets($hndIn);

   $perc = ($c * 100) / 20612;
   $nf = number_format($perc, 2);

   
   //print $c.". ".$line;
   print "\n".$c.". [$nf%]";
   
   $tok1 = strtok($line, " \n\r");
   $tok2 = strtok(" \n\r");
   
   $as = array_search($tok1, $wList);
   if ($as == false)
   {
	  if (trim($tok1) != "")
	  {
        $wList[] = trim($tok1);
        sort($wList);
      }
   }
   
   print "[$tok1]";
   
   $as = array_search($tok2, $wList);
   if ($as == false)
   {
	  if (trim($tok1) != "")
	  {
        $wList[] = trim($tok2);
        sort($wList);
      }
   }
   
   print "[$tok2]";
   
   /*
   $tok = strtok($line, " \n\r");
   while ($tok !== false)
   {
	  print "[$tok]";
      $tok = strtok(" \n\r");
   }
   */
   
   //$wList[]
   
   
   $c++;
}
fclose($hndIn);

print "\n";

//$wList2 = array();
//$wList2 = array_unique($wList); //9196


$hndOut = fopen("wordList.lst", "w");

/*
  for ($i = 0; $i < count($wList2); $i++)
  {
	  fwrite($hndOut, $wList2[$i]."\n");
  }
*/
  
  foreach ($wList as $valor)
  {
     fwrite($hndOut, $valor."\n");
  }
  
fclose($hndOut);


unset($wList);


print "\nDone!";


?>
