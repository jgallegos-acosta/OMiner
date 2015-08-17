<?php
/*
 * Módulo que toma todos los bigramas del corpus y 
 * genera una lista con todos ellos ordenados alfabéticamente
 * 
 */

 $mfb_path = "/home/john/Tesis/project/xtract/OpinionsMFB";
 
 $bglst = array();
 $bglst2 = array();

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
 
// $TotQuerys=0;

$TotLines = 0;
 
 
 
 print "Total de opiniones: ".$hff;
 $fc = 1;
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
            print "Processing [$fc / $hff | ".$nf."%]: ".$filename.".2gm. ".$TotLines."\n";
            
            $fc++;
            
            $hnd1 = fopen($mfb_path."/".$filename.".2gm", "r");
            $linecount = 0;
            while(!feof($hnd1))
            {
               $line = fgets($hnd1);

               if (trim($line) != "")
               {
                 $tok1 = strtok($line, " ");
                 //print "[$tok1]";
                 $tok2 = strtok(" ");
                 //print "[$tok2]";
                 //print "\n"; 
                 $bglst[] = $tok1." ".$tok2;
                 //print $tok1." ".$tok2."\n";
                 $linecount++;
		       }

            }
            fclose($hnd1);
            //print "\n";
            //print "Lineas: ".$linecount;
            //print "Bigramas a buscar: ".($linecount - 1)*2;
            
            //$thisDocQ = (($linecount - 1)*2);
            //$TotQuerys += $thisDocQ;
            
            $TotLines += $linecount;
            //print $thisDocQ." \n";
			
        }
    }
 }

//print "Total querys: ".$TotQuerys;

sort($bglst);

//$hndOut = fopen("bigrams.lst", "w");
//fclose($hndOut);

$oldbg = $bglst[0];
$bglst2[0] = $oldbg;
for ($i = 1; $i < count($bglst); $i++)
{
	if ($bglst[$i] == $oldbg)
	{
	}
	else
	{
		$bglst2[] = $bglst[$i];
		$oldbg = $bglst[$i];
    }
}

//var_dump($bglst2);

print "\nLine count: ".$TotLines. " | Count:".count($bglst). " | Count2:".count($bglst2);

$hndOut = fopen("bigrams.lst", "w");

  for ($i = 0; $i < count($bglst2); $i++)
  {
	  fwrite($hndOut,$bglst2[$i]."\n");
  }

fclose($hndOut);

unset($bglst);
unset($bglst2);

?>
