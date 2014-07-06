<?php

set_include_path(get_include_path().PATH_SEPARATOR.$ms_config['mod_google']['zend_path']);
require_once('Zend/Loader.php');
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Serializer');

function google_show_auth_field($field_name = 'google_password')
{
 global $ms_config;
 $conf = $ms_config['mod_google'];

 echo '<fieldset><legend>Google Calendar Integration</legend>'."\n";
 echo '<div>';
 echo ' <p>Calendar ID: <a href="http://www.google.com/calendar/embed?src='.
      $conf['calendar'].'">'.$conf['calendar'].'</a><br />'."\n";
 echo ' Account to authenticate: '.$conf['email'].'</p>'."\n";
 echo ' <label for="'.$field_name.'">Password</label>'."\n";
 echo ' <input type="password" name="'.$field_name.'" />'."\n";
 echo '</div></fieldset>'."\n";
}

function google_authsub_url()
{
 global $ms_config;
 $conf = $ms_config['mod_google'];
 $next_url = BASE_URL.'lib/google/retrieve_token.php';
 $scope = 'http://www.google.com/calendar/feeds/';
 $secure = 0;
 $session = 1;
 $authsub_url = Zend_Gdata_AuthSub::getAuthSubTokenUri(
   $next_url, $scope, $secure, $session);
 if (isset($conf['hosted']))
 { $authsub_url .= '&hd='.$conf['hosted']; }
 return $authsub_url;
}

function google_authsub_get_session_token()
{
  if (isset($_GET['token']))
  {
    setcookie('gdata_session', '');
    unset($_COOKIE['gdata_session']);
    echo 'google_authsub_get_session_token: processing GET token';
    $session_token = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
    $encoded_token = base64_encode(Zend_Serializer::serialize($session_token));
    setcookie('gdata_session', $encoded_token);
    $_COOKIE['gdata_session'] = $encoded_token;
    return $session_token;
  }
  return null;
}

function google_authsub_revoke()
{
  if (isset($_COOKIE['gdata_session']))
  {
    $session_token = Zend_Serializer::unserialize(base64_decode($_COOKIE['gdata_session']));
    setcookie('gdata_session', '');
    unset($_COOKIE['gdata_session']);
    $was_revoked = Zend_Gdata_AuthSub::AuthSubRevokeToken($session_token);
    return $was_revoked;
  }
  return FALSE;
}

function google_get_session_token()
{
  echo '====== google_get_session_token ==='."\n";
  print_r($_COOKIE);
  if (isset($_COOKIE['gdata_session']))
  {
    return Zend_Serializer::unserialize(base64_decode($_COOKIE['gdata_session']));
  }
  return FALSE;
}

function google_check_auth()
{
 global $ms_config;
 $conf = $ms_config['mod_google'];
 if (!($session_token = google_get_session_token()))
 {
   echo 'google_check_auth: cannot obtail session token from cookie.'."\n";
   return FALSE;
 }
 $client = Zend_Gdata_AuthSub::getHttpClient($session_token);
 $gc = new Zend_Gdata_Calendar($client, $conf['source']);
// $gc->setAuthSubToken($session_token);
 $cal_feed = $gc->getCalendarListFeed();
 $match = FALSE;
 foreach ($cal_feed as $item)
 {
   echo 'checking calendar id: '.rawurldecode($item->id->text)."\n";
   if (strpos(rawurldecode($item->id->text), $conf['calendar']))
   {
     echo ' --> MATCH'."\n";
     $match = TRUE;
     break;
   }
 }
 return $match;
}

function google_get_client($password)
{
 global $ms_config;
 $conf = $ms_config['mod_google'];

 $service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
 try {
  $client = Zend_Gdata_ClientLogin::getHttpClient(
   $conf['email'], $password, $service, null,
   $conf['source']);
 }
 catch (Exception $e)
 {
   die ('google_get_client: error: '.$e->getMessage());
 }

 return $client;
}

function google_create_event($client, $title, $desc, $where, $start_date, $start_time, $end_date, $end_time, $tz_offset = '+08')
{
 global $ms_config;
 $conf = $ms_config['mod_google'];

$gc = new Zend_Gdata_Calendar($client, $conf['source']);
 $entry = $gc->newEventEntry();
 $entry->title = $gc->newTitle(trim($title));
 $entry->where = Array($gc->newWhere($where));
 $entry->content = $gc->newContent($desc);
 $when = $gc->newWhen();
 $when->startTime = "{$start_date}T{$start_time}:00.000{$tz_offset}:00";
 $when->endTime = "{$end_date}T{$end_time}:00.000{$tz_offset}:00";
 $entry->when = Array($when);
 try {
  $created_event = $gc->insertEvent($entry, 'http://www.google.com/calendar/feeds/'.$conf['calendar'].'/private/full');
 }
 catch (Exception $e)
 {
  echo 'google_create_event: error: '.$e->getMessage()."\n";
 }
 return html_entity_decode($created_event->getLink('edit')->href);
// return $created_event->id->text;
}

function google_delete_event($client, $edit_url)
{
 global $ms_config;
 $conf = $ms_config['mod_google'];

 $cal = new Zend_Gdata_Calendar($client, $conf['source']);
 try {
  $cal->delete($edit_url);
 }
 catch (Exception $e)
 {
   echo 'google_delete_event: error: '.$e->getMessage()."\n";
 }
}

?>
