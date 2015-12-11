<?php 
//?x=01.jpg
if(isset($_GET["x"])){
	$imgSource = "./img/" . $_GET["x"];	
	unlink($imgSource); 
	
	 $deleteGoTo = "del_ok.php";
	 if (isset($_SERVER['QUERY_STRING'])) {
		$deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
		$deleteGoTo .= $_SERVER['QUERY_STRING'];
	 }
	 header(sprintf("Location: %s", $deleteGoTo)); 
}
?>