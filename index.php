<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>PIC UPLOAD EXAMPLE</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
	body {
		width: 1000px;
	}
	#image-proview,#reg_pic { 
		max-height: 200px;
		max-width: 200px;
	}
	#thumb {
		max-width: 300px;
		max-height: 200px;
		padding: 15px;
	}

</style>
</head>
<body>
	<?php
		// prepend a base path if Predis is not present in your "include_path".

		require __DIR__.'/vendor/autoload.php';
		Predis\Autoloader::register();
		//use local
		//$redis = new Predis\Client(getenv('REDIS_URL'));
        
		
        //use heroku
		$redis = new Predis\Client(
		array(
			'host' => parse_url($_ENV['REDIS_URL'], PHP_URL_HOST),
			'port' => parse_url($_ENV['REDIS_URL'], PHP_URL_PORT),
			'password' => parse_url($_ENV['REDIS_URL'], PHP_URL_PASS),
		));

		
		


		/*  cache client 引入及設定  */
		// Using MemcacheSASL client
		/*
		include('MemcacheSASL.php');
		$m = new MemcacheSASL;
		$servers = explode(",", getenv("MEMCACHIER_SERVERS"));
		foreach ($servers as $s) {
		  $parts = explode(":", $s);
		  $m->addServer($parts[0], $parts[1]);
		}
		$m->setSaslAuthData(getenv("MEMCACHIER_USERNAME"), getenv("MEMCACHIER_PASSWORD"));
		*/
		/*  cache client 引入及設定  */

		/*  外部 S3 class 設定 */
		//include the S3 class				
		if (!class_exists('S3')) require_once('S3.php');
		//AWS access info
		if (!defined('awsAccessKey')) define('awsAccessKey', getenv('S3_KEY'));
		if (!defined('awsSecretKey')) define('awsSecretKey', getenv('S3_SECRET'));
		//instantiate the class
		$s3 = new S3(awsAccessKey, awsSecretKey);
		
		// 引入 SQS class 並 initiate
// $sqs = new SQS('Access Key Here', 'Secret Key Here');
require_once('sqs.php');
$sqs = new SQS(awsAccessKey, awsSecretKey);

// 先設定好 queue 的位置
$input_queue = "https://sqs.us-west-2.amazonaws.com/500101126759/input";



/******
	上傳檔案到 S3（等待 process 程式處理）
******/
		/* 外部 S3 class 設定 */

		/* 上傳表單 post 回同一頁，判斷如果有上傳把檔案接回來 */
		/* 判斷附檔名，若為常見圖檔(jpg, png, gif)則將檔案內容存進 cache 並上傳到 S3 */
		/* 否則跳出不支援提示 */
		if(isset($_POST['Submit'])){
		    //retreive post variables
		    $fileName = $_FILES['theFile']['name'];
		    $fileTempName = $_FILES['theFile']['tmp_name']; 
		    $path_ext = pathinfo($fileName, PATHINFO_EXTENSION); // get filename extension
		    $ext = strtolower($path_ext);

			$data = file_get_contents($fileTempName);
		    switch ($ext) {
		    	case 'jpg':
		    	case 'jpeg':
		    	case 'png':
		    	case 'gif':
					// get file content
		    		$data = file_get_contents($fileTempName);
					// caching using local file name as key 
		    		//$m->set($fileName,$data,0);

					$redis->set($fileName, $data);

					
					// 範例：把目前時間當成 message 送給 input queue
                    // （縮圖程式的 message 內容改為圖片位置以及希望得到的 size 等資訊）
                    date_default_timezone_set('Asia/Taipei');
                    $send = $sqs->sendMessage($input_queue, $fileName );
                    echo "Message Send: <br>";
                    print_r($send);
                     echo "<br>";
		    		// saving file on S3
					if ($s3->putObjectFile($fileTempName, "nccus3", $fileName, S3::ACL_PUBLIC_READ)) {
					    echo "We successfully uploaded your file.";  
					} else {  
					    echo "Something went wrong while uploading your file... sorry.";  
					} 
		    		break;
		    	default:
		    		echo "File type not supported!";
		    }
		}
		/* end 上傳表單判斷 */
	?>

	<!-- 預覽縮圖 div -->
    <div class="image-proview" id="image-proview-layer">
    	<img id="reg_pic" src="upload_photo.PNG"/>
    </div>
	<!-- end 預覽縮圖 div -->
	<!-- 上傳表單，選擇檔案後 onchange 會改變預覽圖案 -->
    <form action="" method="post" enctype="multipart/form-data" name="form1" id="form1"> 
	    <input class="but" id="theFile" name="theFile" type="file" onchange="ImagesProview(this)" />
	    <input name="Submit" type="submit" value="上傳">
    </form>
    <!-- end 上傳表單 -->
    <br>
    <?php
		/* 用 foreach 把 S3 所有的圖讀出來顯示，若為資料夾就掉過 */    
		// Get the contents of our bucket
		$contents = $s3->getBucket('nccus3');
		foreach ($contents as $file){
			$fname = $file['name'];
			$value = $redis->get($fname);
			$num=strrpos($fname,"/"); // if $file is a directory path
			if ($num === false) {
				$furl = "http://nccus3.s3.amazonaws.com/".$fname;
				echo "<a href=\"image_cache.php?fn=$fname\" alt=\"$fname\"><img id=\"thumb\" src=\"$furl\" /></a>";
			}
		}


		/* end 讀圖 */	
	?>
	<!-- javascript 縮圖程式 -->
	<script type="text/javascript">
		var isIE=function() {
		   return (document.all) ? true : false;
		}

		function ImagesProview(obj) {
			var newPreview = document.getElementById("image-proview-layer");
			var imagelayer = document.getElementById('image-proview') 
			if(imagelayer){
				newPreview.removeChild(imagelayer);
			}

			if (isIE()) {
				obj.select();  
				var imgSrc = document.selection.createRange().text;  
				var objPreviewFake = document.getElementById('image-proview-layer');
				objPreviewFake.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = imgSrc;  
			} else {
				window.URL = window.URL || window.webkitURL;
				newPreview.innerHTML = "<img src='"+window.URL.createObjectURL(obj.files[0])+"' id='image-proview'/>"
			}
		}

		// function autoSizePreview( objPre, originalWidth, originalHeight ) {  
		// 	var zoomParam = clacImgZoomParam( 200, 200, originalWidth, originalHeight );  
		// 	objPre.style.width = zoomParam.width + 'px';  
		// 	objPre.style.height = zoomParam.height + 'px';  
		// 	objPre.style.marginTop = zoomParam.top + 'px';  
		// 	objPre.style.marginLeft = zoomParam.left + 'px';  
		// }  
	      
	 //    function clacImgZoomParam( maxWidth, maxHeight, width, height ) {  
	 //        var param = { width:width, height:height, top:0, left:0 };  
	          
	 //        if ( width>maxWidth || height>maxHeight ) {  
	 //            rateWidth = width / maxWidth;  
	 //            rateHeight = height / maxHeight;  
	              
	 //            if( rateWidth > rateHeight ) {  
		// 			param.width =  maxWidth;  
		// 			param.height = height / rateWidth;  
	 //            } else {  
		// 			param.width = width / rateHeight;  
		// 			param.height = maxHeight;  
	 //            }  
	 //        }
	 //        param.left = (maxWidth - param.width) / 2;  
	 //        param.top = (maxHeight - param.height) / 2;  
	          
	 //        return param;  
	 //    }	 
	</script>
	<!-- javascript 縮圖程式 -->
</body>
</html>