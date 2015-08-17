<?php 
/*
 * Este módulo extrae los resultados arrojados por Google
 *
 */
//18,000 -> 2.07
//19,018 -> 2.17
//19,211 -> 2.19
//A partir de los 18,000 se empezaron a guardar las IP's
//20,612 -> 2.36

//"tanto antigua" AROUND(10) "excelente" -> Positiva

//$proxy_ip = "218.204.143.137"; $proxy_port = "8118";
//$proxy_ip = "190.152.98.102"; $proxy_port = "80";
//$proxy_ip = "91.121.103.144"; $proxy_port = "3128"; //France

//-- México proxy list
//$proxy_ip = "187.176.117.26"; $proxy_port = "3128";
	
//$proxy_ip = "8.29.210.46"; $proxy_port = "3128";

$proxy_ip = "183.203.208.178"; $proxy_port = "8118";

$useProxy = false;
/*
if ($useProxy == true)
{
  $thisIP = get_url("http://checkip.hiktix.net/");
  print $thisIP."\n";
  $resp = testCurlProxy($proxy_ip, $proxy_port)."\n\n";
  if (substr($resp, 0, 5) == "CURLE")
  {
	 print $resp;
     exit(0);
  }
  else
     print $resp;
  //exit(0);
  print "Wait 4 sec.\n\n";
  sleep(4);
}
*/

//$BGList = "bigrams.lst"; $BGRank = "bigrams.rank";
//$BGList = "left_bg.lst";   $BGRank = "bigrams_left.rank";

//usaremos la lista de "diferencia" porque son los bigramas que todavía no se han calculado
//$BGList = "bg_difference.lst";   $BGRank = "negbigrams.rank";

//la lista de los bigramas negativos que faltaron
$BGList = "negbigramsmiss.lst";   $BGRank = "negbigramsmiss.rank";


print "Loading bigrams list... ";
$bigList = array();
$hndBL = fopen ($BGList, "r");
$cbl = 0;
while(!feof($hndBL))
{
   $line = fgets($hndBL);
   $bigList[] = $line;
   $cbl++;
}
fclose($hndBL);

print "done! \n";
//unset($bigList); print $cbl; exit(0);

$linecount = 0;
if (file_exists ($BGRank))
{
   $hndl = fopen($BGRank, "r");
   while(!feof($hndl))
   {
     $line = fgets($hndl);
     if (trim($line) != "")
       $linecount++;
   }
   fclose($hndl);

}
else
{
   $hndl = fopen($BGRank, "w");
   fclose($hndl);
}
echo "\n".$linecount. " bigram hits calculated till now.\n";

print "Getting current IP address...";

$hndIP = fopen ("ipaddress.info", "a");
  $thisIP = get_url("http://checkip.hiktix.net/");
  if (trim($thisIP) == "")
  {
	  unset ($bigList);
	  print "Can't connect :(";
	  fwrite($hndIP, "[ERROR] ".date("H:i:s")." Can't connect.\n");
	  exit(0);
  }
  fwrite($hndIP, $thisIP." ".date("H:i:s")." ".$linecount);
fclose($hndIP);
print "Done. \n";


$waitTime = 1;

$hndOut = fopen($BGRank, "a");

