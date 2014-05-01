<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php

set_time_limit(0); 
ignore_user_abort(true);
ini_set('max_execution_time', 0);

  //main parsing file
  function parseFiles(){
	 $i=1;
	 
	 $storeDocName = array();
	 
	 //clean each .htm document in the docs folder
	 foreach ( glob("docs/*.htm") as $filename ){
		 
		 //define output directory
		 $directory = "Results/unsorted/";
		 
		 //check if the document exist
		 $flag = true;//false;//checkNotExist($filename,$directory);//
		 
		 //if does not exist
		 if($flag){
			 //get the content of each document
			 $contents = file_get_contents($filename);
			 
			 //clean the text
			 $stripped = cleanText($contents);
			 
			 //read the stopwords
			 $stopWords = file_get_contents("StopWords.txt");
			 $stopWords = explode(" ",$stopWords);
			 
			 //tokenize the text in each document (my func)
			 $finalStripped = tokenize($stripped,$i,$stopWords);
			 
			 //get the name of the file and store it in an array
			 $storeDocName[$i] = $filename;
			 
			 //Remove doc from filename to get only the name of the file
			 $filename = preg_replace("/docs\//","",$filename);
			 
			 //add index to filename in order to index document later
			 $filename = substr_replace($filename,"txt",-3,3);
			 
			 //put the filtered content in the file and save the file in a new folder Results
			 file_put_contents($directory.$filename, $finalStripped);
			 
			 
			 
			 //must check, not sure what permission level is this
			 chmod($directory.$filename, 0644);
			 
			 //output the link
			 echo $i.". ".'<a href="'.$directory.$filename.'">'.$filename.'</a><br/>';
			 
			 $i++;
		}	   
		else{
			//get the name of the file and store it in an array
			 $storeDocName[$i] = $filename;
			
			//output the link
			$filename = preg_replace("/docs\//","",$filename);		
			 
			 //echo "<br />";
			//print_r($storeDocName);
			
			$filename = substr_replace($filename,"txt",-3,3);
			//echo $i.". ".'<a href="'.$directory.$filename.'">'.$filename.'</a><br/>';
			
			$i++;
		}
		
	}
	
	file_put_contents("docIndex.json",json_encode($storeDocName));
	
	//sort the strings in all files
	sortString();
 }
 
 //clean the text
 function cleanText($contents){
	 //remove all javascripts
	 $stripped = preg_replace("~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is","",$contents);
	 $stripped = preg_replace("~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is","",$stripped);
	 
	 //remove all tags and keep the contents
	 $stripped =strip_tags($stripped);	 
	 
	 //remove anything but letters and replace them with space
	 $stripped = preg_replace("/[^a-zA-Z ]/"," ",$stripped);	 
	 
	 //change everything to lowercase
	 $stripped = strtolower($stripped);
	 
	 return $stripped;
 }
 
 //tokenize the text in each document
 function tokenize($stripped,$i,$stopWords){
	 
	   $token = strtok($stripped, " ");
	   $newStripped = "";
	   //$docIndex = "\t".$i;
	   if(!in_array($token,$stopWords)){
		   $newStripped = $token;
	   }	   

		while ($token != false)
		{
			$token = strtok(" ");
			$delim = "\r\n";
			if(!in_array($token,$stopWords)){
				$newStripped = $newStripped.$delim.$token;
			}
			
		}
		//$newStripped = substr_replace($newStripped,"",-2,2);
	 return $newStripped;
 }
 
 //check if document already exist
 function checkNotExist($filename,$directory){
	$filename = preg_replace("/docs\//","",$filename);
	$filename = substr_replace($filename,"",-4,4);
	foreach ( glob($directory."*.txt") as $fromResult ){			 
		$fromResult = preg_replace("/Results\/unsorted\//","",$fromResult);
		$fromResult = substr_replace($fromResult,"",-4,4);
		if( $filename == $fromResult){
			return false;
		 }
	}
	return true;
 }
 
 //run matlab script to sort the words in all files
 function sortString(){
	 $matlabFile = "SortString";
	 $command = 'start matlab -nosplash -nodesktop -minimize -r "'.$matlabFile.';exit;"';
	 exec($command);
 }
 
 //call the first mathod
 parseFiles();
 
?>
</body>
</html>