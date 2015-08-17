<?php
/*
 //print_r(UniqueRandomNumbersWithinRange(1, 2615, 10));
 $ar = array();
 $ar = UniqueRandomNumbersWithinRange(0, 2615, 10);
 var_dump($ar); unset($ar); exit(0);
*/

$fso_path = "./OpinionsSO";
$fExt = "fso";

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
	   $arP[$pc] = $fname.".fso";
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
	   $arN[$nc] = $fname.".fso";
	   $nc++;
	   //print $nc.". ".$fname.".fso\n";
   }
}
fclose($hndN);
print " done!";

//var_dump($arN);

$arPIdx = array();
$arNIdx = array();

//print "\n".$pc." ".$nc;
//2047
//332

$arPIdx = UniqueRandomNumbersWithinRange(0, 2046, 300);
$arNIdx = UniqueRandomNumbersWithinRange(0, 331, 300);

//print_r ($arNIdx);exit(0);

$hndAc = fopen("accinfo_300", "w");
$hndBO = fopen("badones_300", "w");

$fc = 1;
$hff = 600;

$MaxRank = -1000000;
$MinRank = 1000000;

$TP = 0; $TN = 0;
$FP = 0; $FN = 0;
$boc = 1;

$rightOnes = 0;

for ($i = 0; $i < 600; $i++)
{

	        if ($i >= 0 AND $i < 300)
	           $entry = $arP[$arPIdx[$i]];

	        if ($i >= 300 AND $i < 600)
	           $entry = $arN[$arNIdx[$i-300]];

            $filename = basename($entry, ".".$fExt);
            
            //print "$i. [".$fso_path."/".$filename.".".$fExt."]\n";


            $perc = ($fc * 100) / $hff;
            $nf = number_format($perc, 2);
            print "[$fc / $hff | ".$nf."%] Processing ".$filename.".".$fExt.". ";
            //fprintf ($hndAc, "Processing ".$filename.".2gm. [$fc / $hff | ".$nf."%%]");
            fprintf ($hndAc, $fc.". ".$filename.".2gm. ");
                        
            $line1 = "";
            $line = "";
            $rankSO = "";
            $rankStars = "";
            $hndSO = fopen($fso_path."/".$filename.".".$fExt, "r");
            while(!feof($hndSO))
            {
                $line = fgets($hndSO);
                if (trim($line) != "")
                {
                  //print " >> $line | $line1 << \n";
                  $rankStars = strtok($line, " \r\n");
                  $rankSO    = strtok($line1, " \r\n");
			    }
                  
                if (trim($line) != "")
                  $line1 = $line;
            }
            
            fclose($hndSO);

            if ($rankSO > 0 AND $rankStars > 30)
            {
				if ($rankSO > $MaxRank) $MaxRank = $rankSO;
				if ($rankSO < $MinRank) $MinRank = $rankSO;
				
				$rightOnes++;
				fprintf($hndAc, " [OK | TP] ");
				$TP++;
			}
            elseif ($rankSO < 0 AND $rankStars < 30)
            {
				if ($rankSO > $MaxRank) $MaxRank = $rankSO;
				if ($rankSO < $MinRank) $MinRank = $rankSO;
				
				$rightOnes++;
				fprintf($hndAc, " [OK | TN] ");
				$TN++;
			}   
			else
			{
				fprintf($hndBO, "$boc. $filename.2g  $rankSO  $rankStars\n");
				
				if ($rankSO < 0 AND $rankStars > 30)
				{    
					$FN++; fprintf ($hndAc, " [ERROR | FN] "); 
				}
				if ($rankSO > 0 AND $rankStars < 30)
				{    
					$FP++; fprintf ($hndAc, " [ERROR | FP] "); 
				}
				$boc++;
			}

            print ">> SO: $rankSO | Stars: $rankStars <<\n";
            fprintf ($hndAc, " -> SO: $rankSO | Stars: $rankStars \n");
            
            $fc++;

}

unset($arP);
unset($arN);
unset($arPIdx);
unset($arNIdx);

print "\nTotal: ".($fc-1);
fprintf ($hndAc, "\nTotal: ".($fc-1)); 
print "\nCorrectos: ".$rightOnes; 
fprintf ($hndAc, "\nCorrectos: ".$rightOnes); 
$perc = ($rightOnes*100)/($hff);
$nf = number_format($perc, 2);
print "\nEfectividad: ".$nf."%";
fprintf ($hndAc, "\nEfectividad: ".$nf."%%");
print "\nTP = $TP, TN = $TN, FP = $FP, FN = $FN";
fprintf ($hndAc, "\nTP = $TP, TN = $TN, FP = $FP, FN = $FN");
$Accr = (($TP + $TN) / ($TP + $TN + $FP + $FN));
$Prec = ($TP / ($TP + $FP));
$Recl = ($TP / ($TP + $FN));
$F1 = (2 * $Prec * $Recl / $Prec + $Recl);
print  "\nPrecision: ".$Prec;
fprintf ($hndAc, "\nPrecision: ".$Prec);
print  "\nRecall: ".$Recl;
fprintf ($hndAc, "\nRecall: ".$Recl);
print  "\nAccuracy: ".$Accr;
fprintf ($hndAc, "\nAccuracy: ".$Accr);
print  "\nF1-score: ".$F1;
fprintf ($hndAc, "\nF1-score: ".$F1);


print "\nMinRank: ".$MinRank;
fprintf ($hndAc, "\nMinRank: ".$MinRank);
print "\nMaxRank: ".$MaxRank;
fprintf ($hndAc, "\nMaxRank: ".$MaxRank);

exit(0);

function UniqueRandomNumbersWithinRange($min, $max, $quantity) 
{
    $numbers = range($min, $max);
    shuffle($numbers);
    return array_slice($numbers, 0, $quantity);
}

?>
