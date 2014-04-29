<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form name='createPL' id='createPL' method='post' >
<?php

set_time_limit(0); 
ignore_user_abort(true);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

function readInvertedIndex(){
	//create path variable to read the .csv file	
	//$filename = "Results/sorted/InvertedIndex.csv";
	$filename = "Results/sorted/";
	
	//call createPL function to create posting list (hashmap)
	$postingList = createPL($filename);
	
	//session_start();
	
	// store session data
	//$_SESSION['PL']=$postingList;
	
	//$arr1 = array ('a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5);
	file_put_contents("PL.json",json_encode($postingList));
	
	//echo "<br />";
	//print_r($postingList);
	
}

//This function creates the posting list
function createPL($path){	
	//$row=0;
	
	/*  The format of the hash index is:
		Dictionary Word=>array ("token"=>array of #docId,"tf"=>array of term freq for each doc)
	*/
	$postingList = array();
	
	//start from here, crashes after reading 3rd .csv file (memory issue)
	
	foreach ( glob($path."*.csv") as $filename ){
	
	//if file exist open the file and store data in variable handle
	if (($handle = fopen($filename, "r")) !== FALSE) {
		//for each line in the .csv file loaded in handle variable
    	while (! feof($handle)){
			//read each line and store it in variable data
			$data = fgetcsv($handle);
			
			//get the 2nd column of data and find the max			
			//$numOdDoc = array_column($data,2);
			
			//if key/dictionary word exist in the posting list modify document and term frequency
			if (array_key_exists($data[0],$postingList)) {
    			$postingList[$data[0]]["token"][] = $data[1];
				$postingList[$data[0]]["tf"][] = $data[2];
				//$termFreq = count($postingList[$data[0]]["tf"]);
				//$postingList[$data[0]]["df"]++;
				
			}
			//else add the key to the posting list
			else{								
				$postingList[$data[0]] = array(
								"token"=>array($data[1]),
								"tf"=>array($data[2])
								);
			}
			
			//$row++;
    	}		
		echo $filename;
    	fclose($handle);
		
	}
		
	}
	return $postingList;
}

readInvertedIndex();

?>

</form>
<!--<script language="JavaScript">document.createPL.submit();</script>-->
</body>
</html>