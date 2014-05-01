<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php

ini_set('memory_limit', '1024M');

function createTfIDF(){
	//get the posting list from boolean model to create tf-idf matrix
	$postingList = json_decode(file_get_contents('PL.json'), true);

	//count the total number of document
	$N = 0;
	foreach ( glob("docs/*.htm") as $filename ){
		$N++;
	}
	
	//echo $N;
	
	//create the Tf-Idf vector
	$TfIdfVector = TfIdfDocVector($postingList,$N);
	
	//make token index file
	$tokenIndex = makeTokenIDF($TfIdfVector);
	
	//make Document vector
	//$docVector = makeDocVector($TfIdfVector,$tokenIndex);
	
	file_put_contents("TfIdf.json",json_encode($TfIdfVector));
	file_put_contents("tokenIndex.json",json_encode($tokenIndex));
	file_put_contents("numberOfDocs.json",json_encode($N));
	
	//echo "<br />";
	//print_r($tokenIndex);
	//print_r($TfIdfVector);
}

function TfIdfDocVector($postingList,$N){
	/*  The format of the hash index is:
		Dictionary Word=>array (0=>IDF,#docID=>tf-idf score)
	*/
	$TfIdf = array();
	
	//lenth of doucment to normalize the tf-idf vector
	$docLength = array_fill(1,$N,0);
	
	//go through the entire posting list to collect tf-idf info
	foreach ($postingList as $key => $value){
		//find doucment freq for each token
		$df = count($value["token"]);
		
		//print_r($value["token"]);
		//echo "<br />";
		//print_r($docLength);
		//echo $df;
		//exit(0);
		
		//find IDF
		$IDF = log10($N/$df);
		$TfIdf[$key] = array(0 => $IDF);
		
		//find the Tf-Idf vector for each document for that token(docID => Tf*Idf)
		for($i = 0; $i < $df; $i++){
			$docID = $value["token"][$i];
			$Tf = $value["tf"][$i];
			$TfIdfScore = $Tf*$IDF;
			$TfIdf[$key][$docID] = $TfIdfScore;
			
			//print_r($value["token"]);
			if($docID<=$N && $docID>=1){
				//echo $docID;
				//echo "<br />";
				$docLength[$docID] = $docLength[$docID]+($TfIdfScore*$TfIdfScore);
			}
			
		//print_r($docLength);
		//echo $df;
		
			
			//find the sum of square of each tf-idf score for each document for that token (length)
			//$docLength[$docID] = $docLength[$docID]+($TfIdfScore*$TfIdfScore);
		}
		//exit(0);
	}
	
	//find the length of each doucment (final step)
	for($i = 1; $i <= $N; $i++){
		$docLength[$i] = sqrt($docLength[$i]);
	}
	
	//print_r($TfIdf);
	//exit(0);
	
	//normalize each document in the Tf-Idf vector by its length
	foreach($TfIdf as $key => $value){
		foreach($value as $docID => $score){
			if($docID != 0){
				$TfIdf[$key][$docID] = $score/$docLength[$docID];		
			}
			
		}
	}
	//print_r($TfIdf);
	//exit(0);
	return $TfIdf;
}

//make token IDF vector
function makeTokenIDF($TfIdfVector){
	$tokenIndex = array();
	$index = 0;
	foreach($TfIdfVector as $key => $value){
		$tokenIndex[$key] = $index;
		//$tokenIndex[$key][1] = $TfIdfVector[$key][0];
		$index++;
	}
	return $tokenIndex;
}

//make document vector
/*function makeDocVector($TfIdfVector,$tokenIndex){
	$docVector = array();
	$index = 0;
	foreach($TfIdfVector as $key){
		foreach($key as $value => $docID){
			$docVector[$docID][$tokenIndex[$key][0]] = $TfIdfVector[]
		}
		$index++;
	}
}*/

createTfIDF();

?>



</body>
</html>