<?php
 $fso_path = './OpinionsSONEG';
 
 $fExt = "fso";

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

$rightOnes = 0;

$time_start = microtime(true);

$hndAc = fopen("negaccinfo", "w");
$hndBO = fopen("negbadones", "w");

$MaxRank = -1000000;
$MinRank = 1000000;

$TP = 0; $TN = 0;
$FP = 0; $FN = 0;
$boc = 1;
if ($handle = opendir($fso_path)) 
{

    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == $fExt)
        {  
            $filename = basename($entry, ".".$fExt);

            $perc = ($fc * 100) / $hff;
            $nf = number_format($perc, 2);
            print "[$fc / $hff | ".$nf."%] Processing ".$filename.".".$fExt.". ";
            //fprintf ($hndAc, "Processing ".$filename.".2gm. [$fc / $hff | ".$nf."%%]");
            fprintf ($hndAc, $fc.". ".$filename.".2gm. ");
            

            //print "\nParsing opinions...";
            //fprintf ($hndLog, " Parsing bigrams...");
            
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

            //if ($rankSO > $MaxRank) $MaxRank = $rankSO;


            if ($rankSO > 0 AND $rankStars >= 30)
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
				//fprintf($hndBO, "$rankSO $rankStars\n");
				fprintf($hndBO, "$boc. $filename.2g  $rankSO  $rankStars\n");
				
				if ($rankSO < 0 AND $rankStars >= 30)
				{    $FN++; fprintf ($hndAc, " [ERROR | FN] "); }
				if ($rankSO > 0 AND $rankStars < 30)
				{    $FP++; fprintf ($hndAc, " [ERROR | FP] "); }
				$boc++;
			}
            
            /*
            print " done ";
            fprintf ($hndAc, " done ");
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            print "[".number_format($time, 2)." s] \n";
            fprintf ($hndAc, "[".number_format($time, 2)." s] \n");
            */
            
            print ">> SO: $rankSO | Stars: $rankStars <<\n";
            fprintf ($hndAc, " -> SO: $rankSO | Stars: $rankStars \n");
            
            $fc++;
            
        }
    }
    
}

//$rightOnes
/*
 * 2618 --> 100
 * $rightOnes -X
 * 
 * */

//$hff = 2379;

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

fclose($hndBO);
fclose($hndAc);

//unset($bigList);
exit(0);

?>
