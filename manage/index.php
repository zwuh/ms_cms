<?php
  require_once('../config.php');
  session_start();
  ms_manage_check_auth();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Management System</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
</head>
<body>
<p>
<a href="../">回首頁</a> &nbsp;
<a href="auth.php?logout">登出</a>
</p>
<hr />
<div>
 <ul>
  <li><a href="node-list.php">頁面管理</a></li>
  <li><a href="people-list.php">人物管理</a></li>
  <li><a href="techrep-list.php">技術報告管理</a></li>
  <li><a href="workshop-list.php">研討會管理</a></li>
  <li><a href="seminar-list.php">演講管理</a></li>
 </ul>
</div>
</body>
</html>
