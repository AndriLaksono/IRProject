<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bull Search</title>


<link href="style.css" rel="stylesheet" type="text/css" />

<?php
require_once "paginator.class.2.php";
ini_set('memory_limit', '512M');

/*session_start();
$_SESSION['tfIdf'] = json_decode(file_get_contents('TfIdf.json'), true);
$_SESSION['tokenIndex'] = json_decode(file_get_contents('tokenIndex.json'), true);
$_SESSION['docIndex'] = json_decode(file_get_contents('docIndex.json'), true);
$_SESSION['qVector'] = array_fill_keys(array_values($_SESSION['tokenIndex']),0);

//count the total number of document
$_SESSION['nOfDocs'] = json_decode(file_get_contents('numberOfDocs.json'), true);

//read the stopwords
$stopWords = file_get_contents("StopWords.txt");
$_SESSION['SW'] = explode(" ",$stopWords);*/
?>

<script type="text/javascript" src="js/jquery-1.11.0.js"></script>
<script type="text/javascript">

$(document).ready(function() {
 //alert('test');
 var search = "<?php echo $_GET['searchTextBox']; ?>"
$("#STB").val(search) ;
	

// Handler for .load() called.
});


</script>
</head>

<body>



	<form name='runQuery' id='runQuery' method='get' action="search.php">
    <div class="search_head">
    <div class="search_bar">
    <a href="search.php">
    <img src="images/bull.png"  /></a>
    <div class="search-box">
		<input class="form-search-box" id="STB" type="text" name='searchTextBox' />
        <div class="search-logo">
   
   		<input type="hidden" name="ipp" value="8"/>
        <input type="hidden" name="page" value="1"/>
		<input class="form-submit-button" type="image" src="images/mg.png" value="Search" /> </div>
        </div></div>
      </div> 
      <div class="contents">
      
<?php

function findDocument(){
	
	//get the value form the textbox
	if ( !empty($_GET['searchTextBox']) && isset($_GET['page']) && $_GET['page']==1){
		session_start();
		//$_SESSION['tfIdf'] = json_decode(file_get_contents('TfIdf.json'), true);
		//$_SESSION['tokenIndex'] = json_decode(file_get_contents('tokenIndex.json'), true);
		$_SESSION['docIndex'] = json_decode(file_get_contents('docIndex.json'), true);
		//$_SESSION['qVector'] = array_fill_keys(array_values($_SESSION['tokenIndex']),0);

		//count the total number of document
		//$_SESSION['nOfDocs'] = json_decode(file_get_contents('numberOfDocs.json'), true);

		//read the stopwords
		$stopWords = file_get_contents("StopWords.txt");
		//$_SESSION['SW'] = explode(" ",$stopWords);
		
		
		//the original tf-idf matrix
		$TfIdf = json_decode(file_get_contents('TfIdf.json'), true);//$_SESSION['tfIdf'];
	
		//maps each token to its index position
		$tokeIndex = json_decode(file_get_contents('tokenIndex.json'), true);//$_SESSION['tokenIndex'];
		
		//dictionary vector initialized to zero for each token in the dictionary
		$qVector = array_fill_keys(array_values($tokeIndex),0);//$_SESSION['qVector'];
	
		//the stop word vector
		$stopW = explode(" ",$stopWords);//$_SESSION['SW'];
	
		//total number of tokens
		$N = json_decode(file_get_contents('numberOfDocs.json'), true);//$_SESSION['nOfDocs'];
	
		//assigns rank to each document, maps rank to document ID
		$docRank = array();
	
		//stores document status, if the document contains none of the query words, the status is 0 else 1
		$QDsimilarity = array_fill(1,$N,0);

		
    	$query = $_GET['searchTextBox'];
		
		//clean the query
		$query = preg_replace("/[^a-zA-Z ]/","",$query);
		
		//change everything to lowercase
		$query = strtolower($query);
		$query = trim($query);		
		$query = preg_replace("/\s+$/"," ",$query);
		
		//parse the query and store it in a temp variable
		$qTemp = explode(" ",$query);
		
		//this array stores the final query (just the original tokens)
		$queryFinal = array();
		$j = 0;
		
		//echo "<br />";
		//print_r($qTemp);
		//exit(0);
		
		//echo "<br />";
		//print_r($qVector);
		
		//remove stop words and find token frequency for each token in query
		for($i=0;$i<count($qTemp);$i++){
			//remove stop words
			if(!in_array($qTemp[$i],$stopW)){
				//find token frequency for each token in query
				if(array_key_exists($qTemp[$i],$TfIdf)){
					$queryFinal[$j] = $qTemp[$i];
					$qVector[$tokeIndex[$qTemp[$i]]]++;
					$j++;
				}				
			}				
		}
		//echo "<br />";
		//print_r($qVector);
		//exit(0);
		
		//if none of the tokens exist in any of the document then exit with no results
		if($j==0){
				echo "</br>";
				echo "No Results Found";
				exit (0);
		}
		else{
			
			//find the normalized query
			$qVector = findNormalizedQuery($qVector,$TfIdf,$queryFinal,$tokeIndex);
			
			//echo "<br />";
			//print_r($QDsimilarity);
			//exit(0);
			
			//find query document similarity vector
			for($i=0;$i<count($queryFinal);$i++){
				
				$token = $TfIdf[$queryFinal[$i]];
				
				//find document index and add the partial values from dot product
				foreach($token as $key => $value){
					if($key > 0 && $key<=$N){
						$QDsimilarity[$key] = $QDsimilarity[$key]+$value*$qVector[$tokeIndex[$queryFinal[$i]]];
					}
					
				}
			}

			//sort query document vector
			arsort($QDsimilarity);
			//echo "<br />";
			//print_r($QDsimilarity);
			//exit(0);
			
			//count the number of docs with non-zero values
			$_SESSION['numD'] = numDocs($QDsimilarity);
			
			//rank documents
			$_SESSION['docRank'] = array_keys($QDsimilarity);
			
			//final query
			$_SESSION['queryFinal'] = $queryFinal;
		}
		
		echo "<br />";
		echo $_SESSION['numD'].",000,000"." relevant results";
		echo "<br /><br />";
		
		returnDoc();
	}	
	else if(isset($_GET['page'])){
		session_start();
		echo "<br />";
		echo $_SESSION['numD'].",000,000"." relevant results";
		echo "<br /><br />";
		returnDoc();
	}
}

