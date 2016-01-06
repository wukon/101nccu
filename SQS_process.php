
<p>SQS PROCESS PAGE!</p>

<?php
// 引入 SQS class 並 initiate
// $sqs = new SQS('Access Key Here', 'Secret Key Here');
	/*  外部 S3 class 設定 */
		//include the S3 class				
		if (!class_exists('S3')) require_once('S3.php');
		//AWS access info
		if (!defined('awsAccessKey')) define('awsAccessKey', getenv('S3_KEY'));
		if (!defined('awsSecretKey')) define('awsSecretKey', getenv('S3_SECRET'));
		//instantiate the class
		$s3 = new S3(awsAccessKey, awsSecretKey);
require_once('sqs.php');
$sqs = new SQS(awsAccessKey, awsSecretKey);

// 先設定好 input/ouput queue 的位置
$input_queue = "https://sqs.us-west-2.amazonaws.com/500101126759/input";
$output_queue = "https://sqs.us-west-2.amazonaws.com/500101126759/process";

// 取出 message
$messages = $sqs->receiveMessage($input_queue);
echo "<p>***Receive Message*** <br>";
print_r($messages);
echo "</p>";
$m = $messages['Messages'][0];
$filename = $m['Body'] ;

$contents = $s3->getBucket('nccus3');
		foreach ($contents as $file){
			$fname = $file['name'];
			$value = $redis->get($fname);
			$num=strrpos($fname,"/"); // if $file is a directory path
			if ($num === false) {
				$furl = "http://nccus3.s3.amazonaws.com/".$filename;
				//echo "<a href=\"image_cache.php?fn=$fname\" alt=\"$fname\"><img id=\"thumb\" src=\"$furl\" /></a>";
			}
		}


/*****
	判斷 message 內容， call 縮圖 function，產生 outout message 內容 
*****/

$src = imagecreatefromjpeg($furl);
// get the source image's widht and hight
$src_w = imagesx($src);
$src_h = imagesy($src);
 
// assign thumbnail's widht and hight
if($src_w > $src_h){
$thumb_w = 100;
$thumb_h = intval($src_h / $src_w * 100);
}else{
$thumb_h = 100;
$thumb_w = intval($src_w / $src_h * 100);
}
 
// if you are using GD 1.6.x, please use imagecreate()
$thumb = imagecreatetruecolor($thumb_w, $thumb_h);
 
// start resize
imagecopyresized($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w,    $src_h);
 
 
// save thumbnail
imagejpeg($thumb, 'abc.jpg');

$input =$s3->inputResource(fopen('abc.jpg',"rb"), filesize('abc.jpg');
if(S3::putObject($input,'smalls3', $filename,S3::ACL_PUBLIC_READ)){
	echo "file upload success";
}

	
// 利用 message 的 ReceiptHandle 刪除 input Queue 裡的 message
$sqs->deleteMessage($input_queue, $m['ReceiptHandle']);
$hint = "ReceiptHandle: ".$m['ReceiptHandle']." is deleted.<br>(Body: ".$m['Body'].")";
echo "<p>***DELETE***<br>".$hint."</p>";

// 把處理結果的 message 放到 output queue 裡
echo "<p>***Result Message Send***<br>";
print_r($sqs->sendMessage($output_queue,$hint));
echo "</p>";

?>

<p><a href="SQS_input.php">Input page</a></p>