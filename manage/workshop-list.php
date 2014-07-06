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
</head>
<body>
<div>
<a href="../">回首頁</a> &nbsp; <a href=".">回管理首頁</a>
&nbsp;<a href="auth.php?logout">登出</a>
&nbsp;<a href="workshop-edit.php">新增研討會</a>
</div>
<hr />
<div>
<?php
 $base = 0;
 if (isset($_GET['base']))
 $base = ms_base(intval($_GET['base']), 0);
?>
 <a href="workshop-list.php?base=<?php
  echo ms_base($base, -PAGE_LENGTH); ?>">前一頁</a> &nbsp;
 <a href="workshop-list.php?base=<?php
  echo ms_base($base, +PAGE_LENGTH); ?>">後一頁</a> &nbsp;
</div>
<hr />
<div>
<table>
 <tr>
  <th>ID</th><th>Title</th><th>Date Begin</th><th>Date End</th>
  <th>Operations</th>  
 </tr>
<?php
  $res = workshop_load_brief_list($dblink, $base);
  $count = 0;
  foreach ($res as $t)
  {
    echo '<tr class="';
    if ($count % 2 == 1)
    { echo 'table_odd_row'; }
    else
    { echo 'table_even_row'; }
    echo '">';
    echo '<td>'.$t['id'].'</td><td>'.$t['title_cht'].'<br />'.
         $t['title_enu'].'</td><td>'.$t['date_begin'].'</td><td>'.
	 $t['date_end'].'</td><td>';
    echo '<a href="workshop-edit.php?modify='.$t['id'].'">編輯</a>&nbsp;'.
         '<a href="workshop-edit.php?delete='.$t['id'].'">刪除</a></td>';
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