//count number of docs with non-zero value
function numDocs($QDsimilarity){
	$count = 0;
	foreach($QDsimilarity as $key => $value){
		if($value == 0){
			break;
		}
		$count++;
	}
	return $count;
}

//find the normalized query
function findNormalizedQuery($qVector,$TfIdf,$queryFinal,$tokeIndex){
	//length of the query vector
	$lengthQV = 0;
	
	//find the tf-idf score for the query
	for($i=0;$i<count($queryFinal);$i++){
		$freqVal =  $qVector[$tokeIndex[$queryFinal[$i]]];
		$qVector[$tokeIndex[$queryFinal[$i]]] = $freqVal*$TfIdf[$queryFinal[$i]][0];
		$score = $qVector[$tokeIndex[$queryFinal[$i]]];
		$lengthQV = $lengthQV+$score*$score;
	}
	
	//normalize the query
	if($lengthQV != 0){
		for($i=0;$i<count($queryFinal);$i++){		
			$qVector[$tokeIndex[$queryFinal[$i]]] = $qVector[$tokeIndex[$queryFinal[$i]]]/$lengthQV;
		}
	}
	
	return $qVector;
}

function returnDoc(){
	$numD = $_SESSION['numD'];
	$docRank = $_SESSION['docRank'];
	$docIndex = $_SESSION['docIndex'];
	$query = $_SESSION['queryFinal'];
	
	$num_rows = $numD;
	$pages = new Paginator;
	$pages->current_page=1;
	$pages->items_total = $num_rows;
	$pages->default_ipp = 8;
	$pages->mid_range = 7;
	$pages->paginate();
	
	//echo "<br><br>";
	echo "<div class=","results","><ul>";

	for($i=$pages->low;$i<=$pages->high;$i++){
			if($i<$numD){
				display($docIndex[$docRank[$i]],$query);
			}
		
			//$nakedFile = preg_replace("/docs\//","",$docIndex[$docRank[$i]]);
			//$nakedFile = substr_replace($nakedFile,"",-4,4);
			
			//echo "<li>", '<a href="'.$docIndex[$docRank[$i]].'">'.$nakedFile.'</a></li>';
			
	}
	echo "</ul></div>";
	echo "<br>";
	echo $pages->display_pages();
}

function display($document,$query){
	// Get Image and surrounding text
    $file = file_get_contents($document);
    $idxs = array();
    $re = '/(?<=[.!?]|[.!?][\'"])\s+/';
    $stripped = preg_replace("/\r|\n/"," ",$file);
    //$stripped = preg_replace("<script.*?/script>","",$file);
	$stripped = preg_replace("~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is","",$stripped);
	$stripped = preg_replace("~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is","",$stripped);
    $stripped = strip_tags($stripped);
	$stripped = strtolower($stripped);
    $sentences = preg_split($re, $stripped, -1, PREG_SPLIT_NO_EMPTY);
	//print_r ($sentences);
	//exit(0);

    $queries = $query;
    $shouldBreak = false;
	$description = "";

    foreach ( $queries as $q){
		$pattern = "|.*?(.{1,100} ".$q." .{1,100}).*|i";
		if($shouldBreak){
			break;
		}
		foreach ($sentences as $s){
			$match = preg_match($pattern,$s);
			if($match == 1){
				$description = preg_replace($pattern,"$1",$s);
				$description = preg_replace("|(".$q.")|i","<b>$1</b>",$description);
				if (strlen( $description) <= (strlen($q) + 200) && strlen($description) > 1){
					$shouldBreak = true;
					break;
				}
			}
		}
	}
	preg_match_all("|<img.*?>|",$file, $matches);
    $largest = -1;
    $img = "";
    $tmp =-1;
    foreach ($matches[0] as $elem){
		$width = preg_replace("|.*width=[ ]*\"(.*?)\".*|","$1",$elem);
		$height = preg_replace("|.*height=[ ]*\"(.*?)\".*|","$1",$elem);
		if (strLen ($width) > 5){
			$width = -1;
			$height= -1;
		} 
		if ( $width > $height ){
			$tmp = $width;   
		}else{
			$tmp = $height;
		}
		if ($tmp > $largest){
			$largest = $tmp;
			$img = $elem;
		}
	}
                    
	$nakedFile = preg_replace("/docs\//","",$document);
	$nakedFile = substr_replace($nakedFile,"",-4,4);
		
	if($description != NULL){
		echo "<li>", '<a href="'.$document.'">'.$nakedFile.'</a></li>';
			
		echo "...".$description."...<br />";
		echo $img."<br/>";
		echo "<br/>";
	}
	else{
		echo "<li>", '<a href="'.$document.'">'.$nakedFile.'</a></li>';
			
		echo "No Description available"."<br />";
		echo $img."<br/>";
		echo "<br/>";
	}
	
}

findDocument();
?>

</div>
</form>

</body>
</html>
