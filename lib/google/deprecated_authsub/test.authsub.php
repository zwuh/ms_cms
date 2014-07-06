<?php
require_once('../../config.php');
set_include_path(get_include_path().PATH_SEPARATOR.$ms_config['mod_google']['zend_path']);
require_once('Zend/Loader.php');
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_HttpClient');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Serializer');
$_authSubKeyFile = null;
$_authSubKeyFilePassphrase = null;
/*
try {
 $client = Zend_Gdata_ClientLogin::getHttpClient(
  'GOOGLE_ACCOUNT',
  'GOOGLE_PASSWORD',
  Zend_Gdata_Calendar::AUTH_SERVICE_NAME,
  null,
  'mscms-dev',
  null );
}
catch (Exception $e)
{
  echo 'Exception: '.$e->getMessage()."\n";
  die ();
}

echo 'token: '.$client->getClientLoginToken()."\n";
$serialized = Zend_Serializer::serialize($client);

*/
$f = fopen('s.txt', 'rw');
//fwrite($f, $serialized);
$serialized = fread($f, 8192);
fclose($f);


try {
 Zend_Loader::loadClass('Zend_Http_Client_Adapter_Socket');
 $n_cli = Zend_Serializer::unserialize($serialized);
} catch (Exception $e)
{ echo 'unserialize: '.$e->getMessage()."\n"; }

echo 'uns: token: '.$n_cli->getClientLoginToken()."\n";

$cal = new Zend_Gdata_Calendar($n_cli, 'mscms-dev');



/*
$client = google_get_calendar_client();
$cal = new Zend_Gdata_Calendar($client, $ms_config['mod_google']['source']);
*/

$cale = $cal->getCalendarListFeed();
foreach ($cale as $item)
{
  echo '==================================='."\n";
  echo 'title: '.$item->title->text."\n";
  echo 'id: '.$item->id->text."\n";
  echo 'Before-selected: selected? ';
  if ($item->getSelected()->getValue())
  { echo 'Yes'; } else { echo 'No'; } echo "\n";
  if (strpos($item->id->text, $ms_config['mod_google']['calendar']) === FALSE) 
  { echo 'Not the target'."\n";
    $item->setSelected(new Zend_Gdata_Calendar_Extension_Selected(FALSE));
  }
  else
  { echo 'This is IT!'."\n";
   try { $item->setSelected(new Zend_Gdata_Calendar_Extension_Selected(TRUE)); }
   catch (Exception $e)
   { die ('Error: '.$e->getMessage());
   }
  }
  echo 'After-selected: selected? ';
  if ($item->getSelected()->getValue())
  { echo 'Yes'; } else { echo 'No'; } echo "\n";
}

/*
$ev = $cal->getCalendarEventFeed();
foreach ($ev as $item)
{
  echo 'Event: '.$item->title->text."\n";
  echo ' Id: '.$item->id->text."\n";
  echo ' Edit: '.$item->getLink('edit')->href."\n";
}
*/

/*
$to_delete =
'';
try {
 $cal->delete($to_delete);
 }
catch (Exception $e)
{ echo 'Exception: '.$e->getMessage()."\n"; }
*/

/*
$cr = google_create_event($client, 'test', 'test text <strong>I</strong>',
 'void', '2011-04-13', '13:00', '2011-04-13', '13:13');
echo 'Created: '.$cr."\n";
*/

function google_get_calendar_client()
{
 global $ms_config;
 $conf = $ms_config['mod_google'];

 $service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
 try {
  $client = Zend_Gdata_ClientLogin::getHttpClient(
   $conf['email'], $conf['password'], $service, null,
   $conf['source']);
 }
 catch (Zend_Gdata_App_AuthException $e)
 {
 }
 catch (Zend_Gdata_App_HttpException $e)
 {
 }
 catch (Zend_Gdata_App_CaptchaRequiredException $e)
 {
 }

 return $client;
}

function google_create_event($client, $title, $desc, $where, $start_date, $start_time, $end_date, $end_time, $tz_offset = '+08')
{
 global $ms_config;
 $conf = $ms_config['mod_google'];
 $gc = new Zend_Gdata_Calendar($client);
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
 { echo 'Error: '.$e->getMessage()."\n"; }
 echo $created_event->getLink('edit')->href."\n";
 return $created_event->id->text;
}

?>
