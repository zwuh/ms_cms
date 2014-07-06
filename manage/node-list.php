<?php
 require_once('../config.php');
 session_start();
 ms_manage_check_auth();

 $dblink = ms_get_db_link();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>List Nodes</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
</head>
<body>
<div>
<a href="../">回首頁</a> &nbsp; <a href=".">回管理首頁</a>
&nbsp;<a href="auth.php?logout">登出</a>
&nbsp;<a href="node-edit.php">新增頁面</a>
</div>
<div>
<?php
 $base = 0;
 if (isset($_GET['base']))
  $base = ms_base(intval($_GET['base']), 0);
?>
<a href="node-list.php?base=<?php
 echo ms_base($base, -PAGE_LENGTH); ?>">前一頁</a> &nbsp;
<a href="node-list.php?base=<?php
 echo ms_base($base, +PAGE_LENGTH); ?>">後一頁</a> &nbsp;
</div>
<hr />
<p>
注意: ID小於或等於 <?php echo $ms_config['special_nodes']; ?> 的頁面是特殊頁面<br />
</p>
<table id="nodelist">
 <tr><th>Node ID</th><th>程式?</th><th>是側板?</th><th>側板 ID</th><th>主旨</th><th>最後修改</th><th>操作</th></tr>
<?php
  $result = ms_load_topic_list($dblink, $base);
  $count = 0;
  foreach ($result as $row)
  {
    echo '<tr class="';
    if ($count % 2 == 1)
    { echo 'table_odd_row'; }
    else
    { echo 'table_even_row'; }
    echo '">';
    echo '<td>'.$row['id'].'</td>';
    echo '<td>'.$row['is_program'].'</td>';
    echo '<td>'.$row['is_sidebar'].'</td>';
    echo '<td>'.$row['sidebar'].'</td>';
    echo '<td>'.$row['title_cht'].'</td>';
    echo '<td>'.$row['mod_time'].'</td>';
    echo '<td><a href="node-edit.php?modify='.$row['id'].'">編輯</a>&nbsp;'.
         '<a href="node-edit.php?delete='.$row['id'].'">刪除</a></td>';
    echo '</tr>'."\n";
    $count ++;
  }
  unset ($result);
?>
</table>
</body>
</html>
<?php
  ms_close_db_link($dblink);
?>