$c = $linecount+1;
for ($i = $linecount; $i < $cbl; $i++)
{
	
   //$line = fgets($hndIn);
   $line = $bigList[$i];
   
   $perc = ($c * 100) / $cbl;
   $nf = number_format($perc, 2);
      
   print "\n".$c. ". [$nf%] ";

   $tok1 = strtok($line, " \n\r");
   $tok2 = strtok(" \n\r");   

   $tok1 = str_replace("_", "+", $tok1);
   $tok2 = str_replace("_", "+", $tok2);

   print "[$tok1]";
   print "[$tok2]";
   
   //$proxy_ip = "190.112.42.131"; $proxy_port = "8080";
/*
   //$gQuery = '%22'.urlencode($tok1).'+'.urlencode($tok2).'%22'.'+AROUND(10)+'.'%22'.urlencode('pésima').'+OR+'.urlencode('dañina').'%22';
   $gQuery = '%22'.urlencode($tok1).'+'.urlencode($tok2).'%22'.'+AROUND(10)+'.'%22'.urlencode('excelente').'%22';
   print "(wait $waitTime s.) ";
   sleep ($waitTime);
   
   if ($useProxy)
      $rankP = fetch_googleCurlProxy($gQuery, $proxy_ip, $proxy_port);
   else
      $rankP = fetch_googleCurl($gQuery);
   print " => ".$rankP;
   //fwrite($hndOut, "\n".trim($line)." ".$rankP." ");
   
   $gQuery = '%22'.urlencode($tok1).'+'.urlencode($tok2).'%22'.'+AROUND(10)+'.'%22'.urlencode('mala').'%22';
   print "(wait $waitTime s.) ";
   sleep ($waitTime);
   if ($useProxy)
     $rankN = fetch_googleCurlProxy($gQuery, $proxy_ip, $proxy_port);
   else
     $rankN = fetch_googleCurl($gQuery);
   print " => ".$rankN;
   //fwrite($hndOut, " ".$rankN);
   
   //fwrite($hndOut, "  ".SO($rankP, $rankN));
   
   fwrite($hndOut, "\n".trim($line)." ".$rankP."  ".$rankN."  ".SO($rankP, $rankN));
*/

   $c++;
}
//fclose($hndIn);
fclose($hndOut);
//unset($bgList);
unset ($bigList);

exit(0);

//------------------------------------------------------------------------------------------

function SO($t_pos_hits, $t_neg_hits)
{
	$pos_hits = 345000000;
	$neg_hits = 226000000;
	$t_pos_hits += 0.01;
	$t_neg_hits += 0.01;
	$SO1 = $t_pos_hits * $neg_hits;
	$SO2 = $t_neg_hits * $pos_hits;
	$SO3 = $SO1 / $SO2;
	$SO4 = log($SO3, 2);
	return $SO4;
}


function fetch_googleCurlProxy($query, $proxy_ip, $proxy_port)
{
  $timeout = 10;
  $ch = curl_init();
  //curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  
  curl_setopt($ch, CURLOPT_URL, "http://www.google.com.mx/search?q=".$query);
  //curl_setopt($ch, CURLOPT_VERBOSE, 0); //not needed
  //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)"); //not needed
  //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201");
  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
  curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
  curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);
  //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $loginpassw);
  $scrape = curl_exec($ch); 
  if (curl_errno($ch) != 0) 
  {
      //return curlErrCodes(curl_errno($ch));
      return -1; //hubo un error de CURL
  }
   $scrapedItem = preg_match_all('/Cerca de.*?resultados/i', $scrape, $matches, PREG_PATTERN_ORDER);
   $results = @$matches[0][0];
   $scrapedItem2 = preg_match_all('/[1-9](?:\d{0,2})(?:,\d{3})*(?:\.\d*[1-9])?|0?\.\d*[1-9]|0/i', $results, $matches2, PREG_PATTERN_ORDER);
   $finalResult = @$matches2[0][0];
  curl_close($ch);
  
   print $scrape;
   
   // if (strlen($scrape) < 500) --> ya nos rebotó, hay que buscar proxy
   
   print "\n".strlen($scrape);
   //exit(0);
   
   $hasNoFound = strpos($scrape, "No se han encontrado resultados");
   if ($hasNoFound !== false) return 0;
   if (trim($results) == "")
   {
	  $xstr1 = strpos($scrape, "resultados (<b>");
      for ($i = $xstr1; $i >= 0; $i--)
      {
	     if ($scrape[$i] == ">")
	     {
		    $spos = $i;
		    break;
	     }
      }
      //print "\nQUERY: [$query]\nRESULTS: [".trim(substr($scrape, $spos+1, ($xstr1 - $spos-1)))."]";
      $res = trim(substr($scrape, $spos+1, ($xstr1 - $spos-1)));
      return $res;
   }
   else
   {
      //print "\nQUERY: [$query]\nRESULTS: [".$results."]";
      $res = substr($results, 8, strlen($results));
      $res = substr($res, 0, -10);
      $res = trim(str_replace(",", "", $res));
      
      return $res;
   }
   return 0;
}

