<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bull Search</title>


<link href="style.css" rel="stylesheet" type="text/css" />

<?php
require_once "paginator.class.2.php";
ini_set('memory_limit', '512M');

//session_start();
//$_SESSION['PL'] = json_decode(file_get_contents('PL.json'), true);

//read the stopwords
//$stopWords = file_get_contents("StopWords.txt");
//$_SESSION['SW'] = explode(" ",$stopWords);
?>
<script type="text/javascript" src="js/jquery-1.11.0.js"></script>
<script type="text/javascript">

$(document).ready(function() {
 //alert('test');
 var search = "<?php echo $_GET['searchTextBox']; ?>"
$("#STB").val(search) ;
$(".a_img").find("img").removeAttr("style");
	

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
   
   		<input type="hidden" name="ipp" value="8"/>
        <input type="hidden" name="page" value="1"/>
		<input class="form-submit-button" type="image" src="images/mg.png" value="Search" /> </div>
        </div></div>
      </div> 
      <div class="contents">
<?php
//if(isset($_POST['name']) && function_exists($_POST['name']))
	//call_user_func($_POST['name']);

//$postingList = $_SESSION['PL'];

//print_r($postingList);
function findDocument(){
	
	
	
	//get the value form the textbox
	if ( ! empty($_GET['searchTextBox']) && isset($_GET['page']) && $_GET['page']==1){
		
		session_start();
		$_SESSION['docNames'] = json_decode(file_get_contents('docIndex.json'), true);
		
		//read the stopwords
		$stopWords = file_get_contents("StopWords.txt");		
		$postingList = json_decode(file_get_contents('PL.json'), true);//$_SESSION['PL'];
		$stopW = explode(" ",$stopWords);
		
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
		$_SESSION['queryFinal'] = $qArray;
		
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
		$_SESSION['docIndex'] = array_values($docIndex);
		$_SESSION['numD'] = count($docIndex);
		echo "<br />";
		echo $_SESSION['numD'].",000,000"." results";
		echo "<br /><br />";
		//print_r($_SESSION['docIndex']);
		//exit(0);
	
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

/*function returnDoc($docIndex){
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
}*/

function returnDoc(){
	$numD = $_SESSION['numD'];
	$docNames = $_SESSION['docNames'];
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
				display($docNames[$docIndex[$i]],$query);
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
	
	$nakedFile = preg_replace("/docs\//","",$document);
	$nakedFile = substr_replace($nakedFile,"",-4,4);
	
	if (preg_match("/<title>(.+)<\/title>/",$file,$matches) && isset($matches[1]))
   		$title = $matches[1];
	else
   		$title = $nakedFile;
   
    $idxs = array();
    //$re = '/(?<= [.!?] | [.!?][\'"])(?<!Mr\.|Mrs\.|Ms\.|Jr\.|Dr\.|Prof\.|Sr\.|St\.|\s[a-z]\.)\s+/ix';
	$re = '?';
    $stripped = preg_replace("/\r|\n/"," ",$file);
    //$stripped = preg_replace("<script.*?/script>","",$file);
	$stripped = preg_replace("~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is","",$stripped);
	$stripped = preg_replace("~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is","",$stripped);
    $stripped = strip_tags($stripped);
	$stripped = strtolower($stripped);
	$stripped = preg_replace("/[^a-zA-Z ]/","?",$stripped);
	$sentences = explode($re, $stripped);
    //$sentences = preg_split($re, $stripped, -1, PREG_SPLIT_NO_EMPTY);
	//print_r ($sentences);
	//exit(0);

    //$queries = $query;
    //$shouldBreak = false;
	$description = "";
	//$numOfSent = 1;
	$max = 0;

	foreach ($sentences as $s){
		$numOfWords = 0;
		//$counter = 0;
		$newQ = "";
		$flag = false;
		foreach ( $query as $q){
			
		/*if($shouldBreak){
			break;
		}*/
		
	
		
			/*$match = preg_match($pattern,$s);
			if($match == 1){
				$description = preg_replace($pattern,"$1",$s);
				$description = preg_replace("|(".$q.")|i","<b>$1</b>",$description);
				if (strlen( $description) <= (strlen($q) + 200) && strlen($description) > 1){
					$shouldBreak = true;
					break;
				}
			}*/
			if (strpos($s,$q) !== false) {
				if($numOfWords == 0)
					$newQ = $newQ.$q;
				else
					$newQ = $newQ." ".$q;
				$numOfWords++;
				$flag = true;
				
				//$tempDesc = preg_replace($pattern,"$1",$s);
				//$description = preg_replace("|(".$q.")|i","<b>$1</b>",$description);
				//$boldQ = "<b>".$q."</b>";
				//$tempDesc = preg_replace('|('.$q.')|','<b>$1</b>',$tempDesc);
				//$description = $description.$tempDesc."...";
			}
			
		}
		
		$pattern = "|.*?(.{1,100} ".$newQ." .{1,100}).*|i";
		if ($flag && $numOfWords >= $max){
			$description = preg_replace($pattern,"$1",$s);
			$description = preg_replace("|(".$newQ.")|i","<b>$1</b>",$description);
			
			//if (strlen( $description) <= (strlen($newQ) + 200) && strlen($description) > 1){
	
			//}
		}
		
	}
	
	
	
	//shorten the description
	$description = (strlen($description) >200) ? substr($description,0,199).'...' : $description;
	
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
                    
	if($description != NULL && $img!= NULL){
		echo "<li>"."<a href='".$document."'>".$title."</a><div class='a_'>";
			
		echo "<div class='a_img'>".$img."</div>";
		echo "<div class='a_desc'>".ucfirst($description)." ...</div></div></li>";
		
	}
	else if($description != NULL && $img== NULL){
		echo "<li>"."<a href='".$document."'>".$title."</a><div class='a_no_image'>";
		//echo '<div class="a_img">'.$img."</div>";
		echo "<div class='a_desc'>".ucfirst($description)." ...</div></div>";
		echo "</li>";
	}
	else if($description == NULL && $img!= NULL){
		echo "<li>"."<a href='".$document."'>".$title."</a>";
			
			
		echo "<div class='a_'><div class='a_img'>".$img."</div>";
		echo "<div class='a_desc'>No description available</div></div>";
		echo "</li>";
	}
	else if($description == NULL && $img== NULL){
		echo "<li>"."<a href='".$document."'>".$title."</a><div class='a_no_image'>";
		
			
		//echo '<div class="a_img">'.$img."</div>";
		echo "<div class='a_desc'>No description available</div></div>";
		echo "</li>";
	}
	
}

findDocument();
?>

</div>
</form>

</body>
</html>