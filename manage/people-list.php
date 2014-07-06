<?php
  require_once('../config.php');
  ms_load_module('people');
  session_start();
  ms_manage_check_auth();
  global $ms_config;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>People List</title>
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
</head>
<body>
<div>
 <a href='..'>回首頁</a> &nbsp; <a href='.'>回管理首頁</a>
 &nbsp;<a href='people-edit.php'>新增人物</a>
 &nbsp;<a href='people-list.php'>人物列表</a>
 &nbsp;<a href='people-list.php?get_titles'>列出現有頭銜</a>
 <hr />
</div>
<?php
 if (isset($_GET['get_titles']))
 {
   $dblink = ms_get_db_link();
   echo "<div>\n";
   $res = people_get_titles($dblink);
   echo " <table>\n";
   echo "  <tr><th>#</th><th>Title Cht</th><th>Title Enu</th></tr>\n";
   $i = 0;
   foreach ($res as $t)
   {
     echo '<tr><td>'.$i.'</td><td>'.$t['title_cht'].'</td><td>'.
          $t['title_enu']."</td></tr>\n";
     $i ++;
   }
   echo "</table>\n";
   ms_close_db_link($dblink);

   exit ("</div></body></html>\n");
 }
?>
<?php
 $valid_types = people_get_type();
 $type_str = $valid_types[0];
 if (isset($_GET['type_str']) && people_verify_type($_GET['type_str']))
 {
   $type_str = $_GET['type_str'];
 }
?>
<div>
<form id="people-list" action="people-list.php" method="get">
 <fieldset>
 <div>
  <label for="type_str">人物類別</label>
  <select name="type_str">
<?php
   foreach ($valid_types as $t)
   {
     echo '<option value="'.$t.'"';
     if (!strcmp($type_str, $t))
     { echo ' selected="selected"'; }
     echo '>'.$t.'</option>'."\n";
   }
?>
  </select>
 </div>
 <div>
<?php
  $base = 0;
  if (isset($_GET['base']))
   $base = ms_base(intval($_GET['base']), 0);
?>
  <label for="base">Base</label>
  <input type="text" name="base" value="<?php echo $base; ?>" />
 </div>
 <input type="submit" value="Submit" />
 </fieldset>
</form>
</div>
<div>
<a href="people-list.php?type_str=<?php echo $type_str; ?>&amp;base=<?php
  echo ms_base($base, -PAGE_LENGTH);
 ?>">前一頁</a> &nbsp;
<a href="people-list.php?type_str=<?php echo $type_str; ?>&amp;base=<?php
 echo ms_base($base, +PAGE_LENGTH); ?>">後一頁</a>
</div>
<hr />
<table>
 <tr>
  <th>ID</th><th>Title</th><th>Name</th><th>Operation</th>
 </tr>
<?php
 $dblink = ms_get_db_link();
 $result = people_load_segment($dblink, $type_str, $base);
 $count = 0;
 foreach ($result as $p)
 {
   echo '<tr class="';
   if ($count % 2 == 1)
   { echo 'table_odd_row'; }
   else
   { echo 'table_even_row'; }
   echo '">';
   echo '<td>'.$p['id'].'</td><td>'.$p['title_cht'].'<br />'.
        $p['title_enu'].'</td><td>'.$p['lastname_cht'].'&nbsp;'.
	$p['firstname_cht'].'<br />'.$p['firstname_enu'].'&nbsp; '.
	$p['lastname_enu'].'</td>';
   echo '<td><a href="people-edit.php?modify='.$p['id'].'">編輯</a>'.
        '&nbsp; <a href="people-edit.php?delete='.$p['id'].'">刪除</a>'.
	'</td>'."\n";
   echo '</tr>'."\n";
   $count ++;
 }

 ms_close_db_link($dblink);
?>
</table>
</body>
</html>
