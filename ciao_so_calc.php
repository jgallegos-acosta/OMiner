<?php

 $mfb_path = './OpinionsMFB';
 $fso_path = './OpinionsSO';
 $xmlPath  = './OpinionsXML';
 
 $BGRank    = "bigrams.rank";
 $BGNegRank = "negbigrams.rank";
 
print "Loading bigrams ranking list... ";
$bigList = array(array());
$hndBL = fopen ($BGRank, "r");
$cbl = 0;
while(!feof($hndBL))
{
   $line = fgets($hndBL);
   if (trim($line) != "")
   {
     $tok1 = strtok($line, " \r\n");
     $tok2 = strtok(" \r\n");
     $tok3 = strtok(" \r\n");
     $tok4 = strtok(" \r\n");
     $tok5 = strtok(" \r\n");
     $bigList[0][$cbl] = $tok1." ".$tok2;
     $bigList[1][$cbl] = $tok5;
     $cbl++;
   }
}
fclose($hndBL);

//var_dump($bigList); print $cbl; unset($bigList); exit(0);

 $fc = 1;

if ($handle = opendir($mfb_path)) 
{
    $hff = 0;
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == '2gm')
         $hff++;
    }
    closedir($handle);
}

$time_start = microtime(true);

if ($handle = opendir($mfb_path)) 
{
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == '2gm')
        {  
            $filename = basename($entry, ".2gm");

            $perc = ($fc * 100) / $hff;
            $nf = number_format($perc, 2);
            print "[$fc / $hff | ".$nf."%] Processing ".$filename.".2gm. ";
            //fprintf ($hndLog, "Processing ".$filename.".2gm. [$fc / $hff | ".$nf."%%]");

            //print "\nParsing opinions...";
            //fprintf ($hndLog, " Parsing bigrams...");
            
            
            $hndSO = fopen($fso_path."/".$filename.".fso", "w");
            $hndBL = fopen ($mfb_path."/".$filename.".2gm", "r");
            $SOsum = 0.0;
            while(!feof($hndBL))
            {
                $line = fgets($hndBL);
                if (trim($line) != "")
                {
                   $tok1 = strtok($line, " \r\n");
                   $tok2 = strtok(" \r\n");
                   $bgToTest = $tok1." ".$tok2;
                   $as = array_search($bgToTest, $bigList[0]); //siempre estará
                   $SOsum += $bigList[1][$as];
                   fwrite ($hndSO, $bgToTest." ".$bigList[1][$as]."\n");
                }
            }
            fwrite($hndSO, $SOsum);
            fwrite($hndSO, getRanking($filename)."\n");
            
            fclose($hndBL);
            fclose($hndSO);
            
            
            print "done ";
            //fprintf ($hndLog, " done ");
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            print "[".number_format($time, 2)." s] \n";
            //fprintf ($hndLog, "[".number_format($time, 2)." s] \n");
            $fc++;
            
        }
    }
}

unset($bigList);
exit(0);

function getRanking($fname)
{
	 global $xmlPath;
     $hndIn = fopen($xmlPath."/".$fname.".xml", "r");
     $str1 = "";
     $letsProceed = false;
     while (!feof($hndIn))
     {
         $char = fgetc($hndIn);
         if ($char == '<')
         {
            $tag = "";
            $tag = $tag.$char;
            do
            {
                $char = fgetc($hndIn);
                $tag = $tag.$char;
            } while ($char != '>');
            $desc_start = stripos(strtolower($tag), "<stars");
            $desc_stop = stripos(strtolower($tag), "</stars");            
            if ($desc_start  !== false) { $letsProceed = true;  }
            if ($desc_stop   !== false) { $letsProceed = false; }
         }
         if ($char != '>' and $letsProceed == true)
         {
             $tok = $char;
             $str1 .= $tok;
         }      
     }
     fclose ($hndIn);
     return $str1;
}

?>
