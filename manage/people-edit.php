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
<title></title>
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
<script language="javascript" type="text/javascript" src="util.js.php"></script>
</head>
<body>
<div>
<a href="../">回首頁</a> &nbsp;
<a href=".">回管理首頁</a> 
&nbsp;<a href="auth.php?logout">登出</a>
&nbsp;<a href="people-list.php">回人物列表</a>
</div>
<?php
 $dblink = ms_get_db_link();
 if (isset($_POST['post']))
 {
   if (isset($_POST['id']))
   {
     people_update($dblink, $_POST);
   }
   else
   {
     people_write($dblink, $_POST);
   }
   exit ('</body></html>');
 }

 if (isset($_GET['delete']))
 {
   people_delete($dblink, intval($_GET['delete']));
   exit('</body></html>');
 }
 else if (isset($_GET['modify']))
 {
   $target = people_load_one($dblink, intval($_GET['modify']));
 }
 else
 {
   $target = Array(
     'title_cht' => '',
     'title_enu' => '',
     'firstname_cht' => '',
     'firstname_enu' => '',
     'lastname_cht' => '',
     'lastname_enu' => '',
     'address_cht' => '',
     'address_enu' => '',
     'field_cht' => '',
     'field_enu' => '',
     'telephone' => '',
     'fax' => '',
     'email' => '',
     'website' => '',
     'type_str' => '',
     'organization_cht' => '',
     'organization_enu' => '',
     'date_begin' => '',
     'date_end' => '',
     'photo_file' => ''
   );
 }
 ms_close_db_link($dblink);
?>
<form name="people-edit" action="people-edit.php" method="post">
<?php
  echo '<input type="hidden" name="post" value="1" />'."\n";
  if (isset($_GET['modify']))
  {
    echo '<input type="hidden" name="id" value="'.
         intval($_GET['modify']).'" />'."\n";
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
  <label for="lastname_cht">中文姓</label>
  <input type="text" name="lastname_cht" value="<?php echo $target['lastname_cht']; ?>" />
 </div>
 <div>
  <label for="firstname_cht">中文名</label>
  <input type="text" name="firstname_cht" value="<?php echo $target['firstname_cht']; ?>" />
 </div>
 <div>
  <label for="lastname_enu">英文姓</label>
  <input type="text" name="lastname_enu" value="<?php echo $target['lastname_enu']; ?>" />
 </div>
 <div>
  <label for="firstname_enu">英文名</label>
  <input type="text" name="firstname_enu" value="<?php echo $target['firstname_enu']; ?>" />
 </div>
 <div>
  <label for="type_str">類別</label>
  <select name="type_str">
<?php
  $valid_types = people_get_type();
  foreach ($valid_types as $t)
  {
    echo '   <option value="'.$t.'" ';
    if (!strcmp($target['type_str'], $t))
    { echo 'selected="selected"'; }
    echo '>'.$t.'</option>'."\n";
  }
?>
  </select>
 </div>
 <div>
  <label for="address_cht">中文地址</label>
  <textarea name="address_cht"><?php echo $target['address_cht']; ?></textarea>
 </div>
 <div>
  <label for="address_enu">英文地址</label>
  <textarea name="address_enu"><?php echo $target['address_enu']; ?></textarea>
 </div>
 <div>
  <label for="organization_cht">中文服務單位</label>
  <input type="text" name="organization_cht" value="<?php echo $target['organization_cht']; ?>" />
 </div>
 <div>
  <label for="organization_enu">英文服務單位</label>
  <input type="text" name="organization_enu" value="<?php echo $target['organization_enu']; ?>" />
 </div>
 <div>
  <label for="field_cht">中文研究領域</label>
  <textarea name="field_cht"><?php echo $target['field_cht']; ?></textarea>
 </div>
 <div>
  <label for="field_enu">英文研究領域</label>
  <textarea name="field_enu"><?php echo $target['field_enu']; ?></textarea>
 </div>
 <div>
  <label for="telephone">電話</label>
  <input type="text" name="telephone" value="<?php echo $target['telephone']; ?>" />
 </div>
 <div>
  <label for="fax">傳真</label>
  <input type="text" name="fax" value="<?php echo $target['fax']; ?>" />
 </div>
 <div>
  <label for="email">E-Mail</label>
  <input type="text" name="email" value="<?php echo $target['email']; ?>" />
 </div>
 <div>
  <label for="website">網站</label>
  <input type="text" name="website" value="<?php echo $target['website']; ?>" />
 </div>
 <div>
  <label for="date_begin">來訪日期</label>
  <input type="text" name="date_begin" value="<?php echo $target['date_begin']; ?>" />
 </div>
 <div>
  <label for="date_end">訪問結束</label>
  <input type="text" name="date_end" value="<?php echo $target['date_end']; ?>" />
 </div>
 <div>
  <label for="photo_file">照片</label>
  <input type="text" name="photo_file" value="<?php echo $target['photo_file']; ?>"  onclick="ms_kcf_photo_selector(this);" />
 </div>
 <input type="reset" name="reset" value="Reset" />
 <input type="submit" name="submit" value="Submit" />
</form>
</body>
</html>
