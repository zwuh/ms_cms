<?php
 require_once('../config.php');
 ms_load_module('techrep');
 session_start();
 ms_manage_check_auth();
 global $ms_config;

 $dblink = ms_get_db_link();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Tech Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
<script lang="javascript" type="text/javascript" src="util.js.php"></script>
</head>
<body>
<div>
<a href="../">回首頁</a> &nbsp; <a href=".">回管理首頁</a>
&nbsp;<a href="techrep-list.php">回技術報告列表</a>
&nbsp;<a href="auth.php?logout">登出</a>
</div>
<hr />
<?php
 if (isset($_GET['delete']))
 {
   techrep_delete($dblink, intval($_GET['delete']));
   exit ('</body></html>');
 }
 else if (isset($_GET['modify']))
 {
   $target = techrep_load_one_by_id($dblink, intval($_GET['modify']));
 }
 else if (isset($_POST['title']))
 {
   if (isset($_POST['id']))
   { techrep_update($dblink, $_POST); }
   else
   { techrep_write($dblink, $_POST); }
   exit ('</body></html>');
 }
 else
 {
   $target = Array (
    'title' => '',
    'author' => '',
    'year' => '',
    'serial' => '',
    'file_abstract' => '',
    'file1' => '',
    'file2' => '',
    'file3' => ''
   );
 }
?>
<div>
<form name="techrep-edit" action="techrep-edit.php" method="post">
<?php
 if (isset($_GET['modify']))
 {
   echo '<input type="hidden" name="id" value="'.
        $target['id'].'" />'."\n";
 }
?>
<div>
 <label for="year">年</label>
 <input type="text" name="year" value="<?php echo $target['year']; ?>"/>
</div>
<div>
 <label for="serial">序</label>
 <input type="text" name="serial" value="<?php echo $target['serial']; ?>" />
</div>
<div>
 <label for="title">主題</label>
 <input type="text" name="title" value="<?php echo $target['title']; ?>" />
</div>
<div>
 <label for="author">作者</label>
 <input type="text" name="author" value="<?php echo $target['author']; ?>" />
</div>
<div>
 <label for="file_abstract">大綱檔</label>
 <input type="text" name="file_abstract" value="<?php echo $target['file_abstract']; ?>" onclick="ms_kcf_techrep_selector(this);" />
</div>
<div>
 <label for="file1">檔案一</label>
 <input type="text" name="file1" value="<?php echo $target['file1']; ?>" onclick="ms_kcf_techrep_selector(this);" />
</div>
<div>
 <label for="file2">檔案二</label>
 <input type="text" name="file2" value="<?php echo $target['file2']; ?>" onclick="ms_kcf_techrep_selector(this);" />
</div>
<div>
 <label for="file3">檔案三</label>
 <input type="text" name="file3" value="<?php echo $target['file3']; ?>" onclick="ms_kcf_techrep_selector(this);" />
</div>
<input type="reset" value="Reset" />
<input type="submit" value="Submit" />
</form>
</div>
</body>
</html>
<?php
 ms_close_db_link($dblink);
?>
