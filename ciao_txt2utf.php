<?php

 $txt_path = './OpinionsTXT';
 $utf_path = './OpinionsUTF';
  
 $fc = 1;

 if ($handle = opendir($txt_path)) 
 {
    $hff = 0;
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'txt')
         $hff++;
    }
    closedir($handle);
 }

 $time_start = microtime(true);

$hndLog = fopen("txt2utf.log", "w");
if ($handle = opendir($txt_path)) 
{
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'txt')
        {  
            $filename = basename($entry, ".txt");
            /*
             * $hff --> 100
             * $fc -->
             * 
             * ($fc * 100) / $hff
             * */
            $perc = ($fc * 100) / $hff;
            $nf = number_format($perc, 2);
            print "Processing ".$filename.".txt. [$fc / $hff | ".$nf."%]";
            fprintf ($hndLog, "Processing ".$filename.".html. [$fc / $hff | ".$nf."%%]");
            //PHP fprintf warning: Too few arguments... es por que con el % se espera %d, o %s, etc..
            //Para evitarlo, solamente se duplica el %...
            
            /********************************************************************************************/

            $hndIn = fopen($txt_path."/".$filename.".txt", "r");
            $hndOut = fopen($utf_path."/".$filename.".utf", "w");

            $time_start = microtime(true);

            while (!feof($hndIn))
            {
                //$theLine++;
                $buff = fgets($hndIn);
                fwrite ($hndOut, utf8_encode($buff));
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
$time = $time_end - $time_start;
print "Total Time [".number_format($time, 2)." s]";
fprintf ($hndLog, "Total Time [".number_format($time, 2)." s]");

fclose($hndLog);

?>
