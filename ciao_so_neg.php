<?php
//Hoover_DYT_8104_D__Opinion_1968048.html.nbg
//Indesit_W_104__Opinion_1605944.html

 $mfb_path = './OpinionsMFB';
 $fso_path = './OpinionsSONEG';
 $nbg_path = './OpinionsNBG';
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

print "Loading negative bigrams ranking list... ";
$nbigList = array(array());
$hndBL = fopen ($BGNegRank, "r");
$cnbl = 0;
while(!feof($hndBL))
{
   $line = fgets($hndBL);

   if (trim($line) != "")
   {
      $term = "";
      $tok = strtok($line, " \n\t");
      $ct = 0;
      $termAr = array();
      while ($tok !== false) 
      {
         //echo "Word=$tok<br />";
         if ($tok != "")
         {
	         if ($ct == 0) $ss = ""; else $ss = " ";
             $term = $ss.$tok;
             $termAr[$ct] = $term;
             $ct++;
         }
         $tok = strtok(" \n\t");
      }
      //print "\n$cnbl. ";
      $nbigList[0][$cnbl] = "";
      for ($i = 0; $i < $ct-3; $i++)
      {
		  //print "[".$termAr[$i]."]";
		  $nbigList[0][$cnbl] .= $termAr[$i];
	  }

	  $nbigList[1][$cnbl] = trim($termAr[$ct-1]);

      unset($termAr);
      //print ".".$nbigList[0][$cbl].". --> .".$nbigList[1][$cbl].".";

      $cnbl++;
   }
}
fclose($hndBL);


//exit(0);

//unset($bigList); unset($nbigList); exit(0); 
//var_dump($nbigList); print $cnbl; unset($nbigList); unset($bigList); exit(0);

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
                   $as = array_search($bgToTest, $bigList[0]);  //siempre estará, porque se calcularon
                                                                //todos y cada uno de los bigramas del 
                                                                //sistema base
                   $SOsum += $bigList[1][$as];
                   fwrite ($hndSO, $bgToTest." ".$bigList[1][$as]."\n");
                }
            }
            
            fwrite ($hndSO, "\n");
            
            $hndNBL = fopen ($nbg_path."/".$filename.".nbg", "r");
            while(!feof($hndNBL))
            {
                $line = fgets($hndNBL);
                if (trim($line) != "")
                {
					$ngram = "";
                    $tok = strtok($line, " \n\r\t");
                    while ($tok !== false) 
                    {
                       $ngram .= $tok." ";
                       $tok = strtok(" \n\t");
                    }
                    $ngram  = trim($ngram);
                    //print " | ".$ngram;
                
                    $as = array_search($ngram, $nbigList[0]); //no siempre estará, porque no se tomaron todos
                                                              //los bigramas negativos para hacer la lista
                                                              //de ranking, pues unos ya estaban en la lista de
                                                              //bigramas del sistema base. A saber, se tomo la
                                                              //diferencia para calcular los bigramas negativos
                                                              //únicos.
                    if ($as !== FALSE)
                    {
                       $SOsum += $nbigList[1][$as];
                       fwrite ($hndSO, $ngram." ".$nbigList[1][$as]."\n");
                    }
                }
            }
            
            fclose($hndNBL);
            
            
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
unset($nbigList);
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
