<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bull Search</title>


<link href="stye.css" rel="stylesheet" type="text/css" />

<?php

ini_set('memory_limit', '512M');

session_start();
$_SESSION['PL'] = json_decode(file_get_contents('PL.json'), true);

//read the stopwords
$stopWords = file_get_contents("StopWords.txt");
$_SESSION['SW'] = explode(" ",$stopWords);
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



	<form name='runQuery' id='runQuery' method='get' action="index.php">
    <div class="search_head">
    <div class="search_bar">
    <a href="index.php">
    <img src="images/bull.png"  /></a>
    <div class="search-box">
		<input class="form-search-box" id="STB" type="text" name='searchTextBox' />
        <div class="search-logo">
   
		<input class="form-submit-button" type="image" src="images/mg.png" value="Search"/> </div>
        </div></div>
      </div> 
      <div class="contents">
<?php
//if(isset($_POST['name']) && function_exists($_POST['name']))
	//call_user_func($_POST['name']);

//$postingList = $_SESSION['PL'];

//print_r($postingList);
function findDocument(){
	
	$postingList = $_SESSION['PL'];
	$stopW = $_SESSION['SW'];
	
	//get the value form the textbox
	if ( ! empty($_GET['searchTextBox'])){
    	$query = $_GET['searchTextBox'];
		
		//clean the query
		$query = preg_replace("/[^a-zA-Z ]/","",$query);
		
		//change everything to lowercase
		$query = strtolower($query);
		$query = trim($query);		
		$query = preg_replace("/\s+$/"," ",$query);
		//echo $query;
		
		//parse the query
		$qTemp = explode(" ",$query);
		$qArray = array();
		$j = 0;
		
		//remove stop words
		for($i=0;$i<count($qTemp);$i++){
			if(!in_array($qTemp[$i],$stopW)){
				$qArray[$j] = $qTemp[$i];
				$j++;
			}
				
		}
		
		//print_r($qArray);
		
		if(count($qArray)==0 || !array_key_exists($qArray[0],$postingList)){
				echo "</br>";
				echo "No Results Found";
				exit (0);
		}
		else{
			$docIndex = $postingList[$qArray[0]]["token"];
			
			//collect document index
			for($i=1;$i<count($qArray);$i++){
				if(!array_key_exists($qArray[$i],$postingList)){
					echo "</br>";
					echo "No Results Found";
					exit (0);
				}
				$tempIndex = $postingList[$qArray[$i]]["token"];
				$docIndex = array_intersect($docIndex,$tempIndex);
			}
		}
		
		echo "<br />";
		echo count($docIndex).",000,000"." results";
		echo "<br /><br />";
		//print_r($docIndex);
		//echo "<br />";
	
		returnDoc($docIndex);
	}	
	
}

function returnDoc($docIndex){
	$index = 1;
	foreach ( glob("docs/*.htm") as $filename ){
		
		//Remove doc from filename to get only the name of the file
		$nakedFile = preg_replace("/docs\//","",$filename);
		$nakedFile = substr_replace($nakedFile,"",-4,4);
		if(in_array($index,$docIndex)){
			//echo $index;
			//output the link
			echo '<a href="'.$filename.'">'.$nakedFile.'</a><br/>';
		}
		$index++;
	}
}

findDocument();
?>

</div>
</form>

</body>
</html>