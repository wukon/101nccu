<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>簡易版檔案上傳@【梅問題教學網】</title>
</head>
<body>
    <form id="form1" name="form1" enctype="multipart/form-data" method="post" action="?upload=true">
      <input type="file" name="Filedata" id="Filedata" /><input type="submit" name="button" id="button" value="上傳" />
      <hr />          
      <?php printf("1.檔案格式限：%s<br>2.檔案大小限：%s KB",$_GET['subName'],$_GET['maxSize']);?>
      <input name="formName" type="hidden" id="formName" value="<?php echo $_GET['formName']; ?>">
      <input name="upFloder" type="hidden" id="upFloder" value="<?php echo $_GET['upFloder']; ?>"> 
      <input name="prevImg" type="hidden" id="prevImg" value="<?php echo $_GET['prevImg']; ?>">
      <input name="rePic" type="hidden" id="rePic" value="<?php echo $_GET['rePicIpt']; ?>">
      <input name="allowSubName" type="hidden" id="allowSubName" value="<?php echo $_GET['subName']; ?>">
      <input name="maxSize" type="hidden" id="maxSize" value="<?php echo $_GET['maxSize']; ?>">
    </form>
</body>
</html>