<?php
if (isset($_GET['upload'])) {

	$file_name = $_FILES['Filedata']['name']; //取得檔名
	$file_size = number_format(($_FILES['Filedata']['size']/1024), 1, '.', ''); //取得檔案大小
	$allowSubName = $_POST['allowSubName']; //允許檔案格式
	$allowMaxSize = $_POST['maxSize']; //允許上傳大小
	$subn_array = explode(",",$allowSubName);//分割允許上傳副檔名
	
	$checkSubName = "";
	$checkSize = "";
	$checkmsg = "";
	
	$fn_array=explode(".",$file_name);//分割檔名
	$mainName = $fn_array[0];//檔名
	$subName = $fn_array[1];//副檔名	
	
	//判斷檔案格式
	foreach($subn_array as $index => $value){
		if($subName == $value){			
			$checkSubName ="ok";
						
			break;
		}else{
			$checkSubName ="不符";
		
		}
	}
	
	//判斷上傳檔案
	if($file_size <= $allowMaxSize){		
		$checkSize = "ok";
	}else{
		$checkSize = "太大";
	}
	

	if($checkSize == "ok" && $checkSubName == "ok"){
		$upFloder = $_POST['upFloder'];
		if($upFloder != ""){
			$upload_dir = $upFloder.'/';
		}
			
		//中文檔名處理
		if (mb_strlen($mainName,"Big5") != strlen($mainName))
		{
			$mainName = "undefine".date("ymdHi");//重新命名=檔名+日期
			$file_name = sprintf("%s.%s",$mainName,$subName);//組合檔名
		}	
		$upload_file = $upload_dir . basename($file_name);
		
		
		//檔名重覆處理
		$x=1;
		while(file_exists($upload_file)){
			$file_name = sprintf("%s_%d.%s",$mainName ,$x++ ,$subName);//組合檔名
			$upload_file = $upload_dir . basename($file_name);
		}
		
		$temploadfile = $_FILES['Filedata']['tmp_name'];
		$result = move_uploaded_file($temploadfile , $upload_file);
	
	}else{
		
		$checkmsg = sprintf("1.檔案格式：%s<hr>2.檔案大小：%s",$checkSubName,$checkSize);
	}
	
?>
	<?php if (isset($result)){ //判斷上傳結果?>
        <script language = "javascript">	
            window.opener.document.getElementById("<?php echo $_POST['prevImg']; ?>").src = '<?php echo $_POST['upFloder']."/" ?>'+'<?php echo $file_name; ?>';
            window.opener.document.getElementById("<?php echo $_POST['prevImg']; ?>").width = 100;
            window.opener.document.getElementById("<?php echo $_POST['formName']; ?>").<?php echo $_POST['rePic']; ?>.value = '<?php echo $file_name; ?>';
            window.close();
        </Script>
    <?php }else{ printf("<b style='color:red;'>上傳失敗:</b><hr>%s",$checkmsg); }?>
    
<?php }else{ ?>
    <?php include_once('mwt_upload_form.php');?>
<?php }?>