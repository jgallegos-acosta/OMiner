<?php
//AROUND(10) "excelente OR buenísimo OR buenísima OR superior OR extraordinario OR extraordinaria OR magnífico OR magnífica OR exquisito OR exquisita"
//hits(excelente) = 345,000,000
//hits(mala) = 226,000,000

//print log(256, 2);

print SO(5,1);

/*
$hndBL = fopen ("bigrams.rank", "r");
$hndSO = fopen ("bigrams.socalc", "w");
while(!feof($hndBL))
{
   $line = fgets($hndBL);
   //$bigList[] = $line;
  
   $tok1 = strtok($line, " \n\r");
   $tok2 = strtok(" \n\r");
   $tok3 = strtok(" \n\r");
   $tok4 = strtok(" \n\r");
   
   print "[$tok1][$tok2][$tok3][$tok4]\n";
   
   fwrite($hndSO, $tok1." ".$tok2." ".$tok3."  ".$tok4."  ".SO($tok3, $tok4)."\n");
   
}
fclose($hndSO);
fclose($hndBL);
*/


function SO($t_pos_hits, $t_neg_hits)
{
	$pos_hits = 345000000;
	$neg_hits = 226000000;
	$t_pos_hits += 0.01;
	$t_neg_hits += 0.01;
	$SO1 = $t_pos_hits * $neg_hits;
	$SO2 = $t_neg_hits * $pos_hits;
	$SO3 = $SO1 / $SO2;
	$SO4 = log($SO3, 2);
	return $SO4;
}


?>