function fetch_googleCurl($query)
{
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_HEADER, 0);  //not needed
   curl_setopt($ch, CURLOPT_VERBOSE, 0); //not needed
   curl_setopt($ch, CURLOPT_URL, "http://www.google.com.mx/search?q=".$query);
   //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)"); //not needed
   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201");
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
   $scrape = curl_exec($ch); 
   $scrapedItem = preg_match_all('/Cerca de.*?resultados/i', $scrape, $matches, PREG_PATTERN_ORDER);
   $results = @$matches[0][0];
   $scrapedItem2 = preg_match_all('/[1-9](?:\d{0,2})(?:,\d{3})*(?:\.\d*[1-9])?|0?\.\d*[1-9]|0/i', $results, $matches2, PREG_PATTERN_ORDER);
   $finalResult = @$matches2[0][0];
   curl_close($ch);
   
   print $scrape;

    if (strlen($scrape) < 500) //--> ya nos rebotó, hay que buscar proxy
    { 
		print "\n".strlen($scrape); 
		
         $hndIP = fopen ("ipaddress.info", "a");
	       fwrite($hndIP, " ".date("H:i:s")." \n");
	     fclose($hndIP);
	  
		exit(0); 
	}
   
   $hasNoFound = strpos($scrape, "No se han encontrado resultados");
   if ($hasNoFound !== false) return 0;
   
   if (trim($results) == "")
   {
	   
	  $xstr1 = strpos($scrape, "resultados (<b>"); //si no fueron varios resultados, probar 1 solo
	  if ($xstr1 === false) 
	      $xstr1 = strpos($scrape, "resultados</div");
	  if ($xstr1 === false) 
         $xstr1 = strpos($scrape, "resultado (<b>");
	  if ($xstr1 === false) 
         $xstr1 = strpos($scrape, "resultado</div");

      if (trim($xstr1) == "")
      { 
		  print "--> [$query] (".$xstr1.")"; 	   
		  $hndEr = fopen ("error.log", "w");
		    fwrite($hndEr, $scrape);
		  fclose($hndEr);
		  
          $hndIP = fopen ("ipaddress.info", "a");
	        fwrite($hndIP, " ".date("H:i:s")." \n");
	      fclose($hndIP);
		  
          exit(0);
	  }

      for ($i = $xstr1; $i >= 0; $i--)
      {
	     if ($scrape[$i] == ">")
	     {
		    $spos = $i;
		    break;
	     }
      }
      //print "\nQUERY: [$query]\nRESULTS: [".trim(substr($scrape, $spos+1, ($xstr1 - $spos-1)))."]";
      $res = trim(substr($scrape, $spos+1, ($xstr1 - $spos-1)));
      return $res;
   }
   else
   {
      //print "\nQUERY: [$query]\nRESULTS: [".$results."]";
      $res = substr($results, 8, strlen($results));
      $res = substr($res, 0, -10);
      $res = trim(str_replace(",", "", $res));
      
      return $res;
   }
   
   return 0;
}

function ping($host, $port, $timeout) 
{ 
  $tB = microtime(true); 
  $fP = fSockOpen($host, $port, $errno, $errstr, $timeout); 
  if (!$fP) { return "down"; } 
  $tA = microtime(true); 
  return round((($tA - $tB) * 1000), 0)." ms"; 
}

