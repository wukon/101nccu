<html>
<head>
<style type="text/css">
/*  #thumb {
    max-width: 400px;
    max-height: 200px;
  }*/
</style>
</head>
<body>
<h1>Pic in Cache</h1>
<a href="index.php">回上一頁</a></body><br /><br />
<?php
  if (isset($_GET["fn"])) {
    $filename = $_GET["fn"];
    $path_ext = pathinfo($filename, PATHINFO_EXTENSION); // get filename extension
    $ext = strtolower($path_ext);
  }

  // Using MemcacheSASL client
  include('MemcacheSASL.php');
  $m = new MemcacheSASL;
  $servers = explode(",", getenv("MEMCACHIER_SERVERS"));
  foreach ($servers as $s) {
    $parts = explode(":", $s);
    $m->addServer($parts[0], $parts[1]);
  }
  $m->setSaslAuthData(getenv("MEMCACHIER_USERNAME"), getenv("MEMCACHIER_PASSWORD"));

  //include the S3 class
  if (!class_exists('S3'))require_once('S3.php');
  //AWS access info
  if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAJHECONHNZXH6XPTQ');
  if (!defined('awsSecretKey')) define('awsSecretKey', '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');
 

  $in_cache = $m->get("$filename");
  if ($in_cache) {
    echo "<img src=\"data:image/$ext;base64,".base64_encode($in_cache)."\" id=\"thumb\" alt=\"image 1\"/>";
  } else {
    echo "MISS...";
  }
   
?>

</body>
</html>
