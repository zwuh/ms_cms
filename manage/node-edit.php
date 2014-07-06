<?php
  require_once('../config.php');
  session_start();
  ms_manage_check_auth();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>EDIT NODE</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>
<body>
<div>
<a href="../">回首頁</a> &nbsp;
<a href=".">回管理首頁</a>
&nbsp;<a href="auth.php?logout">登出</a>
&nbsp;<a href="node-list.php">頁面列表</a>
</div>
<?php
$dblink = ms_get_db_link();

if (isset($_POST['post']))
{
  echo 'Got post request!<br />';
  if (isset($_POST['id']))
  { echo 'ID: '.$_POST['id'].'<br />'; }
  echo 'Title Cht: '.$_POST['title_cht'].'<br />';
  echo 'Title Enu: '.$_POST['title_enu'].'<br />';
  if (isset($_POST['is_sidebar']))
  { echo 'Is Sidebar: '.$_POST['is_sidebar'].'<br />'; }
  else
  { echo 'This entry is not a SIDEBAR.<br />'; }
  if (isset($_POST['is_program']))
  { echo 'Is Program: '.$_POST['is_program'].'<br />'; }
  else
  { echo 'This entry is not a PROGRAM LINK.<br />'; }
  echo 'Sidebar: '.$_POST['sidebar'].'<br />';
  echo 'Cht Ctx: <hr />'.htmlspecialchars($_POST['context_cht']).'<hr />';
  echo 'Enu Ctx: <hr />'.htmlspecialchars($_POST['context_enu']).'<hr />';

  $node = array(
   'title_cht' => ms_escape_string($dblink, $_POST['title_cht']),
   'title_enu' => ms_escape_String($dblink, $_POST['title_enu']),
   'is_sidebar' => intval(isset($_POST['is_sidebar'])),
   'is_program' => intval(isset($_POST['is_program'])),
   'sidebar' => intval($_POST['sidebar']),
   'context_cht' => ms_escape_string($dblink, $_POST['context_cht']),
   'context_enu' => ms_escape_string($dblink, $_POST['context_enu'])
  );

  if (isset($_POST['id'])) /* modification */
  {
    $node['id'] = intval($_POST['id']);
    ms_update_old_node($dblink, $node);
  }
  else /* new node */
  {
    ms_write_new_node($dblink, $node);
  }

  exit('Done insertion/updating, execution halted.<br /></body></html>');
}

if (isset($_GET['delete']))
{
  echo 'Got delete request<br />';
  $id = intval($_GET['delete']);
  echo 'Target node id: '.$id.'<br />';
  ms_delete_node($dblink, $id);

  exit ('Done deletion, execution halted.<br /></body></html>');
}

?>
<hr />
<div>
提醒:<br />
<ul>
 <li>不要設定文字的字型(font), 這既沒有用又破壞相容性還佔空間</li>
 <li>側板的「高度」不可以超過正文, 否則頁尾會蓋過側板</li>
 <li>超連結連到站內其他頁面, 只需要輸入「?node=(編號)」, 通訊協定選「其他」</li>
 <li>用「瀏覽伺服器檔案」建立超連結時, 通訊協定請選「其他」. 此功能無法在下面的編輯器做預覽.</li>
 <li>使用「程式連結」時, 請在英文主旨填入目標.</li>
</ul>
</div>
<hr />

<form id="editnode" action="node-edit.php" method="post">
<div>
<input type="hidden" name="post" />
<?php
 if (isset($_GET['modify'])) /* get one for modification */
 {
//   echo 'EDIT Request for node #'.$_GET['modify'];
   $n_id = intval($_GET['modify']);
   $node = ms_load_full_node($dblink, $n_id);
   echo '<input type="hidden" name="id" value="'.$node['id'].'" />';
 }
 else /* new node */
 {
   $node = array(
     'id' => '',
     'title_cht' => '主題',
     'title_enu' => 'Title',
     'is_sidebar' => '0',
     'is_program' => '0',
     'sidebar' => '2',
     'context_cht' => '中文內文',
     'context_enu' => 'English Context'
   );
 }
?>
</div>
<div>
<label for="title_cht">中文主旨</label>
<input type="text" id="title_cht" name="title_cht" value="<?php echo $node['title_cht']; ?>" />
</div>
<div>
<label for="title_enu">英文主旨</label>
<input type="text" id="title_enu" name="title_enu" value="<?php echo $node['title_enu']; ?>" />
</div>
<div>
 <label for="is_program">這個node是程式連結?</label>
 <input type="checkbox" id="is_program" name="is_program" value="1"
 <?php
  if ($node['is_program'] == 1)
   echo ' checked';
 ?> />
</div>
<div>
 <label for="is_sidebar">這個node是側板?</label>
 <input type="checkbox" id="is_sidebar" name="is_sidebar" value="1"
 <?php
  if ($node['is_sidebar'] == 1)
   echo ' checked';
 ?> />
</div>
<div>
 <label for="sidebar">這一頁搭配的側板ID (留空白即為預設值)</label>
 <input type="text" id="sidebar" name="sidebar" value="<?php echo $node['sidebar']; ?>" />
</div>
<div>
 <label for="context_cht">中文內文</label>
 <textarea id="context_cht" name="context_cht" cols="80" rows="20">
 <?php echo $node['context_cht']; ?></textarea>
</div>
<div>
 <label for="context_enu">英文內文</label>
 <textarea id="context_enu" name="context_enu" cols="80" rows="20">
 <?php echo $node['context_enu']; ?></textarea>
</div>
<div>
<input type="submit" value="Submit" />
</div>
</form>
<script type="text/javascript">
if ( typeof CKEDITOR != 'undefined' )
{
  var editor_cht = CKEDITOR.replace( 'context_cht' );
  var editor_enu = CKEDITOR.replace( 'context_enu' );
}
</script>
<?php
 ms_close_db_link($dblink);
?>
</body>
</html>
