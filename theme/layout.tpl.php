<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page_title; ?></title>
<link href="theme/style.css" rel="stylesheet" type="text/css"></link>
</head>
<body>

<div id="wrapper">
 <div id="header">
  <div id="header_menu">
   <div class="header_option_nb">
   <a href="?node=4">簡介
   <br />
   About</a>
   </div>
   <div class="header_option_lb">
   <a href="?node=5">人物
   <br />
   People</a>
   </div>
   <div class="header_option_lb">
   <a href="?node=6">活動
   <br />
   Activities</a>
   </div>
   <div class="header_option_lb">
   <a href="?node=7">出版品
   <br />
   Publications</a>
   </div>
   <div class="header_option_lb">
   <a href="?node=8">常用連結
   <br />
   Links</a>
   </div>
   <div class="header_option_lb">
    <a href="manage/">管理系統<br />Manage</a>
   </div>
   <div class="header_option_lb">
    <a href="index.php">回首頁<br />Home</a>
   </div>
   <div class="header_option_lb" id="switch_lang">
<?php
 if (LANG_CHT != $page_lang)
 {
   echo '  <a href="?node='.$node_id.'&amp;lang=cht">中文版<br />Chinese</a>';
 }
 else
 {
   echo '  <a href="?node='.$node_id.'&amp;lang=enu">英文版<br />English</a>';
 }
?>
   </div>
  </div>
 </div>
 <div id="nav_content">
  <div id="nav">
   <?php echo $nav; ?>
  </div>
  <div id="content">
   <?php echo $content; ?>
  </div>
 </div>
 <div id="footer">
  <?php echo $footer; ?>
 </div>
</div>
</body>
</html>
