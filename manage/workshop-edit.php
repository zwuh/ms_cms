<?php
 require_once('../config.php');
 ms_load_module('workshop');
 session_start();
 ms_manage_check_auth();
 global $ms_config;

 $dblink = ms_get_db_link();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Workshop</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
<script lang="javascript" type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script lang="javascript" type="text/javascript" src="util.js.php"></script>
</head>
<body>
<div>
<a href="../">回首頁</a> &nbsp; <a href=".">回管理首頁</a>
&nbsp;<a href="workshop-list.php">回研討會列表</a>
&nbsp;<a href="auth.php?logout">登出</a>
</div>
<hr />
<?php
 if (isset($_GET['delete']))
 {
   workshop_delete($dblink, intval($_GET['delete']));
   exit ('</body></html>');
 }
 else if (isset($_GET['modify']))
 {
   $target = workshop_load_one($dblink, intval($_GET['modify']));
 }
 else if (isset($_POST['title_cht']))
 {
   if (isset($_POST['id']))
   { workshop_update($dblink, $_POST); }
   else
   { workshop_write($dblink, $_POST); }
   exit ('</body></html>');
 }
 else
 {
   $target = Array (
    'title_cht' => '',
    'title_enu' => '',
    'date_begin' => '',
    'date_end' => '',
    'context_cht' => '',
    'context_enu' => '',
    'files' => '',
    'accomodation' => ''
   );
 }
?>
<div>
<form name="workshop-edit" action="workshop-edit.php" method="post">
<?php
 if (isset($_GET['modify']))
 {
   echo '<input type="hidden" name="id" value="'.
        $target['id'].'" />'."\n";
 }
?>
<div>
 <label for="title_cht">中文Title</label>
 <input type="text" name="title_cht" value="<?php echo $target['title_cht']; ?>" />
</div>
<div>
 <label for="title_enu">英文Title</label>
 <input type="text" name="title_enu" value="<?php echo $target['title_enu']; ?>" />
</div>
<div>
 <label for="date_begin">起始日期</label>
 <input type="text" name="date_begin" value="<?php echo $target['date_begin']; ?>" />
</div>
<div>
 <label for="date_end">結束日期</label>
 <input type="text" name="date_end" value="<?php echo $target['date_end']; ?>" />
</div>
<?php
 $files = workshop_process_files($target['files']);
 for ($i = 0;$i < 3;$i ++)
 {
?>
<div>
 <label for="<?php echo $files[$i]['field']; ?>"><?php echo $files[$i]['type']; ?></label>
 <input type="text" name="<?php echo $files[$i]['field']; ?>" value="<?php echo $files[$i]['name']; ?>" onclick="ms_kcf_workshop_selector(this);" />
</div>
<?php
 }
?>
<div>
 <label for="context_cht">中文內文</label>
 <textarea id="context_cht" name="context_cht" cols="80" rows="20">
 <?php echo $target['context_cht']; ?></textarea>
</div>
<div>
 <label for="context_enu">英文內文</label>
 <textarea id="context_enu" name="context_enu" cols="80" rows="20">
 <?php echo $target['context_enu']; ?></textarea>
</div>
<div>
 <label for="accomodation">住宿資訊</label>
 <textarea name="accomodation">
 <?php echo $target['accomodation']; ?></textarea>
</div>
<input type="reset" value="Reset" />
<input type="submit" value="Submit" />
</form>
</div>
<script lang="javascript" type="text/javascript">
if ( typeof CKEDITOR != 'undefined' )
{
  var editor_cht = CKEDITOR.replace( 'context_cht' );
  var editor_enu = CKEDITOR.replace( 'context_enu' );
}
</script>
</body>
</html>
<?php
 ms_close_db_link($dblink);
?>
