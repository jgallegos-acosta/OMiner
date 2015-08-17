<?php
class Opinion 
{
        private $index = array();
        private $classes = array('pos', 'neg');
        private $classTokCounts = array('pos' => 0, 'neg' => 0);
        private $tokCount = 0;
        private $classDocCounts = array('pos' => 0, 'neg' => 0);
        private $docCount = 0;
        private $prior = array('pos' => 0.5, 'neg' => 0.5);

        public function addToIndex($file, $class, $limit = 0) 
        {
                $fh = fopen($file, 'r');
                $i = 0;
                if(!in_array($class, $this->classes)) 
                {
                        echo "Invalid class specified\n";
                        return;
                }
                while($line = fgets($fh)) 
                {
                        if($limit > 0 && $i > $limit) 
                        {
                                break;
                        }
                        $i++;
                        
                        $this->docCount++;
                        $this->classDocCounts[$class]++;
                        $tokens = $this->tokenise($line);
                        foreach($tokens as $token) 
                        {
                                if(!isset($this->index[$token][$class])) 
                                {
                                        $this->index[$token][$class] = 0;
                                }
                                $this->index[$token][$class]++;
                                $this->classTokCounts[$class]++;
                                $this->tokCount++;
                        }
                }
                fclose($fh);
        }
        
        public function classify($document) 
        {
                $this->prior['pos'] = $this->classDocCounts['pos'] / $this->docCount;
                $this->prior['neg'] = $this->classDocCounts['neg'] / $this->docCount; 
                $tokens = $this->tokenise($document);
                $classScores = array();

                foreach($this->classes as $class) 
                {
                        $classScores[$class] = 1;
                        foreach($tokens as $token) 
                        {
                                $count = isset($this->index[$token][$class]) ?
                                        $this->index[$token][$class] : 0;

                                $classScores[$class] *= ($count + 1) / 
                                        ($this->classTokCounts[$class] + $this->tokCount);
                        }
                        $classScores[$class] = $this->prior[$class] * $classScores[$class];
                }
                
                arsort($classScores);
                return key($classScores);
        }

        private function tokenise($document) 
        {
                $document = strtolower($document);
                preg_match_all('/\w+/', $document, $matches);
                return $matches[0];
        }
}


$op = new Opinion();
$op->addToIndex('neg_trainset.txt', 'neg');
$op->addToIndex('pos_trainset.txt', 'pos');

$utf_path = "./OpinionsUTF";
$xmlPath  = './OpinionsXML';

$TP = 0; $TN = 0;
$FP = 0; $FN = 0;

$boc = 1;

 print "Loading opinions list...";
 $ar1 = array();
 $hndIn1 = fopen ("op_utf_diff.lst", "r");
 while (($buffer = fgets($hndIn1, 4096)) !== false)
 {
     $buffer = trim($buffer);
     $ar1[] = $buffer;
 }
 fclose($hndIn1);
 
 
$hndAc = fopen("bayes_accinfo", "w");
$hndBO = fopen("bayes_badones", "w");
 
 

$hff = getLines("op_utf_diff.lst");
//$hff = count($ar1);

for ($i = 0; $i < count($ar1); $i++)
{
	$fc = ($i+1);

	$filename = basename($ar1[$i], ".utf");
            
            
            $perc = ($fc * 100) / $hff;
            $nf = number_format($perc, 2);
            print "[$fc / $hff | ".$nf."%] Processing ".$filename.".utf ";
            //fprintf ($hndAc, "Processing ".$filename.".2gm. [$fc / $hff | ".$nf."%%]");
            fprintf ($hndAc, $fc.". ".$filename.".utf. ");
            

$hndP = fopen ($utf_path."/".$filename.".utf", "r");
$doc = "";
while ($buff = fgets($hndP))
{ $doc .= $buff; }
fclose($hndP);
  
  $sentences = explode(".", $doc);
  $score = array('pos' => 0, 'neg' => 0);
  foreach($sentences as $sentence) 
  {
        if(strlen(trim($sentence))) 
        {
                $class = $op->classify($sentence);
                //echo "Classifying: \"" . trim($sentence) . "\" as " . $class . "\n";
                $score[$class]++;
        }
  }
  //var_dump($score);
  print ($i+1).". ".$filename. " - ";
  
  if ($score['pos'] > $score['neg']) { print "pos "; $rankSO = 1; }
  if ($score['pos'] < $score['neg']) { print "neg "; $rankSO = -1; }
  
  

  $rankStars = trim(getRanking($filename));
  
            if ($rankSO > 0 AND $rankStars >= 30)
            {
				$rightOnes++;
				fprintf($hndAc, " [OK | TP] ");
				$TP++;
			}
            elseif ($rankSO < 0 AND $rankStars < 30)
            {				
				$rightOnes++;
				fprintf($hndAc, " [OK | TN] ");
				$TN++;
			}   
			else
			{
				fprintf($hndBO, "$boc. $filename.utf  $rankSO  $rankStars\n");
				
				if ($rankSO < 0 AND $rankStars >= 30)
				{    
					$FN++; fprintf ($hndAc, " [ERROR | FN] "); 
				}
				if ($rankSO > 0 AND $rankStars < 30)
				{    
					$FP++; fprintf ($hndAc, " [ERROR | FP] "); 
				}
				$boc++;
			}  
  
            print ">> SO: $rankSO | Stars: $rankStars <<\n";
            fprintf ($hndAc, " -> SO: $rankSO | Stars: $rankStars \n");

  print "\n";
}

unset($ar1);
$fc = $hff;


fprintf ($hndAc, "\nOpiniones Positivas de Entrenamiento: ". (getLines("train_sets.lst")/2));
fprintf ($hndAc, "\nOpiniones Negativas de Entrenamiento: ". (getLines("train_sets.lst")/2));
print "\nTotal: ".($fc);
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

fclose($hndBO);
fclose($hndAc);


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

function getLines($fname)
{
 $c = 0;
 $hndIn1 = fopen ($fname, "r");
 while (($buffer = fgets($hndIn1, 4096)) !== false)
 {
     $buffer = trim($buffer);
     if ($buffer != "")
     {
		 $c++;
	 }
 }
 fclose($hndIn1);
 return $c;
}
?>
