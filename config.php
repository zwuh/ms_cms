<?php

// TODO: Customize.

$ms_config['sitename'] = 'My Site';

/* Nodes with ID below or equal to this CANNOT be deleted */
$ms_config['special_nodes'] = 8;

$ms_config['secret'] = 'MY_SECRET_HERE';

define('NODE_HOMEPAGE', 1);
define('NODE_DEFAULT_SIDEBAR', 2);
define('NODE_FOOTER', 3);

define('LANG_CHT', 1);
define('LANG_ENU', 2);
$ms_config['lang_str'] = array (
 LANG_CHT => 'cht',
 LANG_ENU => 'enu'
);

define('PAGE_LENGTH', 30);

define('HOST_URL', 'http://test.my.domain/');
define('BASE_URL', HOST_URL.'ms_cms/');

define('ROOT_PATH', '/home/user/public_html/ms_cms/');
define('MODULE_PATH', ROOT_PATH.'lib/');
define('MANAGE_PATH', ROOT_PATH.'manage/');
define('UPLOAD_PATH', ROOT_PATH.'var/');
define('UPLOAD_URL_BASE', 'var/files/');

$ms_config['db'] = array (
 'hostname' => 'localhost',
 'port' => '',
 'username' => 'mscms',
 'password' => 'mscms',
 'database' => 'mscsm',
 'table_prefix' => 'mscms_'
);

$ms_config['lang'] = LANG_CHT;
$ms_config['debug'] = 1;

$ms_config['modules'] = array (
 'core' => MODULE_PATH.'core/core.php',
 'people' => MODULE_PATH.'people/people.php',
 'techrep' => MODULE_PATH.'techrep/techrep.php',
 'workshop' => MODULE_PATH.'workshop/workshop.php',
 'seminar' => MODULE_PATH.'seminar/seminar.php',
 'google' => MODULE_PATH.'google/google.php',
 'dummy' => ''
);

// Google Calendar integration module
$ms_config['mod_google'] = array (
 'zend_path' => MODULE_PATH.'google/ZendFramework-1.11.5/library',
 'calendar' => 'YOUR_GOOGLE_CALENDAR_ID',
 'oauth_consumer_key' => 'YOUR_KEY',
 'oauth_consumer_secret' => 'YOUR_SECRET',
 'source' => 'MySite-mscms',
 'hosted' => '', // Your Google hosted domain, if applicable.
 'scope' => 'http://www.google.com/calendar/feeds/',
 'callback_url' => BASE_URL.'lib/google/retrieve_token.php',
 'request_uri' => 'https://www.google.com/accounts/OAuthGetRequestToken',
 'authorize_uri' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
 'access_uri' => 'https://www.google.com/accounts/OAuthGetAccessToken'
);

require_once($ms_config['modules']['core']);

define('NODE_PEOPLE_CONTENT', 5);
define('NODE_WORKSHOP_CONTENT', 22);
define('NODE_SEMINAR_CONTENT', 21);
define('NODE_TECHREP_CONTENT', 23);

?>

