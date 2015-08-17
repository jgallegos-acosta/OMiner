<?php
// XML to TXT extraction

sleep (2);

  $txt_path = './OpinionsTXT';
  $xml_path = './OpinionsXML';
  /** Crea lista de los XML **/  
  $hndOut1 = fopen ("xmlfiles_list.txt", "w");
  if ($handle = opendir($xml_path)) 
  {
      $hff = 0;
      while (false !== ($entry = readdir($handle))) 
      {
          $ext = pathinfo($entry, PATHINFO_EXTENSION);
          if ($ext == 'xml')
          {
			 $hff++;
			 print "$hff. $entry\n";
		     fprintf ($hndOut1, "$entry\n");
	      }
      }
      closedir($handle);
  }
  fclose ($hndOut1);

  $hndIn1 = fopen ("xmlfiles_list.txt", "r");
  $hndLog = fopen ("xml2txtfiles.log", "w");
  $time_start = microtime(true);
  $TotalFiles = $hff;
  $hff = 0;
  
while ($buff = fgets($hndIn1))
{
 if (trim($buff))
 {
	 
	 
   $filename = trim($buff);
   
  $hff++;
  print "$hff Processing ".$filename.". [$hff / $TotalFiles]";
  fprintf ($hndLog, "$hff Processing ".$filename.". [$hff / $TotalFiles]");
  $hndIn = fopen($xml_path."/".$filename, "r");
  $bname = basename($filename, ".xml");
  $hndOut = fopen($txt_path."/".$bname.".txt", "w");
      
  $letsProceed = false; //procesar sí/no. Valor inicial, no modificable
  
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

            $desc_start = stripos(strtolower($tag), "<description");
            $desc_stop = stripos(strtolower($tag), "</description");
            
            if ($desc_start  !== false) { $letsProceed = true;  }
            if ($desc_stop   !== false) { $letsProceed = false; }


      }
      
      if ($char != '>' and $letsProceed == true)
      {
          $tok = $char;
          //print $tok;
          fwrite($hndOut, $tok);
	  }
      
  }
  fclose ($hndIn);
  fclose ($hndOut);
   print " done!";
  fprintf ($hndLog, " done!");
  $time_end = microtime(true);
  $time = $time_end - $time_start;
  print " [".number_format($time, 2)." s] \n";
  fprintf ($hndLog, " [".number_format($time, 2)." s] \n");
  
  
 }
}

fclose ($hndIn1);
fclose ($hndLog);


?>
