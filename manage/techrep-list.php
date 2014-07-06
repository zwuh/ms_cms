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
</head>
<body>
<div>
<a href="../">回首頁</a> &nbsp; <a href=".">回管理首頁</a>
&nbsp;<a href="techrep-edit.php">新增技術報告</a>
&nbsp;<a href="auth.php?logout">登出</a>
</div>
<hr />
<?php
 $year = 0;
 if (isset($_GET['year']))
 {
   $year = intval($_GET['year']);
 }
 $res = techrep_load_year($dblink, $year);
 $y_res = techrep_get_existing_years($dblink);
?>
<div>
Years:
<?php
 foreach ($y_res as $y)
 {
   echo '[<a href="techrep-list.php?year='.$y.'">'.$y.'</a>]';
 }
 echo "\n<br />\n";
?>
</div>
<hr />
<div>
<table>
 <tr><th>ID</th><th>Year</th><th>Serial</th><th>Title</th><th>Author</th>
     <th>Abstract</th><th>Fulltext</th><th>Operation</th>
 </tr>
<?php
 $count = 0;
 foreach ($res as $t)
 {
   echo '<tr class="';
   if ($count % 2 == 1)
   { echo 'table_odd_row'; }
   else
   { echo 'table_even_row'; }
   echo '">';
   echo '<td>'.$t['id'].'</td><td>'.$t['year'].'</td><td>'.$t['serial'].
        '</td><td>'.$t['title'].'</td><td>'.$t['author'].'</td>';
   echo '<td>'.$t['file_abstract'].'</td><td>'.$t['file_fulltext'].'</td>';
   echo '<td><a href="techrep-edit.php?modify='.$t['id'].'">編輯</a> '.
        '<a href="techrep-edit.php?delete='.$t['id'].'">刪除</a></td>';
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
