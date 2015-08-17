<?php
/*
 * Este módulo genera 2 documentos, uno con palabras con opiniones positivas y otra
 * con palabras de opiniones negativas para tomarlo como conjunto de entrenamiento
 * para el método de Naive Bayes
 * 
 */

$fso_path = "./OpinionsUTF";
$fExt = "utf";

$trainingSet = 200; //300 opiniones positivas y negativas para el training set

$arP = array();
$arN = array();

print "Loading positive files list...";
$hndP = fopen ("posfiles.lst", "r");
$pc = 0;
while ($buff = fgets($hndP))
{
   if (trim($buff) != "")
   {
	   
	   $tok1 = strtok($buff, " \n\r");
	   $fname = basename($tok1, ".xml");
	   $arP[$pc] = $fname.".utf";
	   $pc++;
	   //print $pc.". ".$fname.".fso\n";
   }
}
fclose($hndP);
print " done!\nLoading negative files list...";

$hndN = fopen ("negfiles.lst", "r");
$nc = 0;
while ($buff = fgets($hndN))
{
   if (trim($buff) != "")
   {
	   
	   $tok1 = strtok($buff, " \n\r");
	   $fname = basename($tok1, ".xml");
	   $arN[$nc] = $fname.".utf";
	   $nc++;
	   //print $nc.". ".$fname.".fso\n";
   }
}
fclose($hndN);
print " done!";

$arPIdx = array();
$arNIdx = array();

$arPIdx = UniqueRandomNumbersWithinRange(0, 2046, $trainingSet);
$arNIdx = UniqueRandomNumbersWithinRange(0, 331, $trainingSet);


$hndPTS = fopen ("pos_trainset.txt", "w");
$hndNTS = fopen ("neg_trainset.txt", "w");

$hndTSL = fopen ("train_sets.lst", "w");

for ($i = 0; $i < ($trainingSet*2); $i++)
{
	        if ($i >= 0 AND $i < $trainingSet)
	           $entry = $arP[$arPIdx[$i]];

	        if ($i >= $trainingSet AND $i < ($trainingSet*2))
	           $entry = $arN[$arNIdx[$i-$trainingSet]];

            $filename = basename($entry, ".".$fExt);
            print "$i ".$filename."\n";            
            $hndSO = fopen($fso_path."/".$filename.".".$fExt, "r");
            fwrite($hndTSL, ($i+1).". ".$filename.".".$fExt."\n");
            while(!feof($hndSO))
            {
                $line = fgets($hndSO);
                if (trim($line) != "")
                {
                    $tok = strtok($line, " \n\r\t");
                    print $tok." ";
                    
                    if ($i >= 0 AND $i < $trainingSet)
                        fwrite($hndPTS, $tok." ");
                    
                    if ($i >= $trainingSet AND $i < ($trainingSet*2))
                        fwrite($hndNTS, $tok." ");
                    
			    }
            }
            fclose($hndSO);
}
fclose ($hndPTS);
fclose ($hndNTS);
fclose ($hndTSL);

exit(0);

function UniqueRandomNumbersWithinRange($min, $max, $quantity) 
{
    $numbers = range($min, $max);
    shuffle($numbers);
    return array_slice($numbers, 0, $quantity);
}
?>
