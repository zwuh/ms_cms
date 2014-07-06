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
<title>Seminar Session Edit</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
<script language="javascript" type="text/javascript" src="util.js.php"></script>
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
    $target = seminar_load_one_session($dblink, intval($_GET['modify']));
  }
  else if (isset($_GET['delete']))
  {
    seminar_delete_session($dblink, intval($_GET['delete']));
    exit ('</body></html>');
  }
  else if (isset($_POST['modify']))
  {
    seminar_update_session($dblink, $_POST);
    exit ('</body></html>');
  }
  else if (isset($_POST['new']))
  {
    seminar_write_session($dblink, $_POST);
    exit ('</body></html>');
  }
  else
  {
    $target = seminar_template_session();
  }
?>
<form action="seminar-session-edit.php" method="post">
<div>
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
 echo '<input type="hidden" name="master_id" value="';
 if (isset($_GET['modify']))
 { echo $target['master_id']; }
 else
 { echo intval($_GET['master_id']); }
 echo '" />';
 echo "\n";
 echo '<input type="hidden" name="gcal_edit" value="'.$target['gcal_edit'].'" />'."\n";
?>
</div>
<div>
 <p>注意: 一定要是 yyyy-mm-dd , 沒有十位數要自己補零, 否則Google Calendar無法同步</p>
 <label for="date">Date (yyyy-mm-dd)</label>
 <input type="text" name="date" value="<?php echo $target['date']; ?>" />
</div>
<div>
 <p>注意: 一定要是 hh:mm , 24小時制、沒有十位數要自己補零, 否則Google Calendar無法同步</p>
 <label for="time_begin">Time Begin (hh:mm)</label>
 <input type="text" name="time_begin" value="<?php echo $target['time_begin']; ?>" />
</div>
<div>
 <label for="time_end">Time End (hh:mm)</label>
 <input type="text" name="time_end" value="<?php echo $target['time_end']; ?>" />
</div>
<div>
 <label for="topic_cht">中文Topic</label>
 <input type="text" name="topic_cht" value="<?php echo $target['topic_cht']; ?>" />
</div>
<div>
 <label for="topic_enu">英文Topic</label>
 <input type="text" name="topic_enu" value="<?php echo $target['topic_enu']; ?>" />
</div>
<div>
 <label for="session_serial">場次序</label>
 <input type="text" name="session_serial" value="<?php echo $target['session_serial']; ?>" />
</div>
<?php

 $files = seminar_process_files($target['file_types'],
                                $target['file_names'], TRUE);
 for ($i = 0;$i < 4;$i ++)
 {
   echo '<div>'."\n";
   echo ' <label>Type</label>'."\n";
   echo ' <select name="type[]">'."\n";
   seminar_build_type_options($files[$i]['type']);
   echo ' </select>'."\n";
   echo ' <label>File</label>'."\n";
   echo ' <input type="text" name="file[]" value="'.$files[$i]['name'].
        '" onclick="ms_kcf_seminar_selector(this);" />'."\n";
   echo '</div>'."\n";
 }
?>
<div>
 <label for="location_cht">中文Location</label>
 <input type="text" name="location_cht" value="<?php echo $target['location_cht']; ?>" />
</div>
<div>
 <label for="location_enu">英文Location</label>
 <input type="text" name="location_enu" value="<?php echo $target['location_enu']; ?>" />
</div>
<div>
 <input type="reset" value="Reset" />&nbsp;
 <input type="submit" value="Submit" />
</div>
</form>
</body>
</html>
<?php
 ms_close_db_link($dblink);
?>
