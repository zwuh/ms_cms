<?php

set_include_path(get_include_path().PATH_SEPARATOR.$ms_config['mod_google']['zend_path']);
require_once('Zend/Loader.php');
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Oauth_Consumer');


function google_version()
{
}

function google_oauth_callback_url()
{
 global $ms_config;
 return $ms_config['mod_google']['callback_url'];
}

function google_oauth_get_request_token($callback_url = null)
{
 global $ms_config;
 $conf = $ms_config['mod_google'];
 if (!isset($_SESSION['google_request_token']) &&
     !isset($_SESSION['google_access_token']))
 {
  $zend_config = Array(
   'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
   'version' => '1.0',
   'callbackUrl' => google_oauth_callback_url(),
   'requestTokenUrl' => $conf['request_uri'],
   'consumerKey' => $conf['oauth_consumer_key'],
   'consumerSecret' => $conf['oauth_consumer_secret'],
   'hd' => $conf['hosted']
  );
  if (isset($callback_url))
  {
    $zend_config['callbackUrl'] = $callback_url;
  }
  $consumer = new Zend_Oauth_Consumer($zend_config);
  $token = $consumer->getRequestToken( Array( 'scope' => $conf['scope'] ) );
  $_SESSION['google_request_token'] = serialize($token);
 }
}

function google_oauth_authorize_token()
{
 global $ms_config;
 $conf = $ms_config['mod_google'];
 if (isset($_SESSION['google_request_token']) &&
     !isset($_SESSION['google_access_token']))
 {
  $zend_config = Array(
   'userAuthorizationUrl' => $conf['authorize_uri'],
   'consumerKey' => $conf['oauth_consumer_key'],
   'consumerSecret' => $conf['oauth_consumer_secret'],
  );
  $consumer = new Zend_Oauth_Consumer($zend_config);
  $consumer->redirect( Array ('hd' => $conf['hosted']),
                       unserialize($_SESSION['google_request_token']) );
 }
}

function google_oauth_get_access_token($query)
{
 global $ms_config;
 $conf = $ms_config['mod_google'];
 if (isset($_SESSION['google_request_token']))
 {
  $zend_config = Array(
   'accessTokenUrl' => $conf['access_uri'],
   'consumerKey' => $conf['oauth_consumer_key'],
   'consumerSecret' => $conf['oauth_consumer_secret'],
   'hd' => $conf['hosted']
  );
  $consumer = new Zend_Oauth_Consumer($zend_config);
  $token = $consumer->getAccessToken($query, unserialize($_SESSION['google_request_token']));
  $_SESSION['google_access_token'] = serialize($token);
  $_SESSION['google_request_token'] = null;
 }
 else
 {
  echo 'google_oauth_get_access_token: no request token'."\n";
 }
}

function google_oauth_revoke_token()
{
 if (isset($_SESSION['google_access_token']))
 {
  $token = unserialize($_SESSION['google_access_token']);
  Zend_Gdata_AuthSub::AuthSubRevokeToken($token->toString());
  $_SESSION['google_access_token'] = null;
 }
 if (isset($_SESSION['google_request_token']))
 {
  $_SESSION['google_request_token'] = null;
 }
}

function google_check_auth()
{
 global $ms_config;
 $conf = $ms_config['mod_google'];
 $client = google_get_client();
 $gc = new Zend_Gdata_Calendar($client, $conf['source']);
 $cal_feed = $gc->getCalendarListFeed();
 $match = FALSE;
 foreach ($cal_feed as $item)
 {
//   echo 'checking calendar id: '.rawurldecode($item->id->text)."\n";
   if (strpos(rawurldecode($item->id->text), $conf['calendar']))
   {
//     echo ' --> MATCH'."\n";
     $match = TRUE;
     break;
   }
 }
 return $match;
}

function google_get_client()
{
 global $ms_config;
 $conf = $ms_config['mod_google'];
 $zend_config = Array (
  'consumerKey' => $conf['oauth_consumer_key'],
  'consumerSecret' => $conf['oauth_consumer_secret'],
  'hd' => $conf['hosted']
 );
 if (isset($_SESSION['google_access_token']))
 {
  $access_token = unserialize($_SESSION['google_access_token']);
  try {
   $client = $access_token->getHttpClient($zend_config);
  }
  catch (Exception $e)
  {
    die ('google_get_client: error: '.$e->getMessage());
  }
 }
 else
 {
  echo 'google_get_client: access token not set ?'."\n";
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
