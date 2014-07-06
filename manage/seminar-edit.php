<?php
 require_once('../config.php');
 ms_load_module('seminar');
 session_start();
 ms_manage_check_auth();
 global $ms_config;

 $dblink = ms_get_db_link();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Edit Seminar</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
</head>
<body>
<div>
<a href="../">回首頁</a> &nbsp; <a href=".">回管理首頁</a>
&nbsp;<a href="auth.php?logout">登出</a>
&nbsp;<a href="seminar-list.php">回演講管理</a>
</div>
<hr />
<?php
 if (isset($_GET['modify']))
 {
   $target = seminar_load_one_topic($dblink, intval($_GET['modify']));
 }
 else if (isset($_GET['delete']))
 {
   seminar_delete_master($dblink, intval($_GET['delete']));
   exit ('</body></html>');
 }
 else if (isset($_POST['modify']))
 {
   seminar_update_master($dblink, $_POST);
   exit ('</body></html>');
 }
 else if (isset($_POST['new']))
 {
   seminar_write_master($dblink, $_POST);
   exit ('</body></html>');
 }
 else
 {
   $target = seminar_template_master();
 }
?>
<form name="edit-seminar" action="seminar-edit.php" method="post">
<?php
 if (isset($_GET['modify']))
 {
   echo '<input type="hidden" name="modify" value="'.$target['id'].'" />';
 }
 else
 {
   echo '<input type="hidden" name="new" value="0" />';
 }
 echo "\n";
?>
<div>
 <label for="topic_cht">中文Topic</label>
 <input type="text" name="topic_cht" value="<?php echo $target['topic_cht']; ?>" />
</div>
<div>
 <label for="topic_enu">英文Topic</label>
 <input type="text" name="topic_enu" value="<?php echo $target['topic_enu']; ?>" />
</div>
<div>
 <label for="firstname_cht">中文名</label>
 <input type="test" name="firstname_cht" value="<?php echo $target['firstname_cht']; ?>" />
</div>
<div>
 <label for="lastname_cht">英文名</label>
 <input type="text" name="lastname_cht" value="<?php echo $target['lastname_cht']; ?>" />
</div>
<div>
 <label for="title_cht">中文Title</label>
 <input type="text" name="title_cht" value="<?php echo $target['title_cht']; ?>" />
</div>
<div>
 <label for="firstname_enu">英文名</label>
 <input type="text" name="firstname_enu" value="<?php echo $target['firstname_enu']; ?>" />
</div>
<div>
 <label for="lastname_enu">英文姓</label>
 <input type="text" name="lastname_enu" value="<?php echo $target['lastname_enu']; ?>" />
</div>
<div>
 <label for="title_enu">英文Title</label>
 <input type="text" name="title_enu" value="<?php echo $target['title_enu']; ?>" />
</div>
<div>
 <label for="organization_cht">中文單位</label>
 <input type="text" name="organization_cht" value="<?php echo $target['organization_cht']; ?>" />
</div>
<div>
 <label for="organization_enu">英文單位</label>
 <input type="text" name="organization_enu" value="<?php echo $target['organization_enu']; ?>" />
</div>
<input type="reset" value="Reset" />&nbsp;
<input type="submit" value="submit" />
</form>
<?php
 if (isset($_GET['modify']))
 {
   $sessions = seminar_load_sessions_by_master($dblink, $target['id']);
?>
<hr />
<a href="seminar-session-edit.php?master_id=<?php echo $target['id']; ?>">新增演講場次</a>
<hr />
演講場次:<br />
<table>
 <tr><th>ID</th><th>Serial</th><th>Topic</th><th>Date</th><th>Time Begin</th>
   <th>Time End</th><th>Location</th><th>Files</th>
   <th>Operation</th>
 </tr>
<?php
 $count = 0;
 foreach ($sessions as $s)
 {
   echo '<tr class="';
   if ($count % 2 == 1)
   { echo 'table_odd_row'; }
   else
   { echo 'table_even_row'; }
   echo '">';
   echo '<td>'.$s['id'].'</td><td>'.$s['session_serial'].'</td><td>'.
        $s['topic_cht'].'<br />'.$s['topic_enu'].'</td><td>'.
	$s['date'].'</td><td>'.$s['time_begin'].'</td><td>'.
	$s['time_end'].'</td><td>'.$s['location_cht'].'<br />'.
	$s['location_enu'].'</td><td>';
   $files = seminar_process_files($s['file_types'], $s['file_names'], FALSE);
   array_walk($files, 'seminar_walk_session_files');
   echo '</td><td><a href="seminar-session-edit.php?modify='.$s['id'].
        '">編輯</a>&nbsp<a href="seminar-session-edit.php?delete='.
	$s['id'].'">刪除</a></td>';
   echo "<tr>\n";
   $count ++;
 }
?>
</table>
<?php
 }
?>
</body>
</html>
<?php
 ms_close_db_link($dblink);
?>
