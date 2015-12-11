<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>梅問題教學網【單檔上傳】</title>
<script language="javascript">
function uploadFile(){
	var formName = "form1"; //表單名稱
	var prevImg = "preUpLoadImg"; //顯示圖片ID
	var upFloder = "img"; //上傳目錄名稱
	var rePicName = "rePicFileName"; //回傳圖片上傳名稱
	var subName ="jpg,png,gif"; //可上傳副檔名
	var maxSize = 1024; 
	
	var winTitle = "fileUpload"; //視窗名稱
	var winWidth = 400; //視窗寬
	var winHeight = 180;　//視窗高
	
	window.open('mwt_upload_control.php?formName='+formName+'&prevImg='+prevImg+'&upFloder='+upFloder+'&rePicIpt='+rePicName+'&subName='+subName+"&maxSize="+maxSize,winTitle,'width='+winWidth+',height='+winHeight);	
}
</script>
</head>

<body>
<form id="form1" name="form1" method="post">
    <a href="javascript:uploadFile();"><img src="icon_prev.gif" alt="顯示上傳預覽圖片" name="preUpLoadImg" border="0" id="preUpLoadImg"/></a>
    <input name="rePicFileName" type="text" id="rePicFileName" />
</form>
</body>
</html>