function curlErrCodes($errNumber)
{
   $error_codes=array(
   1 => 'CURLE_UNSUPPORTED_PROTOCOL', 
   2 => 'CURLE_FAILED_INIT', 
   3 => 'CURLE_URL_MALFORMAT', 
   4 => 'CURLE_URL_MALFORMAT_USER', 
   5 => 'CURLE_COULDNT_RESOLVE_PROXY', 
   6 => 'CURLE_COULDNT_RESOLVE_HOST', 
   7 => 'CURLE_COULDNT_CONNECT', 
   8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
   9 => 'CURLE_REMOTE_ACCESS_DENIED',
  11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
  13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
  14 => 'CURLE_FTP_WEIRD_227_FORMAT',
  15 => 'CURLE_FTP_CANT_GET_HOST',
  17 => 'CURLE_FTP_COULDNT_SET_TYPE',
  18 => 'CURLE_PARTIAL_FILE',
  19 => 'CURLE_FTP_COULDNT_RETR_FILE',
  21 => 'CURLE_QUOTE_ERROR',
  22 => 'CURLE_HTTP_RETURNED_ERROR',
  23 => 'CURLE_WRITE_ERROR',
  25 => 'CURLE_UPLOAD_FAILED',
  26 => 'CURLE_READ_ERROR',
  27 => 'CURLE_OUT_OF_MEMORY',
  28 => 'CURLE_OPERATION_TIMEDOUT',
  30 => 'CURLE_FTP_PORT_FAILED',
  31 => 'CURLE_FTP_COULDNT_USE_REST',
  33 => 'CURLE_RANGE_ERROR',
  34 => 'CURLE_HTTP_POST_ERROR',
  35 => 'CURLE_SSL_CONNECT_ERROR',
  36 => 'CURLE_BAD_DOWNLOAD_RESUME',
  37 => 'CURLE_FILE_COULDNT_READ_FILE',
  38 => 'CURLE_LDAP_CANNOT_BIND',
  39 => 'CURLE_LDAP_SEARCH_FAILED',
  41 => 'CURLE_FUNCTION_NOT_FOUND',
  42 => 'CURLE_ABORTED_BY_CALLBACK',
  43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
  45 => 'CURLE_INTERFACE_FAILED',
  47 => 'CURLE_TOO_MANY_REDIRECTS',
  48 => 'CURLE_UNKNOWN_TELNET_OPTION',
  49 => 'CURLE_TELNET_OPTION_SYNTAX',
  51 => 'CURLE_PEER_FAILED_VERIFICATION',
  52 => 'CURLE_GOT_NOTHING',
  53 => 'CURLE_SSL_ENGINE_NOTFOUND',
  54 => 'CURLE_SSL_ENGINE_SETFAILED',
  55 => 'CURLE_SEND_ERROR',
  56 => 'CURLE_RECV_ERROR',
  58 => 'CURLE_SSL_CERTPROBLEM',
  59 => 'CURLE_SSL_CIPHER',
  60 => 'CURLE_SSL_CACERT',
  61 => 'CURLE_BAD_CONTENT_ENCODING',
  62 => 'CURLE_LDAP_INVALID_URL',
  63 => 'CURLE_FILESIZE_EXCEEDED',
  64 => 'CURLE_USE_SSL_FAILED',
  65 => 'CURLE_SEND_FAIL_REWIND',
  66 => 'CURLE_SSL_ENGINE_INITFAILED',
  67 => 'CURLE_LOGIN_DENIED',
  68 => 'CURLE_TFTP_NOTFOUND',
  69 => 'CURLE_TFTP_PERM',
  70 => 'CURLE_REMOTE_DISK_FULL',
  71 => 'CURLE_TFTP_ILLEGAL',
  72 => 'CURLE_TFTP_UNKNOWNID',
  73 => 'CURLE_REMOTE_FILE_EXISTS',
  74 => 'CURLE_TFTP_NOSUCHUSER',
  75 => 'CURLE_CONV_FAILED',
  76 => 'CURLE_CONV_REQD',
  77 => 'CURLE_SSL_CACERT_BADFILE',
  78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
  79 => 'CURLE_SSH',
  80 => 'CURLE_SSL_SHUTDOWN_FAILED',
  81 => 'CURLE_AGAIN',
  82 => 'CURLE_SSL_CRL_BADFILE',
  83 => 'CURLE_SSL_ISSUER_ERROR',
  84 => 'CURLE_FTP_PRET_FAILED',
  84 => 'CURLE_FTP_PRET_FAILED',
  85 => 'CURLE_RTSP_CSEQ_ERROR',
  86 => 'CURLE_RTSP_SESSION_ERROR',
  87 => 'CURLE_FTP_BAD_FILE_LIST',
  88 => 'CURLE_CHUNK_FAILED');
  return  $error_codes[$errNumber];
}

function get_url($url)
{
    $ch = curl_init();
      
    if($ch === false)
    { die('Failed to create curl object'); }
      
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function testCurlProxy($proxy_ip, $proxy_port)
{
  //$proxy_ip = "195.49.187.109"; $proxy_port = "3128";
  $url = 'http://checkip.hiktix.net/'; //URL to get
  $timeout = 10;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0); // no headers in the output
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // output to variable
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
  curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
  curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);
  //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $loginpassw);
  $data = curl_exec($ch);
  if (curl_errno($ch) != 0) 
  {
      //print curlErrCodes(curl_errno($ch));
      return curlErrCodes(curl_errno($ch));
  }
  //print_r(curl_getinfo($ch));
  curl_close($ch);
  return $data;
}

?>
