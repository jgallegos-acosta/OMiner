<?php

 $filename = "Indesit_WIN_101__Opinion_1440636.html.txt";
 $fbase = basename($filename, ".txt");

 $hndIn = fopen($filename, "r");
 $hndOut = fopen($fbase.".utf", "w");
 
$time_start = microtime(true);

 while (!feof($hndIn))
 {
    //$theLine++;
    $buff = fgets($hndIn);
    fprintf ($hndOut, utf8_encode($buff));
 }
 
 fclose($hndIn);
 fclose($hndOut);

print "\nTagging... ";
            $args = array();
            $cmd = "analyze -f es.cfg < ".$fbase.".utf > ".$fbase.".tag";
            exec($cmd, $args, $ret);
            unset($args);
            
 $hndIn = fopen($fbase.".tag", "r");
 $hndOut = fopen($fbase.".utftag", "w");

 while (!feof($hndIn))
 {
    //$theLine++;
    $buff = fgets($hndIn);
    fprintf ($hndOut, utf8_decode($buff));
 }
 fclose($hndIn);
 fclose($hndOut);

$time_end = microtime(true);
$time = $time_end - $time_start;

print "Tagged!"; 
print "Total Time [".number_format($time, 2)." s]";
 
?>
