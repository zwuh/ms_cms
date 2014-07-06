<?php
  require_once('../config.php');
  ms_load_module('google');
  session_start();

  $dblink = ms_get_db_link();
  $message = 'Login Required';

  if (isset($_GET['logout']))
  {
    ms_auth_logout($dblink);
    if (function_exists('google_version'))
    {
      google_oauth_revoke_token();
    }
    $message = 'Logged-out.<br />';
  }
  else if (ms_check_auth())
  {
    if (isset($_SESSION['google_access_token']) &&
        google_check_auth())
    {
      $message = 'REDIRECT (has_google)';
    }
    else if (isset($_SESSION['google_request_token']))
    {
      try {
        google_oauth_get_access_token($_GET);
      } catch (Exception $e)
      {
        $message = 'REDIRECT (Google: Unauthorized request token?)';
      }
      if (google_check_auth())
      {
        $message = 'REDIRECT (Google: success)';
      }
      else
      {
        $message = 'REDIRECT (Google: Cannot get access token)';
      }
    }
    else
    {
      google_oauth_revoke_token();
      $message = 'REDIRECT (Google: OAuth failed)';
    }
  }
  else if (isset($_POST['username']))
  {
    if (ms_auth($dblink, $_POST['username'], $_POST['password']))
    {
      if (function_exists('google_version') &&
          !isset($_POST['no_google']))
      {
        $message = 'Doing Google OAuth';
	google_oauth_get_request_token(BASE_URL.'manage/auth.php');
	google_oauth_authorize_token();
      }
      $message = 'REDIRECT';
    }
    else
    {
      $message = 'Authentication failed.<br />';
    }
  }

  ms_close_db_link($dblink);

  if (FALSE !== strpos($message, 'REDIRECT'))
  {
    header('Location: index.php');
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="../theme/style.css" rel="stylesheet" type="text/css"></link>
</head>
<body>
<?php
 echo $message."<br />\n";
?>
<hr />
<a href="../">回首頁</a>
&nbsp;<a href="auth.php?logout">登出</a>
<hr />
<form method="post" action="auth.php">
<?php
 if (function_exists('google_version'))
 {
?>
<div>
 <label for="no_google">不認證Google</label>
 <input type="checkbox" id="no_google" name="no_google" />
 <p>注意: 若選擇不認證Google, 將無法使用演講行事曆同步功能</p>
</div>
<?php
 }
?>
<div>
 <label for="username">Username</label>
 <input type="text" name="username" />
</div>
<div>
 <label for="password">Password</label>
 <input type="password" name="password" />
</div>
<input type="reset" value="Reset" /> &nbsp;
<input type="submit" value="Authenticate" />
</form>
</body>
</html>
