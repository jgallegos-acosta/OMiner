<?php
/*
 * Lista toda las opiniones que hay 
 * 
 * */

 $mfb_path = './OpinionsUTF';

$fc = 1;

if ($handle = opendir($mfb_path)) 
{
    $hff = 0;
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'utf')
         $hff++;
    }
    closedir($handle);
}

$hndAll = fopen ("opinions_utf.lst", "w");

if ($handle = opendir($mfb_path)) 
{
    while (false !== ($entry = readdir($handle))) 
    {
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        if ($ext == 'utf')
        {  
            $filename = basename($entry, ".utf");
            
            print $fc.". ".$filename.".utf\n";
            fwrite($hndAll, $fc.". ".$filename.".utf\n");
            
            $fc++;
        }
    }
}

fclose($hndAll);

//print ($fc-1);
?>
