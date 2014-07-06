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
<title>Seminar</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
</head>
<body>
<div>
<a href="../">回首頁</a> &nbsp; <a href=".">回管理首頁</a>
&nbsp;<a href="auth.php?logout">登出</a>
&nbsp;<a href="seminar-edit.php">新增演講</a>
</div>
<hr />
<div>
<form action="seminar-edit.php" method="get">
 <div>
  <label for="modify">直接修改(輸入ID)</label>
  <input type="text" name="modify" />
 </div>
 <input type="submit" value="Submit" />
</form>
<form action="seminar-edit.php" method="get">
 <div>
  <label for="delete">直接刪除(輸入ID)</label>
  <input type="text" name="delete" />
 </div>
 <input type="submit" value="Submit" />
</form>
<form action="seminar-list.php" method="get">
 <div>
  <label for="similar">同主題查詢(不分頁)(輸入ID)</label>
  <input type="text" name="similar" />
 </div>
 <input type="submit" value="Submit" />
</form>
<div>
<?php
 $base = 0;
 if (isset($_GET['base']))
 { $base = ms_base(intval($_GET['base']), 0); }
?>
<a href="seminar-list.php?base=<?php
 echo ms_base($base,-PAGE_LENGTH); ?>">前一頁</a> &nbsp;
<a href="seminar-list.php?base=<?php
 echo ms_base($base,+PAGE_LENGTH); ?>">後一頁</a> &nbsp;
</div>
<hr />
<div>
<table>
 <tr><th>ID</th><th>Chair</th><th>Title</th><th>Organization</th>
  <th>Topic</th><th>Operations</th>
 </tr>
<?php
 if (isset($_GET['similar']))
 {
   $res = seminar_load_similar_topics($dblink, intval($_GET['similar']));
   echo '<!-- SIMILAR QUERY -->';
 }
 else
 {
   $res = seminar_load_topics($dblink, $base);
 }
 $count = 0;
 foreach ($res as $t)
 {
   echo '<tr class="';
   if ($count % 2 == 1)
   { echo 'table_odd_row'; }
   else
   { echo 'table_even_row'; }
   echo '">';
   echo '<td>'.$t['id'].'</td><td>'.$t['lastname_cht'].' '.$t['firstname_cht'].
        '<br />'.$t['lastname_enu'].' '.$t['firstname_enu'].'</td><td>'.
	$t['title_cht'].'<br />'.$t['title_enu'].'</td><td>'.
	$t['organization_cht'].'<br />'.$t['organization_enu'].'</td><td>'.
	$t['topic_cht'].'<br />'.$t['topic_enu'].'</td>';
   echo '<td><a href="seminar-edit.php?modify='.$t['id'].'">編輯</a>&nbsp;'.
        '<a href="seminar-edit.php?delete='.$t['id'].'">刪除</a>'.
	'</td>';
   echo "</tr>\n";
   $count ++;
 }
?>
</table>
</div>
</body>
</html>
<?php
 ms_close_db_link($dblink);
?>
