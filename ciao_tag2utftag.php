<?php

 /* TAG to UTF 8 Decode
  */

 $tag_path = './OpinionsTAG';
 $utf_path = './OpinionsUTFTAG';
  
 $fc = 1;

 if ($handle = opendir($tag_path)) 
 {
    $hff = 0;
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'tag')
         $hff++;
    }
    closedir($handle);
 }

$time_start = microtime(true);
$time_start2 = microtime(true);

$hndLog = fopen("tag2utftag.log", "w");
if ($handle = opendir($tag_path)) 
{
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'tag')
        {  
            $filename = basename($entry, ".tag");
            /*
             * $hff --> 100
             * $fc -->
             * 
             * ($fc * 100) / $hff
             * */
            $perc = ($fc * 100) / $hff;
            $nf = number_format($perc, 2);
            print "Processing ".$filename.".tag. [$fc / $hff | ".$nf."%]";
            fprintf ($hndLog, "Processing ".$filename.".tag. [$fc / $hff | ".$nf."%%]");
            //PHP fprintf warning: Too few arguments... es por que con el % se espera %d, o %s, etc..
            //Para evitarlo, solamente se duplica el %...
            
            /********************************************************************************************/

            $hndIn = fopen($tag_path."/".$filename.".tag", "r");
            $hndOut = fopen($utf_path."/".$filename.".utftag", "w");

            $time_start = microtime(true);

            while (!feof($hndIn))
            {
                //$theLine++;
                $buff = fgets($hndIn);
                fwrite ($hndOut, utf8_decode($buff));
            }

            fclose($hndIn);
            fclose($hndOut);


            /********************************************************************************************/
            
            print "done ";
            fprintf ($hndLog, "done ");
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            print "[".number_format($time, 2)." s] \n";
            fprintf ($hndLog, "[".number_format($time, 2)." s] \n");
            $fc++;

         }
    }
}

$time_end = microtime(true);
$time2 = $time_end - $time_start2;
print "Total Time [".number_format($time2, 2)." s]";
fprintf ($hndLog, "Total Time [".number_format($time2, 2)." s]");

fclose($hndLog);

?>
