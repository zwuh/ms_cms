<?php
require_once('../../config.php');
require_once('google.php');

session_start();

echo 'point 0'."\n";
google_oauth_get_request_token();
echo 'point 1'."\n";
print_r($_SESSION);
print_r($_COOKIE);
//die ();
google_oauth_authorize_token();

echo 'point 2'."\n";
/*
echo 'GET:'."\n";
print_r($_GET);
google_oauth_get_access_token($_GET);
echo 'point 3'."\n";
echo 'SESSION:'."\n";
print_r($_SESSION);
*/
/*
$client = google_get_calendar_client();
$cal = new Zend_Gdata_Calendar($client, $ms_config['mod_google']['source']);
*/

/*
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
*/

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


?>
